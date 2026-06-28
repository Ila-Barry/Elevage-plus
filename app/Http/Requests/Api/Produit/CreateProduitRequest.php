<?php
// app/Http/Requests/Api/Produit/CreateProduitRequest.php

namespace App\Http\Requests\Api\Produit;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Produit;
use Illuminate\Validation\Rule;

class CreateProduitRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'elevage_id' => [
                'required',
                'exists:elevages,id',
            ],
            'nom' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],
            'categorie' => [
                'required',
                'string',
                Rule::in(array_keys(Produit::CATEGORIES)),
            ],
            'quantite_initiale' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'seuil_alerte' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'unite' => [
                'required',
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
                'unique:produits,code_barre',
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
        ];
    }

    public function messages(): array
    {
        return [
            'elevage_id.required' => 'Veuillez sélectionner un élevage.',
            'elevage_id.exists' => 'L\'élevage sélectionné n\'existe pas.',
            'nom.required' => 'Le nom du produit est obligatoire.',
            'nom.min' => 'Le nom doit contenir au moins 2 caractères.',
            'categorie.required' => 'La catégorie est obligatoire.',
            'categorie.in' => 'La catégorie sélectionnée n\'est pas valide.',
            'seuil_alerte.required' => 'Le seuil d\'alerte est obligatoire.',
            'seuil_alerte.min' => 'Le seuil d\'alerte doit être supérieur ou égal à 0.',
            'unite.required' => 'L\'unité de mesure est obligatoire.',
            'unite.in' => 'L\'unité sélectionnée n\'est pas valide.',
            'code_barre.unique' => 'Ce code-barres est déjà utilisé.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.max' => 'L\'image ne doit pas dépasser 2 Mo.',
            'date_expiration.after' => 'La date d\'expiration doit être dans le futur.',
        ];
    }
}