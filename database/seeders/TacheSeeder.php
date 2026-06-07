<?php
// database/seeders/TacheSeeder.php

namespace Database\Seeders;

use App\Models\Tache;
use App\Models\Animal;
use App\Models\Elevage;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TacheSeeder extends Seeder
{
    public function run(): void
    {
        $elevages = Elevage::all();
        
        if ($elevages->isEmpty()) {
            $this->command->info('Aucun élevage trouvé.');
            return;
        }
        
        $types = array_keys(Tache::TYPES);
        
        foreach ($elevages as $elevage) {
            $animaux = $elevage->animaux;
            
            // Créer des tâches pour les 30 prochains jours
            for ($i = 1; $i <= 30; $i++) {
                $date = Carbon::today()->addDays($i);
                
                // 70% de chance d'avoir une tâche ce jour
                if (rand(1, 100) <= 70) {
                    $type = $types[array_rand($types)];
                    
                    // 60% de chance d'être liée à un animal, 40% à l'élevage
                    $animal = null;
                    if ($animaux->isNotEmpty() && rand(1, 100) <= 60) {
                        $animal = $animaux->random();
                    }
                    
                    Tache::create([
                        'animal_id' => $animal?->id,
                        'elevage_id' => $elevage->id,
                        'titre' => $this->getTitreForType($type, $animal),
                        'type' => $type,
                        'date_planifiee' => $date,
                        'terminee' => $date->isPast() && rand(1, 100) <= 80,
                        'date_realisee' => $date->isPast() && rand(1, 100) <= 80 ? $date->copy()->addDays(rand(0, 2)) : null,
                        'description' => $this->getDescriptionForType($type),
                    ]);
                }
            }
            
            // Ajouter quelques tâches en retard
            for ($i = 1; $i <= 5; $i++) {
                $type = $types[array_rand($types)];
                $date = Carbon::today()->subDays(rand(1, 15));
                
                $animal = $animaux->isNotEmpty() && rand(1, 100) <= 60 ? $animaux->random() : null;
                
                Tache::create([
                    'animal_id' => $animal?->id,
                    'elevage_id' => $elevage->id,
                    'titre' => $this->getTitreForType($type, $animal),
                    'type' => $type,
                    'date_planifiee' => $date,
                    'terminee' => false,
                    'description' => $this->getDescriptionForType($type),
                ]);
            }
        }
    }
    
    private function getTitreForType(string $type, ?Animal $animal): string
    {
        $titres = [
            'vaccination' => 'Vaccination ' . ($animal ? 'de ' . $animal->nom : 'annuelle'),
            'pesee' => 'Pesée ' . ($animal ? 'de ' . $animal->nom : 'de contrôle'),
            'vermifuge' => 'Vermifuge ' . ($animal ? 'de ' . $animal->nom : 'général'),
            'soin' => 'Soin ' . ($animal ? 'de ' . $animal->nom : 'préventif'),
            'alimentation' => 'Contrôle alimentation',
            'nettoyage' => 'Nettoyage des installations',
            'autre' => 'Tâche de maintenance',
        ];
        
        return $titres[$type] ?? 'Tâche planifiée';
    }
    
    private function getDescriptionForType(string $type): string
    {
        $descriptions = [
            'vaccination' => 'Vaccination annuelle obligatoire. Vérifier les dates de rappel.',
            'pesee' => 'Contrôle du poids pour suivi de croissance.',
            'vermifuge' => 'Traitement vermifuge selon protocole.',
            'soin' => 'Examen de routine et soins nécessaires.',
            'alimentation' => 'Vérifier les stocks et ajuster les rations.',
            'nettoyage' => 'Nettoyage et désinfection des bâtiments.',
            'autre' => 'Tâche à réaliser selon planning.',
        ];
        
        return $descriptions[$type] ?? 'Tâche à effectuer.';
    }
}