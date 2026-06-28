<?php
// app/Http/Requests/Api/Admin/ChangeUserStatusRequest.php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class ChangeUserStatusRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['active', 'bannie'])],
            'motif_ban' => ['required_if:status,bannie', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
            'motif_ban.required_if' => 'Le motif du bannissement est obligatoire.',
            'motif_ban.min' => 'Le motif doit contenir au moins 10 caractères.',
        ];
    }
}