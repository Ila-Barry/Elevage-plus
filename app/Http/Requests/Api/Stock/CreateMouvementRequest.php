<?php
// app/Http/Requests/Api/Stock/CreateMouvementRequest.php

namespace App\Http\Requests\Api\Stock;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Produit;
use App\Models\MouvementStock;

/**
 * Requête de validation pour la création d'un mouvement de stock
 */
class CreateMouvementRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        $type = $this->input('type');

        return [
            'type' => [
                'required',
                'string',
                'in:entree,sortie',
            ],
            'quantite' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
            ],
            'motif' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($type) {
                    $motifs = $type === 'entree' 
                        ? array_keys(MouvementStock::MOTIFS_ENTREE)
                        : array_keys(MouvementStock::MOTIFS_SORTIE);
                    if (!in_array($value, $motifs)) {
                        $fail("Le motif sélectionné n'est pas valide pour ce type de mouvement.");
                    }
                },
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'reference_facture' => [
                'nullable',
                'string',
                'max:50',
            ],
            'fournisseur' => [
                'nullable',
                'string',
                'max:100',
            ],
            'destinataire' => [
                'nullable',
                'string',
                'max:100',
            ],
            'date_mouvement' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Le type de mouvement est obligatoire.',
            'type.in' => 'Le type doit être "entree" ou "sortie".',
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.min' => 'La quantité doit être supérieure à 0.',
            'motif.required' => 'Le motif est obligatoire.',
            'date_mouvement.before_or_equal' => 'La date ne peut pas être dans le futur.',
        ];
    }
}