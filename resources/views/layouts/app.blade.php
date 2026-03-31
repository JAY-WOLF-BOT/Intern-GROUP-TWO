<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Accra Housing Marketplace')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3H7bCOT6WiOku/jfiuJzsc3dwWUkwXkrjX84rios5mlULw3IUoIsDmtlIvIrSad2k7khUzJ+gyZg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Force FontAwesome icons to render with size and no zero dimensions */
        .fa, .fas, .fa-solid, .fa-regular, .fa-brands, .fa-light {
            display: inline-block !important;
            width: auto !important;
            height: auto !important;
            line-height: 1 !important;
            font-size: inherit !important; /* keep text-xl etc. working */
            vertical-align: middle !important;
            text-rendering: auto !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
        }

        .fa-solid, .fas {
            font-family: 'Font Awesome 6 Free' !important;
            font-weight: 900 !important;
        }

        .fa-brands, .fab {
            font-family: 'Font Awesome 6 Brands' !important;
            font-weight: 400 !important;
        }

        .fa-regular, .far {
            font-family: 'Font Awesome 6 Free' !important;
            font-weight: 400 !important;
        }

        /* Ensure pseudo content is shown if Font Awesome is loaded */
        .fa:before, .fas:before, .fa-solid:before, .fa-regular:before, .fa-brands:before, .fa-light:before {
            display: inline-block !important;
            width: auto !important;
            height: auto !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #F53003;
            --primary-light: #FF6B35;
            --primary-dark: #D42802;
            --secondary: #1b1b18;
            --light: #FDFDFC;
            --border: #e3e3e0;
        }

        * {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
        }
    </style>
</head>
<body>
    <!-- Navbar Section (can be overridden by child views) -->
    @section('navbar')
    <nav class="bg-white dark:bg-[#1D1D1A] border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ route('homepage') }}" class="flex items-center space-x-2">
                    <i class="fas fa-building text-red-600 text-2xl"></i>
                    <span class="font-bold text-lg text-gray-900 dark:text-white">Accra Housing</span>
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('listings.index') }}" class="px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-500 transition">Browse Listings</a>

                    @auth
                        @if(auth()->user()->role === 'landlord')
                            <a href="{{ route('dashboard.landlord') }}" class="px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-500 transition">My Listings</a>
                        @else
                            <a href="{{ route('dashboard.tenant') }}" class="px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-500 transition">My Dashboard</a>
                        @endif
                    @endauth
                </div>

                <!-- Right Side -->
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-red-600">
                                <i class="fas fa-user-circle text-xl"></i>
                                <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                            </button>

                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-0 w-48 bg-white dark:bg-[#1D1D1A] rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/10">
                                    <i class="fas fa-user w-4"></i> Profile
                                </a>
                                <a href="{{ route('favorites') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/10">
                                    <i class="fas fa-heart w-4"></i> Favorites
                                </a>
                                <hr class="border-gray-200 dark:border-gray-700">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/10">
                                        <i class="fas fa-sign-out-alt w-4"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-red-600">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    @show

    <!-- Main Content -->
    <main class="flex-1 pb-10">
        @yield('content')
    </main>

    <!-- Default Footer (can be overridden by child views) -->
    @section('footer')
    <footer class="bg-gray-900 dark:bg-[#0a0a0a] text-white mt-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="font-bold mb-4">About</h3>
                    <p class="text-gray-400 text-sm">Affordable housing marketplace connecting tenants with landlords across Accra.</p>
                </div>
                <div>
                    <h3 class="font-bold mb-4">Quick Links</h3>
                    <ul class="text-gray-400 text-sm space-y-2">
                        <li><a href="{{ route('listings.index') }}" class="hover:text-white">Browse</a></li>
                        <li><a href="#" class="hover:text-white">About</a></li>
                        <li><a href="#" class="hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold mb-4">Support</h3>
                    <ul class="text-gray-400 text-sm space-y-2">
                        <li><a href="#" class="hover:text-white">FAQ</a></li>
                        <li><a href="#" class="hover:text-white">Help Center</a></li>
                        <li><a href="#" class="hover:text-white">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold mb-4">Contact</h3>
                    <p class="text-gray-400 text-sm">📧 info@accrahousing.com</p>
                    <p class="text-gray-400 text-sm">📱 +233 (0) 123 456 789</p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-6 pt-6 text-center text-gray-400 text-sm">
                <p>&copy; 2026 Accra Housing Marketplace. All rights reserved.</p>
            </div>
        </div>
    </footer>
    @show

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
</body>
</html>
