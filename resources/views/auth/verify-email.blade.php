@extends('layouts.app')

@section('title', 'Verify Email - Accra Housing')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-white dark:from-gray-900 dark:to-gray-800 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                    <i class="fas fa-envelope text-red-600 text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Verify Your Email</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Enter the verification code sent to your email</p>
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

            <form method="POST" action="{{ route('verify-email') }}" class="space-y-4">
                @csrf

                <!-- Email Display -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                    <div class="px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-gray-900 dark:text-white">
                        {{ session('email', old('email', 'your@email.com')) }}
                    </div>
                </div>

                <!-- Verification Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Verification Code</label>
                    <input type="text" id="code" name="code"
                        placeholder="Enter 6-digit code"
                        maxlength="6"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-800 dark:text-white text-center text-lg tracking-widest"
                        autocomplete="off"
                        required>
                </div>

                <!-- Hidden email field -->
                <input type="hidden" name="email" value="{{ session('email', old('email')) }}">

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition mt-6">
                    Verify Email
                </button>

                <!-- Resend Link -->
                <p class="text-center text-gray-600 dark:text-gray-400">
                    Didn't receive the code?
                    <a href="{{ route('login') }}" class="text-red-600 hover:text-red-700 font-medium">Try again</a>
                </p>
            </form>

            <!-- Help Text -->
            <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-sm text-blue-900 dark:text-blue-300">
                    <i class="fas fa-info-circle mr-2"></i>
                    Check your spam folder if you don't see the email. The code expires in 10 minutes.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
