<?php
// app/Http/Requests/Api/Admin/UpdateUserRequest.php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends ApiRequest
{
    public function rules(): array
    {
        $userId = $this->route('id');
        
        return [
            'name' => ['sometimes', 'string', 'min:2', 'max:100'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'telephone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'telephone')->ignore($userId)],
            'role' => ['sometimes', Rule::in(['admin', 'eleveur', 'visiteur'])],
            'status' => ['sometimes', Rule::in(['active', 'bannie'])],
            'bio' => ['nullable', 'string', 'max:500'],
            'profile_visibility' => ['sometimes', 'in:public,prive'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min' => 'Le nom doit contenir au moins 2 caractères.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'role.in' => 'Le rôle sélectionné n\'est pas valide.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
        ];
    }
}