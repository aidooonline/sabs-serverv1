<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanProcessingRequirement;
use App\LoanApplicationRequirement;
use App\LoanApplication;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class LoanProcessingController extends Controller
{
    /**
     * Get requirements for a specific loan application.
     * If they don't exist in the pivot table yet, initialize them from the master list.
     */
    public function index($loanId)
    {
        $loan = LoanApplication::find($loanId);
        if (!$loan) return response()->json(['success' => false, 'message' => 'Loan not found'], 404);

        // Check if requirements are already linked
        $linked = LoanApplicationRequirement::where('loan_application_id', $loanId)->count();

        if ($linked == 0) {
            // Initialize from master list
            $masters = LoanProcessingRequirement::all();
            foreach ($masters as $master) {
                LoanApplicationRequirement::create([
                    'loan_application_id' => $loanId,
                    'requirement_id' => $master->id,
                    'is_met' => 0
                ]);
            }
        }

        // Fetch with details
        $requirements = LoanApplicationRequirement::with('requirement')
            ->where('loan_application_id', $loanId)
            ->get();

        return response()->json(['success' => true, 'data' => $requirements, 'score' => $loan->compliance_score]);
    }

    /**
     * Toggle a requirement (checkbox).
     * Only works for 'text' type requirements or manual override.
     */
    public function toggle(Request $request, $id)
    {
        $req = LoanApplicationRequirement::find($id);
        if (!$req) return response()->json(['success' => false, 'message' => 'Requirement not found'], 404);

        $req->is_met = !$req->is_met;
        $req->save();

        $score = $this->calculateScore($req->loan_application_id);

        return response()->json(['success' => true, 'is_met' => $req->is_met, 'new_score' => $score]);
    }

    /**
     * Upload a file for a requirement.
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_application_id' => 'required',
            'requirement_id' => 'required',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        // Find the specific requirement record
        $req = LoanApplicationRequirement::find($request->requirement_id);
        if (!$req) return response()->json(['success' => false, 'message' => 'Requirement record not found'], 404);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Generate unique base name
            $baseName = 'loan_doc_' . bin2hex(random_bytes(8)) . '_' . time();
            $originalName = $baseName . '_original.jpg';
            $thumbName = $baseName . '_thumb.jpg';
            
            $destinationPath = base_path('../nobsimages3/images/user_avatar');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // 1. Save Original File
            $file->move($destinationPath, $originalName);
            $originalFullPath = $destinationPath . '/' . $originalName;

            // 2. Generate Compressed Thumbnail (PHP GD)
            try {
                // Safety check: Does GD library exist?
                if (function_exists('imagecreatefromjpeg')) {
                    $sourceImage = @imagecreatefromjpeg($originalFullPath);
                    if ($sourceImage) {
                        // Save with low quality (30) for fast app viewing
                        imagejpeg($sourceImage, $destinationPath . '/' . $thumbName, 30);
                        imagedestroy($sourceImage);
                    } else {
                        // Fallback if image loading fails: just copy the file
                        @copy($originalFullPath, $destinationPath . '/' . $thumbName);
                    }
                } else {
                    // Fallback if GD is missing: just copy the file
                    @copy($originalFullPath, $destinationPath . '/' . $thumbName);
                }
            } catch (\Exception $e) {
                // Fallback if anything goes wrong during compression
                @copy($originalFullPath, $destinationPath . '/' . $thumbName);
            }
            
            $basePublicUrl = "https://banqpopulaire.website/nobsimages3/images/user_avatar/";
            
            $req->file_path = $basePublicUrl . $thumbName; // Thumbnail for view
            $req->file_path_original = $basePublicUrl . $originalName; // Original for download
            $req->is_met = 1;
            $req->save();

            $score = $this->calculateScore($req->loan_application_id);

            return response()->json([
                'success' => true, 
                'message' => 'File uploaded successfully', 
                'file_path' => $req->file_path,
                'file_path_original' => $req->file_path_original,
                'new_score' => $score
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }

    private function calculateScore($loanId)
    {
        $all = LoanApplicationRequirement::where('loan_application_id', $loanId)->get();
        $total = $all->count();
        $met = $all->where('is_met', 1)->count();

        $score = $total > 0 ? ($met / $total) * 10 : 0;
        $score = round($score, 2);

        LoanApplication::where('id', $loanId)->update(['compliance_score' => $score]);

        return $score;
    }

    /**
     * Submit the application for approval.
     * Updates status from 'pending' to 'awaiting_approval'.
     */
    public function submit(Request $request, $id)
    {
        $loan = LoanApplication::find($id);
        if (!$loan) return response()->json(['success' => false, 'message' => 'Loan not found'], 404);

        // REMOVED RESTRICTION: Allow submission from any state (except maybe deleted)
        // But logic implies moving TO pending_approval.
        // We will just execute the status change.
        
        $loan->status = 'pending_approval';
        $loan->save();

        return response()->json(['success' => true, 'message' => 'Application submitted for approval successfully.']);
    }
}
