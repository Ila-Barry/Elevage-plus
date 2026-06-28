<?php

namespace App\Http\Requests\Api\Elevage;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Elevage;
use Illuminate\Validation\Rule;

class UpdateElevageRequest extends ApiRequest
{
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
                'nullable',
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
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],
            'delete_image' => [
                'nullable',
                'string',
            ],
            'statut' => [
                'nullable',
                Rule::in(array_keys(Elevage::STATUTS)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de l\'élevage est obligatoire.',
            'nom.min' => 'Le nom doit contenir au moins 3 caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            'type_elevage.required' => 'Le type d\'élevage est obligatoire.',
            'type_elevage.in' => 'Le type d\'élevage sélectionné n\'est pas valide.',
            'superficie.numeric' => 'La superficie doit être un nombre.',
            'superficie.min' => 'La superficie doit être supérieure ou égale à 0.',
            'image.image' => 'Le fichier doit être une image.',
            'image.max' => 'L\'image ne doit pas dépasser 5 Mo.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
        ];
    }

    // Préparation des données avant validation
    protected function prepareForValidation(): void
    {
        // Nettoyer les champs vides
        if ($this->has('nom') && empty($this->nom)) {
            $this->merge(['nom' => null]);
        }
        
        if ($this->has('type_elevage') && empty($this->type_elevage)) {
            $this->merge(['type_elevage' => null]);
        }
        
        // S'assurer que les champs numériques sont bien formatés
        if ($this->has('superficie')) {
            $this->merge(['superficie' => $this->superficie === '' ? null : $this->superficie]);
        }
    }
}