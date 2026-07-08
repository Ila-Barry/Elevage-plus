<?php
// app/Console/Commands/CheckAnimalWeightLoss.php

namespace App\Console\Commands;

use App\Models\Animal;
use App\Models\AnimalHistorique;
use App\Notifications\AnimalNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckAnimalWeightLoss extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'animals:check-weight-loss';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les pertes de poids des animaux';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Vérification des pertes de poids...');
        
        // Récupérer les animaux actifs
        $animals = Animal::where('statut', 'actif')->with('elevage.user')->get();
        $count = 0;
        
        foreach ($animals as $animal) {
            // ✅ CORRECTION : Utiliser 'champ_modifie' au lieu de 'champ'
            $historique = AnimalHistorique::where('animal_id', $animal->id)
                ->where('champ_modifie', 'poids')  // ← Correction ici
                ->where('action', 'update')        // ← Ajout pour éviter les doublons
                ->where('created_at', '>=', now()->subDays(15))
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($historique) {
                $oldWeight = (float) $historique->ancienne_valeur;
                $currentWeight = $animal->poids;
                
                if ($oldWeight > 0) {
                    $lossPercent = (($oldWeight - $currentWeight) / $oldWeight) * 100;
                    
                    if ($lossPercent >= 10) {
                        $user = $animal->elevage?->user;
                        if ($user) {
                            try {
                                $user->notify(new AnimalNotification($animal, 'weight_alert', [
                                    'old_weight' => $oldWeight,
                                    'new_weight' => $currentWeight
                                ]));
                                $count++;
                                $this->info("⚠️ Alerte perte de poids pour: {$animal->nom} ({$lossPercent}%)");
                            } catch (\Exception $e) {
                                $this->error("❌ Erreur pour {$animal->nom}: " . $e->getMessage());
                            }
                        }
                    }
                }
            }
        }
        
        Log::info('Vérification des pertes de poids terminée', [
            'animals_checked' => $animals->count(),
            'alerts_sent' => $count
        ]);
        
        $this->info("✅ Vérification terminée: {$count} alertes envoyées sur {$animals->count()} animaux.");
        
        return 0;
    }
}