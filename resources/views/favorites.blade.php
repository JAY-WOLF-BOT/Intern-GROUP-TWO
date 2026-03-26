@extends('layouts.app')

@section('title', 'My Favorites - Accra Housing')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">My Favorite Listings</h1>

        <div id="favoritesGrid" class="grid md:grid-cols-3 gap-6">
            <!-- Loaded dynamically -->
        </div>

        <div id="emptyState" style="display: none;" class="text-center py-12">
            <i class="fas fa-heart text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400 mb-4">You don't have any favorites yet</p>
            <a href="/listings" class="inline-block text-red-600 hover:text-red-700 font-medium">Browse listings</a>
        </div>
    </div>
</div>

<script>
async function loadFavorites() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    try {
        const response = await fetch('/api/v1/favorites/my-favorites', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();
        if (data.success && data.data.length > 0) {
            renderFavorites(data.data);
        } else {
            document.getElementById('emptyState').style.display = 'block';
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function renderFavorites(favorites) {
    document.getElementById('favoritesGrid').innerHTML = favorites.map(fav => `
        <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition transform hover:scale-105">
            <div class="relative h-48 bg-gray-300 dark:bg-gray-700">
                ${fav.listing.photos && fav.listing.photos.length > 0
                    ? `<img src="${fav.listing.photos[0].url}" class="w-full h-full object-cover">`
                    : `<div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image text-4xl"></i></div>`
                }
                <div class="absolute top-2 right-2 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">GHS ${fav.listing.price}</div>
                <button onclick="removeFavorite(${fav.id})" class="absolute top-2 left-2 bg-red-600 hover:bg-red-700 text-white w-10 h-10 rounded-full flex items-center justify-center transition">
                    <i class="fas fa-heart"></i>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">${fav.listing.title}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">${fav.listing.neighborhood || 'Accra'}</p>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">${fav.listing.description?.substring(0, 60)}...</p>
                <a href="/listings/${fav.listing.id}" class="block w-full bg-red-600 hover:bg-red-700 text-white text-center py-2 rounded-lg font-medium transition">
                    View Details
                </a>
            </div>
        </div>
    `).join('');
}

async function removeFavorite(id) {
    const token = localStorage.getItem('auth_token');
    try {
        const response = await fetch(`/api/v1/favorites/${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();
        if (data.success) {
            loadFavorites();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

window.addEventListener('DOMContentLoaded', loadFavorites);
</script>
@endsection
