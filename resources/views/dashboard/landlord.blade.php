@extends('layouts.app')

@section('title', 'Landlord Dashboard - Accra Housing')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div id="profilePictureContainer">
                        <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-2xl text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Property Management</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your listings and inquiries</p>
                    </div>
                </div>
                <a href="/listings/create" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i> New Listing
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Stats -->
        <div class="grid md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Total Listings</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white" id="totalListings">0</p>
                    </div>
                    <i class="fas fa-building text-4xl text-red-100"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Approved</p>
                        <p class="text-3xl font-bold text-green-600" id="approvedListings">0</p>
                    </div>
                    <i class="fas fa-check-circle text-4xl text-green-100"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Pending Review</p>
                        <p class="text-3xl font-bold text-yellow-600" id="pendingListings">0</p>
                    </div>
                    <i class="fas fa-clock text-4xl text-yellow-100"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Total Views</p>
                        <p class="text-3xl font-bold text-blue-600" id="totalViews">0</p>
                    </div>
                    <i class="fas fa-eye text-4xl text-blue-100"></i>
                </div>
            </div>
        </div>

        <!-- Listings Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Your Listings</h2>
            </div>

            <div id="listingsContainer" class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Views</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="listingsBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Loaded dynamically -->
                    </tbody>
                </table>
            </div>

            <div id="emptyState" style="display: none;" class="text-center py-12">
                <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">You haven't created any listings yet.</p>
                <a href="/listings/create" class="mt-4 inline-block text-red-600 hover:text-red-700 font-medium">Create your first listing</a>
            </div>
        </div>
    </div>
</div>

<script>
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

async function loadDashboard() {
    try {
        const csrfToken = getCsrfToken();

        // Load profile picture
        const userResponse = await fetch('/api/v1/user/profile', {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        const userData = await userResponse.json();
        if (userData.success && userData.data.profile_picture) {
            const container = document.getElementById('profilePictureContainer');
            container.innerHTML = `<img src="${userData.data.profile_picture}" alt="Profile" class="w-16 h-16 rounded-full object-cover border-2 border-red-500">`;
        }

        // Load listings
        const response = await fetch('/api/v1/listings/landlord/my-listings', {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        if (data.success) {
            renderDashboard(data.data);
        } else {
            console.error('Failed to load listings:', data.message);
        }
    } catch (error) {
        console.error('Error loading dashboard:', error);
    }
}

function renderDashboard(listings) {
    // Update stats
    document.getElementById('totalListings').textContent = listings.length;
    document.getElementById('approvedListings').textContent = listings.filter(l => l.status === 'approved').length;
    document.getElementById('pendingListings').textContent = listings.filter(l => l.status === 'pending').length;
    document.getElementById('totalViews').textContent = listings.reduce((sum, l) => sum + (l.view_count || 0), 0);

    // Render table
    const tbody = document.getElementById('listingsBody');
    if (listings.length === 0) {
        document.getElementById('emptyState').style.display = 'block';
        tbody.innerHTML = '';
        return;
    }

    tbody.innerHTML = listings.map(listing => `
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    ${listing.photos && listing.photos[0]
                        ? `<img src="${listing.photos[0].url}" alt="${listing.title}" class="w-12 h-12 rounded object-cover">`
                        : `<div class="w-12 h-12 rounded bg-gray-300 flex items-center justify-center"><i class="fas fa-home text-gray-600"></i></div>`
                    }
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">${listing.title}</p>
                        <p class="text-sm text-gray-500">${listing.neighborhood || 'Accra'}</p>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">GHS ${listing.price?.toLocaleString()}</td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-full text-xs font-bold ${
                    listing.status === 'approved' ? 'bg-green-100 text-green-800' :
                    listing.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800'
                }">
                    ${listing.status?.charAt(0).toUpperCase() + listing.status?.slice(1) || 'Draft'}
                </span>
            </td>
            <td class="px-6 py-4 text-gray-900 dark:text-white">
                <i class="fas fa-eye mr-2"></i>${listing.view_count || 0}
            </td>
            <td class="px-6 py-4 text-sm space-x-2">
                <a href="/listings/${listing.id}" target="_blank" class="text-blue-600 hover:text-blue-700 font-medium">View</a>
                <a href="/listings/${listing.id}/edit" class="text-orange-600 hover:text-orange-700 font-medium">Edit</a>
                <button onclick="deleteListing(${listing.id})" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
            </td>
        </tr>
    `).join('');
}

async function deleteListing(id) {
    if (!confirm('Are you sure you want to delete this listing?')) return;

    const csrfToken = getCsrfToken();
    try {
        const response = await fetch(`/api/v1/listings/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        if (data.success) {
            alert('Listing deleted successfully');
            loadDashboard();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete listing'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while deleting the listing');
    }
}

window.addEventListener('DOMContentLoaded', loadDashboard);
</script>
@endsection
