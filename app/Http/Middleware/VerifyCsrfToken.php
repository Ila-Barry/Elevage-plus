<?php
// app/Http/Middleware/VerifyCsrfToken.php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        // Exclure les routes API de la protection CSRF
        'api/*',
        'login',
        'register',
        'logout',
        'password/*',
    ];
}