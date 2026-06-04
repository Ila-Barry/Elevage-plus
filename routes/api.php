<?php
// routes/api.php - À compléter avec les routes d'élevage

use App\Http\Controllers\Api\ElevageController;
use App\Http\Controllers\Api\AnimalController;

/*
|--------------------------------------------------------------------------
| Routes protégées (authentification requise)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api'])->group(function () {
    // Routes pour l'utilisateur connecté
    Route::prefix('user')->group(function () {
        Route::get('/elevages', [ElevageController::class, 'userElevages']);
        Route::get('/elevages/stats', [ElevageController::class, 'stats']);
    });
});

/*
|--------------------------------------------------------------------------
| Routes CRUD pour les élevages
|--------------------------------------------------------------------------
*/

// Routes publiques (lecture seule)
Route::get('/elevages', [ElevageController::class, 'index']);
Route::get('/elevages/{id}', [ElevageController::class, 'show']);

// Routes protégées (CRUD complet)
Route::middleware(['auth:api'])->group(function () {
    Route::post('/elevages', [ElevageController::class, 'store']);
    Route::put('/elevages/{id}', [ElevageController::class, 'update']);
    Route::patch('/elevages/{id}', [ElevageController::class, 'update']);
    Route::delete('/elevages/{id}', [ElevageController::class, 'destroy']);
});

// routes/api.php - Ajouter les routes pour les animaux

/*
|--------------------------------------------------------------------------
| Routes publiques (lecture seule)
|--------------------------------------------------------------------------
*/
Route::get('/animaux', [AnimalController::class, 'index']);
Route::get('/animaux/{id}', [AnimalController::class, 'show']);
Route::get('/elevages/{elevageId}/animaux', [AnimalController::class, 'getByElevage']);

/*
|--------------------------------------------------------------------------
| Routes protégées (authentification requise)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api'])->group(function () {
    // CRUD Animaux
    Route::post('/animaux', [AnimalController::class, 'store']);
    Route::put('/animaux/{id}', [AnimalController::class, 'update']);
    Route::patch('/animaux/{id}', [AnimalController::class, 'update']);
    Route::delete('/animaux/{id}', [AnimalController::class, 'destroy']);
    
    // Historique
    Route::get('/animaux/{id}/historique', [AnimalController::class, 'historique']);
    
    // Statistiques utilisateur
    Route::get('/user/animaux/stats', [AnimalController::class, 'userStats']);
});
?>