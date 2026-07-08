<?php
// app/Console/Commands/CheckAnimalHealthAlerts.php

namespace App\Console\Commands;

use App\Models\Animal;
use App\Notifications\AnimalNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckAnimalHealthAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'animals:check-health';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les alertes sanitaires des animaux';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Vérification des alertes sanitaires...');
        
        $animals = Animal::where('statut', 'actif')
            ->whereIn('statut_sanitaire', ['malade', 'critique'])
            ->with('elevage.user')
            ->get();
        
        $count = 0;
        
        foreach ($animals as $animal) {
            $user = $animal->elevage?->user;
            if ($user) {
                try {
                    $user->notify(new AnimalNotification($animal, 'health_alert', [
                        'status' => $animal->statut_sanitaire
                    ]));
                    $count++;
                    $this->info("✅ Alerte envoyée pour: {$animal->nom}");
                } catch (\Exception $e) {
                    $this->error("❌ Erreur pour {$animal->nom}: " . $e->getMessage());
                }
            }
        }
        
        Log::info('Vérification des alertes sanitaires terminée', [
            'animals_checked' => $animals->count(),
            'notifications_sent' => $count
        ]);
        
        $this->info("✅ Vérification terminée: {$count} notifications envoyées sur {$animals->count()} animaux.");
        
        return 0;
    }
}