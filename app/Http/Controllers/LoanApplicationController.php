<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanApplication;
use App\LoanProduct;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoanApplicationController extends Controller
{
    /**
     * List Loan Applications.
     */
    public function index(Request $request)
    {
        $query = LoanApplication::with(['loan_product', 'customer']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Order by newest first
        $applications = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $applications
        ], 200);
    }

    /**
     * Calculate loan details (Preview).
     * Does NOT save to database.
     */
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'loan_product_id' => 'required|exists:loan_products,id',
            'fee_payment_method' => 'required|in:deduct_upfront,pay_separately'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $amount = $request->amount;
        $product = LoanProduct::with('fees')->find($request->loan_product_id);
        $method = $request->fee_payment_method;

        // 1. Calculate Interest (Per Period / Monthly)
        // Rate is per month. Duration needs to be normalized to months.
        $durationInMonths = 0;
        $unit = strtolower($product->duration_unit);
        
        if ($unit == 'month' || $unit == 'months') {
            $durationInMonths = $product->duration;
        } elseif ($unit == 'week' || $unit == 'weeks') {
            $durationInMonths = ($product->duration * 7) / 30;
        } elseif ($unit == 'day' || $unit == 'days') {
            $durationInMonths = $product->duration / 30;
        } else {
            $durationInMonths = $product->duration; // Default fallback
        }

        $totalInterest = $amount * ($product->interest_rate / 100) * $durationInMonths;

        // 2. Calculate Fees
        $totalFees = 0;
        $breakdownFees = [];

        foreach ($product->fees as $fee) {
            $feeAmount = 0;
            if ($fee->type == 'fixed' || $fee->type == 'flat') {
                $feeAmount = $fee->value;
            } elseif ($fee->type == 'percent' || $fee->type == 'percentage') {
                $feeAmount = $amount * ($fee->value / 100);
            }
            
            $totalFees += $feeAmount;
            $breakdownFees[] = [
                'name' => $fee->name,
                'amount' => $feeAmount
            ];
        }

        // 3. Calculate Totals
        $totalRepayment = $amount + $totalInterest; // Client pays back Principal + Interest
        
        // Installment
        // If repayment_frequency is monthly
        $numberOfInstallments = $product->duration; // e.g. 6 months
        $installmentAmount = $totalRepayment / ($numberOfInstallments > 0 ? $numberOfInstallments : 1);

        // Disbursement Logic
        $disbursementAmount = $amount;
        if ($method === 'deduct_upfront') {
            $disbursementAmount = $amount - $totalFees;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'principal' => (float)$amount,
                'total_interest' => $totalInterest,
                'total_fees' => $totalFees,
                'total_repayment' => $totalRepayment,
                'disbursement_amount' => $disbursementAmount,
                'installment_amount' => $installmentAmount,
                'duration' => $product->duration,
                'duration_unit' => $product->duration_unit,
                'fee_breakdown' => $breakdownFees
            ]
        ], 200);
    }

    /**
     * Create a new Loan Application.
     */
    public function store(Request $request)
    {
        // Similar validation
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'loan_product_id' => 'required|exists:loan_products,id',
            'amount' => 'required|numeric|min:1',
            'fee_payment_method' => 'required|in:deduct_upfront,pay_separately',
            // Pre-calculated values passed from frontend for consistency, OR re-calculate here.
            // Better to re-calculate here to prevent tampering, but for speed we might trust logic if secure.
            // Let's re-calculate to be safe.
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        // Re-run calculation logic (simplified for storage)
        $amount = $request->amount;
        $product = LoanProduct::with('fees')->find($request->loan_product_id);
        
        // Calculate Interest
        $durationInMonths = 0;
        $unit = strtolower($product->duration_unit);
        
        if ($unit == 'month' || $unit == 'months') {
            $durationInMonths = $product->duration;
        } elseif ($unit == 'week' || $unit == 'weeks') {
            $durationInMonths = ($product->duration * 7) / 30;
        } elseif ($unit == 'day' || $unit == 'days') {
            $durationInMonths = $product->duration / 30;
        } else {
            $durationInMonths = $product->duration;
        }

        $totalInterest = $amount * ($product->interest_rate / 100) * $durationInMonths;

        // Calculate Fees
        $totalFees = 0;
        foreach ($product->fees as $fee) {
            if ($fee->type == 'fixed' || $fee->type == 'flat') {
                $totalFees += $fee->value;
            } elseif ($fee->type == 'percent' || $fee->type == 'percentage') {
                $totalFees += $amount * ($fee->value / 100);
            }
        }

        $totalRepayment = $amount + $totalInterest;

        $application = LoanApplication::create([
            'customer_id' => $request->customer_id,
            'loan_product_id' => $request->loan_product_id,
            'created_by_user_id' => Auth::id(), // Set the creator
            'assigned_to_user_id' => Auth::id(), // Default assignment to the creator
            'amount' => $amount,
            'total_interest' => $totalInterest,
            'total_fees' => $totalFees,
            'total_repayment' => $totalRepayment,
            'duration' => $product->duration,
            'repayment_frequency' => $product->repayment_frequency,
            'fee_payment_method' => $request->fee_payment_method,
            'status' => 'pending',
            'repayment_start_date' => now()->addMonth() // Default start date? Can be parameterized later.
        ]);

        return response()->json(['success' => true, 'data' => $application, 'message' => 'Loan Application submitted successfully.'], 201);
    }

    /**
     * Display the specified loan application.
     */
    public function show(Request $request, $id)
    {
        $application = LoanApplication::with(['loan_product', 'customer'])->find($id);

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Loan application not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $application], 200);
    }

    /**
     * Get active loans ('disbursed' or 'defaulted').
     * Filters by agent if the user has the 'Agent' role.
     * Allows searching by customer name for all roles.
     */
    public function getActiveLoans(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
            }

            $query = LoanApplication::with(['customer', 'assignedTo'])
                ->whereIn('status', ['disbursed', 'defaulted']);

            // Define manager-level roles
            $managerRoles = ['Admin', 'Manager', 'super admin', 'Owner'];

            // Role-based filtering
            if (!$user->hasRole($managerRoles)) {
                 // If user is not a manager, assume they are an agent and show only their loans
                $query->where('assigned_to_user_id', $user->id);
            }
            
            // Search functionality for all roles
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->whereHas('customer', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('account_number', 'like', "%{$searchTerm}%");
                });
            }
            
            // Allow managers to filter by a specific agent
            if ($user->hasRole($managerRoles) && $request->has('agent_id') && !empty($request->agent_id)) {
                $query->where('assigned_to_user_id', $request->agent_id);
            }

            $applications = $query->orderBy('updated_at', 'desc')->get();

            return response()->json(['success' => true, 'data' => $applications], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'A server error occurred while fetching active loans.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transfer a loan to another agent.
     */
    public function transferLoan(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'new_agent_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $application = LoanApplication::find($id);

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Loan application not found.'], 404);
        }

        $newAgent = User::find($request->new_agent_id);
        if (!$newAgent || !$newAgent->hasRole('Agent')) {
            // Or whatever roles are allowed to manage loans
            return response()->json(['success' => false, 'message' => 'The specified user is not a valid agent.'], 400);
        }

        $application->assigned_to_user_id = $request->new_agent_id;
        $application->save();

        return response()->json(['success' => true, 'message' => 'Loan successfully transferred.', 'data' => $application], 200);
    }
}
