<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Get current authenticated user's profile.
     */
    public function show(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource(Auth::user())
        ], 200);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . Auth::id(),
            'phone_number' => 'nullable|string|unique:users,phone_number,' . Auth::id(),
            'bio' => 'nullable|string|max:500',
            'avatar_url' => 'nullable|string|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            /** @var User $user */
            $user = Auth::user();

            // Update basic fields
            $user->update($request->only(['name', 'email', 'phone_number']));

            // Update profile_info JSON if bio or avatar provided
            if ($request->has('bio') || $request->has('avatar_url')) {
                $profileInfo = $user->profile_info ?? [];
                
                if ($request->has('bio')) {
                    $profileInfo['bio'] = $request->bio;
                }
                
                if ($request->has('avatar_url')) {
                    $profileInfo['avatar'] = $request->avatar_url;
                }

                $user->update(['profile_info' => $profileInfo]);
            }

            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => new UserResource($user)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            /** @var User $user */
            $user = Auth::user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.'
                ], 401);
            }

            // Update password
            $user->update(['password' => Hash::make($request->new_password)]);

            // Optionally revoke all tokens after password change
            // $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Request OTP for phone verification.
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|regex:/^(\+233|0)[0-9]{9,10}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find user by phone
            $user = \App\Models\User::where('phone_number', $request->phone_number)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            // Generate 6-digit OTP
            $otp = random_int(100000, 999999);
            $expiresAt = now()->addMinutes(10);

            // Save OTP
            $user->update([
                'otp_code' => $otp,
                'otp_expires_at' => $expiresAt,
            ]);

            // In production: Send OTP via SMS
            // $this->sendOtpViaSms($user->phone_number, $otp);
            // For testing, log OTP
            Log::info("OTP for {$user->phone_number}: {$otp}");

            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your phone number.',
                'debug_otp' => config('app.debug') ? $otp : null, // Only in debug mode
                'expires_in_minutes' => 10
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to request OTP.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|regex:/^(\+233|0)[0-9]{9,10}$/',
            'otp_code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = \App\Models\User::where('phone_number', $request->phone_number)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            // Verify OTP
            if ($user->otp_code != $request->otp_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP code.'
                ], 401);
            }

            // Check expiration
            if (now()->isAfter($user->otp_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired.'
                ], 401);
            }

            // Clear OTP
            $user->update([
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics for landlord dashboard.
     */
    public function statistics(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        try {
            /** @var User $user */
            $user = Auth::user();

            // Only landlords should access this
            if ($user->role !== 'landlord') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only landlords can access statistics.'
                ], 403);
            }

            $userId = $user->id;
            $listingsCount = $user->listings()->count();
            $verifiedListings = $user->listings()->verified()->count();
            $totalViews = $user->listings()->sum('view_count');
            $totalPayments = \App\Models\Payment::whereHas('listing.landlord', function ($q) use ($userId) {
                $q->where('id', $userId);
            })->where('payment_status', 'completed')->sum('amount');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_listings' => $listingsCount,
                    'verified_listings' => $verifiedListings,
                    'pending_listings' => $listingsCount - $verifiedListings,
                    'total_views' => $totalViews,
                    'total_revenue' => number_format($totalPayments, 2),
                    'average_price' => number_format($user->listings()->average('price') ?? 0, 2),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
