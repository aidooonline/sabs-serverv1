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
            // 1. Bulk Update accounts with old transactions (Strictly isolated by comp_id)
            $count1 = DB::table('nobs_user_account_numbers')
                ->where('comp_id', $companyId)
                ->where('account_status', 'active')
                ->whereNotNull('last_transaction_date')
                ->where('last_transaction_date', '<', $cutoffDate)
                ->update(['account_status' => 'dormant', 'updated_at' => now()]);

            // 2. Bulk Update NEW accounts with NO transactions that are older than 90 days
            $count2 = DB::table('nobs_user_account_numbers')
                ->where('comp_id', $companyId)
                ->where('account_status', 'active')
                ->whereNull('last_transaction_date')
                ->where('created_at', '<', $cutoffDate)
                ->update(['account_status' => 'dormant', 'updated_at' => now()]);

            $total = $count1 + $count2;

            Log::info("Dormancy Check [Comp: $companyId]: Flagged $total accounts as dormant.");
            return $total;

        } catch (\Exception $e) {
            Log::error("Dormancy Check Failed [Comp: $companyId]: " . $e->getMessage());
            return 0;
        }
    }
}
