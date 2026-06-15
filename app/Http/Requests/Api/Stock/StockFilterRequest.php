<?php
// app/Http/Requests/Api/Stock/StockFilterRequest.php

namespace App\Http\Requests\Api\Stock;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Produit;

/**
 * Requête de validation pour les filtres de stock
 */
class StockFilterRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'categorie' => [
                'nullable',
                'string',
                'in:' . implode(',', array_keys(Produit::CATEGORIES)),
            ],
            'statut' => [
                'nullable',
                'string',
                'in:actif,rupture,critique',
            ],
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
            'sort' => [
                'nullable',
                'string',
                'in:nom,quantite,seuil_alerte,created_at,updated_at',
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
                'max:100',
            ],
        ];
    }

    /**
     * Valeurs par défaut
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort' => $this->input('sort', 'nom'),
            'direction' => $this->input('direction', 'asc'),
            'per_page' => $this->input('per_page', 15),
        ]);
    }
}