<?php
// app/Http/Requests/Api/Animal/AnimalFilterRequest.php

namespace App\Http\Requests\Api\Animal;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Animal;

/**
 * Requête de validation pour les filtres d'animaux
 */
class AnimalFilterRequest extends ApiRequest
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
            'espece' => [
                'nullable',
                'string',
                'in:' . implode(',', array_keys(Animal::ESPECES)),
            ],
            'statut_sanitaire' => [
                'nullable',
                'string',
                'in:' . implode(',', array_keys(Animal::STATUTS_SANITAIRES)),
            ],
            'statut' => [
                'nullable',
                'string',
                'in:' . implode(',', array_keys(Animal::STATUTS)),
            ],
            'sexe' => [
                'nullable',
                'string',
                'in:' . implode(',', array_keys(Animal::SEXES)),
            ],
            'age_min' => [
                'nullable',
                'integer',
                'min:0',
                'max:600',
            ],
            'age_max' => [
                'nullable',
                'integer',
                'min:0',
                'max:600',
                'gte:age_min',
            ],
            'poids_min' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'poids_max' => [
                'nullable',
                'numeric',
                'min:0',
                'gte:poids_min',
            ],
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
            'sort' => [
                'nullable',
                'string',
                'in:nom,espece,date_naissance,poids,statut_sanitaire,created_at',
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
            'sort' => $this->input('sort', 'created_at'),
            'direction' => $this->input('direction', 'desc'),
            'per_page' => $this->input('per_page', 15),
        ]);
    }
}