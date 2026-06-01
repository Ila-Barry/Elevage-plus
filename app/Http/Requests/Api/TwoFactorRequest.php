<?php
// app/Http/Requests/Api/TwoFactorRequest.php

namespace App\Http\Requests\Api;

/**
 * Requête de validation pour l'authentification à deux facteurs
 */
class TwoFactorRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'two_factor_code' => [
                'required',
                'string',
                'size:' . config('auth.two_factor_code_length', 6),
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'two_factor_code.required' => 'Le code d\'authentification est obligatoire.',
            'two_factor_code.size' => 'Le code doit contenir 6 chiffres.',
        ];
    }
}