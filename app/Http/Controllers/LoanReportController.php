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
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        // Base query for loan applications (disbursed/active)
        $loanApplicationsQuery = LoanApplication::whereIn('status', ['active', 'repaid'])
                                                ->when($startDate, function ($query) use ($startDate) {
                                                    return $query->whereDate('repayment_start_date', '>=', $startDate);
                                                })
                                                ->when($endDate, function ($query) use ($endDate) {
                                                    return $query->whereDate('repayment_start_date', '<=', $endDate);
                                                });
        
        $totalLoansDisbursedValue = $loanApplicationsQuery->sum('amount');
        $totalLoansDisbursedCount = $loanApplicationsQuery->count();

        // Total Repaid, Interest, Fees Collected
        // Summing from AccountsTransactions with name_of_transaction 'Loan Repayment'
        $repaymentTransactionsQuery = AccountsTransactions::where('name_of_transaction', 'Loan Repayment')
                                                        ->when($startDate, function ($query) use ($startDate) {
                                                            return $query->whereDate('created_at', '>=', $startDate);
                                                        })
                                                        ->when($endDate, function ($query) use ($endDate) {
                                                            return $query->whereDate('created_at', '<=', $endDate);
                                                        });
        $totalRepaid = $repaymentTransactionsQuery->sum('amount');

        // Summing interest and fees from repayment schedules
        $schedulesPaidQuery = LoanRepaymentSchedule::where('status', 'paid')
                                                    ->when($startDate, function ($query) use ($startDate) {
                                                        return $query->whereDate('due_date', '>=', $startDate);
                                                    })
                                                    ->when($endDate, function ($query) use ($endDate) {
                                                        return $query->whereDate('due_date', '<=', $endDate);
                                                    });
        
        $totalInterestCollected = LoanRepaymentSchedule::whereHas('application', function ($query) use ($startDate, $endDate) {
                                                            $query->whereIn('status', ['active', 'repaid'])
                                                                ->when($startDate, function ($subQuery) use ($startDate) {
                                                                    return $subQuery->whereDate('repayment_start_date', '>=', $startDate);
                                                                })
                                                                ->when($endDate, function ($subQuery) use ($endDate) {
                                                                    return $subQuery->whereDate('repayment_start_date', '<=', $endDate);
                                                                });
                                                        })
                                                        ->sum('interest_paid'); // Sum actual paid interest

        $totalFeesCollected = LoanRepaymentSchedule::whereHas('application', function ($query) use ($startDate, $endDate) {
                                                        $query->whereIn('status', ['active', 'repaid'])
                                                            ->when($startDate, function ($subQuery) use ($startDate) {
                                                                return $subQuery->whereDate('repayment_start_date', '>=', $startDate);
                                                            })
                                                            ->when($endDate, function ($subQuery) use ($endDate) {
                                                                return $subQuery->whereDate('repayment_start_date', '<=', $endDate);
                                                            });
                                                    })
                                                    ->sum('fees_paid'); // Sum actual paid fees
        
        // Total Outstanding (All active/partial loans)
        $totalOutstanding = LoanApplication::whereIn('status', ['active', 'partial']) // 'partial' status not in LoanApplication model currently
                                            ->when($startDate, function ($query) use ($startDate) {
                                                return $query->whereDate('repayment_start_date', '>=', $startDate);
                                            })
                                            ->when($endDate, function ($query) use ($endDate) {
                                                return $query->whereDate('repayment_start_date', '<=', $endDate);
                                            })
                                            ->sum(DB::raw('total_repayment - total_paid')); // total_paid is not a column on LoanApplication

        // Correct calculation for Total Outstanding
        // Sum total_due - (principal_paid + interest_paid + fees_paid) from all active/partial schedules
        $activeSchedules = LoanRepaymentSchedule::whereHas('application', function($query) {
                                                $query->where('status', 'active');
                                            })
                                            ->where('status', '!=', 'paid')
                                            ->get(); // Get all active/partial schedules for active loans

        $calculatedOutstanding = 0;
        foreach ($activeSchedules as $schedule) {
            $calculatedOutstanding += ($schedule->principal_due - $schedule->principal_paid) +
                                    ($schedule->interest_due - $schedule->interest_paid) +
                                    ($schedule->fees_due - $schedule->fees_paid);
        }


        // Defaulted Loans (requires a 'default' status or specific logic to identify)
        // For now, let's count loans that are 'active' but overdue in their schedules
        $defaultedLoansCount = LoanApplication::where('status', 'active')
                                                ->whereHas('repayment_schedules', function($query) {
                                                    $query->where('due_date', '<', Carbon::now())
                                                          ->where('status', 'pending'); // Overdue and not paid
                                                })
                                                ->when($startDate, function ($query) use ($startDate) {
                                                    return $query->whereDate('repayment_start_date', '>=', $startDate);
                                                })
                                                ->when($endDate, function ($query) use ($endDate) {
                                                    return $query->whereDate('repayment_start_date', '<=', $endDate);
                                                })
                                                ->count();
        
        // Total value of defaulted loans (simple sum of remaining repayment for defaulted loans)
        $defaultedLoansValue = LoanApplication::where('status', 'active')
                                                ->whereHas('repayment_schedules', function($query) {
                                                    $query->where('due_date', '<', Carbon::now())
                                                          ->where('status', 'pending');
                                                })
                                                ->when($startDate, function ($query) use ($startDate) {
                                                    return $query->whereDate('repayment_start_date', '>=', $startDate);
                                                })
                                                ->when($endDate, function ($query) use ($endDate) {
                                                    return $query->whereDate('repayment_start_date', '<=', $endDate);
                                                })
                                                ->sum(DB::raw('total_repayment - total_paid')); // total_paid is not a column on LoanApplication
        
        // Correct calculation for defaulted loans value: sum of outstanding from their schedules
        $defaultedSchedules = LoanRepaymentSchedule::whereHas('application', function($query) {
                                                $query->where('status', 'active');
                                            })
                                            ->where('status', 'pending')
                                            ->where('due_date', '<', Carbon::now())
                                            ->get();
        $calculatedDefaultedValue = 0;
        foreach ($defaultedSchedules as $schedule) {
            $calculatedDefaultedValue += ($schedule->principal_due - $schedule->principal_paid) +
                                        ($schedule->interest_due - $schedule->interest_paid) +
                                        ($schedule->fees_due - $schedule->fees_paid);
        }


        // Total Cash Available (from CompanyInfo)
        $companyCash = CompanyInfo::first()->amount_in_cash ?? 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_loans_disbursed_value' => round($totalLoansDisbursedValue, 2),
                'total_loans_disbursed_count' => $totalLoansDisbursedCount,
                'total_repaid_amount' => round($totalRepaid, 2),
                'total_interest_collected' => round($totalInterestCollected, 2),
                'total_fees_collected' => round($totalFeesCollected, 2),
                'total_outstanding_value' => round($calculatedOutstanding, 2), // Using calculated value
                'defaulted_loans_count' => $defaultedLoansCount,
                'defaulted_loans_value' => round($calculatedDefaultedValue, 2), // Using calculated value
                'company_cash_balance' => round($companyCash, 2),
                // Add more metrics as needed
            ]
        ]);
    }
}
