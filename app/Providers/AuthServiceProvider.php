<?php
// app/Providers/AuthServiceProvider.php

namespace App\Providers;

use App\Models\Animal;
use App\Models\Elevage;
use App\Policies\AnimalPolicy;
use App\Policies\ElevagePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Elevage::class => ElevagePolicy::class,
        Animal::class => AnimalPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}