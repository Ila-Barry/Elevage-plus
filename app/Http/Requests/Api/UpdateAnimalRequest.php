<?php
// app/Http/Requests/Api/UpdateAnimalRequest.php

namespace App\Http\Requests\Api;

use App\Models\Animal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request pour la mise à jour d'un animal
 * Tous les champs sont optionnels
 */
class UpdateAnimalRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'nom' => [
                'sometimes',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-éèêëàâäôöûüç]+$/',
            ],
            'race' => [
                'sometimes',
                'string',
                'min:2',
                'max:100',
            ],
            'espece' => [
                'sometimes',
                'string',
                Rule::in(array_keys(Animal::ESPECES)),
            ],
            'poids' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:5000',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'statut_sanitaire' => [
                'sometimes',
                'string',
                Rule::in(array_keys(Animal::STATUTS_SANITAIRES)),
            ],
            'date_naissance' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'after:1900-01-01',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
                'dimensions:min_width=200,min_height=200,max_width=2000,max_height=2000',
            ],
        ];
    }

    /**
     * Messages d'erreur
     */
    public function messages(): array
    {
        return [
            'nom.min' => 'Le nom doit contenir au moins 2 caractères.',
            'poids.min' => 'Le poids ne peut pas être négatif.',
            'espece.in' => 'Espèce invalide.',
            'date_naissance.before_or_equal' => 'La date de naissance ne peut pas être dans le futur.',
            'photo.mimes' => 'Format d\'image non supporté.',
        ];
    }
}