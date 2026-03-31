@extends('layouts.app')

@section('title', 'Register - Accra Housing')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-white dark:from-gray-900 dark:to-gray-800 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-8">
            <h1 class="text-3xl font-bold mb-2 text-center text-gray-900 dark:text-white">Create Account</h1>
            <p class="text-center text-gray-600 dark:text-gray-400 mb-8">Join the Accra Housing Marketplace</p>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <span class="font-semibold">Registration Successful!</span>
                    </div>
                    <p class="mb-2">{{ session('success') }}</p>
                    @if(strpos(session('success'), 'Code for demo:') !== false)
                        <div class="mt-3 p-3 bg-green-200 dark:bg-green-800/50 rounded-lg border-2 border-dashed border-green-400">
                            <p class="text-sm font-mono text-center text-green-800 dark:text-green-200">
                                <strong>OTP Code:</strong>
                                <span class="text-lg font-bold tracking-wider">
                                    {{ substr(session('success'), strpos(session('success'), 'Code for demo:') + 15) }}
                                </span>
                            </p>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="{{ route('verify-phone') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                                Proceed to Verification
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                <input type="hidden" name="role" value="tenant" />
                <input type="hidden" name="verification_method" value="phone" />

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

            <!-- Demo Info -->
            <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-xs text-blue-800 dark:text-blue-300">
                    <strong>Demo Mode:</strong> When using SMS/OTP verification, the OTP code will be displayed prominently on this page after registration.
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
