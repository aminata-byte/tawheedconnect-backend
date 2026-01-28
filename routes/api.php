<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\OustazeController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\Api\EventController;

/*
|--------------------------------------------------------------------------
| API Routes - TawheedConnect
|--------------------------------------------------------------------------
*/

// ==========================================
// Route de test pour vérifier que l'API fonctionne
// ==========================================
Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'TawheedConnect API v1.0',
        'timestamp' => now(),
    ]);
});

// ==========================================
// Routes publiques (SANS authentification)
// ==========================================
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-phone', [AuthController::class, 'verifyPhone']);
    Route::post('/resend-code', [AuthController::class, 'resendCode']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// ==========================================
// Routes protégées (AVEC authentification Sanctum)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    // Authentification
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/user', [AuthController::class, 'user']); // Alias de /me
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // Upload de fichiers
    Route::prefix('upload')->group(function () {
        Route::post('/photo', [FileUploadController::class, 'uploadPhoto']);
        Route::post('/logo', [FileUploadController::class, 'uploadLogo']);
    });

    // Routes pour les événements
    Route::apiResource('events', EventController::class);
    // Paiements
    Route::prefix('payments')->group(function () {
        Route::post('/initiate', [PaymentController::class, 'initiate']);
    });

    // ==========================================
    // Routes pour les Oustazes (CORRIGÉ - plus de duplication)
    // ==========================================
    Route::apiResource('oustazes', OustazeController::class);
    
    // OU si vous préférez définir manuellement (choisir UNE seule méthode) :
    /*
    Route::prefix('oustazes')->group(function () {
        Route::get('/', [OustazeController::class, 'index']);
        Route::post('/', [OustazeController::class, 'store']);
        Route::get('/{id}', [OustazeController::class, 'show']);
        Route::put('/{id}', [OustazeController::class, 'update']);
        Route::delete('/{id}', [OustazeController::class, 'destroy']);
    });
    */
});

// ==========================================
// Routes de test temporaires (à retirer en production)
// ==========================================
if (config('app.env') !== 'production') {
    Route::post('/test-upload-logo', [FileUploadController::class, 'uploadLogo']);
}
