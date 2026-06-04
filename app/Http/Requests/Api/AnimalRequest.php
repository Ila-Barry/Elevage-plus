<?php
// app/Http/Requests/Api/AnimalRequest.php

namespace App\Http\Requests\Api;

use App\Models\Animal;
use App\Models\Elevage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request pour la création d'un animal
 */
class AnimalRequest extends FormRequest
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
            'elevage_id' => [
                'required',
                'integer',
                'exists:elevages,id',
                function ($attribute, $value, $fail) {
                    $elevage = Elevage::find($value);
                    if (!$elevage || $elevage->user_id !== auth()->id()) {
                        $fail('Vous devez être propriétaire de cet élevage pour y ajouter des animaux.');
                    }
                },
            ],
            'nom' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-éèêëàâäôöûüç]+$/',
            ],
            'race' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],
            'espece' => [
                'required',
                'string',
                Rule::in(array_keys(Animal::ESPECES)),
            ],
            'poids' => [
                'required',
                'numeric',
                'min:0',
                'max:5000', // 5000kg max pour un bovin
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'statut_sanitaire' => [
                'required',
                'string',
                Rule::in(array_keys(Animal::STATUTS_SANITAIRES)),
            ],
            'date_naissance' => [
                'required',
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
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'elevage_id.required' => 'L\'élevage est requis.',
            'elevage_id.exists' => 'L\'élevage sélectionné n\'existe pas.',
            'nom.required' => 'Le nom de l\'animal est requis.',
            'nom.min' => 'Le nom doit contenir au moins 2 caractères.',
            'nom.regex' => 'Le nom contient des caractères non autorisés.',
            'race.required' => 'La race est requise.',
            'espece.required' => 'L\'espèce est requise.',
            'espece.in' => 'Espèce invalide.',
            'poids.required' => 'Le poids est requis.',
            'poids.min' => 'Le poids ne peut pas être négatif.',
            'poids.regex' => 'Le poids doit avoir maximum 2 décimales.',
            'statut_sanitaire.required' => 'Le statut sanitaire est requis.',
            'date_naissance.required' => 'La date de naissance est requise.',
            'date_naissance.before_or_equal' => 'La date de naissance ne peut pas être dans le futur.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'Format d\'image non supporté.',
            'photo.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ];
    }

    /**
     * Préparation des données avant validation
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('nom')) {
            $this->merge(['nom' => ucfirst(trim($this->nom))]);
        }
        
        if ($this->has('race')) {
            $this->merge(['race' => ucfirst(trim($this->race))]);
        }
    }
}