<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    /**
     * Search listings with advanced filters.
     */
    public function searchListings(Request $request): JsonResponse
    {
        $query = Listing::with(['landlord', 'primaryPhoto', 'photos'])
            ->verified()
            ->available();

        // Budget filters
        if ($request->has('budget_min') && is_numeric($request->budget_min)) {
            $query->where('price', '>=', $request->budget_min);
        }

        if ($request->has('budget_max') && is_numeric($request->budget_max)) {
            $query->where('price', '<=', $request->budget_max);
        }

        // Neighborhood filter
        if ($request->has('neighborhood') && !empty($request->neighborhood)) {
            $query->byNeighborhood($request->neighborhood);
        }

        // Bedrooms filter
        if ($request->has('bedrooms') && is_numeric($request->bedrooms)) {
            $query->byBedrooms($request->bedrooms);
        }

        // Property type filter
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

        // Add WhatsApp link to each listing
        $listings->getCollection()->transform(function ($listing) {
            $listing->whatsapp_link = $listing->whats_app_link;
            return $listing;
        });

        return response()->json([
            'success' => true,
            'data' => $listings,
            'filters_applied' => [
                'budget_min' => $request->budget_min,
                'budget_max' => $request->budget_max,
                'neighborhood' => $request->neighborhood,
                'bedrooms' => $request->bedrooms,
                'property_type' => $request->property_type,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ]
        ]);
    }
}
