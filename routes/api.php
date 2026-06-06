<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ElevageController;
use App\Http\Controllers\Api\AnimalController;
use App\Http\Controllers\Api\TacheController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\ProduitController;
use App\Http\Controllers\Api\StockController;

/*
|--------------------------------------------------------------------------
| Routes publiques (sans authentification)
|--------------------------------------------------------------------------
*/

Route::apiResource('elevages', ElevageController::class)
    ->only(['index', 'show']);

Route::apiResource('animaux', AnimalController::class)
    ->only(['index', 'show']);

// Route factice pour éviter l'erreur de redirection
Route::get('/login', function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Non authentifié. Veuillez fournir un token valide.'
    ], 401);
})->name('login');

// Routes publiques (sans authentification)
// Authentification (publique)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-2fa', [AuthController::class, 'verifyTwoFactor']);
    Route::get('/profile/{id}', [AuthController::class, 'publicProfile']);
});

// Élevages (lecture seule publique)
Route::get('/elevages', [ElevageController::class, 'index']);
Route::get('/elevages/{id}', [ElevageController::class, 'show']);

// Animaux (lecture seule publique)
Route::get('/animaux', [AnimalController::class, 'index']);
Route::get('/animaux/{id}', [AnimalController::class, 'show']);
Route::get('/elevages/{elevageId}/animaux', [AnimalController::class, 'getByElevage']);

// Tâches (lecture seule publique)
Route::get('/taches', [TacheController::class, 'index']);
Route::get('/taches/{id}', [TacheController::class, 'show']);
Route::get('/taches/calendar', [TacheController::class, 'calendar']);
Route::get('/elevages/{elevageId}/taches/stats', [TacheController::class, 'stats']);

// Publications (lecture seule publique)
Route::get('/publications', [PublicationController::class, 'index']);
Route::get('/publications/{id}', [PublicationController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Routes protégées (authentification requise)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api'])->group(function () {
    
    // ========== AUTHENTIFICATION ==========
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/toggle-2fa', [AuthController::class, 'toggleTwoFactor']);
        Route::put('/notification-preferences', [AuthController::class, 'updateNotificationPreferences']);
        Route::put('/profile-visibility', [AuthController::class, 'updateProfileVisibility']);
        Route::delete('/account', [AuthController::class, 'deleteAccount']);
    });
    
    // ========== UTILISATEUR (routes spécifiques) ==========
    Route::prefix('user')->group(function () {
        // Élevages
        Route::get('/elevages', [ElevageController::class, 'userElevages']);
        Route::get('/elevages/stats', [ElevageController::class, 'stats']);
        
        // Animaux
        Route::get('/animaux/stats', [AnimalController::class, 'userStats']);
        
        // Tâches
        Route::get('/taches', [TacheController::class, 'userTaches']);
    });
    
    // ========== CRUD ÉLEVAGES ==========
    Route::post('/elevages', [ElevageController::class, 'store']);
    Route::put('/elevages/{id}', [ElevageController::class, 'update']);
    Route::patch('/elevages/{id}', [ElevageController::class, 'update']);
    Route::delete('/elevages/{id}', [ElevageController::class, 'destroy']);
    
    // ========== CRUD ANIMAUX ==========
    Route::post('/animaux', [AnimalController::class, 'store']);
    Route::put('/animaux/{id}', [AnimalController::class, 'update']);
    Route::patch('/animaux/{id}', [AnimalController::class, 'update']);
    Route::delete('/animaux/{id}', [AnimalController::class, 'destroy']);
    Route::get('/animaux/{id}/historique', [AnimalController::class, 'historique']);
    
    // ========== CRUD TÂCHES ==========
    Route::post('/taches', [TacheController::class, 'store']);
    Route::put('/taches/{id}', [TacheController::class, 'update']);
    Route::patch('/taches/{id}', [TacheController::class, 'update']);
    Route::delete('/taches/{id}', [TacheController::class, 'destroy']);
    Route::post('/taches/{id}/complete', [TacheController::class, 'complete']);
    
    // ========== PUBLICATIONS ==========
    Route::prefix('publications')->group(function () {
        Route::post('/', [PublicationController::class, 'store']);
        Route::put('/{id}', [PublicationController::class, 'update']);
        Route::delete('/{id}', [PublicationController::class, 'destroy']);
        Route::post('/{id}/like', [PublicationController::class, 'toggleLike']);
        Route::post('/{id}/comments', [PublicationController::class, 'addComment']);
        Route::put('/comments/{id}', [PublicationController::class, 'updateComment']);
        Route::delete('/comments/{id}', [PublicationController::class, 'deleteComment']);
        Route::post('/{id}/report', [PublicationController::class, 'report']);
        Route::post('/{id}/share', [PublicationController::class, 'share']);
    });
});

