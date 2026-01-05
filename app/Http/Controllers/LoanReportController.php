<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanApplication;
use App\LoanRepaymentSchedule;
use App\AccountsTransactions;
use App\CentralLoanAccount;
use App\CompanyInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanReportController extends Controller
{
    /**
     * Get key metrics for the loan dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLoanDashboardMetrics(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;

        // 1. Disbursed Loans (Value & Count) - Based on Disbursement Date
        // Note: Using repayment_start_date as proxy for disbursement_date if disbursement_date is null, 
        // but ideally should be disbursement_date column if it exists. 
        // Assuming 'created_at' or 'repayment_start_date' marks the start. 
        // Let's use repayment_start_date as the effective disbursement date for now.
        $disbursedQuery = LoanApplication::whereIn('status', ['active', 'repaid', 'defaulted', 'disbursed'])
                                         ->when($startDate, function ($query) use ($startDate) {
                                             return $query->where('repayment_start_date', '>=', $startDate);
                                         })
                                         ->when($endDate, function ($query) use ($endDate) {
                                             return $query->where('repayment_start_date', '<=', $endDate);
                                         });

        $totalLoansDisbursedValue = $disbursedQuery->sum('amount');
        $totalLoansDisbursedCount = $disbursedQuery->count();

        // 2. Total Repayments (Cash In) - Based on Transaction Date (AccountsTransactions)
        $repaymentsQuery = AccountsTransactions::where('is_loan', 1)
                                        ->where('name_of_transaction', 'Loan Repayment')
                                        ->when($startDate, function ($query) use ($startDate) {
                                            return $query->where('created_at', '>=', $startDate);
                                        })
                                        ->when($endDate, function ($query) use ($endDate) {
                                            return $query->where('created_at', '<=', $endDate);
                                        });

        $totalRepaid = $repaymentsQuery->sum('amount');

        // Note: The legacy transaction table does not split Interest/Fees. 
        // We approximate "Collected Interest/Fees" by looking at Schedules updated in this period.
        // This is an estimation because 'updated_at' updates on any change, but it's the closest proxy without a dedicated splits table.
        $scheduleUpdatesQuery = LoanRepaymentSchedule::where('total_paid', '>', 0)
                                        ->when($startDate, function ($query) use ($startDate) {
                                            return $query->where('updated_at', '>=', $startDate);
                                        })
                                        ->when($endDate, function ($query) use ($endDate) {
                                            return $query->where('updated_at', '<=', $endDate);
                                        });

        $totalInterestCollected = $scheduleUpdatesQuery->sum('interest_paid');
        
        // 3. Fees Collected
        // Part A: Fees from Schedules (Approx)
        $feesFromRepayments = $scheduleUpdatesQuery->sum('fees_paid');

        // Part B: Upfront Fees (collected at Disbursement)
        // We assume upfront fees are collected when the loan is disbursed.
        $upfrontFees = 0;
        $loansWithUpfront = $disbursedQuery->where('fee_payment_method', 'deduct_upfront')->with('loan_product.fees')->get();
        
        foreach ($loansWithUpfront as $loan) {
            $loanFees = 0;
            if ($loan->loan_product && $loan->loan_product->fees) {
                foreach ($loan->loan_product->fees as $fee) {
                    if ($fee->pivot->is_active) {
                        if ($fee->type == 'fixed') {
                            $loanFees += $fee->value;
                        } else {
                            $loanFees += ($loan->amount * ($fee->value / 100));
                        }
                    }
                }
            }
            $upfrontFees += $loanFees;
        }

        $totalFeesCollected = $feesFromRepayments + $upfrontFees;


        // 4. Outstanding (Snapshot - All Time)
        // Ignore date filters for "Money Still Owed" as it refers to CURRENT debt.
        // Only active/defaulted loans have outstanding balances.
        // We can use the 'outstanding_balance' accessor if it performs well, or calculate via SQL.
        // SQL is faster: (total_principal + total_interest + total_fees) - (paid_principal + paid_interest + paid_fees)
        // But easier is Sum(amount) - Sum(principal_repaid) for principal...
        // Let's stick to the reliable Scheduler math for now, but for ALL active loans.
        $activeSchedules = LoanRepaymentSchedule::whereHas('application', function($query) {
                                                $query->whereIn('status', ['active', 'defaulted']);
                                            })
                                            ->where('status', '!=', 'paid')
                                            ->select(DB::raw('
                                                SUM(principal_due - principal_paid) as pending_principal,
                                                SUM(interest_due - interest_paid) as pending_interest,
                                                SUM(fees_due - fees_paid) as pending_fees
                                            '))
                                            ->first();
                                            
        $calculatedOutstanding = ($activeSchedules->pending_principal ?? 0) + 
                                 ($activeSchedules->pending_interest ?? 0) + 
                                 ($activeSchedules->pending_fees ?? 0);


        // 5. Defaulted Loans (Snapshot - All Time)
        // Active loans with OVERDUE schedules.
        $defaultedLoansCount = LoanApplication::where('status', 'active')
                                                ->whereHas('repaymentSchedules', function($query) {
                                                    $query->where('due_date', '<', Carbon::now())
                                                          ->where('status', 'pending');
                                                })
                                                ->count();
                                                
        // Value of defaulted loans (Outstanding balance of these loans)
        $defaultedSchedules = LoanRepaymentSchedule::whereHas('application', function($query) {
                                                $query->where('status', 'active')
                                                      ->whereHas('repaymentSchedules', function($subQ) {
                                                          $subQ->where('due_date', '<', Carbon::now())
                                                               ->where('status', 'pending');
                                                      });
                                            })
                                            ->where('status', '!=', 'paid') // Get all unpaid schedules for these defaulted loans
                                            ->select(DB::raw('
                                                SUM(principal_due - principal_paid) as pending_principal,
                                                SUM(interest_due - interest_paid) as pending_interest,
                                                SUM(fees_due - fees_paid) as pending_fees
                                            '))
                                            ->first();

        $calculatedDefaultedValue = ($defaultedSchedules->pending_principal ?? 0) + 
                                    ($defaultedSchedules->pending_interest ?? 0) + 
                                    ($defaultedSchedules->pending_fees ?? 0);


        // Total Cash Available (from CompanyInfo)
        $companyId = auth()->user()->comp_id;
        $companyCash = CompanyInfo::where('id', $companyId)->value('amount_in_cash') ?? 0;

        // Pool Balance (Central Loan Account)
        $poolBalance = CentralLoanAccount::sum('balance') ?? 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_loans_disbursed_value' => round($totalLoansDisbursedValue, 2),
                'total_loans_disbursed_count' => $totalLoansDisbursedCount,
                'total_repaid_amount' => round($totalRepaid, 2),
                'total_interest_collected' => round($totalInterestCollected, 2),
                'total_fees_collected' => round($totalFeesCollected, 2),
                'total_outstanding_value' => round($calculatedOutstanding, 2),
                'defaulted_loans_count' => $defaultedLoansCount,
                'defaulted_loans_value' => round($calculatedDefaultedValue, 2),
                'company_cash_balance' => round($companyCash, 2),
                'pool_balance' => round($poolBalance, 2),
            ]
        ]);
    }

    /**
     * Get a list of actual defaulted loans.
     * A loan is considered defaulted if it's active and has at least one overdue and pending schedule.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActualDefaultedLoans(Request $request)
    {
        $defaultedLoans = LoanApplication::with(['customer', 'loan_product', 'repaymentSchedules'])
                                        ->where('status', 'active')
                                        ->whereHas('repaymentSchedules', function ($query) {
                                            $query->where('due_date', '<', Carbon::now())
                                                  ->where('status', 'pending'); // Overdue and not paid
                                        })
                                        ->get();

        return response()->json([
            'success' => true,
            'data' => $defaultedLoans
        ], 200);
    }

    /**
     * Get transaction history for a specific dashboard metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardTransactionHistory(Request $request)
    {
        $metric = $request->query('metric');
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;
        
        $data = [];

        switch ($metric) {
            case 'disbursed':
                // Align with metric: uses repayment_start_date
                $query = LoanApplication::with('customer')
                    ->whereIn('status', ['active', 'repaid', 'defaulted', 'disbursed']);

                if ($startDate) $query->whereDate('repayment_start_date', '>=', $startDate);
                if ($endDate) $query->whereDate('repayment_start_date', '<=', $endDate);

                $data = $query->orderBy('repayment_start_date', 'desc')
                    ->get()
                    ->map(function ($loan) {
                        return [
                            'id' => $loan->id,
                            'customer' => $loan->customer,
                            'amount' => $loan->amount,
                            'date' => $loan->repayment_start_date, // Display disbursement date
                        ];
                    });
                break;

            case 'repaid':
                $query = AccountsTransactions::where('is_loan', 1)
                    ->where('name_of_transaction', 'Loan Repayment');
                    // Note: Cannot eager load 'loanApplication' easily as no direct FK. 
                    // We'll have to rely on 'account_number' or parsing 'det_rep_name_of_transaction'.
                    // For now, let's just show the transaction details available.

                if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                if ($endDate) $query->whereDate('created_at', '<=', $endDate);

                $data = $query->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($repayment) {
                        return [
                            'id' => $repayment->id,
                            'customer' => ['name' => $repayment->det_rep_name_of_transaction], // Fallback label
                            'amount' => $repayment->amount,
                            'date' => $repayment->created_at,
                        ];
                    });
                break;

            case 'interest':
                // Cannot isolate interest transactions easily in legacy table. 
                // Showing all repayments as a fallback, or we could query Schedules (but those aren't 'transactions').
                // Let's return empty or all repayments with a note? 
                // Let's return the repayment transactions to be safe.
                $query = AccountsTransactions::where('is_loan', 1)
                    ->where('name_of_transaction', 'Loan Repayment');

                if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                if ($endDate) $query->whereDate('created_at', '<=', $endDate);

                $data = $query->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($repayment) {
                        return [
                            'id' => $repayment->id,
                            'customer' => ['name' => $repayment->det_rep_name_of_transaction],
                            'amount' => $repayment->amount, // We don't know the exact interest portion here
                            'date' => $repayment->created_at,
                        ];
                    });
                break;

            case 'charges':
                // Similar limitation for charges.
                $query = AccountsTransactions::where('is_loan', 1)
                    ->where('name_of_transaction', 'Loan Repayment');

                if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                if ($endDate) $query->whereDate('created_at', '<=', $endDate);

                $data = $query->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($repayment) {
                        return [
                            'id' => $repayment->id,
                            'customer' => ['name' => $repayment->det_rep_name_of_transaction],
                            'amount' => $repayment->amount, // We don't know the exact fees portion here
                            'date' => $repayment->created_at,
                        ];
                    });
                break;

            case 'outstanding':
                // All active loans (filtered by start date if provided, though usually Outstanding is a snapshot)
                // If dates are provided, we show loans disbursed in that range that are still active
                $query = LoanApplication::with('customer')
                    ->whereIn('status', ['active', 'defaulted']);

                if ($startDate) $query->whereDate('repayment_start_date', '>=', $startDate);
                if ($endDate) $query->whereDate('repayment_start_date', '<=', $endDate);

                $data = $query->orderBy('updated_at', 'desc')
                    ->get()
                    ->map(function ($loan) {
                        return [
                            'id' => $loan->id,
                            'customer' => $loan->customer,
                            'amount' => $loan->outstanding_balance, // Accessor
                            'date' => $loan->repayment_start_date,
                        ];
                    });
                break;

            case 'defaulted':
                // Align with metric: Active loans with OVERDUE schedules
                // Or loans explicitly marked as 'defaulted'
                $query = LoanApplication::with('customer')
                    ->where(function($q) {
                        $q->where('status', 'defaulted')
                          ->orWhere(function($subQ) {
                              $subQ->where('status', 'active')
                                   ->whereHas('repaymentSchedules', function ($schedQ) {
                                       $schedQ->where('due_date', '<', Carbon::now())
                                              ->where('status', 'pending');
                                   });
                          });
                    });

                if ($startDate) $query->whereDate('repayment_start_date', '>=', $startDate);
                if ($endDate) $query->whereDate('repayment_start_date', '<=', $endDate);

                $data = $query->orderBy('updated_at', 'desc')
                    ->get()
                    ->map(function ($loan) {
                        return [
                            'id' => $loan->id,
                            'customer' => $loan->customer,
                            'amount' => $loan->outstanding_balance,
                            'date' => $loan->updated_at,
                        ];
                    });
                break;
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }
}