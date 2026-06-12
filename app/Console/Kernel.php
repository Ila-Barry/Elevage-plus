<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Vérification des rappels de vaccination - toutes les 6 heures
        $schedule->command('alerts:check-vaccinations')->everySixHours();
        
        // Vérification des stocks critiques - toutes les 4 heures
        $schedule->command('alerts:check-stock')->everyFourHours();
        
        // Vérification des pertes de poids - une fois par jour
        $schedule->command('alerts:check-weight-loss')->daily();
        
        // Envoi des rappels de tâches - toutes les 15 minutes
        $schedule->command('task:send-reminders')->everyFifteenMinutes();
        
        // Nettoyage des anciennes notifications (30 jours) - tous les jours
        $schedule->command('notifications:clean --days=30')->daily();
    }
}