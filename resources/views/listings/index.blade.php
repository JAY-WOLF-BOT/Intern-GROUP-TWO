@extends('layouts.app')

@section('title', 'Browse Listings - Accra Housing')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-red-600 to-red-700 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold mb-4">Find Your Perfect Home</h1>
            <p class="text-lg text-red-100">Affordable housing and rooms across Accra</p>
        </div>
    </div>

    <!-- Search Bar Section -->
    <div class="bg-white dark:bg-[#1D1D1A] sticky top-16 z-40 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <form id="searchForm" class="space-y-4">
                <div class="grid md:grid-cols-5 gap-4">
                    <!-- Budget Min -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Min Budget (GHS)</label>
                        <input type="number" name="budget_min" id="budget_min" placeholder="100"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
                    </div>

                    <!-- Budget Max -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Budget (GHS)</label>
                        <input type="number" name="budget_max" id="budget_max" placeholder="5000"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
                    </div>

                    <!-- Bedrooms -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bedrooms</label>
                        <select name="bedrooms" id="bedrooms"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
                            <option value="">Any</option>
                            <option value="1">1 Bedroom</option>
                            <option value="2">2 Bedrooms</option>
                            <option value="3">3 Bedrooms</option>
                            <option value="4">4+ Bedrooms</option>
                        </select>
                    </div>

                    <!-- Property Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property Type</label>
                        <select name="property_type" id="property_type"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
                            <option value="">All Types</option>
                            <option value="apartment">Apartment</option>
                            <option value="house">House</option>
                            <option value="studio">Studio</option>
                            <option value="shared_room">Shared Room</option>
                            <option value="bungalow">Bungalow</option>
                        </select>
                    </div>

                    <!-- Neighborhood -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Neighborhood</label>
                        <input type="text" name="neighborhood" id="neighborhood" placeholder="e.g., Osu"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="searchListings()" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition">
                        <i class="fas fa-search mr-2"></i> Search
                    </button>
                    <button type="button" onclick="resetFilters()" class="px-6 bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-bold py-2 rounded-lg transition">
                        Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div id="listingsContainer" class="grid md:grid-cols-3 gap-6">
            <!-- Loading Skeleton -->
            @for ($i = 0; $i < 6; $i++)
                <div class="bg-gray-200 dark:bg-gray-800 rounded-xl h-80 animate-pulse"></div>
            @endfor
        </div>
    </div>
</div>

<script>
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

async function searchListings() {
    const formData = new FormData(document.getElementById('searchForm'));
    const params = new URLSearchParams(formData);

    try {
        const response = await fetch(`/api/v1/listings?${params}`);
        const data = await response.json();

        if (data.success) {
            renderListings(data.data);
        }
    } catch (error) {
        console.error('Search error:', error);
    }
}

function renderListings(listings) {
    const container = document.getElementById('listingsContainer');

    if (listings.length === 0) {
        container.innerHTML = '<div class="col-span-3 text-center py-12"><p class="text-gray-600 dark:text-gray-400">No listings found. Try adjusting your filters.</p></div>';
        return;
    }

    container.innerHTML = listings.map(listing => `
        <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition transform hover:scale-105">
            <!-- Image -->
            <div class="relative h-48 bg-gray-300 dark:bg-gray-700">
                ${listing.photos && listing.photos.length > 0
                    ? `<img src="${listing.photos[0].url}" alt="${listing.title}" class="w-full h-full object-cover">`
                    : `<div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image text-4xl"></i></div>`
                }
                <div class="absolute top-2 right-2 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">GHS ${listing.price}</div>
                ${listing.id ? `
                    <button onclick="toggleFavorite(${listing.id})" class="absolute top-2 left-2 bg-white hover:bg-gray-100 text-red-600 w-10 h-10 rounded-full flex items-center justify-center transition">
                        <i class="fas fa-heart"></i>
                    </button>
                ` : ''}
            </div>

            <!-- Info -->
            <div class="p-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">${listing.title}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">${listing.description?.substring(0, 80)}...</p>

                <!-- Details -->
                <div class="grid grid-cols-3 gap-2 mb-4 text-sm">
                    <div class="text-center">
                        <i class="fas fa-bed text-red-600 mb-1"></i>
                        <p class="text-gray-700 dark:text-gray-300">${listing.bedrooms} <span class="text-xs">Beds</span></p>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-bath text-red-600 mb-1"></i>
                        <p class="text-gray-700 dark:text-gray-300">${listing.bathrooms || 1} <span class="text-xs">Bath</span></p>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-expand text-red-600 mb-1"></i>
                        <p class="text-gray-700 dark:text-gray-300">${listing.area_sqft || 'N/A'} <span class="text-xs">sqft</span></p>
                    </div>
                </div>

                <!-- Neighborhood -->
                <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>${listing.neighborhood || 'Accra'}
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <a href="/listings/${listing.id}" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-center py-2 rounded-lg transition font-medium">
                        View Details
                    </a>
                    <a href="https://wa.me/${listing.landlord?.phone_number || '233'}" target="_blank"
                        class="flex-1 bg-green-500 hover:bg-green-600 text-white text-center py-2 rounded-lg transition font-medium">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>
    `).join('');
}

function resetFilters() {
    document.getElementById('searchForm').reset();
    searchListings();
}

async function toggleFavorite(listingId) {
    const csrfToken = getCsrfToken();

    try {
        const response = await fetch(`/api/v1/favorites/add`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ listing_id: listingId })
        });
        const data = await response.json();
        if (data.success) {
            alert('Added to favorites!');
        } else if (response.status === 401) {
            alert('Please login to add favorites');
            window.location.href = '/login';
        } else {
            alert('Error: ' + (data.message || 'Failed to add favorite'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred');
    }
}

// Load listings on page load
window.addEventListener('DOMContentLoaded', searchListings);
</script>
@endsection
