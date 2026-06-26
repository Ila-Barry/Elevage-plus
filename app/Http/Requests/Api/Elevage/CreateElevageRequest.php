<?php
// app/Http/Requests/Api/Elevage/CreateElevageRequest.php

namespace App\Http\Requests\Api\Elevage;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Elevage;
use Illuminate\Validation\Rule;

/**
 * Requête de validation pour la création d'un élevage
 */
class CreateElevageRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'nom' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'type_elevage' => [
                'required',
                'string',
                Rule::in(array_keys(Elevage::TYPES_ELEVAGE)),
            ],
            'localisation' => [
                'required',
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
                'max:5048',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de l\'élevage est obligatoire.',
            'nom.min' => 'Le nom doit contenir au moins 3 caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            
            'type_elevage.required' => 'Le type d\'élevage est obligatoire.',
            'type_elevage.in' => 'Le type d\'élevage sélectionné n\'est pas valide.',
            
            'localisation.required' => 'La localisation est obligatoire.',
            'localisation.max' => 'La localisation ne peut pas dépasser 200 caractères.',
            
            'superficie.numeric' => 'La superficie doit être un nombre.',
            'superficie.min' => 'La superficie doit être supérieure ou égale à 0.',
            
            'description.max' => 'La description ne peut pas dépasser 2000 caractères.',
            
            'latitude.between' => 'La latitude doit être comprise entre -90 et 90.',
            'longitude.between' => 'La longitude doit être comprise entre -180 et 180.',
            
            'telephone.regex' => 'Le numéro de téléphone n\'est pas valide.',
            'email_contact.email' => 'L\'email de contact n\'est pas valide.',
            
            'image.image' => 'Le fichier doit être une image.',
            'image.max' => 'L\'image ne doit pas dépasser 5 Mo.',
        ];
    }

    /**
     * Nettoie et filtre les données entrantes
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer le numéro de téléphone
        if ($this->has('telephone')) {
            $this->merge([
                'telephone' => preg_replace('/[^0-9+]/', '', $this->telephone),
            ]);
        }
        
        // Nettoyer le code postal
        if ($this->has('code_postal')) {
            $this->merge([
                'code_postal' => strtoupper(trim($this->code_postal)),
            ]);
        }
    }
}