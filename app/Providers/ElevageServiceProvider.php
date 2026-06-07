<?php
// app/Providers/ElevageServiceProvider.php

namespace App\Providers;

use App\Models\Elevage;
use App\Policies\ElevagePolicy;
use App\Services\ElevageService;
use Illuminate\Support\ServiceProvider;

class ElevageServiceProvider extends ServiceProvider
{
    /**
     * Enregistrement des services
     */
    public function register(): void
    {
        $this->app->singleton(ElevageService::class, function ($app) {
            return new ElevageService();
        });
    }

    /**
     * Bootstrap des services
     */
    public function boot(): void
    {
        // Enregistrement de la politique
        \Gate::policy(Elevage::class, ElevagePolicy::class);
    }
}