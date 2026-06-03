<?php
// app/Http/Requests/Api/CommentaireRequest.php

namespace App\Http\Requests\Api;

/**
 * Requête de validation pour les commentaires
 */
class CommentaireRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'contenu' => [
                'required',
                'string',
                'min:2',
                'max:5000',
            ],
            'parent_id' => [
                'nullable',
                'exists:commentaires,id',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'contenu.required' => 'Le commentaire ne peut pas être vide.',
            'contenu.min' => 'Le commentaire doit contenir au moins 2 caractères.',
            'contenu.max' => 'Le commentaire ne peut pas dépasser 5000 caractères.',
            'parent_id.exists' => 'Le commentaire parent n\'existe pas.',
        ];
    }

    /**
     * Nettoie et filtre les données entrantes
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('contenu')) {
            $this->merge([
                'contenu' => strip_tags($this->contenu, '<br><strong><em>'),
            ]);
        }
    }
}