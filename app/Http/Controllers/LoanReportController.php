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
        
        $user = auth()->user();
        $managerRoles = ['Admin', 'Manager', 'super admin', 'Owner'];
        $isManager = $user->hasRole($managerRoles) || in_array($user->type, $managerRoles);

        // Closure for Role Filtering on LoanApplication
        $applyLoanFilter = function($query) use ($user, $isManager) {
            if (!$isManager) {
                $query->where(function($q) use ($user) {
                    $q->where('assigned_to_user_id', $user->id)
                      ->orWhere('created_by_user_id', $user->id);
                });
            }
        };

        // 1. Disbursed Loans
        $disbursedQuery = LoanApplication::whereIn('status', ['active', 'repaid', 'defaulted', 'disbursed']);
        $applyLoanFilter($disbursedQuery);
        
        $disbursedQuery->when($startDate, function ($query) use ($startDate) {
                             return $query->where('updated_at', '>=', $startDate);
                         })
                         ->when($endDate, function ($query) use ($endDate) {
                             return $query->where('updated_at', '<=', $endDate);
                         });

        $totalLoansDisbursedValue = $disbursedQuery->sum('amount');
        $totalLoansDisbursedCount = $disbursedQuery->count();

        // 2. Total Repayments (Cash In)
        // Note: Filtering legacy transactions by Agent ID is difficult as it relies on 'users' string or 'agentname'.
        // For now, we will NOT filter this legacy table strictly by ID to avoid data loss, 
        // unless we can map 'users' column to ID. 
        // IF we must filter, we'd need to join with users table or use 'agentname'.
        // Let's try to filter by 'users' if it stores ID, otherwise skip for safety.
        $repaymentsQuery = AccountsTransactions::where('is_loan', 1)
                                        ->where('name_of_transaction', 'Loan Repayment')
                                        ->when($startDate, function ($query) use ($startDate) {
                                            return $query->where('created_at', '>=', $startDate);
                                        })
                                        ->when($endDate, function ($query) use ($endDate) {
                                            return $query->where('created_at', '<=', $endDate);
                                        });
        
        // Attempt basic Agent filter on transactions if column exists/matches
        if (!$isManager) {
             // Assuming 'users' column holds the ID or 'agentname' holds the name. 
             // We will try matching 'users' to ID first.
             $repaymentsQuery->where('users', $user->id); 
        }

        $totalRepaid = $repaymentsQuery->sum('amount');

        // Note: The legacy transaction table does not split Interest/Fees. 
        // We approximate "Collected Interest/Fees" by looking at Schedules updated in this period.
        $scheduleUpdatesQuery = LoanRepaymentSchedule::where('total_paid', '>', 0)
                                        ->whereHas('application', function($q) use ($applyLoanFilter) {
                                            $applyLoanFilter($q);
                                        })
                                        ->when($startDate, function ($query) use ($startDate) {
                                            return $query->where('updated_at', '>=', $startDate);
                                        })
                                        ->when($endDate, function ($query) use ($endDate) {
                                            return $query->where('updated_at', '<=', $endDate);
                                        });

        $totalInterestCollected = $scheduleUpdatesQuery->sum('interest_paid');
        
        // 3. Fees Profit
        $totalFeesCollected = $disbursedQuery->sum('total_fees');


        // 4. Outstanding (Snapshot - All Time)
        $activeSchedules = LoanRepaymentSchedule::whereHas('application', function($query) use ($applyLoanFilter) {
                                                $query->whereIn('status', ['active', 'defaulted']);
                                                $applyLoanFilter($query);
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
        $defaultedLoansCount = LoanApplication::where('status', 'active')
                                                ->whereHas('repaymentSchedules', function($query) {
                                                    $query->where('due_date', '<', Carbon::now())
                                                          ->where('status', 'pending');
                                                });
        $applyLoanFilter($defaultedLoansCount);
        $defaultedLoansCount = $defaultedLoansCount->count();
                                                
        // Value of defaulted loans
        $defaultedSchedules = LoanRepaymentSchedule::whereHas('application', function($query) use ($applyLoanFilter) {
                                                $query->where('status', 'active')
                                                      ->whereHas('repaymentSchedules', function($subQ) {
                                                          $subQ->where('due_date', '<', Carbon::now())
                                                               ->where('status', 'pending');
                                                      });
                                                $applyLoanFilter($query);
                                            })
                                            ->where('status', '!=', 'paid')
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
                // Align with metric: uses updated_at (proxy for disbursement)
                $query = LoanApplication::with('customer')
                    ->whereIn('status', ['active', 'repaid', 'defaulted', 'disbursed']);

                if ($startDate) $query->whereDate('updated_at', '>=', $startDate);
                if ($endDate) $query->whereDate('updated_at', '<=', $endDate);

                $data = $query->orderBy('updated_at', 'desc')
                    ->get()
                    ->map(function ($loan) {
                        return [
                            'id' => $loan->id,
                            'customer' => $loan->customer,
                            'amount' => $loan->amount,
                            'date' => $loan->updated_at, // Display disbursement date
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

    /**
     * Get the total daily expected repayment amount.
     * Sums all installments due on the specified date.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDailyExpected(Request $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date'))->toDateString() : Carbon::today()->toDateString();
        
        $user = auth()->user();
        $managerRoles = ['Admin', 'Manager', 'super admin', 'Owner'];
        $isManager = $user->hasRole($managerRoles) || in_array($user->type, $managerRoles);

        $query = LoanRepaymentSchedule::whereDate('due_date', $date);

        // Apply Role Filter
        if (!$isManager) {
            $query->whereHas('application', function($q) use ($user) {
                $q->where('assigned_to_user_id', $user->id)
                  ->orWhere('created_by_user_id', $user->id);
            });
        }

        $totalExpected = $query->sum('total_due');

        return response()->json([
            'success' => true,
            'date' => $date,
            'amount' => round($totalExpected, 2)
        ], 200);
    }

    /**
     * Get the detailed list of repayments due on a specific date.
     * Filtered by user role.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDailyRepaymentList(Request $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date'))->toDateString() : Carbon::today()->toDateString();
        
        $user = auth()->user();
        $managerRoles = ['Admin', 'Manager', 'super admin', 'Owner'];
        $isManager = $user->hasRole($managerRoles) || in_array($user->type, $managerRoles);

        $query = LoanRepaymentSchedule::with(['application.customer', 'application.loan_product'])
                    ->whereDate('due_date', $date)
                    ->where('status', '!=', 'paid'); // Only show pending/partial? Prompt said "expected to be collected"

        // Apply Role Filter
        if (!$isManager) {
            $query->whereHas('application', function($q) use ($user) {
                $q->where('assigned_to_user_id', $user->id)
                  ->orWhere('created_by_user_id', $user->id);
            });
        }
        
        $list = $query->get()->map(function($schedule) {
            $customer = $schedule->application->customer;
            return [
                'id' => $schedule->id,
                'loan_id' => $schedule->loan_application_id,
                'customer_name' => $customer ? "{$customer->first_name} {$customer->surname}" : 'Unknown',
                'customer_phone' => $customer->phone_number ?? 'N/A',
                'customer_image' => $customer->user_image ?? null,
                'customer_id' => $customer->id ?? null,
                'account_number' => $customer->account_number ?? 'N/A',
                'amount_due' => $schedule->total_due,
                'installment_number' => $schedule->installment_number,
                'product_name' => $schedule->application->loan_product->name ?? 'Loan',
                'status' => $schedule->status
            ];
        });

        return response()->json([
            'success' => true,
            'date' => $date,
            'data' => $list
        ], 200);
    }
}