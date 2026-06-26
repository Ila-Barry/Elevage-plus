<?php
// app/Http/Requests/Api/Admin/ReviewPublicationRequest.php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class ReviewPublicationRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['approve', 'reject'])],
            'note_moderation' => ['nullable', 'string', 'max:500'],
            'justification' => ['required_if:action,reject', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'L\'action est obligatoire.',
            'action.in' => 'L\'action sélectionnée n\'est pas valide.',
            'justification.required_if' => 'La justification est obligatoire pour le rejet.',
        ];
    }
}