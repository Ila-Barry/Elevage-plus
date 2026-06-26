<?php
// app/Http/Requests/Api/CommentaireRequest.php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CommentaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'contenu' => 'required|string|min:1|max:5000',
            'parent_id' => 'nullable|exists:commentaires,id',
        ];
    }

    public function messages(): array
    {
        return [
            'contenu.required' => 'Le commentaire ne peut pas être vide.',
            'contenu.min' => 'Le commentaire doit contenir au moins 1 caractère.',
            'contenu.max' => 'Le commentaire ne peut pas dépasser 5000 caractères.',
        ];
    }
}