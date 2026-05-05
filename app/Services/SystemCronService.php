<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SystemCronService
{
    /**
     * Finds accounts with no activity for 90+ days and flags them as dormant.
     * Restricted strictly by Company ID for data integrity.
     */
    public function updateDormancyStatus($companyId)
    {
        $cutoffDate = Carbon::now()->subDays(90)->toDateTimeString();

        try {
            // 1. RECOVERY & INTEGRITY: Automatically activate any accounts (including LOANS) that have recent activity or are currently dormant but fresh
            $recovered = DB::table('nobs_user_account_numbers')
                ->where('comp_id', $companyId)
                ->where('account_status', 'dormant')
                ->where(function($q) use ($cutoffDate) {
                    // Rule: If it's a Loan OR it's had a transaction in the last 90 days
                    $q->where('account_type', 'LIKE', '%Loan%')
                      ->orWhere('account_type', 'LIKE', '%Repayment%')
                      ->orWhere('last_transaction_date', '>=', $cutoffDate);
                })
                ->update(['account_status' => 'active', 'updated_at' => now()]);

            // 2. Bulk Update accounts with old transactions (Exclude Loans)
            $count1 = DB::table('nobs_user_account_numbers')
                ->where('comp_id', $companyId)
                ->where('account_status', 'active')
                ->where('account_type', 'NOT LIKE', '%Loan%')
                ->where('account_type', 'NOT LIKE', '%Repayment%')
                ->whereNotNull('last_transaction_date')
                ->where('last_transaction_date', '<', $cutoffDate)
                ->update(['account_status' => 'dormant', 'updated_at' => now()]);

            // 3. Bulk Update NEW accounts with NO transactions (Exclude Loans)
            $count2 = DB::table('nobs_user_account_numbers')
                ->where('comp_id', $companyId)
                ->where('account_status', 'active')
                ->where('account_type', 'NOT LIKE', '%Loan%')
                ->where('account_type', 'NOT LIKE', '%Repayment%')
                ->whereNull('last_transaction_date')
                ->where('created_at', '<', $cutoffDate)
                ->update(['account_status' => 'dormant', 'updated_at' => now()]);

            $total = $count1 + $count2;

            Log::info("Dormancy Check [Comp: $companyId]: Flagged $total accounts as dormant. (Loans Excluded)");
            return $total;

        } catch (\Exception $e) {
            Log::error("Dormancy Check Failed [Comp: $companyId]: " . $e->getMessage());
            return 0;
        }
    }
}
