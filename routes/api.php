<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\ProduitController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\ElevageController;
use App\Http\Controllers\Api\AnimalController;



/*
|--------------------------------------------------------------------------
| API Routes - Authentification
|--------------------------------------------------------------------------
*/

// gere la redirection vers la route de login pour les utilisateurs non authentifiés
Route::get('/login', function () {
    return response()->json([
        'success' => false,
        'message' => 'Non authentifié. Veuillez vous connecter.'
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


/*
|--------------------------------------------------------------------------
| API Routes - Publications
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
});


/*
|--------------------------------------------------------------------------
| API Routes - Élevages
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api'])->prefix('elevages')->group(function () {
    // CRUD de base
    Route::get('/', [ElevageController::class, 'index']);
    Route::post('/', [ElevageController::class, 'store']);
    Route::get('/statistiques', [ElevageController::class, 'statistiques']);
    Route::get('/{id}', [ElevageController::class, 'show']);
    Route::put('/{id}', [ElevageController::class, 'update']);
    Route::delete('/{id}', [ElevageController::class, 'destroy']);
    
    // Actions spécifiques
    Route::patch('/{id}/statut', [ElevageController::class, 'changeStatut']);
});


/*
|--------------------------------------------------------------------------
| API Routes - Animaux
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api'])->prefix('animaux')->group(function () {
    // CRUD de base
    Route::get('/', [AnimalController::class, 'index']);
    Route::post('/', [AnimalController::class, 'store']);
    Route::get('/statistiques', [AnimalController::class, 'statistiques']);
    Route::get('/{id}', [AnimalController::class, 'show']);
    Route::put('/{id}', [AnimalController::class, 'update']);
    Route::delete('/{id}', [AnimalController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| API Routes - Messagerie
|--------------------------------------------------------------------------
*/

// Routes pour la messagerie (authentification requise)
Route::middleware(['auth:api'])->prefix('messaging')->group(function () {
    // Conversations
    Route::get('/conversations', [App\Http\Controllers\Api\MessageController::class, 'getConversations']);
    Route::get('/conversations/{conversationId}/messages', [App\Http\Controllers\Api\MessageController::class, 'getMessages']);
    Route::post('/conversations/{conversationId}/read', [App\Http\Controllers\Api\MessageController::class, 'markConversationAsRead']);
    
    // Messages
    Route::post('/send', [App\Http\Controllers\Api\MessageController::class, 'sendMessage']);
    Route::delete('/messages/{messageId}', [App\Http\Controllers\Api\MessageController::class, 'deleteMessage']);
    
    // Utilitaires
    Route::get('/unread-count', [App\Http\Controllers\Api\MessageController::class, 'getUnreadCount']);
});