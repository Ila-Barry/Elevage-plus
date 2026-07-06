<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        \App\Console\Commands\CheckAnimalHealthAlerts::class,
        \App\Console\Commands\CheckAnimalWeightLoss::class,
        \App\Console\Commands\CheckTaskReminders::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Vérification des rappels de vaccination - toutes les 6 heures
        $schedule->command('alerts:check-vaccinations')->everySixHours();
        
        // Vérification des stocks critiques - toutes les 4 heures
        $schedule->command('alerts:check-stock')->everyFourHours();
        
        // Vérification des pertes de poids - une fois par jour
        $schedule->command('alerts:check-weight-loss')->daily();
        
        // Nettoyage des anciennes notifications (30 jours) - tous les jours
        $schedule->command('notifications:clean --days=30')->daily();

        // Vérifier les alertes sanitaires toutes les 6 heures
        $schedule->command('animals:check-health')->everySixHours();
        
        // Vérifier les pertes de poids tous les jours
        $schedule->command('animals:check-weight-loss')->daily();

        // Vérifier les rappels toutes les 15 minutes
        $schedule->command('tasks:check-reminders')->everyFifteenMinutes();
    }
}