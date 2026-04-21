<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportSystemController extends Controller
{
    public function getLiveReport(Request $request)
    {
        try {
            // DIAGNOSTIC TEST: Prove the route is working
            return response()->json([
                'success' => true, 
                'message' => 'Connection Successful. The route and middleware are working fine.',
                'debug' => [
                    'user_id' => auth()->id(),
                    'comp_id' => auth()->user()->comp_id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Diagnostic Error: ' . $e->getMessage()], 500);
        }
    }

    public function saveSnapshot(Request $request) { return response()->json(['success' => true]); }
    public function exportCsv(Request $request) { return response()->json(['success' => true]); }
}
