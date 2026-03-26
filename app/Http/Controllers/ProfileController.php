<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Upload profile picture
     */
    public function uploadProfilePicture(Request $request)
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

            // Store new picture
            $path = $request->file('profile_picture')->store('profiles', 'public');
            \Log::info('Profile picture stored at: ' . $path);

            $url = asset('storage/' . $path);

            // Update user
            $user->update([
                'profile_picture' => $path,
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

            $user->update(['profile_picture' => null]);
            \Log::info('Profile picture record removed for user ' . $user->id);

            return back()->with('success', 'Profile picture removed successfully!');
        } catch (\Exception $e) {
            \Log::error('Profile picture deletion failed: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to delete profile picture: ' . $e->getMessage());
        }
    }
}
