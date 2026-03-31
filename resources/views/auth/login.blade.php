@extends('layouts.app')

@section('title', 'Login - Accra Housing')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-white dark:from-gray-900 dark:to-gray-800 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-8">
            <h1 class="text-3xl font-bold mb-2 text-center text-gray-900 dark:text-white">Welcome Back</h1>
            <p class="text-center text-gray-600 dark:text-gray-400 mb-8">Sign in to your account</p>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                        required autofocus>
                    @error('email')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                        required>
                    @error('password')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Role Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">I am a...</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative">
                            <input type="radio" name="role" value="tenant" {{ old('role', 'tenant') === 'tenant' ? 'checked' : '' }} class="sr-only" required>
                            <div class="p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-red-500 transition" id="tenant-option">
                                <i class="fas fa-home text-red-600 text-xl mb-2"></i>
                                <p class="font-medium text-gray-900 dark:text-white">Tenant</p>
                                <p class="text-xs text-gray-500">Looking for housing</p>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="role" value="landlord" {{ old('role') === 'landlord' ? 'checked' : '' }} class="sr-only" required>
                            <div class="p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-red-500 transition" id="landlord-option">
                                <i class="fas fa-building text-red-600 text-xl mb-2"></i>
                                <p class="font-medium text-gray-900 dark:text-white">Landlord</p>
                                <p class="text-xs text-gray-500">Listing properties</p>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }} class="w-4 h-4 text-red-600 rounded">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Remember me</span>
                    </label>
                    <a href="{{ route('forgot-password') }}" class="text-sm text-red-600 hover:text-red-700 font-medium">
                        Forgot password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition mt-6">
                    Sign In
                </button>

                <!-- Sign Up Link -->
                <p class="text-center text-gray-600 dark:text-gray-400 mt-6">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-red-600 hover:text-red-700 font-medium">Register here</a>
                </p>
            </form>

            <!-- Demo Credentials Info -->
            <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-xs text-blue-800 dark:text-blue-300">
                    <strong>Demo:</strong> Use any email with password to test the application.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    // Highlight selected role
    document.querySelectorAll('input[name="role"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('[id$="-option"]').forEach(el => {
                el.classList.remove('border-red-500', 'bg-red-50', 'dark:bg-red-900/10');
                el.classList.add('border-gray-300');
            });
            if (this.checked) {
                this.parentElement.querySelector('[id$="-option"]').classList.add('border-red-500', 'bg-red-50', 'dark:bg-red-900/10');
                this.parentElement.querySelector('[id$="-option"]').classList.remove('border-gray-300');
            }
        });
        if (radio.checked) {
            radio.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
