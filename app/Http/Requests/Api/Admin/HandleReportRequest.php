<?php
// app/Http/Requests/Api/Admin/HandleReportRequest.php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class HandleReportRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'action' => [
                'required',
                Rule::in([
                    'publication_supprimee',
                    'publication_bloquee',
                    'utilisateur_averti',
                    'utilisateur_banni',
                    'aucune_action',
                ]),
            ],
            'commentaire_moderation' => ['nullable', 'string', 'max:500'],
            'justification' => ['required_if:action,publication_bloquee,publication_supprimee', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'L\'action est obligatoire.',
            'action.in' => 'L\'action sélectionnée n\'est pas valide.',
            'justification.required_if' => 'La justification est obligatoire pour cette action.',
        ];
    }
}