<?php
// app/Http/Controllers/Web/AuthController.php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Déconnexion depuis le web
     */
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Déconnecter de la session web
            Auth::guard('web')->logout();
            
            // Invalider le token JWT s'il existe
            try {
                $token = JWTAuth::getToken();
                if ($token) {
                    JWTAuth::invalidate($token);
                }
            } catch (\Exception $e) {
                Log::warning('Erreur lors de l\'invalidation JWT', ['message' => $e->getMessage()]);
            }
            
            // Nettoyer la session
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            Log::info('Déconnexion web réussie', ['user_id' => $user?->id]);
            
            return redirect('/auth/login')->with('success', 'Vous êtes déconnecté.');
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la déconnexion web', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect('/auth/login')->with('error', 'Erreur lors de la déconnexion.');
        }
    }
}