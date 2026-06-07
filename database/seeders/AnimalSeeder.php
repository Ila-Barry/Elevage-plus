<?php
// database/seeders/AnimalSeeder.php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Elevage;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AnimalSeeder extends Seeder
{
    /**
     * Execute le seeder
     */
    public function run(): void
    {
        $elevages = Elevage::all();
        
        if ($elevages->isEmpty()) {
            $this->command->info('Aucun élevage trouvé. Veuillez d\'abord exécuter ElevageSeeder.');
            return;
        }
        
        $racesByEspece = [
            'bovin' => ['Holstein', 'Montbéliarde', 'Charolaise', 'Limousine', 'Blonde d\'Aquitaine'],
            'ovin' => ['Boulonnaise', 'Île-de-France', 'Lacaune', 'Mérinos', 'Suffolk'],
            'caprin' => ['Alpine', 'Saanen', 'Poitevine', 'Corse', 'Rove'],
            'volaille' => ['Poulet', 'Pintade', 'Canard', 'Dinde', 'Caille'],
            'autre' => ['Mixte', 'Autre race'],
        ];
        
        $nomsPossibles = [
            'bovin' => ['Marguerite', 'Blanche', 'Brune', 'Noire', 'Rousse', 'Belle', 'Douce', 'Gentille'],
            'ovin' => ['Blanchon', 'Noir', 'Gris', 'Panaché', 'Boucle', 'Mouton'],
            'caprin' => ['Chevrette', 'Biquet', 'Blanche', 'Noire', 'Câline', 'Grisette'],
            'volaille' => ['Pile', 'Faune', 'Rousse', 'Blanche', 'Noire', 'Picorine'],
        ];
        
        foreach ($elevages as $elevage) {
            // Chaque élevage a entre 5 et 30 animaux
            $nbAnimaux = rand(5, 30);
            
            for ($i = 1; $i <= $nbAnimaux; $i++) {
                // Déterminer l'espèce principale selon le type d'élevage
                $espece = $this->getEspeceFromElevageType($elevage->type_elevage);
                
                // Parfois un animal d'une autre espèce
                if (rand(1, 10) > 7) {
                    $espece = array_rand(Animal::ESPECES);
                }
                
                $races = $racesByEspece[$espece] ?? $racesByEspece['autre'];
                $noms = $nomsPossibles[$espece] ?? ['Animal'];
                
                Animal::create([
                    'elevage_id' => $elevage->id,
                    'nom' => $noms[array_rand($noms)] . ' ' . ($i),
                    'race' => $races[array_rand($races)],
                    'espece' => $espece,
                    'poids' => $this->getRandomWeight($espece),
                    'statut_sanitaire' => $this->getRandomStatut(),
                    'date_naissance' => Carbon::now()->subDays(rand(30, 1095)), // 1 mois à 3 ans
                    'description' => $this->getRandomDescription(),
                    'img_url' => null,
                ]);
            }
        }
    }
    
    /**
     * Détermine l'espèce selon le type d'élevage
     */
    private function getEspeceFromElevageType(string $type): string
    {
        $mapping = [
            'bovins' => 'bovin',
            'ovins' => 'ovin',
            'caprins' => 'caprin',
            'volailles' => 'volaille',
            'mixte' => array_rand(['bovin', 'ovin', 'caprin', 'volaille']),
            'autres' => 'autre',
        ];
        
        return $mapping[$type] ?? 'autre';
    }
    
    /**
     * Poids aléatoire selon l'espèce
     */
    private function getRandomWeight(string $espece): float
    {
        $weights = [
            'bovin' => [200, 800],
            'ovin' => [30, 80],
            'caprin' => [25, 70],
            'volaille' => [1, 5],
            'autre' => [10, 100],
        ];
        
        $range = $weights[$espece] ?? [10, 100];
        return round(rand($range[0] * 10, $range[1] * 10) / 10, 1);
    }
    
    /**
     * Statut sanitaire aléatoire
     */
    private function getRandomStatut(): string
    {
        $statuts = array_keys(Animal::STATUTS_SANITAIRES);
        $probabilities = [80, 10, 5, 5]; // 80% sain, etc.
        
        $rand = rand(1, 100);
        $cumulative = 0;
        
        foreach ($statuts as $index => $statut) {
            $cumulative += $probabilities[$index];
            if ($rand <= $cumulative) {
                return $statut;
            }
        }
        
        return 'sain';
    }
    
    /**
     * Description aléatoire
     */
    private function getRandomDescription(): string
    {
        $descriptions = [
            'Animal en bonne santé, vacciné à jour.',
            'Né à la ferme, bonne constitution.',
            'Suivi vétérinaire régulier.',
            'Excellent état général, bon caractère.',
            'En période de croissance, poids conforme.',
        ];
        
        return rand(1, 10) > 7 ? $descriptions[array_rand($descriptions)] : '';
    }
}