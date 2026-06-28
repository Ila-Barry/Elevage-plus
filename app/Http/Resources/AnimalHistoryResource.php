<?php
// app/Http/Resources/AnimalHistoryResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource AnimalHistoryResource
 * 
 * Formate la réponse API pour l'historique d'un animal
 */
class AnimalHistoryResource extends JsonResource
{
    /**
     * Transforme le resource en tableau.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'champ_modifie' => $this->champ_modifie,
            'ancienne_valeur' => $this->formatValue($this->ancienne_valeur),
            'nouvelle_valeur' => $this->formatValue($this->nouvelle_valeur),
            'action' => $this->action,
            'action_label' => $this->getActionLabel(),
            'utilisateur' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'ip_address' => $this->ip_address,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
        ];
    }

    /**
     * Formate la valeur pour l'affichage
     */
    protected function formatValue($value): ?string
    {
        if (is_null($value)) {
            return null;
        }
        
        // Décoder le JSON si nécessaire
        if ($this->isJson($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return json_encode($decoded, JSON_PRETTY_PRINT);
            }
        }
        
        return (string) $value;
    }

    /**
     * Vérifie si une chaîne est du JSON valide
     */
    protected function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Libellé de l'action
     */
    protected function getActionLabel(): string
    {
        return match($this->action) {
            'create' => 'Création',
            'update' => 'Modification',
            'delete' => 'Suppression',
            default => ucfirst($this->action),
        };
    }
}