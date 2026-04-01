<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ListingController extends Controller
{
    /**
     * Show create listing form
     */
    public function create()
    {
        if (Auth::user()->role !== 'landlord') {
            return redirect('/dashboard/tenant')->with('error', 'Only landlords can create listings.');
        }

        return view('listings.create');
    }

    /**
     * Store listing with photos
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'landlord') {
            return redirect('/dashboard/tenant')->with('error', 'Only landlords can create listings.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:1',
            'area_sqft' => 'nullable|integer|min:0',
            'property_type' => 'required|in:apartment,house,studio,shared_room,bungalow',
            'category' => 'required|in:student_housing,luxury,commercial,family,budget,short_term',
            'neighborhood' => 'required|string|max:255',
            'furnished' => 'nullable|boolean',
            'wifi' => 'nullable|boolean',
            'parking' => 'nullable|boolean',
            'security' => 'nullable|boolean',
            'pool' => 'nullable|boolean',
            'gym' => 'nullable|boolean',
            'photos' => 'required|array|min:3|max:3',
            'photos.*' => 'required|file|mimes:jpeg,png,jpg,gif|max:5120',
            'location_lat' => 'nullable|numeric',
            'location_long' => 'nullable|numeric',
        ]);

        try {
            // Use provided location or generate default
            $locationLat = $request->location_lat ?? (5.6037 + (rand(-100, 100) / 10000));
            $locationLong = $request->location_long ?? (-0.1870 + (rand(-100, 100) / 10000));
            $locationAddress = $request->neighborhood . ', Accra, Ghana';

            // Create listing
            $listing = Listing::create([
                'landlord_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'deposit' => $validated['deposit'] ?? 0,
                'bedrooms' => $validated['bedrooms'],
                'bathrooms' => $validated['bathrooms'],
                'area_sqft' => $validated['area_sqft'] ?? 0,
                'property_type' => $validated['property_type'],
                'category' => $validated['category'],
                'neighborhood' => $validated['neighborhood'],
                'location_address' => $locationAddress,
                'location_lat' => $locationLat,
                'location_long' => $locationLong,
                'furnished' => $validated['furnished'] ?? false,
                'wifi' => $validated['wifi'] ?? false,
                'parking' => $validated['parking'] ?? false,
                'security' => $validated['security'] ?? false,
                'pool' => $validated['pool'] ?? false,
                'gym' => $validated['gym'] ?? false,
                'verification_status' => 'approved',
                'is_available' => true,
            ]);

            // Handle photo uploads
            if ($request->hasFile('photos')) {
                $photos = $request->file('photos');

                foreach ($photos as $index => $photo) {
                    try {
                        // Store photo in storage/app/public/listings/{listing_id}
                        $path = $photo->store('listings/' . $listing->id, 'public');
                        $url = asset('storage/' . $path);

                        // Create photo record
                        Photo::create([
                            'listing_id' => $listing->id,
                            'photo_path' => $path,
                            'photo_url' => $url,
                            'is_primary' => $index === 0,
                            'order' => $index + 1,
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Photo upload failed: ' . $e->getMessage());
                    }
                }
            }

            return redirect('/dashboard/landlord')->with('success', 'Property created successfully and is now visible to tenants!');

        } catch (\Exception $e) {
            \Log::error('Listing creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create listing: ' . $e->getMessage());
        }
    }

    /**
     * Edit listing
     */
    public function edit($id)
    {
        $listing = Listing::findOrFail($id);

        if ($listing->landlord_id !== Auth::id()) {
            return redirect('/dashboard/landlord')->with('error', 'Unauthorized.');
        }

        return view('listings.edit', compact('listing'));
    }

    /**
     * Update listing
     */
    public function update(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);

        if ($listing->landlord_id !== Auth::id()) {
            return redirect('/dashboard/landlord')->with('error', 'Unauthorized.');
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:1',
            'area_sqft' => 'nullable|integer|min:0',
            'property_type' => 'nullable|in:apartment,house,studio,shared_room,bungalow',
            'neighborhood' => 'nullable|string|max:255',
            'furnished' => 'nullable|boolean',
            'wifi' => 'nullable|boolean',
            'parking' => 'nullable|boolean',
            'security' => 'nullable|boolean',
            'pool' => 'nullable|boolean',
            'gym' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
        ]);

        try {
            $listing->update($validated);
            return redirect('/dashboard/landlord')->with('success', 'Property updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update listing.');
        }
    }

    /**
     * Delete listing
     */
    public function destroy($id)
    {
        $listing = Listing::findOrFail($id);

        if ($listing->landlord_id !== Auth::id()) {
            return redirect('/dashboard/landlord')->with('error', 'Unauthorized.');
        }

        try {
            // Delete photos
            foreach ($listing->photos as $photo) {
                \Storage::disk('public')->delete($photo->photo_path);
                $photo->delete();
            }

            $listing->delete();
            return redirect('/dashboard/landlord')->with('success', 'Property deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete listing.');
        }
    }
}
