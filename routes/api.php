<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Authentification
|--------------------------------------------------------------------------
*/

// Route factice pour éviter l'erreur de redirection
Route::get('/login', function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Non authentifié. Veuillez fournir un token valide.'
    ], 401);
})->name('login');

// Routes publiques (sans authentification)
Route::prefix('auth')->group(function () {
    // Inscription et connexion
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-2fa', [AuthController::class, 'verifyTwoFactor']);
    
    // Profil public (accessible sans authentification)
    Route::get('/profile/{id}', [AuthController::class, 'publicProfile']);
});

// Routes protégées (nécessitent authentification)
Route::middleware(['auth:api'])->prefix('auth')->group(function () {
    // Gestion de session
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    
    // Profil utilisateur
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    
    // Sécurité
    Route::post('/toggle-2fa', [AuthController::class, 'toggleTwoFactor']);
    
    // Préférences
    Route::put('/notification-preferences', [AuthController::class, 'updateNotificationPreferences']);
    Route::put('/profile-visibility', [AuthController::class, 'updateProfileVisibility']);
    
    // Suppression de compte
    Route::delete('/account', [AuthController::class, 'deleteAccount']);
});

// Routes réservées aux administrateurs
Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    // Les routes admin seront ajoutées plus tard
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Bienvenue administrateur']);
    });
});