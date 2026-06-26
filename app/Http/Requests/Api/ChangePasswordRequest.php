<?php
// app/Http/Requests/Api/ChangePasswordRequest.php

namespace App\Http\Requests\Api;

use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
                'current_password',
            ],
            'new_password' => [
                'required',
                'string',
                'confirmed',
                'different:current_password',
                'min:6',
            ],
            'new_password_confirmation' => [
                'required',
                'string',
                'min:6',
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
            'new_password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'new_password_confirmation.required' => 'La confirmation du mot de passe est obligatoire.',
            'new_password_confirmation.min' => 'La confirmation doit contenir au moins 6 caractères.',
        ];
    }

    /**
     * Gestion de l'échec de validation
     */
    protected function failedValidation(Validator $validator): void
    {
        Log::warning('Échec de validation du changement de mot de passe', [
            'user_id' => auth()->id(),
            'errors' => $validator->errors()->toArray()
        ]);
        
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    /**
     * Préparation des données pour la validation
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        
        Log::info('Tentative de changement de mot de passe', [
            'user_id' => auth()->id(),
            'has_current_password' => !empty($this->current_password),
        ]);
    }
}