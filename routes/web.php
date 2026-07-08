<?php

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
Route::get('/login', function () {
    return view('auth/login');
})->name('login');

Route::get('/auth/login', function () {
    return view('auth/login');
})->name('web.login');

Route::get('/auth/register', function () {
    return view('auth/register');
})->name('web.register');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', function () {
    return redirect('/auth/login');
})->name('logout.get');

Route::get('/auth/verify-2fa', function () {
    return view('auth/verify-2fa');
})->name('verify.2fa');

// ========== ROUTES PROTÉGÉES (AUTHENTIFIÉ) ==========
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/auth/profile', function () {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('web.login');
        }
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
            return redirect()->route('web.login');
        }
        $stats = [
            'publications' => $user->publications()->count(),
            'likes_received' => $user->publications()->sum('nbr_likes') ?? 0,
            'commentaires' => $user->commentaires()->count(),
            'elevages' => $user->elevages()->count(),
        ];
        return view('auth/parametre', compact('user', 'stats'));
    });
    
    // ========== ROUTES PRINCIPALES ==========
    Route::get('/elevages', function () {
        return view('elevages');
    });
    
    // ✅ ROUTE POUR VOIR UN ÉLEVAGE SPÉCIFIQUE
    Route::get('/elevages/{id}', function ($id) {
        return view('elevages', compact('id'));
    })->name('elevages.show');
    
    Route::get('/animaux', function () {
        return view('animaux');
    });
    
    // ✅ ROUTE POUR VOIR UN ANIMAL SPÉCIFIQUE
    Route::get('/animaux/{id}', function ($id) {
        return view('animaux', compact('id'));
    })->name('animaux.show');
    
    Route::get('/taches', function () {
        return view('taches');
    });
    
    // ✅ ROUTE POUR VOIR UNE TÂCHE SPÉCIFIQUE
    Route::get('/taches/{id}', function ($id) {
        return view('taches', compact('id'));
    })->name('taches.show');
    
    Route::get('/stocks', function () {
        return view('stocks');
    });
    
    // ✅ ROUTE POUR VOIR UN PRODUIT SPÉCIFIQUE
    Route::get('/stocks/{id}', function ($id) {
        return view('stocks', compact('id'));
    })->name('stocks.show');
    
    Route::get('/blog', function () {
        return view('blog');
    });
    
    // ✅ ROUTE POUR VOIR UN ARTICLE SPÉCIFIQUE
    Route::get('/blog/{id}', function ($id) {
        return view('blog', compact('id'));
    })->name('blog.show');
    
    Route::get('/messages', function () {
        return view('messages');
    });
    
    // ✅ ROUTE POUR VOIR UNE CONVERSATION SPÉCIFIQUE
    Route::get('/messagerie', function () {
        return view('messages');
    })->name('messagerie');
    
    Route::get('/notification', function () {
        return view('notification');
    })->name('notifications');
}); // Fin du groupe auth

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
    
    Route::get('/messages/{id}', function ($id) {
        return view('admin/messages', compact('id'));
    })->name('admin.messages');
    
    Route::get('/statistique', function () {
        return view('admin/statistique');
    });
}); // Fin du groupe admin