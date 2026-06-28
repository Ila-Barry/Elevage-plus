<?php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseTrait;

/**
 * Middleware AdminMiddleware
 * 
 * Vérifie que l'utilisateur authentifié a le rôle 'admin'
 */
class AdminMiddleware
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return $this->unauthorizedResponse('Authentification requise.');
        }
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Vérifier si l'utilisateur a le rôle admin
        if (!$user->isAdmin()) {
            return $this->forbiddenResponse('Accès réservé aux administrateurs.');
        }
        
        return $next($request);
    }
}