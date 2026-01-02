<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SystemCronService
{
    /**
     * Finds accounts with no activity for 90+ days and flags them as dormant.
     */
    public function updateDormancyStatus()
    {
        $cutoffDate = Carbon::now()->subDays(90);
        $count = 0;

        try {
            // Find accounts that are currently active but their last transaction was before the cutoff
            $accountsToUpdate = DB::table('nobs_user_account_numbers')
                ->where('account_status', 'active')
                ->where('last_transaction_date', '<', $cutoffDate)
                ->get();
            
            foreach ($accountsToUpdate as $account) {
                DB::table('nobs_user_account_numbers')
                    ->where('id', $account->id)
                    ->update(['account_status' => 'dormant']);
                $count++;
            }

            Log::info("Dormancy Check: Flagged $count accounts as dormant.");
            return $count;

        } catch (\Exception $e) {
            Log::error("Dormancy Check Failed: " . $e->getMessage());
            return 0;
        }
    }
}
