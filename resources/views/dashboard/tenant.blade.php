@extends('layouts.app')

@section('title', 'Tenant Dashboard - Accra Housing')

@section('navbar')
    <!-- Top Navbar -->
    <nav class="bg-blue-600 h-16 sticky top-0 z-50 md:ml-64 shadow-sm flex items-center justify-between px-6 border-b border-gray-100">
        <a href="{{ url('/') }}" class="flex items-center space-x-2 text-white hover:text-blue-200 transition">
            <i class="fa-solid fa-building text-white text-xl"></i>
            <span class="font-bold text-lg">Accra Housing - Tenant</span>
        </a>

        <div class="flex items-center">
            <div class="relative group">
                <button class="flex items-center space-x-3 text-white hover:text-blue-200 focus:outline-none transition py-2">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold leading-none">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-blue-200 font-medium">Tenant</p>
                    </div>
                    
                    <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center border-2 border-blue-100">
                        <span class="text-blue-600 font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-blue-500 transition"></i>
                </button>

                <div class="absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-gray-100">
                    <a href="{{ route('profile') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-user-circle mr-3 opacity-70"></i> My Profile
                    </a>
                    <a href="{{ route('favorites') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50">
                        <i class="fas fa-heart mr-3 opacity-70"></i> Favorites
                    </a>
                    <hr class="my-1 border-gray-50">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 font-medium">
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
        --primary: #2563eb;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeInUp 0.6s ease-out; }
    .icon-cyan { color: #06b6d4; }
    .icon-teal { color: #14b8a6; }
    .icon-indigo { color: #4f46e5; }
    .icon-purple { color: #a855f7; }
    .icon-violet { color: #7c3aed; }
    .icon-amber { color: #f59e0b; }
    .icon-rose { color: #f43f5e; }
    .icon-emerald { color: #10b981; }
    .icon-sky { color: #0ea5e9; }
</style>

    <!-- Page Wrapper: flex column, min height screen, allows footer to naturally flow -->
    <div class="flex flex-col min-h-screen bg-gray-50">
        <!-- Sidebar Container (fixed) -->
        <div class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-blue-50 to-white shadow-lg z-10 flex flex-col">
            <!-- Logo/Brand -->
            <div class="flex items-center justify-center h-16 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                <i class="fas fa-door-open mr-2"></i>
                <h1 class="text-xl font-bold">Tenant Portal</h1>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="/dashboard/tenant" class="flex items-center px-4 py-3 text-gray-700 bg-gray-200 rounded-lg transition-all duration-300 hover:bg-gray-300 hover:translate-x-1">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
                <a href="/listings" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 rounded-lg transition-all duration-300 hover:translate-x-1">
                    <i class="fas fa-building mr-3"></i>
                    Properties
                </a>
                <a href="/messages" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 rounded-lg transition-all duration-300 hover:translate-x-1">
                    <i class="fas fa-envelope mr-3"></i>
                    Messages
                </a>
                <a href="/payments" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 rounded-lg transition-all duration-300 hover:translate-x-1">
                    <i class="fas fa-credit-card mr-3"></i>
                    Payments
                </a>
                <a href="/payments/history" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 rounded-lg transition-all duration-300 hover:translate-x-1">
                    <i class="fas fa-history mr-3"></i>
                    Payment History
                </a>
                <a href="/favorites" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 rounded-lg transition-all duration-300 hover:translate-x-1">
                    <i class="fas fa-heart mr-3"></i>
                    Favorites
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
                        <p class="text-xs text-gray-500">Tenant</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area (flex-1 to grow and push footer down) -->
        <div class="flex-1 ml-0 md:ml-64 animate-fade-in pt-24 md:pt-20 lg:pt-10">
            <div class="max-w-7xl mx-auto p-4 md:p-8">
                <!-- Header -->
                <header class="bg-white shadow-sm border-b border-gray-200 mb-6">
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Good Morning, {{ auth()->user()->name }}</h1>
                            <p class="text-gray-600">Welcome back to your tenant dashboard</p>
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
                    <!-- Rent Overview Card -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-2xl shadow-lg p-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium mb-1">Current Rent Payment</p>
                                <h2 class="text-4xl font-bold mb-2">GHS 1,500</h2>
                                <p class="text-blue-100">Due: March 31, 2026</p>
                            </div>
                            <div class="text-6xl opacity-20">
                                <i class="fas fa-home"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition-transform">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-600">Active Tenancy</h3>
                                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">1 Property</p>
                            <p class="text-sm text-gray-600 mt-2">Lease expires: 12/31/2026</p>
                        </div>

                        <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition-transform">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-600">Saved Properties</h3>
                                <i class="fas fa-heart text-red-600 text-2xl"></i>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">8 Listings</p>
                            <p class="text-sm text-gray-600 mt-2">In your favorites</p>
                        </div>

                        <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition-transform">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-600">Payment Status</h3>
                                <i class="fas fa-credit-card text-blue-600 text-2xl"></i>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">Paid</p>
                            <p class="text-sm text-green-600 mt-2">Last payment: 3/1/2026</p>
                        </div>
                    </div>

                    <!-- Maintenance and Contact -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Maintenance Request Shortcut -->
                        <div class="bg-white rounded-2xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-tools mr-2 icon-amber"></i>Maintenance</h3>
                            <div class="text-center">
                                <button class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition text-lg">
                                    <i class="fas fa-tools mr-2"></i>Report an Issue
                                </button>
                                <p class="text-sm text-gray-600 mt-2">Need repairs or maintenance? Let your landlord know.</p>
                            </div>
                        </div>

                        <!-- Contact Section -->
                        <div class="bg-white rounded-2xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-address-book mr-2 icon-rose"></i>Contact Landlord</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="text-center">
                                    <i class="fas fa-phone icon-emerald mb-3" style="font-size: 2rem; display: block;"></i>
                                    <p class="text-sm font-medium text-gray-600">Phone/WhatsApp</p>
                                    <p class="text-gray-900">+233 (0) 123 456 789</p>
                                    <a href="tel:+2330123456789" class="text-emerald-600 hover:text-emerald-700 font-medium">Call Now</a>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-envelope icon-sky mb-3" style="font-size: 2rem; display: block;"></i>
                                    <p class="text-sm font-medium text-gray-600">Email Support</p>
                                    <p class="text-gray-900">support@accrahousing.com</p>
                                    <a href="mailto:support@accrahousing.com" class="text-sky-600 hover:text-sky-700 font-medium">Send Email</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Tenancy Details -->
                    <div class="bg-white rounded-2xl shadow p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Your Current Tenancy</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <i class="fas fa-map-marker-alt icon-rose mb-3" style="font-size: 1.25rem; display: block;"></i>
                                <p class="text-sm font-medium text-gray-600">Property Address</p>
                                <p class="text-gray-900">123 Main Street, Accra, Ghana</p>
                            </div>
                            <div>
                                <i class="fas fa-user icon-indigo mb-3" style="font-size: 1.25rem; display: block;"></i>
                                <p class="text-sm font-medium text-gray-600">Landlord Name</p>
                                <p class="text-gray-900">{{ auth()->user()->name }}</p>
                            </div>
                            <div>
                                <i class="fas fa-calendar icon-indigo mb-3" style="font-size: 1.25rem; display: block;"></i>
                                <p class="text-sm font-medium text-gray-600">Lease Expiry</p>
                                <p class="text-gray-900">December 31, 2026</p>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

@endsection

@section('footer')
<footer class="bg-gradient-to-r from-blue-600 to-blue-700 text-white mt-12 border-t border-blue-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid md:grid-cols-3 gap-8">
            <div>
                <h3 class="font-bold mb-4">Rentals</h3>
                <ul class="text-blue-100 text-sm space-y-2">
                    <li><a href="/listings" class="hover:text-white">Browse Properties</a></li>
                    <li><a href="/favorites" class="hover:text-white">My Favorites</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold mb-4">Account</h3>
                <ul class="text-blue-100 text-sm space-y-2">
                    <li><a href="/profile" class="hover:text-white">Profile</a></li>
                    <li><a href="/payments" class="hover:text-white">Payments</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold mb-4">Support</h3>
                <ul class="text-blue-100 text-sm space-y-2">
                    <li><a href="#" class="hover:text-white">Help</a></li>
                    <li><a href="#" class="hover:text-white">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-blue-500 mt-8 pt-8 text-center text-blue-100 text-sm">
            <p>&copy; 2026 Accra Housing - Tenant Mode. All rights reserved.</p>
        </div>
    </div>
</footer>
@endsection
