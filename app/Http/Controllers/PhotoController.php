<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Photo;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PhotoController extends Controller
{
    /**
     * Upload a photo for a listing.
     */
    public function upload(Request $request, $listingId): JsonResponse
    {
        $listing = Listing::findOrFail($listingId);

        // Check if user is authenticated and owns the listing
        if (!Auth::check() || $listing->landlord_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only upload photos for your own listings.'
            ], 403);
        }

        // Check photo limit
        if (Photo::hasReachedPhotoLimit($listingId)) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum 3 photos allowed per listing.',
                'remaining_slots' => Photo::getRemainingPhotoSlots($listingId)
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max, added webp
            'is_primary' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Validate file exists and is readable
            $uploadedFile = $request->file('photo');
            if (!$uploadedFile || !$uploadedFile->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file upload.'
                ], 400);
            }

            // Upload to Cloudinary
            $cloudinaryUpload = Cloudinary::upload($uploadedFile->getRealPath(), [
                'folder' => 'housing-marketplace/listings/' . $listingId,
                'public_id' => 'listing_' . $listingId . '_' . time() . '_' . uniqid(),
                'transformation' => [
                    ['width' => 800, 'height' => 600, 'crop' => 'fill'],
                    ['quality' => 'auto']
                ]
            ]);

            // If setting as primary, remove primary status from other photos
            if ($request->boolean('is_primary')) {
                Photo::where('listing_id', $listingId)->update(['is_primary' => false]);
            }

            // Create photo record
            $photo = Photo::create([
                'listing_id' => $listingId,
                'photo_path' => $cloudinaryUpload->getPublicId(),
                'photo_url' => $cloudinaryUpload->getSecurePath(),
                'is_primary' => $request->boolean('is_primary'),
                'order' => (Photo::where('listing_id', $listingId)->max('order') ?? 0) + 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully.',
                'data' => $photo,
                'remaining_slots' => Photo::getRemainingPhotoSlots($listingId)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a photo.
     */
    public function delete($photoId): JsonResponse
    {
        $photo = Photo::findOrFail($photoId);
        $listing = $photo->listing;

        // Check if user is authenticated and owns the listing
        if (!Auth::check() || $listing->landlord_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete photos from your own listings.'
            ], 403);
        }

        try {
            // Delete from Cloudinary (only if photo_path exists)
            if ($photo->photo_path) {
                Cloudinary::destroy($photo->photo_path);
            }

            // Delete from database
            $photo->delete();

            // Reorder remaining photos
            $remainingPhotos = Photo::where('listing_id', $listing->id)
                ->orderBy('order')
                ->get();

            foreach ($remainingPhotos as $index => $remainingPhoto) {
                $remainingPhoto->update(['order' => $index + 1]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Photo deleted successfully.',
                'remaining_slots' => Photo::getRemainingPhotoSlots($listing->id)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete photo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
