<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanProduct;
use App\LoanFee;
use Illuminate\Support\Facades\Validator;

class LoanProductController extends Controller
{
    /**
     * List all loan products.
     */
    public function index()
    {
        $products = LoanProduct::with('fees')->where('is_active', 1)->get();
        return response()->json(['success' => true, 'data' => $products], 200);
    }

    /**
     * Store a new loan product.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'interest_rate' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'duration_unit' => 'required|in:month,week,day',
            'repayment_frequency' => 'required|in:monthly,weekly,daily',
            'description' => 'nullable|string',
            'fees' => 'nullable|array',
            'fees.*' => 'exists:loan_fees,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $product = LoanProduct::create($request->all());

        if ($request->has('fees')) {
            $product->fees()->sync($request->fees);
        }

        return response()->json(['success' => true, 'data' => $product, 'message' => 'Loan Product created successfully'], 201);
    }

    /**
     * List all available fees.
     */
    public function getFees()
    {
        $fees = LoanFee::all();
        return response()->json(['success' => true, 'data' => $fees], 200);
    }

    /**
     * Create a new reusable fee.
     */
    public function storeFee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,percent'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $fee = LoanFee::create($request->all());

        return response()->json(['success' => true, 'data' => $fee, 'message' => 'Fee created successfully'], 201);
    }
}