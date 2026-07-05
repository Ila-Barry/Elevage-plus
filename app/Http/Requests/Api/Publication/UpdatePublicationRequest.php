<?php
// app/Http/Requests/Api/Publication/UpdatePublicationRequest.php

namespace App\Http\Requests\Api\Publication;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;

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
                'nullable',
                'string',
                'min:2',
                'max:1000000',
            ],
            // ✅ Images multiples
            'images' => [
                'nullable',
                'array',
                'max:5',
            ],
            'images.*' => [
                'image',
                'mimes:jpeg,png,jpg,webp,gif',
                'max:5120',
            ],
            // ✅ Vidéos multiples
            'videos' => [
                'nullable',
                'array',
                'max:2',
            ],
            'videos.*' => [
                'file',
                'mimes:mp4,avi,mov,webm',
                'max:51200',
            ],
            // ✅ Documents multiples
            'documents' => [
                'nullable',
                'array',
                'max:3',
            ],
            'documents.*' => [
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
                'max:10240',
            ],
            'delete_images' => [
                'sometimes',
                'boolean',
            ],
            'delete_videos' => [
                'sometimes',
                'boolean',
            ],
            'delete_documents' => [
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
            'contenu.min' => 'Le contenu doit contenir au moins 2 caractères.',
            'contenu.max' => 'Le contenu ne peut pas dépasser 1 million de caractères.',
            'images.max' => 'Vous ne pouvez pas ajouter plus de 5 images.',
            'images.*.image' => 'Le fichier doit être une image.',
            'images.*.max' => 'Chaque image ne doit pas dépasser 5 Mo.',
            'videos.max' => 'Vous ne pouvez pas ajouter plus de 2 vidéos.',
            'videos.*.max' => 'Chaque vidéo ne doit pas dépasser 50 Mo.',
            'documents.max' => 'Vous ne pouvez pas ajouter plus de 3 documents.',
            'documents.*.max' => 'Chaque document ne doit pas dépasser 10 Mo.',
        ];
    }
}