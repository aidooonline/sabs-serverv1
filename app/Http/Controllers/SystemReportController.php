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
     * SPEED OPTIMIZED: Uses stored balance and indexed joins.
     */
    public function getDormantList()
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            $isAgent = $this->isAgentOnly();
            $userId = auth()->id();

            // --- PERFORMANCE OPTIMIZED QUERY ---
            // Using JOIN instead of subqueries for names. Ensure MIN(id) to avoid duplicate customer rows.
            $query = DB::table('nobs_user_account_numbers as ua')
                ->leftJoin('nobs_registration as reg', function($join) {
                    $join->on('ua.primary_account_number', '=', 'reg.account_number')
                         ->whereRaw('reg.id = (SELECT MIN(id) FROM nobs_registration as r2 WHERE r2.account_number = reg.account_number)');
                })
                ->select(
                    'ua.id', 
                    'ua.account_number',
                    'ua.account_type',
                    DB::raw("COALESCE(reg.first_name, 'Unknown') as first_name"),
                    DB::raw("COALESCE(reg.surname, 'Customer') as surname"),
                    'reg.phone_number',
                    'ua.balance', // Instant loading from account table
                    DB::raw("COALESCE(ua.last_transaction_date, ua.created_at) as last_active_date"),
                    DB::raw("DATEDIFF(NOW(), COALESCE(ua.last_transaction_date, ua.created_at)) as days_inactive")
                )
                ->where('ua.comp_id', $compId)
                ->where('ua.account_status', 'dormant');

            if ($isAgent) {
                $query->where('reg.user', $userId);
            }

            // PAGINATION: Critical for 3,700+ rows
            $list = $query->orderBy('ua.last_transaction_date', 'DESC')
                          ->orderBy('ua.created_at', 'DESC')
                          ->paginate(50);

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
        $user = auth()->user();
        if (!$user) return false;
        
        $role = strtolower($user->type_name ?? $user->type ?? 'Staff');
        $mgmtRoles = ['admin', 'manager', 'owner', 'super admin', 'god admin'];
        
        return in_array($role, $mgmtRoles);
    }

    private function isAgentOnly()
    {
        $user = auth()->user();
        if (!$user) return false;
        
        $role = strtolower($user->type_name ?? $user->type ?? 'Staff');
        $mgmtRoles = ['admin', 'manager', 'owner', 'super admin', 'god admin'];
        
        // They are an Agent ONLY if they don't have management roles but have the Agent type/role
        return !in_array($role, $mgmtRoles) && ($role === 'agent' || $role === 'staff');
    }
}
