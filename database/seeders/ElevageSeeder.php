<?php
// database/seeders/ElevageSeeder.php

namespace Database\Seeders;

use App\Models\Elevage;
use App\Models\User;
use Illuminate\Database\Seeder;

class ElevageSeeder extends Seeder
{
    /**
     * Execute le seeder
     */
    public function run(): void
    {
        // Récupérer les utilisateurs éleveurs
        $eleveurs = User::where('role', 'user')->get();
        
        if ($eleveurs->isEmpty()) {
            // Créer un utilisateur de test si nécessaire
            $eleveurs = User::factory(5)->create(['role' => 'user']);
        }
        
        $types = Elevage::TYPES_ELEVAGE;
        $localisations = ['Casablanca', 'Rabat', 'Fès', 'Marrakech', 'Tanger', 'Agadir', 'Meknès'];
        
        foreach ($eleveurs as $eleveur) {
            // Chaque éleveur a entre 1 et 3 élevages
            $nbElevages = rand(1, 3);
            
            for ($i = 1; $i <= $nbElevages; $i++) {
                Elevage::create([
                    'user_id' => $eleveur->id,
                    'nom' => $this->getElevageName($types[array_rand($types)], $i),
                    'localisation' => $localisations[array_rand($localisations)],
                    'superficie' => rand(100, 5000),
                    'type_elevage' => $types[array_rand($types)],
                    'description' => $this->getRandomDescription(),
                    'img_url' => null,
                ]);
            }
        }
    }
    
    /**
     * Génère un nom d'élevage
     */
    private function getElevageName(string $type, int $index): string
    {
        $prefixes = ['Ferme', 'Domaine', 'Élevage', 'Ferme familiale', 'Domaine agricole'];
        $suffixes = ['des collines', 'de la vallée', 'du soleil', 'de la plaine', 'du bonheur'];
        
        $typeLabels = [
            'bovins' => 'bovine',
            'ovins' => 'ovine', 
            'caprins' => 'caprine',
            'volailles' => 'avicole',
            'mixte' => 'mixte',
            'autres' => '',
        ];
        
        $label = $typeLabels[$type] ?? '';
        
        return $prefixes[array_rand($prefixes)] . ' ' . $label . ' ' . $suffixes[array_rand($suffixes)];
    }
    
    /**
     * Génère une description aléatoire
     */
    private function getRandomDescription(): string
    {
        $descriptions = [
            'Élevage moderne avec des installations récentes.',
            'Exploitation familiale transmise depuis 3 générations.',
            'Élevage bio certifié, respectueux de l\'environnement.',
            'Production locale de qualité, vente directe à la ferme.',
            'Élevage en plein air, bien-être animal garanti.',
        ];
        
        return $descriptions[array_rand($descriptions)];
    }
}