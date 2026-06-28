<?php
// app/Http/Requests/Api/LoginRequest.php

namespace App\Http\Requests\Api;

/**
 * Requête de validation pour la connexion
 */
class LoginRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'login' => [  
                'required',
                'string',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
            ],
            'remember' => [
                'nullable',
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
            'login.required' => 'L\'email ou le téléphone est obligatoire.',
            
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
        ];
    }
}