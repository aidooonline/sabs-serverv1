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
    public function index(Request $request)
    {
        $query = LoanProduct::with('fees')->where('is_active', 1)->latest();

        if ($request->has('all')) {
            $products = $query->get();
        } else {
            $products = $query->paginate(10);
        }

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
     * Show a specific loan product.
     */
    public function show($id)
    {
        $product = LoanProduct::with('fees')->find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Loan Product not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $product], 200);
    }

    /**
     * Update a loan product.
     */
    public function update(Request $request, $id)
    {
        $product = LoanProduct::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Loan Product not found'], 404);
        }

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

        $product->update($request->except('fees'));

        if ($request->has('fees')) {
            $product->fees()->sync($request->fees);
        }

        // Reload to include fees in response
        $product->load('fees');

        return response()->json(['success' => true, 'data' => $product, 'message' => 'Loan Product updated successfully'], 200);
    }

    /**
     * Delete a loan product.
     */
    public function destroy($id)
    {
        $product = LoanProduct::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Loan Product not found'], 404);
        }

        // Optional: Check active loans if we want to be strict, but User said it's fine.
        // SoftDelete will preserve the record for FK integrity anyway.
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Loan Product deleted successfully'], 200);
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
            'type' => 'required|in:fixed,flat,percent'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $fee = LoanFee::create($request->all());

        return response()->json(['success' => true, 'data' => $fee, 'message' => 'Fee created successfully'], 201);
    }

    /**
     * Update an existing fee.
     */
    public function updateFee(Request $request, $id)
    {
        $fee = LoanFee::find($id);

        if (!$fee) {
            return response()->json(['success' => false, 'message' => 'Fee not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,flat,percent'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $fee->update($request->all());

        return response()->json(['success' => true, 'data' => $fee, 'message' => 'Fee updated successfully'], 200);
    }

    /**
     * Delete a fee.
     */
    public function deleteFee($id)
    {
        $fee = LoanFee::find($id);

        if (!$fee) {
            return response()->json(['success' => false, 'message' => 'Fee not found'], 404);
        }

        // Optional: Check if fee is used by any product before deleting?
        // For now, standard deletion (pivot table handles cleanup via foreign keys if defined, or manually).
        $fee->products()->detach(); // Detach from all products first
        $fee->delete();

        return response()->json(['success' => true, 'message' => 'Fee deleted successfully'], 200);
    }
}