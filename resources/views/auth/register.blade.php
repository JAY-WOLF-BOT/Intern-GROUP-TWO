@extends('layouts.app')

@section('title', 'Register - Accra Housing')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-white dark:from-gray-900 dark:to-gray-800 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-8">
            <h1 class="text-3xl font-bold mb-2 text-center text-gray-900 dark:text-white">Create Account</h1>
            <p class="text-center text-gray-600 dark:text-gray-400 mb-8">Join the Accra Housing Marketplace</p>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                        required>
                    @error('name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                        required>
                    @error('email')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                        placeholder="+233501234567"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                        required>
                    @error('phone_number')
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
                </div>

                <!-- Verification Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">First time? Verify with...</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative">
                            <input type="radio" name="verification_method" value="email" checked class="sr-only" required>
                            <div class="p-4 border-2 border-red-300 rounded-lg cursor-pointer hover:border-red-500 transition bg-red-50 dark:bg-red-900/10" id="email-option">
                                <i class="fas fa-envelope text-red-600 text-xl mb-2"></i>
                                <p class="font-medium text-gray-900 dark:text-white text-sm">Email</p>
                                <p class="text-xs text-gray-500">Verification link sent</p>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="verification_method" value="phone" class="sr-only" required>
                            <div class="p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-red-500 transition" id="phone-option">
                                <i class="fas fa-phone text-red-600 text-xl mb-2"></i>
                                <p class="font-medium text-gray-900 dark:text-white text-sm">SMS/OTP</p>
                                <p class="text-xs text-gray-500">OTP sent to phone</p>
                            </div>
                        </label>
                    </div>
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

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                        required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition mt-6">
                    Create Account
                </button>

                <!-- Sign In Link -->
                <p class="text-center text-gray-600 dark:text-gray-400 mt-6">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-red-600 hover:text-red-700 font-medium">Sign In</a>
                </p>
            </form>
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

    // Highlight selected verification method
    document.querySelectorAll('input[name="verification_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('[id$="-option"]').forEach(el => {
                if (el.id.includes('email') || el.id.includes('phone')) {
                    el.classList.remove('border-red-500', 'border-red-300', 'bg-red-50', 'dark:bg-red-900/10');
                    el.classList.add('border-gray-300');
                }
            });
            if (this.checked) {
                const optionId = this.value === 'email' ? 'email-option' : 'phone-option';
                const option = document.getElementById(optionId);
                option.classList.add('border-red-500', 'border-red-300', 'bg-red-50', 'dark:bg-red-900/10');
                option.classList.remove('border-gray-300');
            }
        });
        if (radio.checked) {
            radio.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
