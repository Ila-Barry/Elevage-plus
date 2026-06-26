<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ProfileController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========== ROUTES PUBLIQUES ==========
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/profilEleveur/{id}', [ProfileController::class, 'show'])->name('profile.show');

// ========== ROUTES D'AUTHENTIFICATION ==========
// ✅ CHANGEMENT ICI : Renommer pour éviter le conflit
Route::get('/auth/login', function () {
    return view('auth/login');
})->name('web.login'); // ✅ Nom unique

Route::get('/auth/register', function () {
    return view('auth/register');
})->name('web.register'); // ✅ Nom unique

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', function () {
    return redirect('/auth/login');
})->name('logout.get');

Route::get('/auth/verify-2fa', function () {
    return view('auth/verify-2fa');
})->name('verify.2fa');

// ========== ROUTES PROTÉGÉES (AUTHENTIFIÉ) ==========
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Éleveur
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/auth/profile', function () {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('web.login'); // ✅ Utiliser le nouveau nom
        }
        
        // Statistiques
        $stats = [
            'publications' => $user->publications()->count(),
            'likes_received' => $user->publications()->sum('nbr_likes') ?? 0,
            'commentaires' => $user->commentaires()->count(),
            'elevages' => $user->elevages()->count(),
            'animaux' => $user->elevages()->withCount('animaux')->get()->sum('animaux_count') ?? 0,
        ];
        
        return view('auth/profile', compact('user', 'stats'));
    });
    
    Route::get('/auth/parametre', function () {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('web.login'); // ✅ Utiliser le nouveau nom
        }
        
        // Récupérer les statistiques
        $stats = [
            'publications' => $user->publications()->count(),
            'likes_received' => $user->publications()->sum('nbr_likes') ?? 0,
            'commentaires' => $user->commentaires()->count(),
            'elevages' => $user->elevages()->count(),
        ];
        
        return view('auth/parametre', compact('user', 'stats'));
    });
    
    // Gestion
    Route::get('/elevages', function () {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('web.login');
        }
        
        return view('elevages', compact('user'));
    });
    
    Route::get('/animaux', function () {
        return view('animaux');
    });
    
    Route::get('/taches', function () {
        return view('taches');
    });
    
    Route::get('/stocks', function () {
        return view('stocks');
    });
    
    // Communauté
    Route::get('/blog', function () {
        return view('blog');
    });
    
    Route::get('/messages', function () {
        return view('messages');
    });
    
    Route::get('/notification', function () {
        return view('notification');
    });
});

// ========== ROUTES ADMIN (AUTHENTIFIÉ + ADMIN) ==========
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin/dashboard');
    })->name('admin.dashboard');
    
    Route::get('/utilisateur', function () {
        return view('admin/utilisateur');
    });
    
    Route::get('/publication', function () {
        return view('admin/publication');
    });
    
    Route::get('/signale', function () {
        return view('admin/signale');
    });
    
    Route::get('/statistique', function () {
        return view('admin/statistique');
    });
});

// ========== VÉRIFICATION D'EMAIL ==========
Route::get('/verify-email', function (Request $request) {
    $verifyUrl = $request->query('verify_url');
    
    if (!$verifyUrl) {
        return view('verify-email', [
            'error' => 'Lien de vérification manquant.',
            'success' => false
        ]);
    }

    $verifyUrl = urldecode($verifyUrl);
    
    return view('verify-email', [
        'verify_url' => $verifyUrl,
        'error' => null,
        'success' => true
    ]);
})->name('verify.email');