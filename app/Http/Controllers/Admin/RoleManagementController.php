<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RoleManagementController extends Controller
{
    /**
     * Display a listing of roles with their permissions.
     */
    public function index()
    {
        $roles = Role::with('permissions')->where('guard_name', 'web')->get();
        return response()->json($roles);
    }

    /**
     * Display a listing of all available permissions.
     */
    public function getAllPermissions()
    {
        $permissions = Permission::where('guard_name', 'web')->get();
        
        // Define categories for grouping
        $categories = [
            'User Management' => ['manage_users', 'manage_roles', 'view_notifications', 'manage_settings'],
            'Customer Management' => ['view_customers', 'register_customer', 'edit_customer', 'view_customer_profile'],
            'Account Management' => ['view_accounts', 'create_account', 'edit_account', 'view_account_balances'],
            'Transactions' => ['view_deposits', 'create_deposit', 'view_withdrawals', 'create_withdrawal', 'view_withdrawal_requests', 'approve_withdrawal_requests', 'view_reversals', 'perform_reversal'],
            'Financials' => ['view_commissions', 'view_system_commission', 'view_balance'],
            'Loans' => ['view_loans_menu', 'view_loan_dashboard', 'view_active_loans', 'view_requested_loans', 'create_loan_application', 'view_loan_application', 'process_loan', 'approve_loans', 'disburse_loans', 'collect_loan_repayment', 'view_loan_history', 'manage_defaults'],
            'Treasury & Products' => ['view_treasury_menu', 'view_capital_accounts', 'transfer_funds', 'manage_loan_products', 'manage_loan_fees'],
            'Daily Collections' => ['view_daily_collections', 'view_daily_withdrawals'],
            'Reports & Dashboards' => ['view_reports', 'view_reporting_dashboard', 'view_dashboard_today', 'view_dashboard_week', 'view_dashboard_month', 'view_dashboard_year', 'view_dashboard_all_time']
        ];

        $grouped = [];
        
        // Initialize groups
        foreach ($categories as $key => $values) {
            $grouped[$key] = [];
        }
        // $grouped['Other'] = []; // Hiding 'Other' category per user request

        foreach ($permissions as $perm) {
            foreach ($categories as $cat => $names) {
                if (in_array($perm->name, $names)) {
                    $grouped[$cat][] = $perm;
                    break; 
                }
            }
        }

        return response()->json($grouped);
    }

    /**
     * Update permissions for a specific role.
     */
    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findById($id, 'web');

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Role permissions updated successfully',
            'role' => $role->load('permissions')
        ]);
    }
}
