<?php
// app/Http/Requests/Api/Publication/UpdatePublicationRequest.php

namespace App\Http\Requests\Api\Publication;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule; // ← AJOUTER CETTE LIGNE

/**
 * Requête de validation pour la mise à jour d'une publication
 */
class UpdatePublicationRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'titre' => [
                'sometimes',
                'string',
                'min:5',
                'max:200',
            ],
            'categorie' => [
                'sometimes',
                'string',
                'in:experience,conseil,alerte',
            ],
            'contenu' => [
                'sometimes',
                'string',
                'min:10',
                'max:1000000',
            ],
            'image' => [
                'nullable',
                File::image()
                    ->max(5 * 1024)
                    ->dimensions(Rule::dimensions()->maxWidth(2000)->maxHeight(2000)),
            ],
            'video' => [
                'nullable',
                File::types(['mp4', 'mov', 'avi'])
                    ->max(50 * 1024),
            ],
            'fichier' => [
                'nullable',
                File::types(['pdf', 'doc', 'docx', 'xls', 'xlsx'])
                    ->max(10 * 1024),
            ],
            'delete_image' => [
                'sometimes',
                'boolean',
            ],
            'delete_video' => [
                'sometimes',
                'boolean',
            ],
            'delete_fichier' => [
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
            'titre.min' => 'Le titre doit contenir au moins 5 caractères.',
            'titre.max' => 'Le titre ne peut pas dépasser 200 caractères.',
            'contenu.min' => 'Le contenu doit contenir au moins 10 caractères.',
            'contenu.max' => 'Le contenu ne peut pas dépasser 1 million de caractères.',
            'image.image' => 'Le fichier doit être une image.',
            'image.max' => 'L\'image ne doit pas dépasser 5 Mo.',
            'video.max' => 'La vidéo ne doit pas dépasser 50 Mo.',
            'fichier.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
        ];
    }
}