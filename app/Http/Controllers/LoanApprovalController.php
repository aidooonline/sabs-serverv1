<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanApplication;
use Illuminate\Support\Facades\Validator;

class LoanApprovalController extends Controller
{
    /**
     * Approve a Loan Application.
     */
    public function approve(Request $request, $id)
    {
        $application = LoanApplication::find($id);

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found'], 404);
        }

        if ($application->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Application is not pending'], 400);
        }

        // Logic for approval
        // For now, just change status. 
        // In future sprints, this might trigger document checks or schedule generation if not done yet.
        
        $application->status = 'approved';
        // $application->approved_by = $request->user()->id; // If we had this column
        $application->save();

        return response()->json([
            'success' => true, 
            'message' => 'Application approved successfully.',
            'data' => $application
        ], 200);
    }

    /**
     * Reject a Loan Application.
     */
    public function reject(Request $request, $id)
    {
        $application = LoanApplication::find($id);

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found'], 404);
        }

        if ($application->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Application is not pending'], 400);
        }

        $application->status = 'rejected';
        // $application->rejected_by = $request->user()->id;
        // $application->rejection_reason = $request->reason; 
        $application->save();

        return response()->json([
            'success' => true, 
            'message' => 'Application rejected.',
            'data' => $application
        ], 200);
    }
}
