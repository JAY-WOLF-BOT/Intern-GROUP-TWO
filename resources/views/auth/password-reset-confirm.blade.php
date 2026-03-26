@extends('layouts.app')

@section('title', 'Reset Password Confirmation - Accra Housing')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-white dark:from-gray-900 dark:to-gray-800 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full mb-4">
                    <i class="fas fa-check text-green-600 text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Check Your Email</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">We've sent password reset instructions to your email.</p>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('email'))
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <p class="text-sm text-blue-900 dark:text-blue-300 mb-3">
                        <strong>Email:</strong> {{ session('email') }}
                    </p>
                </div>
            @endif

            @if (session('demo_token'))
                <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-400 dark:border-yellow-700 rounded-lg">
                    <p class="text-sm font-medium text-yellow-900 dark:text-yellow-300 mb-2">
                        <i class="fas fa-lightbulb mr-2"></i> Demo Mode
                    </p>
                    <p class="text-xs text-yellow-800 dark:text-yellow-400 mb-3">
                        For demo purposes, here's your reset token:
                    </p>
                    <div class="bg-white dark:bg-gray-800 p-3 rounded font-mono text-xs break-all border border-yellow-200 dark:border-yellow-800">
                        {{ session('demo_token') }}
                    </div>
                    <p class="text-xs text-yellow-800 dark:text-yellow-400 mt-3">
                        ✓ Use this token in the "Reset Password" form below or click the link in your email.
                    </p>
                </div>
            @endif

            <!-- Reset Password Form (for demo) -->
            <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Reset Your Password Now</h2>

                <form method="POST" action="{{ route('reset-password') }}" class="space-y-4">
                    @csrf

                    <!-- Email -->
                    <input type="hidden" name="email" value="{{ session('email') }}">
                    <input type="hidden" name="token" value="{{ session('demo_token') }}">

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                        <input type="password" id="password" name="password"
                            placeholder="At least 8 characters"
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
                            placeholder="Confirm your password"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                            required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition mt-6">
                        <i class="fas fa-key mr-2"></i> Reset Password
                    </button>
                </form>
            </div>

            <!-- Back to Login -->
            <p class="text-center text-gray-600 dark:text-gray-400 mt-6">
                <a href="{{ route('login') }}" class="text-red-600 hover:text-red-700 font-medium">Back to Login</a>
            </p>
        </div>
    </div>
</div>
@endsection
