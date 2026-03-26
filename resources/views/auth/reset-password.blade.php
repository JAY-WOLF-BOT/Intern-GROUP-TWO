@extends('layouts.app')

@section('title', 'Reset Password - Accra Housing')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-white dark:from-gray-900 dark:to-gray-800 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                    <i class="fas fa-lock text-red-600 text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Reset Password</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Create a strong new password</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('reset-password') }}" class="space-y-4">
                @csrf

                <!-- Email (hidden) -->
                <input type="hidden" name="email" value="{{ $email }}">

                <!-- Token (hidden) -->
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            placeholder="At least 8 characters"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                            required>
                        <button type="button" class="absolute right-3 top-2.5 text-gray-600 dark:text-gray-400" onclick="togglePassword('password', 'password-icon')">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Must be at least 8 characters long
                    </p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Re-enter your new password"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                            required>
                        <button type="button" class="absolute right-3 top-2.5 text-gray-600 dark:text-gray-400" onclick="togglePassword('password_confirmation', 'confirm-icon')">
                            <i class="fas fa-eye" id="confirm-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Password Requirements -->
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <p class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-2">Strong Password Tips:</p>
                    <ul class="text-xs text-blue-800 dark:text-blue-400 space-y-1">
                        <li><i class="fas fa-check mr-2"></i>At least 8 characters</li>
                        <li><i class="fas fa-check mr-2"></i>Mix of uppercase and lowercase</li>
                        <li><i class="fas fa-check mr-2"></i>Include numbers or symbols</li>
                        <li><i class="fas fa-check mr-2"></i>Don't use personal information</li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition mt-6">
                    <i class="fas fa-key mr-2"></i> Set New Password
                </button>

                <!-- Back to Login -->
                <p class="text-center text-gray-600 dark:text-gray-400">
                    <a href="{{ route('login') }}" class="text-red-600 hover:text-red-700 font-medium">Back to Login</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Real-time password confirmation check
    document.getElementById('password_confirmation').addEventListener('change', function() {
        const password = document.getElementById('password').value;
        const confirmation = this.value;

        if (password && confirmation && password !== confirmation) {
            this.classList.add('border-red-500');
        } else {
            this.classList.remove('border-red-500');
        }
    });
</script>
@endsection
