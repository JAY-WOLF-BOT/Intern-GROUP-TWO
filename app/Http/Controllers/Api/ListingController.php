<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    /**
     * Get all listings (public, verified only).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Listing::with(['landlord', 'photos', 'primaryPhoto'])
                ->verified()
                ->available();

            // Filter by budget range
            if ($request->has('budget_min') && is_numeric($request->budget_min)) {
                $query->where('price', '>=', $request->budget_min);
            }

            if ($request->has('budget_max') && is_numeric($request->budget_max)) {
                $query->where('price', '<=', $request->budget_max);
            }

            // Filter by neighborhood
            if ($request->has('neighborhood') && !empty($request->neighborhood)) {
                $query->byNeighborhood($request->neighborhood);
            }

            // Filter by bedrooms
            if ($request->has('bedrooms') && is_numeric($request->bedrooms)) {
                $query->byBedrooms($request->bedrooms);
            }

            // Filter by property type
            if ($request->has('property_type') && !empty($request->property_type)) {
                $query->byPropertyType($request->property_type);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $allowedSortFields = ['price', 'created_at', 'bedrooms', 'view_count'];

            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
            }

            $listings = $query->paginate(15);

            return response()->json([
                'success' => true,
                'data' => ListingResource::collection($listings),
                'meta' => [
                    'total' => $listings->total(),
                    'per_page' => $listings->perPage(),
                    'current_page' => $listings->currentPage(),
                    'last_page' => $listings->lastPage(),
                    'total_pages' => $listings->lastPage(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch listings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single listing by ID.
     */
    public function show($listingId): JsonResponse
    {
        try {
            $listing = Listing::with(['landlord', 'photos', 'primaryPhoto'])
                ->verified()
                ->findOrFail($listingId);

            // Increment view count
            $listing->increment('view_count');

            return response()->json([
                'success' => true,
                'data' => new ListingResource($listing)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get authenticated user's listings (for landlords).
     */
    public function myListings(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        try {
            $listings = Listing::with(['photos', 'primaryPhoto'])
                ->byLandlord(Auth::id())
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => ListingResource::collection($listings),
                'meta' => [
                    'total' => $listings->total(),
                    'per_page' => $listings->perPage(),
                    'current_page' => $listings->currentPage(),
                    'last_page' => $listings->lastPage(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch your listings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new listing (landlords only).
     */
    public function store(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:1',
            'area_sqft' => 'nullable|integer|min:0',
            'property_type' => 'required|in:apartment,house,studio,shared_room,bungalow',
            'neighborhood' => 'required|string',
            'location_address' => 'nullable|string',
            'location_lat' => 'nullable|numeric',
            'location_long' => 'nullable|numeric',
            'furnished' => 'nullable|boolean',
            'wifi' => 'nullable|boolean',
            'parking' => 'nullable|boolean',
            'security' => 'nullable|boolean',
            'pool' => 'nullable|boolean',
            'gym' => 'nullable|boolean',
            'photos' => 'nullable|array|max:3',
            'photos.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate default location if not provided
            $locationAddress = $request->location_address ?? $request->neighborhood . ', Accra';
            $locationLat = $request->location_lat ?? 5.6037 + (rand(-100, 100) / 10000);
            $locationLong = $request->location_long ?? -0.1870 + (rand(-100, 100) / 10000);

            $listing = Listing::create([
                'landlord_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'deposit' => $request->deposit ?? 0,
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'area_sqft' => $request->area_sqft ?? 0,
                'property_type' => $request->property_type,
                'neighborhood' => $request->neighborhood,
                'location_address' => $locationAddress,
                'location_lat' => $locationLat,
                'location_long' => $locationLong,
                'furnished' => $request->furnished ? 1 : 0,
                'wifi' => $request->wifi ? 1 : 0,
                'parking' => $request->parking ? 1 : 0,
                'security' => $request->security ? 1 : 0,
                'pool' => $request->pool ? 1 : 0,
                'gym' => $request->gym ? 1 : 0,
                'verification_status' => 'approved',
                'is_available' => true,
            ]);

            // Handle photo uploads
            if ($request->hasFile('photos')) {
                $photos = $request->file('photos');

                foreach ($photos as $index => $photo) {
                    // Store photo in storage/app/listings
                    $path = $photo->store('listings/' . $listing->id, 'public');
                    $url = asset('storage/' . $path);

                    // Create photo record
                    \App\Models\Photo::create([
                        'listing_id' => $listing->id,
                        'photo_path' => $path,
                        'photo_url' => $url,
                        'is_primary' => $index === 0,
                        'order' => $index + 1,
                    ]);
                }
            }

            $listing->load(['photos', 'primaryPhoto', 'landlord']);

            return response()->json([
                'success' => true,
                'message' => 'Listing created successfully and is now visible to tenants!',
                'data' => new ListingResource($listing)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create listing.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update listing (owner only).
     */
    public function update(Request $request, $listingId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        try {
            $listing = Listing::findOrFail($listingId);

            if ($listing->landlord_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only update your own listings.'
                ], 403);
            }

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'bedrooms' => 'nullable|integer|min:1',
                'bathrooms' => 'nullable|integer|min:1',
                'is_available' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $listing->update($request->only([
                'title', 'description', 'price', 'bedrooms', 'bathrooms', 'is_available'
            ]));

            $listing->load(['photos', 'primaryPhoto', 'landlord']);

            return response()->json([
                'success' => true,
                'message' => 'Listing updated successfully.',
                'data' => new ListingResource($listing)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update listing.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete listing (owner only).
     */
    public function destroy($listingId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        try {
            $listing = Listing::findOrFail($listingId);

            if ($listing->landlord_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own listings.'
                ], 403);
            }

            $listing->delete();

            return response()->json([
                'success' => true,
                'message' => 'Listing deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete listing.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
