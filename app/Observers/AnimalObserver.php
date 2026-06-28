<?php
// app/Observers/AnimalObserver.php

namespace App\Observers;

use App\Models\Animal;
use App\Models\AnimalHistorique;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class AnimalObserver
{
    /**
     * Handle the Animal "created" event.
     */
    public function created(Animal $animal): void
    {
        try {
            AnimalHistorique::create([
                'animal_id' => $animal->id,
                'user_id' => auth()->id(),
                'champ_modifie' => 'all',
                'ancienne_valeur' => null,
                'nouvelle_valeur' => json_encode($animal->getAttributes(), JSON_UNESCAPED_UNICODE),
                'action' => 'create',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Erreur lors de l\'enregistrement de l\'historique (create): ' . $e->getMessage());
        }
    }

    /**
     * Handle the Animal "updated" event.
     */
    public function updated(Animal $animal): void
    {
        try {
            $changes = $animal->getChanges();
            $original = $animal->getOriginal();
            
            foreach ($changes as $field => $newValue) {
                if ($field === 'updated_at') {
                    continue;
                }
                
                $oldValue = $original[$field] ?? null;
                
                if ($oldValue == $newValue) {
                    continue;
                }
                
                AnimalHistorique::create([
                    'animal_id' => $animal->id,
                    'user_id' => auth()->id(),
                    'champ_modifie' => $field,
                    'ancienne_valeur' => is_array($oldValue) ? json_encode($oldValue, JSON_UNESCAPED_UNICODE) : (string) $oldValue,
                    'nouvelle_valeur' => is_array($newValue) ? json_encode($newValue, JSON_UNESCAPED_UNICODE) : (string) $newValue,
                    'action' => 'update',
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Erreur lors de l\'enregistrement de l\'historique (update): ' . $e->getMessage());
        }
    }

    /**
     * Handle the Animal "deleting" event (AVANT la suppression)
     */
    public function deleting(Animal $animal): void
    {
        try {
            // Sauvegarder les données avant suppression
            $data = $animal->getAttributes();
            
            AnimalHistorique::create([
                'animal_id' => $animal->id,
                'user_id' => auth()->id(),
                'champ_modifie' => 'all',
                'ancienne_valeur' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'nouvelle_valeur' => null,
                'action' => 'delete',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Erreur lors de l\'enregistrement de l\'historique (delete): ' . $e->getMessage());
        }
    }
}