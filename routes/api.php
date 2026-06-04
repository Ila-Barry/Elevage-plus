<?php
// routes/api.php - À compléter avec les routes d'élevage

use App\Http\Controllers\Api\ElevageController;

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