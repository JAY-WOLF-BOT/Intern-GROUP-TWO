@extends('layouts.app')

@section('title', 'Landlord Dashboard - Accra Housing')

@section('navbar')
    <!-- Top Navbar -->
    <nav class="bg-red-600 h-16 sticky top-0 z-50 md:ml-64 shadow-lg flex items-center justify-between px-6">
        <div class="flex items-center space-x-2 text-white">
            <a href="{{ url('/') }}" class="flex items-center space-x-2 text-white hover:opacity-80 transition">
                <i class="fas fa-building text-red-200 text-xl"></i>
                <span class="font-bold text-lg">Accra Housing - Landlord</span>
            </a>
        </div>

        <div class="flex items-center space-x-4">
            <div class="relative group">
                <button class="flex items-center space-x-3 text-white hover:text-red-100 focus:outline-none transition">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold leading-none">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-red-200">Landlord</p>
                    </div>
                    
                    <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center border-2 border-red-400">
                        <span class="text-red-600 font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-red-200"></i>
                </button>

                <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-gray-100">
                    <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600">
                        <i class="fas fa-user-circle mr-3"></i> My Profile
                    </a>
                    
                    <hr class="my-1 border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">
                            <i class="fas fa-sign-out-alt mr-3"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
@endsection

@section('content')
<style>
    :root {
        --primary: #dc2626;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeInUp 0.6s ease-out; }
    .progress-bar { width: 0%; transition: width 1.5s ease-out; }
    .progress-bar.animate { width: var(--width); }
</style>

    <!-- Page Wrapper: flex column, min height screen, allows footer to naturally flow -->
    <div class="flex flex-col min-h-screen bg-gray-50">
        <!-- Sidebar Container (fixed) -->
        <div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-red-50 to-white shadow-lg z-50 flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-300">
            <!-- Logo/Brand -->
            <div class="flex items-center justify-center h-16 bg-gradient-to-r from-red-600 to-red-700 text-white">
                <i class="fas fa-crown mr-2"></i>
                <h1 class="text-xl font-bold">Landlord Portal</h1>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="/dashboard/landlord" class="flex items-center px-4 py-3 text-gray-700 bg-gray-200 rounded-lg transition-all duration-300 hover:bg-gray-300 hover:translate-x-1">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
                <a href="/listings" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 rounded-lg transition-all duration-300 hover:translate-x-1">
                    <i class="fas fa-building mr-3"></i>
                    Properties
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 rounded-lg transition-all duration-300 hover:translate-x-1">
                    <i class="fas fa-envelope mr-3"></i>
                    Messages
                </a>
                <a href="/listings/create" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 rounded-lg transition-all duration-300 hover:translate-x-1">
                    <i class="fas fa-plus-circle mr-3"></i>
                    Listing
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 rounded-lg transition-all duration-300 hover:translate-x-1">
                    <i class="fas fa-cog mr-3"></i>
                    Settings
                </a>
            </nav>

            <!-- User Info -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center">
                    <img src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'Guest') . '&background=0D8ABC&color=fff' }}" alt="Profile" class="w-10 h-10 rounded-full mr-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'Guest' }}</p>
                        <p class="text-xs text-gray-500">Landlord</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area (flex-1 to grow and push footer down) -->
        <div class="flex-1 ml-0 md:ml-64 animate-fade-in pt-24 md:pt-20 lg:pt-10">
            <div class="max-w-7xl mx-auto p-4 md:p-8">
                <!-- Header -->
                <div class="md:hidden mb-4">
                    <button id="sidebarToggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                        <i class="fas fa-bars"></i> Menu
                    </button>
                </div>
                <header class="bg-white shadow-sm border-b border-gray-200 mb-6">
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Good Morning, {{ auth()->user()->name }}</h1>
                            <p class="text-gray-600">Welcome back to your dashboard</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button class="p-2 text-gray-600 hover:text-gray-900">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="p-2 text-gray-600 hover:text-gray-900">
                                <i class="fas fa-bell"></i>
                            </button>
                            <img src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'Guest') . '&background=0D8ABC&color=fff' }}" alt="Profile" class="w-10 h-10 rounded-full">
                        </div>
                    </div>
                </header>

                <!-- Content -->
                <main class="space-y-6">
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg hover:-translate-y-1 transition-transform">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                                    <p class="text-2xl font-bold text-gray-900">₵45,231</p>
                                    <p class="text-sm text-green-600">+12.5%</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-dollar-sign text-green-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg hover:-translate-y-1 transition-transform">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Maintenance Cost</p>
                                    <p id="maintenanceCost" class="text-2xl font-bold text-gray-900">₵8,450</p>
                                    <p class="text-sm text-red-600">-3.2%</p>
                                </div>
                                <div class="p-3 bg-red-100 rounded-full">
                                    <i class="fas fa-wrench text-red-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg hover:-translate-y-1 transition-transform">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total Listings</p>
                                    <p id="totalListings" class="text-2xl font-bold text-gray-900">24</p>
                                    <p class="text-sm text-blue-600">+8.1%</p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-building text-blue-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg hover:-translate-y-1 transition-transform">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Views</p>
                                    <p id="totalViews" class="text-2xl font-bold text-gray-900">1,429</p>
                                    <p class="text-sm text-purple-600">+15.3%</p>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-eye text-purple-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Reports -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Sales Report Progress Bars -->
                        <div class="bg-white rounded-2xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Monthly Revenue</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>January</span>
                                        <span>₵12,345</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="progress-bar bg-green-500 h-2 rounded-full" style="--width: 65%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>February</span>
                                        <span>₵15,678</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="progress-bar bg-green-500 h-2 rounded-full" style="--width: 82%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>March</span>
                                        <span>₵18,901</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="progress-bar bg-green-500 h-2 rounded-full" style="--width: 98%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Revenue Chart -->
                        <div class="bg-white rounded-2xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Total Revenue</h3>
                            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                                <div class="text-center">
                                    <i class="fas fa-chart-line text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-500">Revenue Chart Placeholder</p>
                                    <p class="text-sm text-gray-400">Line chart will be implemented here</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Property List -->
                    <div class="bg-white rounded-2xl shadow p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Your Listings</h3>
                        <div id="emptyState" class="text-center text-gray-500 py-10 hidden">
                            <p>No listings found. Add your first property to get started.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white text-sm" aria-label="Landlord listings">
                                <thead class="border-b bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Property</th>
                                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Rent</th>
                                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Status</th>
                                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Views</th>
                                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="listingsBody">
                                    <tr class="border-b">
                                        <td class="px-6 py-4 text-gray-700" colspan="5">Loading listings...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Footer -->
        <footer class="ml-0 md:ml-64 mt-auto bg-gradient-to-r from-slate-800 to-slate-900 text-white border-t-2 border-red-600">
            <div class="max-w-7xl px-8 py-6">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-crown text-red-600 text-lg"></i>
                        <span class="font-bold text-lg">Landlord Mode</span>
                    </div>
                    <span class="text-xs bg-red-600 text-white px-3 py-1 rounded-full">Dashboard</span>
                </div>
                
                <div class="grid grid-cols-3 gap-6 mb-4">
                    <div>
                        <h4 class="font-semibold text-sm mb-2 text-red-400">Properties</h4>
                        <ul class="text-gray-300 text-sm space-y-1">
                            <li><a href="/listings" class="hover:text-white transition">My Listings</a></li>
                            <li><a href="/listings/create" class="hover:text-white transition">Add Property</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm mb-2 text-red-400">Account</h4>
                        <ul class="text-gray-300 text-sm space-y-1">
                            <li><a href="/profile" class="hover:text-white transition">Profile</a></li>
                            <li><a href="/settings" class="hover:text-white transition">Settings</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm mb-2 text-red-400">Support</h4>
                        <ul class="text-gray-300 text-sm space-y-1">
                            <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                            <li><a href="#" class="hover:text-white transition">Contact</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-slate-700 pt-4 text-center text-gray-400 text-xs">
                    <p>&copy; 2026 Accra Housing - Landlord Dashboard. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    @if ($errors->any())
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <strong>Error!</strong> {{ $errors->first() }}
        </div>
    @endif

    @if (session('success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    <script>
        function getCsrfToken() {
            const tokenElement = document.querySelector('meta[name="csrf-token"]');
            return tokenElement ? tokenElement.getAttribute('content') : '';
        }

        function updateStats({ totalListings = 0, approvedListings = 0, pendingListings = 0, totalViews = 0 }) {
            const totalElem = document.getElementById('totalListings');
            if (totalElem) totalElem.textContent = totalListings;

            const viewsElem = document.getElementById('totalViews');
            if (viewsElem) viewsElem.textContent = totalViews.toLocaleString();
        }

        function renderDashboard(listings) {
            updateStats({
                totalListings: listings.length,
                totalViews: listings.reduce((acc, l) => acc + (l.view_count || 0), 0)
            });

            const tbody = document.getElementById('listingsBody');
            const emptyState = document.getElementById('emptyState');

            if (!tbody) return;

            if (!listings.length) {
                tbody.innerHTML = '';
                if (emptyState) emptyState.classList.remove('hidden');
                return;
            }

            if (emptyState) emptyState.classList.add('hidden');

            tbody.innerHTML = listings.map(listing => `
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            ${listing.photos?.length ? `<img src="${listing.photos[0].url}" alt="${listing.title}" class="w-12 h-12 rounded object-cover">` : `<div class="w-12 h-12 rounded bg-gray-200 flex items-center justify-center"><i class="fas fa-home text-gray-600"></i></div>`}
                            <div>
                                <p class="font-medium text-gray-900">${listing.title || 'Untitled'}</p>
                                <p class="text-sm text-gray-500">${listing.neighborhood || 'Accra'}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-900 font-medium">GHS ${listing.price?.toLocaleString() || '0'}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold ${
                            listing.status === 'approved' ? 'bg-green-100 text-green-800' :
                            listing.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                            'bg-red-100 text-red-800'}">
                            ${listing.status?.charAt(0).toUpperCase() + (listing.status?.slice(1) || 'Draft')}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-900">${(listing.view_count || 0).toLocaleString()}</td>
                    <td class="px-6 py-4 text-sm space-x-2">
                        <a href="/listings/${listing.id}" target="_blank" class="text-blue-600 hover:text-blue-700 font-medium">View</a>
                        <a href="/listings/${listing.id}/edit" class="text-orange-600 hover:text-orange-700 font-medium">Edit</a>
                        <button onclick="deleteListing(${listing.id})" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                    </td>
                </tr>
            `).join('');
        }

        async function loadDashboard() {
            try {
                const csrfToken = getCsrfToken();

                const response = await fetch('/api/v1/listings/landlord/my-listings', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success && data.data) {
                    renderDashboard(data.data);
                }
            } catch (error) {
                console.error('Error loading dashboard:', error);
            }
        }

@endsection

@section('footer')
<footer class="bg-gradient-to-r from-red-700 to-red-900 text-white mt-12 border-t border-red-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid md:grid-cols-3 gap-8">
            <div>
                <h3 class="font-bold mb-4">Listings</h3>
                <ul class="text-red-100 text-sm space-y-2">
                    <li><a href="/listings" class="hover:text-white">My Listings</a></li>
                    <li><a href="/listings/create" class="hover:text-white">Add Property</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold mb-4">Management</h3>
                <ul class="text-red-100 text-sm space-y-2">
                    <li><a href="/profile" class="hover:text-white">Profile</a></li>
                    <li><a href="/settings" class="hover:text-white">Settings</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold mb-4">Support</h3>
                <ul class="text-red-100 text-sm space-y-2">
                    <li><a href="#" class="hover:text-white">Help</a></li>
                    <li><a href="#" class="hover:text-white">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-red-500 mt-8 pt-8 text-center text-red-100 text-sm">
            <p>&copy; 2026 Accra Housing - Landlord Mode. All rights reserved.</p>
        </div>
    </div>
</footer>
@endsection
