<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Constructor - applies middleware for permissions
     */
    public function __construct()
    {
        // Laratrust middleware examples:
        $this->middleware('permission:view-users')->only(['index', 'show']);
        $this->middleware('permission:create-users')->only(['store']);
        $this->middleware('permission:update-users')->only(['update']);
        $this->middleware('permission:delete-users')->only(['destroy']);
        $this->middleware('permission:assign-roles')->only(['assignRoles']);
    }

    /**
     * Get all users (only for admins)
     */
    public function index()
    {
        // Eager load roles and permissions to avoid N+1 problem
        $users = User::with(['roles', 'permissions'])->get();
        
        return response()->json([
            'users' => $users
        ]);
    }

    /**
     * Get specific user
     */
    public function show(User $user)
    {
        return response()->json([
            'user' => $user->load(['roles', 'permissions'])
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'user' => $user->load(['roles', 'permissions'])
        ]);
    }

    /**
     * Assign roles to a user
     */
    public function assignRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        // Sync roles (replace all existing roles with new ones)
        $user->syncRoles($request->roles);

        return response()->json([
            'message' => 'Roles assigned successfully',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * Give permissions to a user
     */
    public function givePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Give permissions to user
        $user->givePermissions($request->permissions);

        return response()->json([
            'message' => 'Permissions assigned successfully',
            'user' => $user->load('permissions')
        ]);
    }
}