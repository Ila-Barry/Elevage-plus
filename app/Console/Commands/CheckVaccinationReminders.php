<?php
// app/Console/Commands/CheckVaccinationReminders.php

namespace App\Console\Commands;

use App\Models\Tache;
use App\Events\VaccinationDue;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckVaccinationReminders extends Command
{
    protected $signature = 'alerts:check-vaccinations';
    protected $description = 'Vérifie et envoie les rappels de vaccination';

    public function handle(): int
    {
        $this->info('Vérification des rappels de vaccination...');
        
        $count = 0;
        
        // Récupérer les tâches de vaccination à venir dans les 3 jours
        $taches = Tache::where('type', 'vaccination')
            ->where('terminee', false)
            ->whereBetween('date_planifiee', [now(), now()->addDays(3)])
            ->with('animal')
            ->get();
        
        foreach ($taches as $tache) {
            if ($tache->animal) {
                event(new VaccinationDue($tache->animal, $tache));
                $count++;
                $this->line("Rappel vaccination envoyé pour: {$tache->animal->nom}");
            }
        }
        
        $this->info("✅ {$count} rappel(s) de vaccination envoyé(s).");
        
        return Command::SUCCESS;
    }
}