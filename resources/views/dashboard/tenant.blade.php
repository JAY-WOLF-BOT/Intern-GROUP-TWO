@extends('layouts.app')

@section('title', 'Tenant Dashboard - Accra Housing')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center gap-4">
                <div id="profilePictureContainer">
                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-2xl text-gray-400"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Welcome Back!</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Find your perfect home in Accra</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Quick Stats -->
        <div class="grid md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">My Favorites</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white" id="favCount">0</p>
                    </div>
                    <i class="fas fa-heart text-4xl text-red-100"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Payments Made</p>
                        <p class="text-3xl font-bold text-blue-600" id="paymentCount">0</p>
                    </div>
                    <i class="fas fa-credit-card text-4xl text-blue-100"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Total Spent</p>
                        <p class="text-3xl font-bold text-green-600" id="totalSpent">GHS 0</p>
                    </div>
                    <i class="fas fa-wallet text-4xl text-green-100"></i>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="flex border-b border-gray-200 dark:border-gray-700">
                <button onclick="switchTab('favorites')" class="tab-btn px-6 py-4 font-medium text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:border-red-500 transition active" data-tab="favorites">
                    <i class="fas fa-heart mr-2"></i> My Favorites
                </button>
                <button onclick="switchTab('payments')" class="tab-btn px-6 py-4 font-medium text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:border-red-500 transition" data-tab="payments">
                    <i class="fas fa-receipt mr-2"></i> Payment History
                </button>
            </div>

            <!-- Favorites Tab -->
            <div id="favorites-tab" class="tab-content p-6">
                <div id="favoritesContainer" class="grid md:grid-cols-3 gap-6">
                    <!-- Loaded dynamically -->
                </div>
                <div id="noFavorites" style="display: none;" class="text-center py-12">
                    <i class="fas fa-heart text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">No favorites yet. Start exploring listings!</p>
                    <a href="/listings" class="mt-4 inline-block text-red-600 hover:text-red-700 font-medium">Browse Listings</a>
                </div>
            </div>

            <!-- Payments Tab -->
            <div id="payments-tab" class="tab-content p-6" style="display: none;">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300">Property</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300">Status</th>
                            </tr>
                        </thead>
                        <tbody id="paymentsList" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>
                <div id="noPayments" style="display: none;" class="text-center py-12">
                    <i class="fas fa-receipt text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">No payment history yet.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function switchTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');

    // Remove active class from buttons
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('active');
        el.classList.remove('border-red-600');
        el.classList.add('border-transparent');
    });

    // Show selected tab
    document.getElementById(tab + '-tab').style.display = 'block';

    // Add active class to button
    document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
    document.querySelector(`[data-tab="${tab}"]`).classList.add('border-red-600');
    document.querySelector(`[data-tab="${tab}"]`).classList.remove('border-transparent');
}

async function loadDashboard() {
    const csrfToken = getCsrfToken();

    try {
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

        // Load favorites
        const favResponse = await fetch('/api/v1/favorites/my-favorites', {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        const favData = await favResponse.json();
        if (favData.success) {
            renderFavorites(favData.data);
        }

        // Load payments
        const payResponse = await fetch('/api/v1/payments/history', {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        const payData = await payResponse.json();
        if (payData.success) {
            renderPayments(payData.data);
        }
    } catch (error) {
        console.error('Error loading dashboard:', error);
    }
}

function renderFavorites(favorites) {
    document.getElementById('favCount').textContent = favorites.length;

    if (favorites.length === 0) {
        document.getElementById('noFavorites').style.display = 'block';
        document.getElementById('favoritesContainer').innerHTML = '';
        return;
    }

    document.getElementById('favoritesContainer').innerHTML = favorites.map(fav => `
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
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">${fav.listing.neighborhood || 'Accra'}</p>
                <a href="/listings/${fav.listing.id}" class="block w-full bg-red-600 hover:bg-red-700 text-white text-center py-2 rounded-lg font-medium transition">
                    View Details
                </a>
            </div>
        </div>
    `).join('');
}

function renderPayments(payments) {
    document.getElementById('paymentCount').textContent = payments.length;

    let totalSpent = 0;
    payments.forEach(p => {
        totalSpent += parseFloat(p.amount) || 0;
    });
    document.getElementById('totalSpent').textContent = `GHS ${totalSpent.toFixed(2)}`;

    if (payments.length === 0) {
        document.getElementById('noPayments').style.display = 'block';
        document.getElementById('paymentsList').innerHTML = '';
        return;
    }

    document.getElementById('paymentsList').innerHTML = payments.map(payment => `
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <td class="px-4 py-3 text-gray-900 dark:text-white">${new Date(payment.created_at).toLocaleDateString()}</td>
            <td class="px-4 py-3 text-gray-900 dark:text-white">${payment.listing?.title}</td>
            <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">GHS ${parseFloat(payment.amount).toFixed(2)}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 capitalize">${payment.payment_type}</td>
            <td class="px-4 py-3">
                <span class="px-3 py-1 rounded-full text-xs font-bold ${
                    payment.status === 'completed' ? 'bg-green-100 text-green-800' :
                    payment.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800'
                }">
                    ${payment.status?.charAt(0).toUpperCase() + payment.status?.slice(1)}
                </span>
            </td>
        </tr>
    `).join('');
}

async function removeFavorite(id) {
    const csrfToken = getCsrfToken();
    try {
        const response = await fetch(`/api/v1/favorites/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) {
            alert('Removed from favorites');
            loadDashboard();
        } else {
            alert('Error: ' + (data.message || 'Failed to remove favorite'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred');
    }
}

window.addEventListener('DOMContentLoaded', loadDashboard);
</script>
@endsection
