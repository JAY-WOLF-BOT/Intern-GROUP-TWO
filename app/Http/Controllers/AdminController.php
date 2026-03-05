<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Verify and approve a listing.
     */
    public function verifyListing(Request $request, $listingId): JsonResponse
    {
        $listing = Listing::findOrFail($listingId);

        if ($listing->verification_status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Listing is already verified.'
            ], 400);
        }

        $listing->update([
            'verification_status' => 'approved',
            'rejection_reason' => null // Clear any previous rejection reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Listing has been verified and approved.',
            'data' => $listing->load('landlord', 'photos')
        ]);
    }

    /**
     * Reject a listing with reason.
     */
    public function rejectListing(Request $request, $listingId): JsonResponse
    {
        $listing = Listing::findOrFail($listingId);

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $listing->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Listing has been rejected.',
            'data' => $listing->load('landlord')
        ]);
    }
}
