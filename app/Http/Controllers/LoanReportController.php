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

        // Total Repaid (Principal + Interest + Fees)
        // Calculated directly from the repayment schedules to ensure accuracy
        $totalRepaid = LoanRepaymentSchedule::whereHas('application', function ($query) use ($startDate, $endDate) {
                                                $query->whereIn('status', ['active', 'repaid'])
                                                    ->when($startDate, function ($subQuery) use ($startDate) {
                                                        return $subQuery->whereDate('repayment_start_date', '>=', $startDate);
                                                    })
                                                    ->when($endDate, function ($subQuery) use ($endDate) {
                                                        return $subQuery->whereDate('repayment_start_date', '<=', $endDate);
                                                    });
                                            })
                                            ->sum(DB::raw('principal_paid + interest_paid + fees_paid'));

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
                                                ->whereHas('repaymentSchedules', function($query) {
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
                'total_outstanding_value' => round($calculatedOutstanding, 2), // Using calculated value
                'defaulted_loans_count' => $defaultedLoansCount,
                'defaulted_loans_value' => round($calculatedDefaultedValue, 2), // Using calculated value
                'company_cash_balance' => round($companyCash, 2),
                'pool_balance' => round($poolBalance, 2),
                // Add more metrics as needed
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