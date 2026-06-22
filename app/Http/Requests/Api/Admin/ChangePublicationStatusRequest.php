<?php
// app/Http/Requests/Api/Admin/ChangePublicationStatusRequest.php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class ChangePublicationStatusRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'statut' => ['required', Rule::in(['publiee', 'signalee', 'bloquee'])],
            'justification' => ['required_if:statut,bloquee', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
            'justification.required_if' => 'La justification est obligatoire pour le blocage.',
            'justification.min' => 'La justification doit contenir au moins 10 caractères.',
        ];
    }
}