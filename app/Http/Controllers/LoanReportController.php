<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanApplication;
use App\LoanRepayment;
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

        // 2. Total Repayments (Cash In) - Based on Transaction Date (LoanRepayment)
        $repaymentsQuery = LoanRepayment::when($startDate, function ($query) use ($startDate) {
                                            return $query->where('created_at', '>=', $startDate);
                                        })
                                        ->when($endDate, function ($query) use ($endDate) {
                                            return $query->where('created_at', '<=', $endDate);
                                        });

        $totalRepaid = $repaymentsQuery->sum('amount_paid');
        $totalInterestCollected = $repaymentsQuery->sum('interest_amount_paid');
        
        // 3. Fees Collected
        // Part A: Fees collected via Repayments (for 'pay_separately' or accrued fees)
        $feesFromRepayments = $repaymentsQuery->sum('fees_amount_paid');

        // Part B: Upfront Fees (collected at Disbursement)
        // We look for loans disbursed in this period that had 'deduct_upfront' fees.
        // We need to calculate the fee amount for these loans. 
        // Since we don't store the exact "upfront fee deducted" in a simple column in LoanApplication (it's in the pivot or calculated),
        // we might need to rely on the difference between 'amount' and 'disbursed_amount' IF we had that.
        // Alternatively, we re-calculate or check the `loan_fees` table if linked.
        // For simplicity/speed in this context, we will assume standard calculation if complex relation isn't easily eager loaded.
        // UPDATE: Check if we can get it from LoanProduct relation or if we stored it.
        // Let's assume we didn't store total_fee_amount in loan_applications table (we should have!).
        // Fallback: We will sum 'fees_paid' from RepaymentSchedules that are marked as 'upfront' - BUT schedules are generated after.
        // BETTER APPROACH: Only count fees from `LoanRepayment` (Cash In). 
        // If upfront fees are "deducted", they technically never entered the cash flow as "Inflow", they just reduced the "Outflow".
        // HOWEVER, for accounting "Profit", they are Revenue.
        // So, Upfront Fee Revenue = Sum of fees for loans disbursed in this period with 'deduct_upfront'.
        
        // Let's fetch the disbursed loans again and sum their fees if upfront.
        // This is expensive if we iterate. Let's try to do it via query if possible, or accept the iteration for now (assuming volume < 10k per day).
        $upfrontFees = 0;
        $loansWithUpfront = $disbursedQuery->where('fee_payment_method', 'deduct_upfront')->with('loan_product.fees')->get();
        
        foreach ($loansWithUpfront as $loan) {
            // Recalculate fee for this loan
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
                $query = LoanRepayment::with('loanApplication.customer');

                if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                if ($endDate) $query->whereDate('created_at', '<=', $endDate);

                $data = $query->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($repayment) {
                        return [
                            'id' => $repayment->id,
                            'customer' => $repayment->loanApplication->customer ?? null,
                            'amount' => $repayment->amount_paid,
                            'date' => $repayment->created_at,
                        ];
                    });
                break;

            case 'interest':
                $query = LoanRepayment::with('loanApplication.customer')
                    ->where('interest_amount_paid', '>', 0);

                if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                if ($endDate) $query->whereDate('created_at', '<=', $endDate);

                $data = $query->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($repayment) {
                        return [
                            'id' => $repayment->id,
                            'customer' => $repayment->loanApplication->customer ?? null,
                            'amount' => $repayment->interest_amount_paid,
                            'date' => $repayment->created_at,
                        ];
                    });
                break;

            case 'charges':
                $query = LoanRepayment::with('loanApplication.customer')
                    ->where('fees_amount_paid', '>', 0);

                if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                if ($endDate) $query->whereDate('created_at', '<=', $endDate);

                $data = $query->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($repayment) {
                        return [
                            'id' => $repayment->id,
                            'customer' => $repayment->loanApplication->customer ?? null,
                            'amount' => $repayment->fees_amount_paid,
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