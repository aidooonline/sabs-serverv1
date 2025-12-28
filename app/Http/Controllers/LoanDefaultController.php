<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanDefaultLog;
use App\LoanApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoanDefaultController extends Controller
{
    /**
     * Log an action related to a defaulted loan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $loanApplicationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function logAction(Request $request, $loanApplicationId)
    {
        $validator = Validator::make($request->all(), [
            'action_type' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $loanApplication = LoanApplication::find($loanApplicationId);

        if (!$loanApplication) {
            return response()->json(['success' => false, 'message' => 'Loan Application not found.'], 404);
        }

        $log = LoanDefaultLog::create([
            'loan_application_id' => $loanApplicationId,
            'action_type' => $request->action_type,
            'description' => $request->description,
            'created_by' => Auth::id(), // Log the currently authenticated user
        ]);

        return response()->json(['success' => true, 'message' => 'Action logged successfully.', 'data' => $log], 201);
    }
}
