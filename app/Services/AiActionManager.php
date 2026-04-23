<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * AiActionManager - Prepares and executes sensitive write actions.
 * Implements Human-in-the-loop (HITL) security.
 */
class AiActionManager
{
    /**
     * Prepares a confirmation payload for sensitive actions.
     */
    public function prepareAction($action, $params)
    {
        $payload = [
            'action' => $action,
            'params' => $params,
            'timestamp' => time(),
            'user_id' => Auth::id()
        ];

        // Unique ID for this action to be confirmed
        $actionId = 'act_' . uniqid();
        
        // Temporarily store the pending action in cache or DB if persistence is needed
        // For simplicity, we pass the payload to the frontend to be sent back on confirm.

        return [
            'ui_type' => 'action_confirmation_card',
            'ui_metadata' => [
                'action_id' => $actionId,
                'action_type' => $action,
                'title' => $this->getActionTitle($action),
                'description' => $this->getActionDescription($action, $params),
                'risk_level' => $this->getRiskLevel($action),
                'payload' => $payload // This will be sent back to /ai/execute-action
            ],
            'caption' => "I have prepared the requested action. Please review and authorize it below."
        ];
    }

    private function getActionTitle($action)
    {
        $titles = [
            'reactivate_account' => 'Reactivate Account',
            'toggle_user_status' => 'Change User Status',
            'perform_reversal' => 'Perform Transaction Reversal'
        ];
        return $titles[$action] ?? 'Authorize Action';
    }

    private function getActionDescription($action, $params)
    {
        if ($action === 'reactivate_account') {
            return "Authorize the re-activation of account number: " . ($params['account_number'] ?? 'Unknown');
        }
        if ($action === 'toggle_user_status') {
            return "Enable or Disable User ID: " . ($params['user_id'] ?? 'Unknown');
        }
        return "Please confirm you want to proceed with this request.";
    }

    private function getRiskLevel($action)
    {
        return in_array($action, ['perform_reversal', 'toggle_user_status']) ? 'High' : 'Medium';
    }

    /**
     * Executes the action after user confirmation.
     * MUST re-verify permissions here.
     */
    public function executeAction($payload)
    {
        $action = $payload['action'];
        $params = $payload['params'];
        $compId = Auth::user()->comp_id;

        Log::info("AI Execute Action: " . $action, ['user' => Auth::id(), 'params' => $params]);

        try {
            if ($action === 'reactivate_account') {
                $acc = DB::table('nobs_user_account_numbers')
                    ->where('account_number', $params['account_number'])
                    ->where('comp_id', $compId)
                    ->update(['account_status' => 'active']);
                return ['success' => true, 'message' => "Account {$params['account_number']} has been reactivated."];
            }

            if ($action === 'toggle_user_status') {
                // Security: Only management can toggle status
                if (!Auth::user()->hasAnyRole(['Admin', 'Owner', 'Manager'])) {
                    return ['success' => false, 'message' => 'Insufficient permissions to toggle user status.'];
                }
                
                $user = DB::table('users')->where('id', $params['user_id'])->where('comp_id', $compId)->first();
                if (!$user) return ['success' => false, 'message' => 'User not found.'];
                
                DB::table('users')->where('id', $user->id)->update(['is_disabled' => !$user->is_disabled]);
                $status = !$user->is_disabled ? 'Disabled' : 'Enabled';
                return ['success' => true, 'message' => "User {$user->name} is now $status."];
            }

            return ['success' => false, 'message' => "Action '$action' not recognized."];

        } catch (\Throwable $e) {
            Log::error("AI Execution Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'A technical error occurred during execution.'];
        }
    }
}
