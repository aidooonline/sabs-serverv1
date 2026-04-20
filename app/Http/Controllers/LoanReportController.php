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
        $compId = $user->comp_id;
        
        // Standardized manager-level role check
        $managerRoles = ['admin', 'manager', 'super admin', 'owner'];
        $userRoleNames = $user->roles->pluck('name')->map(function($role) { return strtolower($role); })->toArray();
        $userType = strtolower($user->type);
        $isManager = !empty(array_intersect($userRoleNames, $managerRoles)) || in_array($userType, $managerRoles);

        // Closure for Role and Company Filtering
        $applyLoanFilter = function($query) use ($user, $isManager, $compId) {
            $query->where('comp_id', $compId);
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

        // 2. Total Repayments (Actual Cash Collected)
        $repaymentsQuery = AccountsTransactions::where('comp_id', $compId)
                                        ->where('is_loan', 1)
                                        ->where('name_of_transaction', 'Loan Repayment')
                                        ->when($startDate, function ($query) use ($startDate) {
                                            return $query->where('created_at', '>=', $startDate);
                                        })
                                        ->when($endDate, function ($query) use ($endDate) {
                                            return $query->where('created_at', '<=', $endDate);
                                        });
        
        if (!$isManager) {
             $repaymentsQuery->where('users', $user->id); 
        }

        $totalRepaid = $repaymentsQuery->sum('amount');

        // 3. Interest and Fees (Collected vs Projected)
        $scheduleUpdatesQuery = LoanRepaymentSchedule::where('comp_id', $compId)
                                        ->where('total_paid', '>', 0)
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
        $totalFeesCollected = $scheduleUpdatesQuery->sum('fees_paid');
        $totalProjectedFees = $disbursedQuery->sum('total_fees');


        // 4. Expected Today (Arrears + Today's Due)
        $today = Carbon::today()->toDateString();
        $expectedQuery = LoanRepaymentSchedule::where('comp_id', $compId)
                                        ->where('status', '!=', 'paid')
                                        ->whereDate('due_date', '<=', $today)
                                        ->whereHas('application', function($q) use ($applyLoanFilter) {
                                            $applyLoanFilter($q);
                                            $q->whereIn('status', ['active', 'defaulted']);
                                        });
        
        $totalExpectedValue = $expectedQuery->sum(DB::raw('total_due - total_paid'));


        // 5. Outstanding (Snapshot - All Time)
        $activeSchedules = LoanRepaymentSchedule::where('comp_id', $compId)
                                            ->whereHas('application', function($query) use ($applyLoanFilter) {
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


        // 6. Defaulted Loans (Snapshot - All Time)
        $defaultedLoansQuery = LoanApplication::where('comp_id', $compId)
                                                ->where('status', 'active')
                                                ->whereHas('repaymentSchedules', function($query) {
                                                    $query->where('due_date', '<', Carbon::now())
                                                          ->where('status', 'pending');
                                                });
        $applyLoanFilter($defaultedLoansQuery);
        $defaultedLoansCount = $defaultedLoansQuery->count();
                                                
        // Value of defaulted loans (Arrears only)
        $defaultedSchedules = LoanRepaymentSchedule::where('comp_id', $compId)
                                            ->where('status', 'pending')
                                            ->where('due_date', '<', Carbon::now())
                                            ->whereHas('application', function($query) use ($applyLoanFilter) {
                                                $query->where('status', 'active');
                                                $applyLoanFilter($query);
                                            })
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
        $companyCash = CompanyInfo::where('id', $compId)->value('amount_in_cash') ?? 0;

        // Pool Balance (Central Loan Account)
        $poolBalance = CentralLoanAccount::where('comp_id', $compId)->sum('balance') ?? 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_loans_disbursed_value' => round($totalLoansDisbursedValue, 2),
                'total_loans_disbursed_count' => $totalLoansDisbursedCount,
                'total_repaid_amount' => round($totalRepaid, 2),
                'total_interest_collected' => round($totalInterestCollected, 2),
                'total_fees_collected' => round($totalFeesCollected, 2),
                'total_fees_projected' => round($totalProjectedFees, 2),
                'total_expected_today' => round($totalExpectedValue, 2),
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
        
        $user = auth()->user();
        $compId = $user->comp_id;
        
        // Standardized manager-level role check
        $managerRoles = ['admin', 'manager', 'super admin', 'owner'];
        $userRoleNames = $user->roles->pluck('name')->map(function($role) { return strtolower($role); })->toArray();
        $userType = strtolower($user->type);
        $isManager = !empty(array_intersect($userRoleNames, $managerRoles)) || in_array($userType, $managerRoles);

        $data = [];

        switch ($metric) {
            case 'disbursed':
                $query = LoanApplication::with('customer')
                    ->where('comp_id', $compId)
                    ->whereIn('status', ['active', 'repaid', 'defaulted', 'disbursed']);

                if (!$isManager) {
                    $query->where(function($q) use ($user) {
                        $q->where('assigned_to_user_id', $user->id)
                          ->orWhere('created_by_user_id', $user->id);
                    });
                }

                if ($startDate) $query->whereDate('updated_at', '>=', $startDate);
                if ($endDate) $query->whereDate('updated_at', '<=', $endDate);

                $data = $query->orderBy('updated_at', 'desc')
                    ->get()
                    ->map(function ($loan) {
                        return [
                            'id' => $loan->id,
                            'customer' => [
                                'name' => $loan->customer ? "{$loan->customer->first_name} {$loan->customer->surname}" : 'Unknown',
                                'account_number' => $loan->customer->account_number ?? 'N/A'
                            ],
                            'amount' => $loan->amount,
                            'date' => $loan->updated_at,
                        ];
                    });
                break;

            case 'repaid':
            case 'interest':
            case 'charges':
                // For repaid/interest/charges, we look at actual transactions
                $query = AccountsTransactions::where('comp_id', $compId)
                    ->where('is_loan', 1)
                    ->where('name_of_transaction', 'Loan Repayment');

                if (!$isManager) {
                    $query->where('users', $user->id);
                }

                if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                if ($endDate) $query->whereDate('created_at', '<=', $endDate);

                $data = $query->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($repayment) {
                        return [
                            'id' => $repayment->id,
                            'customer' => ['name' => $repayment->det_rep_name_of_transaction],
                            'amount' => $repayment->amount,
                            'date' => $repayment->created_at,
                        ];
                    });
                break;

            case 'outstanding':
                $query = LoanApplication::with('customer')
                    ->where('comp_id', $compId)
                    ->whereIn('status', ['active', 'defaulted']);

                if (!$isManager) {
                    $query->where(function($q) use ($user) {
                        $q->where('assigned_to_user_id', $user->id)
                          ->orWhere('created_by_user_id', $user->id);
                    });
                }

                if ($startDate) $query->whereDate('repayment_start_date', '>=', $startDate);
                if ($endDate) $query->whereDate('repayment_start_date', '<=', $endDate);

                $data = $query->orderBy('updated_at', 'desc')
                    ->get()
                    ->map(function ($loan) {
                        return [
                            'id' => $loan->id,
                            'customer' => [
                                'name' => $loan->customer ? "{$loan->customer->first_name} {$loan->customer->surname}" : 'Unknown',
                                'account_number' => $loan->customer->account_number ?? 'N/A'
                            ],
                            'amount' => $loan->outstanding_balance,
                            'date' => $loan->repayment_start_date,
                        ];
                    });
                break;

            case 'defaulted':
                $query = LoanApplication::with('customer')
                    ->where('comp_id', $compId)
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

                if (!$isManager) {
                    $query->where(function($q) use ($user) {
                        $q->where('assigned_to_user_id', $user->id)
                          ->orWhere('created_by_user_id', $user->id);
                    });
                }

                if ($startDate) $query->whereDate('repayment_start_date', '>=', $startDate);
                if ($endDate) $query->whereDate('repayment_start_date', '<=', $endDate);

                $data = $query->orderBy('updated_at', 'desc')
                    ->get()
                    ->map(function ($loan) {
                        return [
                            'id' => $loan->id,
                            'customer' => [
                                'name' => $loan->customer ? "{$loan->customer->first_name} {$loan->customer->surname}" : 'Unknown',
                                'account_number' => $loan->customer->account_number ?? 'N/A'
                            ],
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
        $compId = $user->comp_id;
        
        // Standardized manager-level role check
        $managerRoles = ['admin', 'manager', 'super admin', 'owner'];
        $userRoleNames = $user->roles->pluck('name')->map(function($role) { return strtolower($role); })->toArray();
        $userType = strtolower($user->type);
        $isManager = !empty(array_intersect($userRoleNames, $managerRoles)) || in_array($userType, $managerRoles);

        // Fetch loans that have pending schedules on or before the selected date
        $query = LoanApplication::with(['customer', 'loan_product', 'repaymentSchedules' => function($q) use ($date) {
                    $q->where('status', 'pending')->whereDate('due_date', '<=', $date);
                }])
                ->where('comp_id', $compId)
                ->whereIn('status', ['active', 'defaulted'])
                ->whereHas('repaymentSchedules', function($q) use ($date) {
                    $q->where('status', 'pending')->whereDate('due_date', '<=', $date);
                });

        // Apply Role Filter
        if (!$isManager) {
            $query->where(function($q) use ($user) {
                $q->where('assigned_to_user_id', $user->id)
                  ->orWhere('created_by_user_id', $user->id);
            });
        }
        
        $list = $query->get()->map(function($app) {
            $customer = $app->customer;
            $dueSchedules = $app->repaymentSchedules;
            
            return [
                'id' => $dueSchedules->first()->id ?? $app->id, // Maintain an ID for the list
                'loan_id' => $app->id,
                'customer_name' => $customer ? "{$customer->first_name} {$customer->surname}" : 'Unknown',
                'customer_phone' => $customer->phone_number ?? 'N/A',
                'customer_image' => $customer->user_image ?? null,
                'customer_id' => $customer->id ?? null,
                'account_number' => $customer->account_number ?? 'N/A',
                'amount_due' => $dueSchedules->sum('total_due') - $dueSchedules->sum('total_paid'),
                'installment_number' => $dueSchedules->count() > 1 
                                        ? $dueSchedules->min('installment_number') . '-' . $dueSchedules->max('installment_number') 
                                        : $dueSchedules->first()->installment_number ?? 'N/A',
                'installments_count' => $dueSchedules->count(),
                'product_name' => $app->loan_product->name ?? 'Loan',
                'status' => $dueSchedules->count() > 1 ? 'Multiple Overdue' : ($dueSchedules->first()->status ?? 'pending')
            ];
        });

        return response()->json([
            'success' => true,
            'date' => $date,
            'data' => $list
        ], 200);
    }
}