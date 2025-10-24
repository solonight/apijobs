<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Authentication Endpoints"
 * )
 */
// The above annotation groups all authentication-related endpoints under the "Auth" tag in Swagger UI.

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'in:user,admin,employer'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role 
        $role = $request->input('role', 'user'); // default to 'user' if not provided
        $user->assignRole($role);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('roles:name', 'permissions:name'),
        ]);
    }

        /**
         * @OA\Post(
         *     path="/api/register",
         *     tags={"Auth"},
         *     summary="Register a new user",
         *     description="Creates a new user account.",
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"name", "email", "password"},
         *             @OA\Property(property="name", type="string"),
         *             @OA\Property(property="email", type="string", format="email"),
         *             @OA\Property(property="password", type="string", format="password"),
         *             @OA\Property(property="role", type="string", enum={"user", "admin", "employer"})
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="User registered successfully",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="access_token", type="string"),
         *             @OA\Property(property="token_type", type="string"),
         *             @OA\Property(property="user", ref="#/components/schemas/User")
         *         )
         *     )
         * )
         */
        // The above annotation documents the POST /api/register endpoint.

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401); // <-- 401 Unauthorized
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('roles:name', 'permissions:name'),
        ]);
    }

        /**
         * @OA\Post(
         *     path="/api/login",
         *     tags={"Auth"},
         *     summary="Login a user",
         *     description="Authenticates a user and returns an access token.",
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"email", "password"},
         *             @OA\Property(property="email", type="string", format="email"),
         *             @OA\Property(property="password", type="string", format="password")
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Login successful",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="access_token", type="string"),
         *             @OA\Property(property="token_type", type="string"),
         *             @OA\Property(property="user", ref="#/components/schemas/User")
         *         )
         *     ),
         *     @OA\Response(
         *         response=401,
         *         description="Invalid credentials",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="message", type="string")
         *         )
         *     )
         * )
         */
        // The above annotation documents the POST /api/login endpoint.

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

        /**
         * @OA\Post(
         *     path="/api/logout",
         *     tags={"Auth"},
         *     summary="Logout a user",
         *     description="Logs out the authenticated user.",
         *     @OA\Response(
         *         response=200,
         *         description="Logout successful",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="message", type="string")
         *         )
         *     )
         * )
         */
        // The above annotation documents the POST /api/logout endpoint.

    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('roles:name', 'permissions:name')
        ]);
    }

        /**
         * @OA\Get(
         *     path="/api/user",
         *     tags={"Auth"},
         *     summary="Get authenticated user",
         *     description="Returns the currently authenticated user.",
         *     @OA\Response(
         *         response=200,
         *         description="Authenticated user",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="user", ref="#/components/schemas/User")
         *         )
         *     )
         * )
         */
        // The above annotation documents the GET /api/user endpoint.
}