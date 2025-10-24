<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints for managing users"
 * )
 */
// The above annotation groups all user-related endpoints under the "Users" tag in Swagger UI.

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
         * @OA\Get(
         *     path="/api/users",
         *     tags={"Users"},
         *     summary="Get all users",
         *     description="Returns a list of all users. Only accessible by admins.",
         *     @OA\Response(
         *         response=200,
         *         description="Successful response",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(
         *                 property="users",
         *                 type="array",
         *                 @OA\Items(ref="#/components/schemas/User")
         *             )
         *         )
         *     )
         * )
         */
        // The above annotation documents the GET /api/users endpoint.

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
         * @OA\Get(
         *     path="/api/users/{id}",
         *     tags={"Users"},
         *     summary="Get a specific user",
         *     description="Returns details for a specific user.",
         *     @OA\Parameter(
         *         name="id",
         *         in="path",
         *         required=true,
         *         description="User ID",
         *         @OA\Schema(type="integer")
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Successful response",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(
         *                 property="user",
         *                 ref="#/components/schemas/User"
         *             )
         *         )
         *     )
         * )
         */
        // The above annotation documents the GET /api/users/{id} endpoint.

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
         * @OA\Put(
         *     path="/api/users/{id}",
         *     tags={"Users"},
         *     summary="Update a user",
         *     description="Update a user's profile information.",
         *     @OA\Parameter(
         *         name="id",
         *         in="path",
         *         required=true,
         *         description="User ID",
         *         @OA\Schema(type="integer")
         *     ),
         *     @OA\RequestBody(
         *         required=false,
         *         @OA\JsonContent(
         *             required={"name", "email"},
         *             @OA\Property(property="name", type="string"),
         *             @OA\Property(property="email", type="string", format="email")
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Successful response",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(
         *                 property="user",
         *                 ref="#/components/schemas/User"
         *             )
         *         )
         *     )
         * )
         */
        // The above annotation documents the PUT /api/users/{id} endpoint.

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
         * @OA\Post(
         *     path="/api/users/{id}/roles",
         *     tags={"Users"},
         *     summary="Assign roles to a user",
         *     description="Assign one or more roles to a user.",
         *     @OA\Parameter(
         *         name="id",
         *         in="path",
         *         required=true,
         *         description="User ID",
         *         @OA\Schema(type="integer")
         *     ),
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"roles"},
         *             @OA\Property(
         *                 property="roles",
         *                 type="array",
         *                 @OA\Items(type="string")
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Roles assigned successfully",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="message", type="string"),
         *             @OA\Property(property="user", ref="#/components/schemas/User")
         *         )
         *     )
         * )
         */
        // The above annotation documents the POST /api/users/{id}/roles endpoint.

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

        /**
         * @OA\Post(
         *     path="/api/users/{id}/permissions",
         *     tags={"Users"},
         *     summary="Give permissions to a user",
         *     description="Assign one or more permissions to a user.",
         *     @OA\Parameter(
         *         name="id",
         *         in="path",
         *         required=true,
         *         description="User ID",
         *         @OA\Schema(type="integer")
         *     ),
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"permissions"},
         *             @OA\Property(
         *                 property="permissions",
         *                 type="array",
         *                 @OA\Items(type="string")
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Permissions assigned successfully",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="message", type="string"),
         *             @OA\Property(property="user", ref="#/components/schemas/User")
         *         )
         *     )
         * )
         */
        // The above annotation documents the POST /api/users/{id}/permissions endpoint.

    public function destroy($id)
    {
        $admin = auth()->user();
        if (!$admin->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);

        if ($user->hasRole('admin')) {
            return response()->json(['error' => 'Cannot delete another admin'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

        /**
         * @OA\Delete(
         *     path="/api/users/{id}",
         *     tags={"Users"},
         *     summary="Delete a user",
         *     description="Delete a user by ID. Only admins can delete users.",
         *     @OA\Parameter(
         *         name="id",
         *         in="path",
         *         required=true,
         *         description="User ID",
         *         @OA\Schema(type="integer")
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="User deleted successfully",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="message", type="string")
         *         )
         *     ),
         *     @OA\Response(
         *         response=403,
         *         description="Unauthorized or cannot delete another admin",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="error", type="string")
         *         )
         *     )
         * )
         */
        // The above annotation documents the DELETE /api/users/{id} endpoint.
}