<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LogService
{
    public static function auth(string $event, array $data = []): void
    {
        Log::channel('auth')->info($event, array_merge([
            'user_id' => Auth::id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString()
        ], $data));
    }

    public static function security(string $event, array $data = []): void
    {
        Log::channel('security')->warning($event, array_merge([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString()
        ], $data));
    }

    public static function api(string $method, string $uri, array $data = []): void
    {
        Log::channel('api')->info("API Request: {$method} {$uri}", array_merge([
            'user_id' => Auth::id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString()
        ], $data));
    }

    public static function database(string $query, array $data = []): void
    {
        Log::channel('database')->info($query, array_merge([
            'timestamp' => now()->toDateTimeString()
        ], $data));
    }

    public static function loginAttempt(string $email, bool $success, array $data = []): void
    {
        self::auth('Tentative de connexion', array_merge([
            'email' => $email,
            'success' => $success
        ], $data));

        if (!$success) {
            self::security('Tentative de connexion échouée', [
                'email' => $email,
                'ip' => request()->ip()
            ]);
        }
    }

    public static function suspiciousActivity(string $activity, array $data = []): void
    {
        self::security('Activité suspecte: ' . $activity, $data);
    }
}