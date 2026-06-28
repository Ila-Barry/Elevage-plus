<?php
// app/Http/Requests/Api/Admin/SendNewsletterRequest.php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class SendNewsletterRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'sujet' => ['required', 'string', 'min:3', 'max:200'],
            'contenu' => ['required', 'string', 'min:10', 'max:10000'],
            'cibles' => ['required', Rule::in(['tous', 'eleveurs_uniquement', 'admins_uniquement'])],
            'envoyer_maintenant' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'sujet.required' => 'Le sujet est obligatoire.',
            'sujet.min' => 'Le sujet doit contenir au moins 3 caractères.',
            'contenu.required' => 'Le contenu est obligatoire.',
            'contenu.min' => 'Le contenu doit contenir au moins 10 caractères.',
            'cibles.required' => 'La cible est obligatoire.',
            'cibles.in' => 'La cible sélectionnée n\'est pas valide.',
        ];
    }
}