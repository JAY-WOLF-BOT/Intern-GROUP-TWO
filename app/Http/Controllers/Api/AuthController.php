<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user (tenant or landlord).
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:tenant,landlord',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'profile_info' => json_encode([
                    'bio' => $request->bio ?? null,
                    'avatar' => null,
                ]),
            ]);

            $token = $user->createToken('mobile-app', ['*'])->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully.',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user with email and password.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check credentials
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        try {
            // Revoke old tokens
            $user->tokens()->delete();

            // Create new token
            $token = $user->createToken('mobile-app', ['*'])->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login with OTP (phone-based authentication).
     */
    public function loginWithOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'otp_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // Verify OTP
        if ($user->otp_code !== $request->otp_code || now()->isAfter($user->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.'
            ], 401);
        }

        try {
            // Clear OTP and create token
            $user->update([
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);

            $user->tokens()->delete();
            $token = $user->createToken('mobile-app', ['*'])->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user())
        ], 200);
    }

    /**
     * Logout and revoke token.
     */
    public function logout(Request $request): JsonResponse
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ], 200);
    }

    /**
     * Refresh token (revoke old, create new).
     */
    public function refresh(Request $request): JsonResponse
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        try {
            // Revoke old token
            $request->user()->currentAccessToken()->delete();

            // Create new token
            $token = $request->user()->createToken('mobile-app', ['*'])->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully.',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
