<?php
// app/Http/Requests/Api/ElevageRequest.php

namespace App\Http\Requests\Api;

use App\Models\Elevage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * Request pour la création d'un élevage
 * 
 * Valide les données d'entrée pour la création
 */
class ElevageRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête
     * Seuls les utilisateurs authentifiés peuvent créer un élevage
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Règles de validation
     * 
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nom' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-éèêëàâäôöûüç\'\(\)]+$/',
            ],
            'localisation' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
            'superficie' => [
                'required',
                'integer',
                'min:0',
                'max:1000000',
            ],
            'type_elevage' => [
                'required',
                'string',
                Rule::in(Elevage::TYPES_ELEVAGE),
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
                'max:2048', // 2MB max
                'dimensions:min_width=300,min_height=300,max_width=2000,max_height=2000',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de l\'élevage est requis.',
            'nom.min' => 'Le nom doit contenir au moins 3 caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            'nom.regex' => 'Le nom contient des caractères non autorisés.',
            'localisation.required' => 'La localisation est requise.',
            'superficie.required' => 'La superficie est requise.',
            'superficie.min' => 'La superficie ne peut pas être négative.',
            'superficie.max' => 'La superficie est trop grande.',
            'type_elevage.required' => 'Le type d\'élevage est requis.',
            'type_elevage.in' => 'Type d\'élevage invalide. Types acceptés : ' . implode(', ', Elevage::TYPES_ELEVAGE),
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'Format d\'image non supporté. Utilisez JPEG, PNG, JPG, GIF ou WEBP.',
            'photo.max' => 'L\'image ne doit pas dépasser 2 Mo.',
            'photo.dimensions' => 'L\'image doit faire au moins 300x300 pixels.',
        ];
    }

    /**
     * Préparation des données avant validation
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer le nom
        if ($this->has('nom')) {
            $this->merge([
                'nom' => trim($this->nom),
            ]);
        }
        
        // Supprimer les espaces dans superficie si présent
        if ($this->has('superficie') && is_string($this->superficie)) {
            $this->merge([
                'superficie' => (int) preg_replace('/\s+/', '', $this->superficie),
            ]);
        }
    }

    /**
     * Après validation réussie
     */
    protected function passedValidation(): void
    {
        $this->merge([
            'user_id' => auth()->id(),
        ]);
    }
}