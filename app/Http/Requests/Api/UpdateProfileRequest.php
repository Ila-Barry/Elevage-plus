<?php
// app/Http/Requests/Api/UpdateProfileRequest.php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class UpdateProfileRequest extends ApiRequest
{
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name' => [
                'sometimes',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s\'-]+$/u',
            ],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'telephone' => [
                'nullable', // ✅ CHANGÉ : nullable au lieu de sometimes
                'string',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'min:8',
                'max:20',
                Rule::unique('users', 'telephone')->ignore($userId),
            ],
            'bio' => [
                'nullable',
                'string',
                'max:500',
            ],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:' . config('app.avatar_max_size', 5120),
            ],
            'profile_visibility' => [
                'sometimes',
                'string',
                'in:public,prive',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min' => 'Le nom doit contenir au moins 2 caractères.',
            'name.regex' => 'Le nom ne doit contenir que des lettres, espaces, tirets et apostrophes.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'telephone.regex' => 'Le numéro de téléphone n\'est pas valide.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.max' => 'La photo ne doit pas dépasser 5 Mo.',
            'profile_visibility.in' => 'La visibilité du profil doit être "public" ou "prive".',
        ];
    }
}