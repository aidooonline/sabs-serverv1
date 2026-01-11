<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');
        $currentUser = Auth::user();
        
        // Ensure roles are loaded
        if (!$currentUser->relationLoaded('roles')) {
            $currentUser->load('roles');
        }

        // Robust Security: Check loaded role names directly (Case-Insensitive)
        $userRoles = $currentUser->roles->pluck('name')->map(function($name) {
            return strtolower($name);
        })->toArray();
        
        $allowedRoles = ['admin', 'super admin', 'owner'];
        $isAdmin = (bool) array_intersect($userRoles, $allowedRoles);

        // If not an Admin, restrict list to ONLY their own profile
        if (!$isAdmin) {
            $query->where('id', $currentUser->id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role if provided
        if ($request->has('role')) {
            $role = $request->role;
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($users);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => $request->is_active ?? true,
            'created_by' => Auth::id(),
            'type' => 'regular' // Default type, roles handle permissions
        ]);

        // Assign Role
        $user->assignRole($request->role);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->load('roles')
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with('roles')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Security: If current user is not Admin/Super Admin/Owner, restrict what they can update
        $currentUser = Auth::user();
        
        if (!$currentUser->relationLoaded('roles')) {
            $currentUser->load('roles');
        }
        
        $userRoles = $currentUser->roles->pluck('name')->map(function($name) {
            return strtolower($name);
        })->toArray();
        $isAdmin = (bool) array_intersect($userRoles, ['admin', 'super admin', 'owner']);
        
        $isRestricted = !$isAdmin;

        if ($isRestricted && $currentUser->id !== $user->id) {
             return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|unique:users,phone,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'sometimes|required|exists:roles,name',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($isRestricted) {
            // Agents can only update Name and Password
            $user->fill($request->only(['name']));
        } else {
            // Admins can update everything except password (handled below) and role (handled below)
            $user->fill($request->except(['password', 'role']));
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Sync Role only if NOT restricted
        if (!$isRestricted && $request->has('role')) {
            $user->syncRoles([$request->role]);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'message' => 'User status updated',
            'is_active' => $user->is_active
        ]);
    }
}
