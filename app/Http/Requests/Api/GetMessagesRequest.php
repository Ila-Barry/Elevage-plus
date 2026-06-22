<?php
// app/Http/Requests/Api/GetMessagesRequest.php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request pour la récupération des messages d'une conversation
 */
class GetMessagesRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ];
    }
}