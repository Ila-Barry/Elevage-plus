<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rules\Password;

class RegisterRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telephone' => ['required', 'string', 'min:8', 'max:20', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Password::min(6)->letters()->mixedCase()->numbers()],
            'bio' => ['nullable', 'string', 'max:500'],
            'type_elevage' => ['nullable', 'string', 'max:5000'], // ← Plus restrictif, accepte tout texte
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'name.min' => 'Le nom doit contenir au moins 2 caractères.',
            
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'Veuillez entrer un email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            
            'telephone.required' => 'Le téléphone est obligatoire.',
            'telephone.unique' => 'Ce téléphone est déjà utilisé.',
            
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            
            'type_elevage.max' => 'Le type d\'élevage ne doit pas dépasser 5000 caractères.',
        ];
    }
}