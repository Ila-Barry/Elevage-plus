<?php
// app/Http/Requests/Api/Publication/CreatePublicationRequest.php

namespace App\Http\Requests\Api\Publication;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;

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
                'max:1000000',
            ],
            // ✅ Images multiples
            'images' => [
                'nullable',
                'array',
                'max:5', // Maximum 5 images
            ],
            'images.*' => [
                'image',
                'mimes:jpeg,png,jpg,webp,gif',
                'max:5120', // 5MB chacune
            ],
            // ✅ Vidéos multiples
            'videos' => [
                'nullable',
                'array',
                'max:2', // Maximum 2 vidéos
            ],
            'videos.*' => [
                'file',
                'mimes:mp4,avi,mov,webm',
                'max:51200', // 50MB chacune
            ],
            // ✅ Documents multiples
            'documents' => [
                'nullable',
                'array',
                'max:3', // Maximum 3 documents
            ],
            'documents.*' => [
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
                'max:10240', // 10MB chacune
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
            
            'images.max' => 'Vous ne pouvez pas ajouter plus de 5 images.',
            'images.*.image' => 'Le fichier doit être une image.',
            'images.*.max' => 'Chaque image ne doit pas dépasser 5 Mo.',
            
            'videos.max' => 'Vous ne pouvez pas ajouter plus de 2 vidéos.',
            'videos.*.file' => 'Le fichier doit être une vidéo.',
            'videos.*.max' => 'Chaque vidéo ne doit pas dépasser 50 Mo.',
            
            'documents.max' => 'Vous ne pouvez pas ajouter plus de 3 documents.',
            'documents.*.file' => 'Le fichier doit être un document valide.',
            'documents.*.max' => 'Chaque document ne doit pas dépasser 10 Mo.',
        ];
    }

    /**
     * Nettoie et filtre les données entrantes
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('contenu')) {
            $this->merge([
                'contenu' => strip_tags($this->contenu, '<p><br><strong><em><u><h1><h2><h3><ul><ol><li>'),
            ]);
        }
    }
}