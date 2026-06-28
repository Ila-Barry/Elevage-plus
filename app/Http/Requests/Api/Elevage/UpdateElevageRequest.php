<?php
// app/Http/Requests/Api/Elevage/UpdateElevageRequest.php

namespace App\Http\Requests\Api\Elevage;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Elevage;
use Illuminate\Validation\Rule;

/**
 * Requête de validation pour la mise à jour d'un élevage
 */
class UpdateElevageRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'nom' => [
                'sometimes',
                'string',
                'min:3',
                'max:100',
            ],
            'type_elevage' => [
                'sometimes',
                'string',
                Rule::in(array_keys(Elevage::TYPES_ELEVAGE)),
            ],
            'localisation' => [
                'sometimes',
                'string',
                'max:200',
            ],
            'superficie' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'adresse' => [
                'nullable',
                'string',
                'max:200',
            ],
            'ville' => [
                'nullable',
                'string',
                'max:100',
            ],
            'code_postal' => [
                'nullable',
                'string',
                'max:20',
            ],
            'pays' => [
                'nullable',
                'string',
                'max:100',
            ],
            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],
            'telephone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
            ],
            'email_contact' => [
                'nullable',
                'email',
                'max:100',
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
            'statut' => [
                'sometimes',
                Rule::in(array_keys(Elevage::STATUTS)),
            ],
            'delete_image' => [
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
            'nom.min' => 'Le nom doit contenir au moins 3 caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            'type_elevage.in' => 'Le type d\'élevage sélectionné n\'est pas valide.',
            'superficie.numeric' => 'La superficie doit être un nombre.',
            'superficie.min' => 'La superficie doit être supérieure ou égale à 0.',
            'latitude.between' => 'La latitude doit être comprise entre -90 et 90.',
            'longitude.between' => 'La longitude doit être comprise entre -180 et 180.',
            'telephone.regex' => 'Le numéro de téléphone n\'est pas valide.',
            'email_contact.email' => 'L\'email de contact n\'est pas valide.',
            'image.image' => 'Le fichier doit être une image.',
            'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
        ];
    }
}