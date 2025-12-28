<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanApplication;
use App\LoanProduct;
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
            'created_by' => $request->user() ? $request->user()->id : null,
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
}
