<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\CompanyInfo;  
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role; 

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($validatedData)) {
            $user = Auth::user();
            $customToken = $this->createCustomToken($user->id, 'mysecretkey');
            $user->api_token = $customToken;
            $user->save();

            $companyinfo = CompanyInfo::where('id',$user->comp_id)->get()->toArray();
            $userinfo = Auth::user();

            // Get roles and permissions using Spatie's methods
            $roles = $userinfo->getRoleNames(); // Get names of roles

            // --- THIS IS THE NEW PART ---
            // We get only the permissions that are relevant to the mobile app
            $relevant_permissions = [
                'collect_repayments', 'manage_treasury', 'manage_products', 'approve_loans',
                'disburse_loans', 'manage_users', 'view_reports', 'create_loans', 'process_loans'
            ];
            $permissions = $userinfo->getPermissionsViaRoles()->whereIn('name', $relevant_permissions)->pluck('name');
            // --- END OF NEW PART ---

            $userinfo = $userinfo->toArray(); // Convert back to array for consistency

            return response()->json([
                'token' => $customToken,
                'companyid' => $user->comp_id,
                'companyinfo' => $companyinfo,
                'user' => $userinfo,
                'roles' => $roles,
                'permissions' => $permissions
            ], 200);
        } else {
            return response()->json(['error' => 'UnAuthorised'], 401);
        }
    }

    public function createCustomToken($userId, $secretKey)
    {
        $timestamp = time();
        $tokenData = $userId . $timestamp;
        $customToken = hash_hmac('sha256', $tokenData, $secretKey);
        return $customToken;
    }
}
