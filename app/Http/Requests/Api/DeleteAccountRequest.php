<?php
// app/Http/Requests/Api/DeleteAccountRequest.php

namespace App\Http\Requests\Api;

/**
 * Requête de validation pour la suppression de compte
 */
class DeleteAccountRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                'current_password',
            ],
            'confirmation_text' => [
                'required',
                'string',
                'in:SUPPRIMER',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.current_password' => 'Le mot de passe est incorrect.',
            'confirmation_text.required' => 'Veuillez taper "SUPPRIMER" pour confirmer.',
            'confirmation_text.in' => 'Veuillez taper exactement "SUPPRIMER" pour confirmer.',
        ];
    }
}