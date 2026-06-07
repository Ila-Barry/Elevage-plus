<?php
// app/Http/Requests/Api/Produit/UpdateProduitRequest.php

namespace App\Http\Requests\Api\Produit;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Produit;
use Illuminate\Validation\Rule;

/**
 * Requête de validation pour la mise à jour d'un produit
 */
class UpdateProduitRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        $produitId = $this->route('produit');

        return [
            'nom' => [
                'sometimes',
                'string',
                'min:2',
                'max:100',
            ],
            'categorie' => [
                'sometimes',
                'string',
                Rule::in(array_keys(Produit::CATEGORIES)),
            ],
            'seuil_alerte' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'unite' => [
                'sometimes',
                'string',
                Rule::in(array_keys(Produit::UNITES)),
            ],
            'fournisseur' => [
                'nullable',
                'string',
                'max:100',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'code_barre' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('produits', 'code_barre')->ignore($produitId),
            ],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
            'prix_unitaire' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'date_expiration' => [
                'nullable',
                'date',
                'after:today',
            ],
            'emplacement_stockage' => [
                'nullable',
                'string',
                'max:100',
            ],
            'statut' => [
                'sometimes',
                Rule::in(array_keys(Produit::STATUTS)),
            ],
            'delete_photo' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'nom.min' => 'Le nom doit contenir au moins 2 caractères.',
            'categorie.in' => 'La catégorie sélectionnée n\'est pas valide.',
            'seuil_alerte.min' => 'Le seuil d\'alerte doit être supérieur ou égal à 0.',
            'unite.in' => 'L\'unité sélectionnée n\'est pas valide.',
            'code_barre.unique' => 'Ce code-barres est déjà utilisé.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.max' => 'L\'image ne doit pas dépasser 2 Mo.',
            'prix_unitaire.min' => 'Le prix unitaire doit être supérieur ou égal à 0.',
            'date_expiration.after' => 'La date d\'expiration doit être dans le futur.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
        ];
    }
}