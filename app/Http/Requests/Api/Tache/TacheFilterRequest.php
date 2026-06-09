<?php
// app/Http/Requests/Api/Tache/TacheFilterRequest.php

namespace App\Http\Requests\Api\Tache;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Tache;

/**
 * Requête de validation pour les filtres de tâches
 */
class TacheFilterRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'elevage_id' => [
                'nullable',
                'exists:elevages,id',
            ],
            'animal_id' => [
                'nullable',
                'exists:animaux,id',
            ],
            'type' => [
                'nullable',
                'string',
                'in:' . implode(',', array_keys(Tache::TYPES)),
            ],
            'priorite' => [
                'nullable',
                'string',
                'in:' . implode(',', array_keys(Tache::PRIORITES)),
            ],
            'statut' => [
                'nullable',
                'string',
                'in:toutes,terminees,a_venir,retard',
            ],
            'date_debut' => [
                'nullable',
                'date',
            ],
            'date_fin' => [
                'nullable',
                'date',
                'after_or_equal:date_debut',
            ],
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
            'sort' => [
                'nullable',
                'string',
                'in:date_planifiee,priorite,type,titre,created_at',
            ],
            'direction' => [
                'nullable',
                'string',
                'in:asc,desc',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],
        ];
    }

    /**
     * Valeurs par défaut
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort' => $this->input('sort', 'date_planifiee'),
            'direction' => $this->input('direction', 'asc'),
            'per_page' => $this->input('per_page', 15),
            'statut' => $this->input('statut', 'a_venir'),
        ]);
    }
}