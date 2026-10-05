<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $role = Role::where(
            'role_name',
            'Student'
        )->first();

        if (!$role) {
            $role = Role::create([
                'role_name' => 'Student',
            ]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role_id' => $role->role_id,
        ]);

        $token = $user->createToken(
            'mentorlink-token'
        )->plainTextToken;

        $user->load('role');

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    /**
     * Login user.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);

        $user = User::where(
            'email',
            $validated['email']
        )->first();

        if (
            !$user ||
            !Hash::check(
                $validated['password'],
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'email' => [
                    'The provided credentials are incorrect.',
                ],
            ]);
        }

        $token = $user->createToken(
            'mentorlink-token'
        )->plainTextToken;

        $user->load('role');

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Logout current user.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
        ]);
    }

    /**
     * Get current authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'role',
            'mentorProfile',
            'skills',
        ]);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }
}