/*
|--------------------------------------------------------------------------
| Routes ADMIN (authentification + rôle admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    
    // Publications
    Route::prefix('publications')->group(function () {
        Route::get('/', [PublicationController::class, 'adminIndex']);
        Route::post('/{id}/block', [PublicationController::class, 'adminBlock']);
        Route::post('/{id}/unblock', [PublicationController::class, 'adminUnblock']);
        Route::delete('/reports/{id}', [PublicationController::class, 'adminDeleteReport']);
    });
    
    // Dashboard admin (à compléter)
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Bienvenue administrateur']);
    });
});

/*
|--------------------------------------------------------------------------
| Route fallback pour 404
|--------------------------------------------------------------------------
*/

// Routes publiques (sans authentification)
Route::prefix('publications')->group(function () {
    Route::get('/', [PublicationController::class, 'index']);
    Route::get('/{id}', [PublicationController::class, 'show']);
});

// Routes protégées (authentification requise)
Route::middleware(['auth:api'])->prefix('publications')->group(function () {
    // CRUD
    Route::post('/', [PublicationController::class, 'store']);
    Route::put('/{id}', [PublicationController::class, 'update']);
    Route::delete('/{id}', [PublicationController::class, 'destroy']);
    
    // Likes
    Route::post('/{id}/like', [PublicationController::class, 'toggleLike']);
    
    // Commentaires
    Route::post('/{id}/comments', [PublicationController::class, 'addComment']);
    Route::put('/comments/{id}', [PublicationController::class, 'updateComment']);
    Route::delete('/comments/{id}', [PublicationController::class, 'deleteComment']);
    
    // Signalements
    Route::post('/{id}/report', [PublicationController::class, 'report']);
    
    // Partages
    Route::post('/{id}/share', [PublicationController::class, 'share']);
});

// Routes admin (authentification + rôle admin)
Route::middleware(['auth:api', 'admin'])->prefix('admin/publications')->group(function () {
    Route::get('/', [PublicationController::class, 'adminIndex']);
    Route::post('/{id}/block', [PublicationController::class, 'adminBlock']);
    Route::post('/{id}/unblock', [PublicationController::class, 'adminUnblock']);
    Route::delete('/reports/{id}', [PublicationController::class, 'adminDeleteReport']);
});

// Routes protégées
Route::middleware(['auth:api'])->group(function () {

    Route::apiResource('elevages', ElevageController::class)
        ->except(['index', 'show']);

    Route::apiResource('animaux', AnimalController::class)
        ->except(['index', 'show']);

});


/*
|--------------------------------------------------------------------------
| API Routes - Produits et Stocks
|--------------------------------------------------------------------------
*/

// Routes protégées (authentification requise)
Route::middleware(['auth:api'])->prefix('stock')->group(function () {
    
    // Produits
    Route::prefix('produits')->group(function () {
        Route::get('/', [ProduitController::class, 'index']);
        Route::post('/', [ProduitController::class, 'store']);
        Route::get('/critiques', [ProduitController::class, 'produitsCritiques']);
        Route::get('/rupture', [ProduitController::class, 'produitsRupture']);
        Route::get('/statistiques', [ProduitController::class, 'statistiques']);
        Route::get('/{id}', [ProduitController::class, 'show']);
        Route::put('/{id}', [ProduitController::class, 'update']);
        Route::delete('/{id}', [ProduitController::class, 'destroy']);
    });
    
    // Mouvements de stock
    Route::prefix('mouvements')->group(function () {
        Route::get('/', [StockController::class, 'historique']);
        Route::post('/{produitId}/entree', [StockController::class, 'addStock']);
        Route::post('/{produitId}/sortie', [StockController::class, 'removeStock']);
    });
    
    // Rapports
    Route::get('/rapport', [StockController::class, 'rapport']);
    Route::fallback(function () {
        return response()->json([
            'success' => false,
            'message' => 'Route non trouvée'
        ], 404);
    }); 
});