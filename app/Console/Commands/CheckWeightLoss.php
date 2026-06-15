<?php
// app/Console/Commands/CheckWeightLoss.php

namespace App\Console\Commands;

use App\Models\Animal;
use App\Events\WeightLossDetected;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckWeightLoss extends Command
{
    protected $signature = 'alerts:check-weight-loss';
    protected $description = 'Vérifie et envoie les alertes de perte de poids suspecte';

    public function handle(): int
    {
        $this->info('Vérification des pertes de poids...');
        
        $count = 0;
        
        // Récupérer les animaux avec une perte de poids de plus de 10% en 15 jours
        $animaux = Animal::where('statut', 'actif')
            ->whereHas('historiques', function ($query) {
                $query->where('champ_modifie', 'poids')
                    ->where('created_at', '>', now()->subDays(15));
            })
            ->get();
        
        foreach ($animaux as $animal) {
            $historique = $animal->historiques()
                ->where('champ_modifie', 'poids')
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($historique) {
                $ancienPoids = (float) $historique->ancienne_valeur;
                $nouveauPoids = (float) $historique->nouvelle_valeur;
                
                if ($ancienPoids > 0 && $nouveauPoids > 0) {
                    $perte = (($ancienPoids - $nouveauPoids) / $ancienPoids) * 100;
                    
                    if ($perte >= 10) {
                        event(new WeightLossDetected($animal, $ancienPoids, $nouveauPoids));
                        $count++;
                        $this->line("Alerte perte de poids pour: {$animal->nom} ({$perte}%)");
                    }
                }
            }
        }
        
        $this->info("✅ {$count} alerte(s) perte de poids envoyée(s).");
        
        return Command::SUCCESS;
    }
}