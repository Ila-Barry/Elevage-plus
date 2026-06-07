<?php
// app/Http/Requests/Api/Elevage/ElevageFilterRequest.php

namespace App\Http\Requests\Api\Elevage;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Elevage;

/**
 * Requête de validation pour les filtres d'élevage
 */
class ElevageFilterRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'type_elevage' => [
                'nullable',
                'string',
                'in:' . implode(',', array_keys(Elevage::TYPES_ELEVAGE)),
            ],
            'statut' => [
                'nullable',
                'string',
                'in:' . implode(',', array_keys(Elevage::STATUTS)),
            ],
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
            'localisation' => [
                'nullable',
                'string',
                'max:100',
            ],
            'sort' => [
                'nullable',
                'string',
                'in:nom,superficie,date_creation,created_at,total_animaux',
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
            'sort' => $this->input('sort', 'date_creation'),
            'direction' => $this->input('direction', 'desc'),
            'per_page' => $this->input('per_page', 12),
        ]);
    }
}