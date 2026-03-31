<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile page
     */
    public function show()
    {
        return view('profile');
    }

    /**
     * Update personal information
     */
    public function updateInfo(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }

    /**
     * Update profile avatar
     */
    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'profile_picture' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $user = Auth::user();
            if (!$user) {
                \Log::error('Upload attempted without authenticated user');
                return back()->with('error', 'You must be logged in to upload a profile picture.');
            }

            // Delete old profile picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
                \Log::info('Deleted old profile picture for user ' . $user->id);
            }

            // Store new picture in avatars directory
            $path = $request->file('profile_picture')->store('avatars', 'public');
            \Log::info('Profile picture stored at: ' . $path);

            $url = asset('storage/' . $path);

            // Update user:
            // - store internal path for the avatar file
            // - set publicly accessible URL (profile_photo_url)
            $user->update([
                'profile_picture' => $path,
                'profile_photo_url' => asset('storage/' . $path),
            ]);
            \Log::info('Profile picture updated for user ' . $user->id . ': ' . $path);

            return back()->with('success', 'Profile picture updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Profile picture validation failed: ' . json_encode($e->errors()));
            return back()->with('error', 'Invalid file. Please upload a JPEG, PNG, GIF image under 2MB.')->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Profile picture upload failed: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to upload profile picture: ' . $e->getMessage());
        }
    }

    /**
     * Delete profile picture
     */
    public function deleteProfilePicture()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                \Log::error('Delete attempted without authenticated user');
                return back()->with('error', 'You must be logged in.');
            }

            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
                \Log::info('Profile picture deleted for user ' . $user->id);
            }

            $user->update([
                'profile_picture' => null,
                'profile_photo_url' => null,
            ]);
            \Log::info('Profile picture record removed for user ' . $user->id);

            return back()->with('success', 'Profile picture removed successfully!');
        } catch (\Exception $e) {
            \Log::error('Profile picture deletion failed: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to delete profile picture: ' . $e->getMessage());
        }
    }

    /**
     * Get profile data
     */
    public function getProfile()
    {
        $user = Auth::user();
        $profilePictureUrl = $user->profile_picture
            ? asset('storage/' . $user->profile_picture)
            : null;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'role' => $user->role,
                'profile_picture' => $profilePictureUrl,
            ]
        ]);
    }

}
