<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;

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

// Payment Routes
Route::middleware('auth:sanctum')->prefix('payments')->group(function () {
    Route::post('/initiate', [PaymentController::class, 'initiatePayment']);
    Route::get('/status/{paymentId}', [PaymentController::class, 'checkStatus']);
});

// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::patch('/listings/{listingId}/verify', [AdminController::class, 'verifyListing']);
    Route::patch('/listings/{listingId}/reject', [AdminController::class, 'rejectListing']);
});