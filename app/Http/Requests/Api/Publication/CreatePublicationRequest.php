<?php
// app/Http/Requests/Api/Publication/CreatePublicationRequest.php

namespace App\Http\Requests\Api\Publication;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule; // ← AJOUTER CETTE LIGNE

/**
 * Requête de validation pour la création d'une publication
 */
class CreatePublicationRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'titre' => [
                'required',
                'string',
                'min:5',
                'max:200',
            ],
            'categorie' => [
                'required',
                'string',
                'in:experience,conseil,alerte',
            ],
            'contenu' => [
                'required',
                'string',
                'min:10',
                'max:1000000', // 1 million de caractères
            ],
            'image' => [
                'nullable',
                File::image()
                    ->max(5 * 1024) // 5MB
                    ->dimensions(Rule::dimensions()->maxWidth(2000)->maxHeight(2000)),
            ],
            'video' => [
                'nullable',
                File::types(['mp4', 'mov', 'avi'])
                    ->max(50 * 1024), // 50MB
            ],
            'fichier' => [
                'nullable',
                File::types(['pdf', 'doc', 'docx', 'xls', 'xlsx'])
                    ->max(10 * 1024), // 10MB
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.min' => 'Le titre doit contenir au moins 5 caractères.',
            'titre.max' => 'Le titre ne peut pas dépasser 200 caractères.',
            
            'categorie.required' => 'La catégorie est obligatoire.',
            'categorie.in' => 'La catégorie sélectionnée n\'est pas valide.',
            
            'contenu.required' => 'Le contenu est obligatoire.',
            'contenu.min' => 'Le contenu doit contenir au moins 10 caractères.',
            'contenu.max' => 'Le contenu ne peut pas dépasser 1 million de caractères.',
            
            'image.image' => 'Le fichier doit être une image.',
            'image.max' => 'L\'image ne doit pas dépasser 5 Mo.',
            'video.max' => 'La vidéo ne doit pas dépasser 50 Mo.',
            'fichier.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
        ];
    }

    /**
     * Nettoie et filtre les données entrantes
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer le contenu HTML (strip_tags pour éviter XSS)
        if ($this->has('contenu')) {
            $this->merge([
                'contenu' => strip_tags($this->contenu, '<p><br><strong><em><u><h1><h2><h3><ul><ol><li>'),
            ]);
        }
    }
}