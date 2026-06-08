<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Models\Elevage;
use App\Observers\ElevageObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Elevage::observe(ElevageObserver::class);
    }
}