<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanProduct;
use App\LoanFee;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LoanProductController extends Controller
{
    // --- FEES ---

    public function getFees()
    {
        $fees = LoanFee::all();
        return response()->json(['success' => true, 'data' => $fees], 200);
    }

    public function storeFee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'type' => 'required|in:percent,flat',
            'value' => 'required|numeric',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $fee = LoanFee::create($request->all());
        return response()->json(['success' => true, 'data' => $fee], 201);
    }

    // --- PRODUCTS ---

    public function index()
    {
        // Return products with their fees
        $products = LoanProduct::with('fees')->where('is_active', 1)->get();
        return response()->json(['success' => true, 'data' => $products], 200);
    }

    public function storeProduct(Request $request)
    {
        // Expected JSON:
        // {
        //   "name": "SME Loan",
        //   "min_principal": 100,
        //   "max_principal": 5000,
        //   "duration_options": "30,60,90",
        //   "repayment_frequency_options": "Weekly,Monthly",
        //   "fee_ids": [1, 2, 5]  // Array of Fee IDs to attach
        // }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'min_principal' => 'required|numeric',
            'max_principal' => 'required|numeric',
            'duration_options' => 'required|string', // Validated as string for now
            'repayment_frequency_options' => 'required|string',
            'fee_ids' => 'array',
            'fee_ids.*' => 'exists:loan_fees,id',
            'created_by' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        DB::beginTransaction();

        try {
            $product = LoanProduct::create([
                'name' => $request->name,
                'description' => $request->description,
                'min_principal' => $request->min_principal,
                'max_principal' => $request->max_principal,
                'duration_options' => $request->duration_options,
                'repayment_frequency_options' => $request->repayment_frequency_options,
                'created_by' => $request->created_by ?? 0
            ]);

            if ($request->has('fee_ids')) {
                $product->fees()->attach($request->fee_ids);
            }

            DB::commit();
            return response()->json(['success' => true, 'data' => $product->load('fees')], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
