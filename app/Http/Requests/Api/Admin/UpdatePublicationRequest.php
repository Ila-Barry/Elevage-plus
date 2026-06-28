<?php
// app/Http/Requests/Api/Admin/UpdatePublicationRequest.php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Publication;
use Illuminate\Validation\Rule;

/**
 * Requête de validation pour la mise à jour d'une publication par l'admin
 */
class UpdatePublicationRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'titre' => ['sometimes', 'string', 'min:5', 'max:200'],
            'categorie' => ['sometimes', Rule::in(array_keys(Publication::CATEGORIES))],
            'contenu' => ['sometimes', 'string', 'min:10', 'max:1000000'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'statut' => ['sometimes', Rule::in(['publiee', 'signalee', 'bloquee'])],
            'raison_blocage' => ['nullable', 'string', 'max:500'],
            'user_id' => ['sometimes', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'titre.min' => 'Le titre doit contenir au moins 5 caractères.',
            'titre.max' => 'Le titre ne peut pas dépasser 200 caractères.',
            'categorie.in' => 'La catégorie sélectionnée n\'est pas valide.',
            'contenu.min' => 'Le contenu doit contenir au moins 10 caractères.',
            'contenu.max' => 'Le contenu ne peut pas dépasser 1 million de caractères.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
            'user_id.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
        ];
    }
}