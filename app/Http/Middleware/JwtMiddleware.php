<?php
// app/Http/Middleware/JwtMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Traits\ApiResponseTrait;

class JwtMiddleware extends BaseMiddleware
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return $this->unauthorizedResponse('Token expiré. Veuillez vous reconnecter.');
        } catch (TokenInvalidException $e) {
            return $this->unauthorizedResponse('Token invalide.');
        } catch (JWTException $e) {
            return $this->unauthorizedResponse('Token manquant.');
        }

        if (!$user) {
            return $this->unauthorizedResponse('Utilisateur non trouvé.');
        }

        // Vérifier si l'utilisateur est banni
        if ($user->isBanned()) {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->forbiddenResponse('Votre compte a été banni.');
        }

        return $next($request);
    }
}