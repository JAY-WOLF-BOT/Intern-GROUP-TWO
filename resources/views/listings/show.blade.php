@extends('layouts.app')

@section('title', 'Listing Details - Accra Housing')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div id="loadingState" class="grid md:grid-cols-3 gap-8">
            <div class="md:col-span-2 bg-gray-200 dark:bg-gray-800 rounded-xl h-96 animate-pulse"></div>
            <div class="bg-gray-200 dark:bg-gray-800 rounded-xl h-96 animate-pulse"></div>
        </div>

        <div id="listingContent" style="display: none;">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Left: Images & Details -->
                <div class="md:col-span-2">
                    <!-- Image Gallery -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg mb-6">
                        <div id="mainImage" class="w-full h-96 bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                            <i class="fas fa-image text-4xl text-gray-400"></i>
                        </div>

                        <!-- Thumbnails -->
                        <div id="thumbnails" class="grid grid-cols-4 gap-2 p-4"></div>
                    </div>

                    <!-- Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg mb-6">
                        <h2 class="text-2xl font-bold mb-2 text-gray-900 dark:text-white" id="listingTitle"></h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-6" id="listingDesc"></p>

                        <!-- Amenities -->
                        <div class="grid md:grid-cols-3 gap-4 py-6 border-t border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <i class="fas fa-bed text-red-600 text-xl mb-2"></i>
                                <p class="font-medium text-gray-900 dark:text-white" id="bedrooms"></p>
                            </div>
                            <div>
                                <i class="fas fa-bath text-red-600 text-xl mb-2"></i>
                                <p class="font-medium text-gray-900 dark:text-white" id="bathrooms"></p>
                            </div>
                            <div>
                                <i class="fas fa-expand text-red-600 text-xl mb-2"></i>
                                <p class="font-medium text-gray-900 dark:text-white" id="area"></p>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="my-6">
                            <h3 class="text-lg font-bold mb-3 text-gray-900 dark:text-white">Features</h3>
                            <ul id="features" class="space-y-2"></ul>
                        </div>

                        <!-- Description -->
                        <div class="my-6">
                            <h3 class="text-lg font-bold mb-3 text-gray-900 dark:text-white">About This Listing</h3>
                            <p id="fullDesc" class="text-gray-700 dark:text-gray-300"></p>
                        </div>
                    </div>
                </div>

                <!-- Right: Price & Contact -->
                <div>
                    <!-- Price Card -->
                    <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-xl p-6 text-white shadow-lg mb-6">
                        <p class="text-red-100 mb-1">Monthly Rent</p>
                        <h2 class="text-4xl font-bold mb-4" id="price">GHS 0</h2>

                        <!-- Landlord Info -->
                        <div class="bg-white/20 rounded-lg p-4 mb-4">
                            <p class="text-sm text-red-100 mb-2">Listed by</p>
                            <p class="font-bold" id="landlordName"></p>
                            <p class="text-sm text-red-100" id="landlordPhone"></p>
                        </div>

                        <!-- Contact Buttons -->
                        <div class="space-y-3">
                            <a id="whatsappBtn" href="#" target="_blank"
                                class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-3 rounded-lg font-bold transition">
                                <i class="fab fa-whatsapp mr-2"></i> Contact on WhatsApp
                            </a>
                            <button onclick="initiatePayment()"
                                class="w-full bg-white text-red-600 hover:bg-gray-100 font-bold py-3 rounded-lg transition">
                                <i class="fas fa-credit-card mr-2"></i> Pay Viewing Fee
                            </button>
                        </div>
                    </div>

                    <!-- Favorite Button -->
                    <button onclick="toggleFavorite()"
                        class="w-full mb-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 font-bold py-3 rounded-lg transition">
                        <i id="favoriteIcon" class="fas fa-heart mr-2"></i> <span id="favoriteText">Add to Favorites</span>
                    </button>

                    <!-- Location Map -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-lg">
                        <h3 class="font-bold mb-3 text-gray-900 dark:text-white">Location</h3>
                        <p id="neighborhood" class="text-gray-700 dark:text-gray-300 mb-4">
                            <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                        </p>
                        <div class="w-full h-40 bg-gray-300 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400">
                            <i class="fas fa-map text-3xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" style="display: none;" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-8 max-w-md w-full">
        <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Pay Viewing Fee</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Pay a small fee to verify your interest in this property.</p>

        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Viewing Fee</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">GHS 25.00</p>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
            <select id="paymentMethod" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                <option value="mtn">MTN Mobile Money</option>
                <option value="vodafone">Vodafone Cash</option>
                <option value="airteltigo">AirtelTigo Money</option>
            </select>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
            <input type="tel" id="phoneNumber" placeholder="+233501234567"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
        </div>

        <div class="flex gap-3">
            <button onclick="closePaymentModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-bold py-2 rounded-lg transition">
                Cancel
            </button>
            <button onclick="processPayment()" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition">
                Pay Now
            </button>
        </div>
    </div>
