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
        // Add Role-based authorization check
        if (!\Auth::user()->hasRole(['Admin', 'Owner', 'super admin'])) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to approve applications.'], 403);
        }

        $application = LoanApplication::find($id);

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found'], 404);
        }

        // Status must be 'pending_approval' to be approved
        if ($application->status !== 'pending_approval') {
            return response()->json(['success' => false, 'message' => 'Application is not awaiting approval.'], 400);
        }

        // Enforce Compliance Score of 10/10
        if ($application->compliance_score < 10) {
            return response()->json([
                'success' => false, 
                'message' => 'Compliance Check Failed: Score is ' . $application->compliance_score . '/10. All required documents (Agreement, Applicant Pic, Guarantor Pic) must be uploaded.'
            ], 400);
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
        // Add Role-based authorization check
        if (!\Auth::user()->hasRole(['Admin', 'Owner', 'super admin'])) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to reject applications.'], 403);
        }

        $application = LoanApplication::find($id);

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found'], 404);
        }

        // Allow rejection from either pending state
        if (!in_array($application->status, ['pending', 'pending_approval'])) {
            return response()->json(['success' => false, 'message' => 'Application cannot be rejected at this stage.'], 400);
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
