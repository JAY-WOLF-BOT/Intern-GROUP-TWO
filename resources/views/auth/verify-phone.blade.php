@extends('layouts.app')

@section('title', 'Verify Phone - Accra Housing')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-white dark:from-gray-900 dark:to-gray-800 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                    <i class="fas fa-mobile-alt text-red-600 text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Verify Your Phone</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Enter the OTP code sent to your phone</p>
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

            <form method="POST" action="{{ route('verify-phone') }}" class="space-y-4">
                @csrf

                <!-- Phone Display -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                    <div class="px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-gray-900 dark:text-white">
                        {{ $phone ?? 'your phone' }}
                    </div>
                </div>

                <!-- OTP Code -->
                <div>
                    <label for="otp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Enter 6-Digit OTP</label>
                    <input type="text" id="otp" name="otp"
                        placeholder="000000"
                        maxlength="6"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-800 dark:text-white text-center text-2xl tracking-widest font-mono"
                        autocomplete="off"
                        required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition mt-6">
                    Verify OTP
                </button>

                <!-- Resend OTP -->
                <form method="POST" class="mt-4">
                    @csrf
                    <button type="submit" formaction="{{ route('resend-otp') }}" class="w-full text-red-600 hover:text-red-700 font-medium py-2 border border-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/10 transition">
                        <i class="fas fa-redo mr-2"></i> Resend OTP
                    </button>
                </form>

                <!-- Back to Login -->
                <p class="text-center text-gray-600 dark:text-gray-400">
                    <a href="{{ route('login') }}" class="text-red-600 hover:text-red-700 font-medium">Back to Login</a>
                </p>
            </form>

            <!-- Help Text -->
            <div class="mt-8 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                <p class="text-sm text-yellow-900 dark:text-yellow-300">
                    <i class="fas fa-lightbulb mr-2"></i>
                    <strong>Demo Mode:</strong> The OTP code is displayed in the success message above.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-format OTP input to numbers only
    document.getElementById('otp').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
@endsection