</div>

<script>
let currentListing = null;
let listingId = new URLSearchParams(window.location.search).get('id') || window.location.pathname.split('/').pop();

async function loadListing() {
    try {
        const response = await fetch(`/api/v1/listings/${listingId}`);
        const data = await response.json();

        if (data.success) {
            currentListing = data.data;
            renderListing(currentListing);
        }
    } catch (error) {
        console.error('Error loading listing:', error);
    }
}

function renderListing(listing) {
    document.getElementById('listingTitle').textContent = listing.title;
    document.getElementById('listingDesc').textContent = listing.description?.substring(0, 150) + '...';
    document.getElementById('fullDesc').textContent = listing.description;
    document.getElementById('price').textContent = `GHS ${listing.price?.toLocaleString()}`;
    document.getElementById('bedrooms').textContent = `${listing.bedrooms} Bedrooms`;
    document.getElementById('bathrooms').textContent = `${listing.bathrooms || 1} Bathrooms`;
    document.getElementById('area').textContent = `${listing.area_sqft || 'N/A'} sqft`;
    document.getElementById('neighborhood').textContent = listing.neighborhood || 'Accra';

    // Landlord Info
    if (listing.landlord) {
        document.getElementById('landlordName').textContent = listing.landlord.name;
        document.getElementById('landlordPhone').textContent = listing.landlord.phone_number;
    }

    // WhatsApp Link
    if (listing.whatsapp_link) {
        document.getElementById('whatsappBtn').href = listing.whatsapp_link;
    }

    // Photos
    if (listing.photos && listing.photos.length > 0) {
        document.getElementById('mainImage').innerHTML = `<img src="${listing.photos[0].url}" class="w-full h-full object-cover">`;

        document.getElementById('thumbnails').innerHTML = listing.photos.map((photo, idx) => `
            <img src="${photo.url}" onclick="changeImage('${photo.url}')"
                class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75 transition">
        `).join('');
    }

    // Features
    const features = [
        listing.furnished ? '✓ Furnished' : '',
        listing.wifi ? '✓ Free WiFi' : '',
        listing.parking ? '✓ Parking' : '',
        listing.security ? '✓ Security' : '',
        listing.pool ? '✓ Pool' : ''
    ].filter(f => f);

    document.getElementById('features').innerHTML = features.map(f =>
        `<li class="text-gray-700 dark:text-gray-300">${f}</li>`
    ).join('');

    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('listingContent').style.display = 'block';
}

function changeImage(src) {
    document.getElementById('mainImage').innerHTML = `<img src="${src}" class="w-full h-full object-cover">`;
}

function initiatePayment() {
    document.getElementById('paymentModal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}

async function processPayment() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        alert('Please login to make payments');
        window.location.href = '/login';
        return;
    }

    const phoneNumber = document.getElementById('phoneNumber').value;
    const method = document.getElementById('paymentMethod').value;

    if (!phoneNumber) {
        alert('Please enter your phone number');
        return;
    }

    try {
        const response = await fetch(`/api/v1/payments/initiate`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                listing_id: currentListing.id,
                amount: 25.00,
                payment_type: 'viewing_fee',
                network: method,
                phone_number: phoneNumber
            })
        });

        const data = await response.json();
        if (data.success) {
            alert('Payment initiated! Confirm the prompt on your phone.');
            closePaymentModal();
        } else {
            alert('Payment error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Payment error. Please try again.');
    }
}

async function toggleFavorite() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        alert('Please login to add favorites');
        window.location.href = '/login';
        return;
    }

    try {
        const response = await fetch(`/api/v1/favorites/add`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ listing_id: currentListing.id })
        });

        const data = await response.json();
        if (data.success) {
            document.getElementById('favoriteIcon').classList.add('text-red-600');
            document.getElementById('favoriteText').textContent = 'Added to Favorites';
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

window.addEventListener('DOMContentLoaded', loadListing);
</script>
@endsection
