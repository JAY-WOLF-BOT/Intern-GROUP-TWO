<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingResource;
use App\Models\Favorite;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    /**
     * Add listing to user's favorites.
     */
    public function addFavorite(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'listing_id' => 'required|integer|exists:listings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if already favorited
            $existingFavorite = Favorite::where('user_id', Auth::id())
                ->where('listing_id', $request->listing_id)
                ->first();

            if ($existingFavorite) {
                return response()->json([
                    'success' => false,
                    'message' => 'This listing is already in your favorites.'
                ], 400);
            }

            // Create favorite
            $favorite = Favorite::create([
                'user_id' => Auth::id(),
                'listing_id' => $request->listing_id,
            ]);

            $listing = Listing::with(['landlord', 'photos', 'primaryPhoto'])
                ->findOrFail($request->listing_id);

            return response()->json([
                'success' => true,
                'message' => 'Listing added to favorites.',
                'data' => [
                    'favorite_id' => $favorite->id,
                    'listing' => new ListingResource($listing)
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add listing to favorites.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove listing from favorites.
     */
    public function removeFavorite($favoriteId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        try {
            $favorite = Favorite::findOrFail($favoriteId);

            // Verify ownership
            if ($favorite->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only remove your own favorites.'
                ], 403);
            }

            $favorite->delete();

            return response()->json([
                'success' => true,
                'message' => 'Listing removed from favorites.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove favorite.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get user's favorited listings.
     */
    public function myFavorites(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        try {
            $query = Favorite::where('user_id', Auth::id())
                ->with(['listing' => function ($q) {
                    $q->with(['landlord', 'photos', 'primaryPhoto'])
                        ->verified()
                        ->available();
                }]);

            // Filter by neighborhood if provided
            if ($request->has('neighborhood') && !empty($request->neighborhood)) {
                $neighborhood = $request->neighborhood;
                $query->whereHas('listing', function ($q) use ($neighborhood) {
                    $q->where('neighborhood', $neighborhood);
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            if ($sortBy === 'price') {
                $query->orderBy(
                    Listing::select('price')
                        ->whereColumn('listings.id', 'favorites.listing_id'),
                    $sortOrder === 'asc' ? 'asc' : 'desc'
                );
            } else {
                $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
            }

            $favorites = $query->paginate(12);

            // Transform response
            $listings = $favorites->map(function ($favorite) {
                return new ListingResource($favorite->listing);
            });

            return response()->json([
                'success' => true,
                'data' => $listings,
                'meta' => [
                    'total' => $favorites->total(),
                    'per_page' => $favorites->perPage(),
                    'current_page' => $favorites->currentPage(),
                    'last_page' => $favorites->lastPage(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch favorites.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if listing is favorited by current user.
     */
    public function isFavorited($listingId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'is_favorited' => false
            ], 200);
        }

        try {
            $isFavorited = Favorite::where('user_id', Auth::id())
                ->where('listing_id', $listingId)
                ->exists();

            return response()->json([
                'success' => true,
                'is_favorited' => $isFavorited
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check favorite status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all favorites (optional).
     */
    public function clearAll(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        try {
            $deletedCount = Favorite::where('user_id', Auth::id())->delete();

            return response()->json([
                'success' => true,
                'message' => "Cleared {$deletedCount} favorite(s)."
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear favorites.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
