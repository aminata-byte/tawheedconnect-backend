<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\FileUploadController;

/*
|--------------------------------------------------------------------------
| API Routes - TawheedConnect
|--------------------------------------------------------------------------
*/

// Route de test pour vérifier que tout marche
Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'TawheedConnect API v1.0',
        'timestamp' => now(),
    ]);
});

// Routes publiques (sans authentification)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-phone', [AuthController::class, 'verifyPhone']);
    Route::post('/resend-code', [AuthController::class, 'resendCode']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Routes protégées (authentification Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});



// Routes protégées
Route::middleware('auth:sanctum')->group(function () {

    // Upload photo profil
    Route::post('/upload/photo', [FileUploadController::class, 'uploadPhoto']);

    // Upload logo
    Route::post('/upload/logo', [FileUploadController::class, 'uploadLogo']);
});

// Route de test temporaire (SANS auth)
Route::post('/test-upload-logo', [FileUploadController::class, 'uploadLogo']);
