<?php
// app/Http/Requests/Api/ChangePasswordRequest.php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rules\Password;

/**
 * Requête de validation pour le changement de mot de passe
 */
class ChangePasswordRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                'current_password', // Vérifie que le mot de passe correspond
            ],
            'new_password' => [
                'required',
                'string',
                'confirmed',
                'different:current_password',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'new_password_confirmation' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            
            'new_password.required' => 'Le nouveau mot de passe est obligatoire.',
            'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'new_password.different' => 'Le nouveau mot de passe doit être différent de l\'ancien.',
            'new_password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ];
    }
}