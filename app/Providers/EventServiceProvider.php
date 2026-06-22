<?php
// app/Providers/EventServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Animal;
use App\Models\Publication;
use App\Models\Tache;
use App\Models\Produit;
use App\Observers\DashboardCacheObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Enregistrer les observateurs pour invalider le cache du dashboard
        Animal::observe(DashboardCacheObserver::class);
        Publication::observe(DashboardCacheObserver::class);
        Tache::observe(DashboardCacheObserver::class);
        Produit::observe(DashboardCacheObserver::class);
    }
}