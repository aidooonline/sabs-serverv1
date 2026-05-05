<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemReportController extends Controller
{
    /**
     * Get high-level financial health stats for the company.
     */
    public function getDormancyStats()
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            
            $stats = [
                'total_active' => DB::table('nobs_user_account_numbers')->where('comp_id', $compId)->where('account_status', 'active')->count(),
                'total_dormant' => DB::table('nobs_user_account_numbers')->where('comp_id', $compId)->where('account_status', 'dormant')->count(),
                'last_scan' => DB::table('accounts')->where('id', $compId)->value('loan_cron_last_run')
            ];

            return response()->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get detailed list of dormant accounts for UI rendering.
     * ACCURACY OPTIMIZED: Synchronized with history running balance.
     */
    public function getDormantList()
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $user = auth()->user();
            if (!$user) return response()->json(['success' => false, 'message' => 'Session expired'], 401);

            $compId = (int)$user->comp_id;
            $isAgent = $this->isAgentOnly();
            $userId = auth()->id();

            if (!$compId) return response()->json(['success' => false, 'message' => 'Company context missing'], 400);

            // --- HIGH-PRECISION INTEGRITY QUERY ---
            $query = DB::table('nobs_user_account_numbers as ua')
                ->select(
                    'ua.id', 
                    'ua.account_number',
                    'ua.account_type',
                    // Name Lookup via Subquery (properly bound compId)
                    DB::raw("(SELECT COALESCE(first_name, 'Unknown') FROM nobs_registration 
                              WHERE account_number = ua.primary_account_number AND comp_id = $compId LIMIT 1) as first_name"),
                    DB::raw("(SELECT COALESCE(surname, 'Customer') FROM nobs_registration 
                              WHERE account_number = ua.primary_account_number AND comp_id = $compId LIMIT 1) as surname"),
                    DB::raw("(SELECT phone_number FROM nobs_registration 
                              WHERE account_number = ua.primary_account_number AND comp_id = $compId LIMIT 1) as phone_number"),
                    // THE SOURCE OF TRUTH
                    DB::raw("(SELECT balance FROM nobs_transactions 
                              WHERE account_number = ua.account_number 
                              AND account_type = ua.account_type 
                              AND comp_id = $compId AND is_shown = 1 AND row_version = 2 
                              ORDER BY id DESC LIMIT 1) as last_transaction_balance"),
                    'ua.balance as stored_balance',
                    DB::raw("COALESCE(ua.last_transaction_date, ua.created_at) as last_active_date"),
                    DB::raw("DATEDIFF(NOW(), COALESCE(ua.last_transaction_date, ua.created_at)) as days_inactive")
                )
                ->where('ua.comp_id', $compId)
                ->where('ua.account_status', 'dormant')
                ->where('ua.account_type', 'NOT LIKE', '%Loan%');

            if ($isAgent) {
                // If agent, filter by registration link
                $query->whereExists(function($q) use ($userId) {
                    $q->select(DB::raw(1))
                      ->from('nobs_registration')
                      ->whereColumn('account_number', 'ua.primary_account_number')
                      ->where('user', $userId);
                });
            }

            $list = $query->orderBy('ua.last_transaction_date', 'DESC')
                          ->orderBy('ua.created_at', 'DESC')
                          ->paginate(50);

            // Sync virtual column for UI
            $list->getCollection()->transform(function($item) {
                $item->recalculated_balance = $item->last_transaction_balance ?? $item->stored_balance ?? 0;
                return $item;
            });

            return response()->json(['success' => true, 'data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get history of automated SMS logs.
     */
    public function getSmsLogs()
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            $logs = DB::table('sms_logs')
                ->leftJoin('nobs_registration', 'sms_logs.customer_id', '=', 'nobs_registration.id')
                ->where('sms_logs.comp_id', $compId)
                ->select(
                    'sms_logs.*',
                    'nobs_registration.first_name',
                    'nobs_registration.surname'
                )
                ->orderBy('sms_logs.created_at', 'DESC')
                ->paginate(30);

            return response()->json(['success' => true, 'data' => $logs]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Data Integrity Report (Orphan accounts, mismatched balances).
     */
    public function getIntegrityReport()
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            
            $orphans = DB::table('nobs_user_account_numbers')
                ->where('comp_id', $compId)
                ->whereNotExists(function($q) {
                    $q->select(DB::raw(1))->from('nobs_registration')
                      ->whereColumn('account_number', 'nobs_user_account_numbers.primary_account_number');
                })->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'orphan_accounts' => $orphans,
                    'health_score' => $orphans > 0 ? 'Warning' : 'Healthy'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function isManagement()
    {
        $user = \Auth::user() ?: \Auth::guard('api')->user();
        if (!$user) return false;
        
        $role = strtolower($user->type_name ?: $user->type ?: 'Staff');
        $managementRoles = ['admin', 'manager', 'owner', 'super admin', 'god admin'];
        
        // Check against type string OR Spatie role if available
        $hasMgmtType = in_array($role, $managementRoles);
        $hasMgmtRole = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['Admin', 'Owner', 'super admin', 'Manager', 'God Admin']);

        return $hasMgmtType || $hasMgmtRole;
    }

    private function isAgentOnly()
    {
        $user = \Auth::user() ?: \Auth::guard('api')->user();
        if (!$user) return false;
        
        // They are an Agent ONLY if they don't have management privileges
        return !$this->isManagement();
    }
}
