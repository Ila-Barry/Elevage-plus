<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\ProduitController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\ElevageController;
use App\Http\Controllers\Api\AnimalController;
use App\Http\Controllers\Api\TacheController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;

// api admin controllers
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Admin\AdminPublicationController;
use App\Http\Controllers\Api\Admin\AdminReportController;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminNewsletterController;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationNotification;
use App\Http\Controllers\Api\WebPushController;


/*
|--------------------------------------------------------------------------
| API Routes - Home (Public)
|--------------------------------------------------------------------------
*/

// Routes Home (publiques)
Route::prefix('home')->group(function () {
    Route::get('/posts', [HomeController::class, 'getPosts']);
    Route::get('/stats', [HomeController::class, 'getStats']);
});

Route::prefix('profile')->group(function () {
    Route::get('/{id}', [ProfileController::class, 'apiShow']);
    Route::get('/{id}/posts', [ProfileController::class, 'apiPosts']);
    Route::post('/{id}/follow', [ProfileController::class, 'toggleFollow'])->middleware('auth:api');
});

/*
|--------------------------------------------------------------------------
| API Routes - Authentification
|--------------------------------------------------------------------------
*/
// ✅ CHANGEMENT ICI : Renommer la route pour éviter le conflit
Route::get('/unauthorized', function () {
    return response()->json([
        'success' => false,
        'message' => 'Non authentifié. Veuillez vous connecter.'
    ], 401);
})->name('api.unauthorized'); // ✅ Nom unique

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
    Route::get('/users', [AuthController::class, 'getUsers'])->name('api.auth.users');
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
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Bienvenue administrateur']);
    });
});

/*
|--------------------------------------------------------------------------
| API Routes - Publications
|--------------------------------------------------------------------------
*/

Route::prefix('publications')->group(function () {
    Route::get('/', [PublicationController::class, 'index']);
    Route::get('/{id}', [PublicationController::class, 'show']);
});

// Routes protégées (authentification requise)
Route::middleware(['auth:api'])->prefix('publications')->group(function () {
    Route::post('/', [PublicationController::class, 'store']);
    Route::put('/{id}', [PublicationController::class, 'update']);
    Route::delete('/{id}', [PublicationController::class, 'destroy']);
    
     // ✅ Likes
    Route::post('/{id}/like', [PublicationController::class, 'toggleLike']);
    Route::get('/{id}/likes', [PublicationController::class, 'getLikes']);
    Route::get('/{id}/check-like', [PublicationController::class, 'checkLike']);
    
    // Commentaires
    Route::get('/{id}/comments', [PublicationController::class, 'getComments']);
    Route::post('/{id}/comments', [PublicationController::class, 'addComment']);
    Route::delete('/comments/{id}', [PublicationController::class, 'deleteComment']);
    
    // Signalements
    Route::post('/{id}/report', [PublicationController::class, 'report']);
    
   // ✅ Partages
    Route::post('/{id}/share', [PublicationController::class, 'share']);
    Route::get('/{id}/shares', [PublicationController::class, 'getShares']);
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
    Route::get('/all', [AnimalController::class, 'getAll']);
    // ✅ Route pour vérifier les alertes sanitaires (peut être appelée par cron)
    Route::post('/check-health-alerts', [AnimalController::class, 'checkHealthAlerts']);
});

