<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;

class RoleSetupController extends Controller
{
    public function setup()
    {
        // 1. Create Roles
        $roles = ['Admin', 'Manager', 'Agent'];
        
        foreach ($roles as $roleName) {
            if (!Role::where('name', $roleName)->where('guard_name', 'web')->exists()) {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
            }
        }

        // 2. Create Permissions
        $permissions = [
            'manage_treasury',
            'manage_products',
            'approve_loans',
            'disburse_loans',
            'manage_users',
            'view_reports',
            'create_loans',
            'process_loans',
            'collect_repayments'
        ];

        foreach ($permissions as $perm) {
            if (!Permission::where('name', $perm)->where('guard_name', 'web')->exists()) {
                Permission::create(['name' => $perm, 'guard_name' => 'web']);
            }
        }

        // 3. Assign Permissions to Roles
        $admin = Role::findByName('Admin', 'web');
        $all_permissions = Permission::where('guard_name', 'web')->get();
        $admin->syncPermissions($all_permissions); 

        $manager = Role::findByName('Manager', 'web');
        $manager_permissions = Permission::where('guard_name', 'web')->whereIn('name', [
            'approve_loans',
            'disburse_loans',
            'view_reports',
            'create_loans', // Managers can also help
            'process_loans',
            'collect_repayments'
        ])->get();
        $manager->syncPermissions($manager_permissions);

        $agent = Role::findByName('Agent', 'web');
        $agent_permissions = Permission::where('guard_name', 'web')->whereIn('name', [
            'create_loans',
            'process_loans',
            'collect_repayments'
        ])->get();
        $agent->syncPermissions($agent_permissions);

        return response()->json(['success' => true, 'message' => 'Roles and Permissions setup successfully.']);
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user) return response()->json(['success' => false, 'message' => 'User not found'], 404);

        $role = Role::findByName($request->role, 'web');
        if (!$role) return response()->json(['success' => false, 'message' => 'Role not found'], 404);

        // Remove existing roles (assuming single role per user for this simple flow)
        $user->syncRoles([$role]);

        return response()->json(['success' => true, 'message' => "Role {$request->role} assigned to {$user->name}"]);
    }
    
    /**
     * Get all roles.
     */
    public function getRoles()
    {
        $roles = Role::where('guard_name', 'web')->pluck('name');
        return response()->json(['success' => true, 'data' => $roles]);
    }
}
