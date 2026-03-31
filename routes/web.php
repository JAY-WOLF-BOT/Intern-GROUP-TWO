<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;

// Public routes
Route::get('/', function () {
    return view('Homepage');
})->name('homepage');

// Authentication routes
Route::middleware('guest')->group(function () {
    // Register
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Login
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Email Verification
    Route::get('/verify-email', [AuthController::class, 'verifyEmail'])->name('verify-email');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('verify-email.post');

    // Phone Verification
    Route::get('/verify-phone', [AuthController::class, 'verifyPhone'])->name('verify-phone');
    Route::post('/verify-phone', [AuthController::class, 'verifyPhone'])->name('verify-phone.post');
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resend-otp');

    // Password Reset
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::get('/password-reset-confirm', [AuthController::class, 'showResetConfirm'])->name('password-reset-confirm');
    Route::get('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});


// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard routes
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role === 'landlord') {
            return redirect('/dashboard/landlord');
        } elseif ($user->role === 'tenant') {
            return redirect('/dashboard/tenant');
        }
        return redirect('/'); // fallback
    })->name('dashboard');

    Route::get('/dashboard/landlord', function () {
        return view('dashboard.landlord');
    })->middleware('landlord')->name('dashboard.landlord');

    Route::get('/dashboard/tenant', function () {
        return view('dashboard.tenant');
    })->name('dashboard.tenant');

    // Listings - browsing (protected)
    Route::get('/listings', function () {
        return view('listings.index');
    })->name('listings.index');

    Route::get('/listings/{id}', function ($id) {
        return view('listings.show');
    })->name('listings.show');

    // Listings management
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::get('/listings/{id}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{id}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{id}', [ListingController::class, 'destroy'])->name('listings.destroy');

    // User routes
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::post('/profile/info', [ProfileController::class, 'updateInfo'])->name('profile.info');
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
        Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
        Route::delete('/profile/picture', [ProfileController::class, 'deleteProfilePicture'])->name('profile.picture.delete');
    });

    Route::get('/favorites', function () {
        return view('favorites');
    })->name('favorites');

    // Messages - coming soon
    Route::get('/messages', function () {
        return view('coming-soon', ['feature' => 'Messages']);
    })->name('messages.index');

    // Payments - coming soon
    Route::get('/payments', function () {
        return view('coming-soon', ['feature' => 'Payments']);
    })->name('payments.index');

    Route::get('/payments/history', function () {
        return view('coming-soon', ['feature' => 'Payment History']);
    })->name('payments.history');

    // Settings
    Route::get('/settings', function () {
        return view('coming-soon', ['feature' => 'Settings']);
    })->name('settings');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
