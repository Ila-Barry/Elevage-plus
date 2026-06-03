<?php
// request pour les signallements
// app/Http/Requests/Api/Publication/ReportPublicationRequest.php

namespace App\Http\Requests\Api\Publication;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Report;

/**
 * Requête de validation pour le signalement d'une publication
 */
class ReportPublicationRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'motif' => [
                'required',
                'string',
                'in:' . implode(',', array_keys(Report::MOTIFS)),
            ],
            'commentaire' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'motif.required' => 'Le motif du signalement est obligatoire.',
            'motif.in' => 'Le motif sélectionné n\'est pas valide.',
            'commentaire.max' => 'Le commentaire ne peut pas dépasser 500 caractères.',
        ];
    }
}