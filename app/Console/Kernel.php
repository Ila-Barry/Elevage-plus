<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\SendTacheRappels::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Envoi des rappels toutes les minutes
        $schedule->command('tache:send-rappels')->everyMinute();
        
        // Régénération des rappels chaque nuit (sécurité)
        $schedule->command('tache:regenerate-rappels')->dailyAt('01:00');
    }
}