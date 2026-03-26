@extends('layouts.app')

@section('title', 'Forgot Password - Accra Housing')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-white dark:from-gray-900 dark:to-gray-800 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                    <i class="fas fa-lock text-red-600 text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Forgot Password?</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">No worries! Enter your email and we'll send you a reset link.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('forgot-password') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="your@email.com"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                        required>
                    @error('email')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition mt-6">
                    <i class="fas fa-paper-plane mr-2"></i> Send Reset Link
                </button>

                <!-- Back to Login -->
                <p class="text-center text-gray-600 dark:text-gray-400">
                    Remember your password?
                    <a href="{{ route('login') }}" class="text-red-600 hover:text-red-700 font-medium">Back to Login</a>
                </p>
            </form>

            <!-- Help Text -->
            <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-sm text-blue-900 dark:text-blue-300">
                    <i class="fas fa-info-circle mr-2"></i>
                    We'll send a password reset link to your email. The link expires in 1 hour.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