/*
|--------------------------------------------------------------------------
| API Routes - Tâches
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api'])->prefix('taches')->group(function () {
    // CRUD de base
    Route::get('/', [TacheController::class, 'index']);
    Route::get('/calendar', [TacheController::class, 'calendar']);
    Route::get('/statistiques', [TacheController::class, 'statistiques']);
    Route::post('/', [TacheController::class, 'store']);
    Route::get('/{id}', [TacheController::class, 'show']);
    Route::put('/{id}', [TacheController::class, 'update']);
    Route::delete('/{id}', [TacheController::class, 'destroy']);
    
    // Actions spécifiques
    Route::patch('/{id}/complete', [TacheController::class, 'complete']);
});

/*
|--------------------------------------------------------------------------
| API Routes - Messagerie
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api'])->prefix('messaging')->group(function () {
    Route::get('/users', [MessageController::class, 'getAllUsersForMessaging']); 
    Route::get('/conversations', [MessageController::class, 'getConversations']);
    Route::get('/conversations/{conversationId}/messages', [MessageController::class, 'getMessages']);
    Route::post('/conversations/{conversationId}/read', [MessageController::class, 'markConversationAsRead']);
    Route::post('/send', [MessageController::class, 'sendMessage']);
    Route::delete('/messages/{messageId}', [MessageController::class, 'deleteMessage']);
    Route::post('/upload-media', [MessageController::class, 'uploadMedia']);
    Route::get('/stickers', [MessageController::class, 'getAvailableStickers']);
    Route::get('/unread-count', [MessageController::class, 'getUnreadCount']);
});

// ============================================================
// ROUTES DE NOTIFICATIONS
// ============================================================

Route::middleware(['auth:api'])->prefix('notifications')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::get('/unread', [App\Http\Controllers\Api\NotificationController::class, 'unread']);
    Route::get('/stats', [App\Http\Controllers\Api\NotificationController::class, 'stats']);
    Route::post('/{id}/read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::post('/read-all', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
    Route::delete('/{id}', [App\Http\Controllers\Api\NotificationController::class, 'destroy']);
    Route::delete('/', [App\Http\Controllers\Api\NotificationController::class, 'destroyAll']);
    Route::get('/by-type/{type}', [App\Http\Controllers\Api\NotificationController::class, 'byType']);
});

/*
|--------------------------------------------------------------------------
| API Routes - Administration
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    
    // Dashboard et Statistiques
    Route::prefix('dashboard')->group(function () {
        Route::get('/kpis', [AdminDashboardController::class, 'kpis']);
        Route::get('/evolution', [AdminDashboardController::class, 'evolution']);
        Route::get('/repartition', [AdminDashboardController::class, 'repartition']);
        Route::get('/activites-recentes', [AdminDashboardController::class, 'activitesRecentes']);
    });
    
    Route::prefix('stats')->group(function () {
        Route::get('/engagement', [AdminDashboardController::class, 'engagement']);
        Route::get('/rapport-mensuel', [AdminDashboardController::class, 'rapportMensuel']);
    });
    
    // Gestion des Utilisateurs
    Route::prefix('users')->group(function () {
        Route::get('/', [AdminUserController::class, 'index']);
        Route::get('/export/csv', [AdminUserController::class, 'exportCSV']);
        Route::get('/{id}', [AdminUserController::class, 'show']);
        Route::put('/{id}', [AdminUserController::class, 'update']);
        Route::patch('/{id}/status', [AdminUserController::class, 'changeStatus']);
        Route::post('/{id}/reset-password', [AdminUserController::class, 'resetPassword']);
        Route::delete('/{id}', [AdminUserController::class, 'destroy']);
    });
    
    // Gestion des Publications
    Route::prefix('publications')->group(function () {
        Route::get('/', [AdminPublicationController::class, 'index']);
        Route::get('/{id}', [AdminPublicationController::class, 'show']);
        Route::put('/{id}', [AdminPublicationController::class, 'update']);
        Route::patch('/{id}/status', [AdminPublicationController::class, 'changeStatus']);
        Route::post('/{id}/review', [AdminPublicationController::class, 'review']);
        Route::delete('/{id}', [AdminPublicationController::class, 'destroy']);
    });
    
    // Gestion des Signalements
    Route::prefix('reports')->group(function () {
        Route::get('/', [AdminReportController::class, 'index']);
        Route::get('/{id}', [AdminReportController::class, 'show']);
        Route::post('/{id}/handle', [AdminReportController::class, 'handle']);
        Route::post('/{id}/ignore', [AdminReportController::class, 'ignore']);
    });
    
    // Newsletter
    Route::prefix('newsletter')->group(function () {
        Route::post('/send', [AdminNewsletterController::class, 'send']);
    });
});

// Route pour renvoyer l'email de vérification
Route::post('/email/resend', function (Request $request) {
    $user = $request->user();
    
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Non authentifié.'
        ], 401);
    }
    
    if ($user->email_verified_at) {
        return response()->json([
            'status' => 'error',
            'message' => 'Email déjà vérifié.'
        ], 422);
    }
    
    $user->sendEmailVerificationNotification();
    
    return response()->json([
        'status' => 'success',
        'message' => 'Email de vérification renvoyé avec succès.'
    ]);
})->middleware('auth:api')->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    try {
        \Log::info('Tentative de vérification email', ['user_id' => $id, 'hash' => $hash]);
        
        $user = User::findOrFail($id);
        \Log::info('Utilisateur trouvé', ['user_id' => $user->id, 'email' => $user->email]);
        
        $expectedHash = sha1($user->getEmailForVerification());
        \Log::info('Validation du hash', ['provided_hash' => $hash, 'expected_hash' => $expectedHash]);
        
        if (!hash_equals($expectedHash, $hash)) {
            \Log::warning('Hash invalide pour vérification email', ['user_id' => $id]);
            return response()->json([
                'status' => 'error',
                'message' => 'Lien de vérification invalide.'
            ], 403);
        }
        
        $user->email_verified_at = now();
        $user->status = 'active';
        $user->save();
        
        \Log::info('Email vérifié avec succès', ['user_id' => $user->id, 'email' => $user->email]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Email vérifié avec succès. Votre compte est maintenant actif.'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Erreur vérification email: ' . $e->getMessage(), ['exception' => $e]);
        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors de la vérification.'
        ], 500);
    }
})->middleware(['signed'])->name('verification.verify');

/*
|--------------------------------------------------------------------------
| API Routes - Dashboard et Statistiques
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api'])->prefix('dashboard')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\DashboardController::class, 'index']);
    Route::get('/charts', [App\Http\Controllers\Api\DashboardController::class, 'chartData']);
    Route::post('/refresh-cache', [App\Http\Controllers\Api\DashboardController::class, 'refreshCache']);
});

Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard/stats', [App\Http\Controllers\Api\DashboardController::class, 'adminStats']);
});



/*
|--------------------------------------------------------------------------
| API Routes - notifications
|--------------------------------------------------------------------------
*/
Route::post('/webpush/subscribe', [WebPushController::class, 'subscribe'])->middleware('auth:api');
Route::post('/webpush/unsubscribe', [WebPushController::class, 'unsubscribe'])->middleware('auth:api');
Route::post('/webpush/update-subscription', [WebPushController::class, 'update'])->middleware('auth:api');