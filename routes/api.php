<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================================================
// MOBILE API V1 (React Native)
// ============================================================================

// Authentication Routes (Public)
Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login-with-otp', [AuthController::class, 'loginWithOtp']);
    
    // Protected auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

// User Routes
Route::middleware('auth:sanctum')->prefix('v1/user')->group(function () {
    Route::get('/profile', [UserController::class, 'show']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::post('/password/change', [UserController::class, 'changePassword']);
    Route::get('/statistics', [UserController::class, 'statistics']);
});

// Listing Routes
Route::prefix('v1/listings')->group(function () {
    // Public routes
    Route::get('/', [ListingController::class, 'index']);
    Route::get('/{listingId}', [ListingController::class, 'show']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [ListingController::class, 'store']);
        Route::put('/{listingId}', [ListingController::class, 'update']);
        Route::delete('/{listingId}', [ListingController::class, 'destroy']);
        Route::get('/landlord/my-listings', [ListingController::class, 'myListings']);
    });
});

// Payment Routes
Route::middleware('auth:sanctum')->prefix('v1/payments')->group(function () {
    Route::post('/initiate', [ApiPaymentController::class, 'initiate']);
    Route::get('/status/{paymentId}', [ApiPaymentController::class, 'checkStatus']);
    Route::get('/history', [ApiPaymentController::class, 'paymentHistory']);
    Route::post('/confirm', [ApiPaymentController::class, 'confirmPayment']); // Webhook endpoint
});

// Favorite Routes
Route::middleware('auth:sanctum')->prefix('v1/favorites')->group(function () {
    Route::post('/add', [FavoriteController::class, 'addFavorite']);
    Route::delete('/{favoriteId}', [FavoriteController::class, 'removeFavorite']);
    Route::get('/my-favorites', [FavoriteController::class, 'myFavorites']);
    Route::get('/is-favorited/{listingId}', [FavoriteController::class, 'isFavorited']);
    Route::post('/clear-all', [FavoriteController::class, 'clearAll']);
});

// OTP Routes
Route::prefix('v1/otp')->group(function () {
    Route::post('/request', [UserController::class, 'requestOtp']);
    Route::post('/verify', [UserController::class, 'verifyOtp']);
});

// ============================================================================
// LEGACY ROUTES (Keep for backward compatibility)
// ============================================================================

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Search & Discovery Routes
Route::prefix('search')->group(function () {
    Route::get('/listings', [SearchController::class, 'searchListings']);
});

// Photo Upload Routes
Route::middleware('auth:sanctum')->prefix('photos')->group(function () {
    Route::post('/upload/{listingId}', [PhotoController::class, 'upload']);
    Route::delete('/{photoId}', [PhotoController::class, 'delete']);
});

// Payment Routes (Legacy)
Route::middleware('auth:sanctum')->prefix('payments')->group(function () {
    Route::post('/initiate', [PaymentController::class, 'initiatePayment']);
    Route::get('/status/{paymentId}', [PaymentController::class, 'checkStatus']);
});

// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::patch('/listings/{listingId}/verify', [AdminController::class, 'verifyListing']);
    Route::patch('/listings/{listingId}/reject', [AdminController::class, 'rejectListing']);
});