<?php
// app/Http/Requests/Api/Admin/PublicationFilterRequest.php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Publication;

class PublicationFilterRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'statut' => ['nullable', 'string', 'in:publiee,signalee,bloquee'],
            'categorie' => ['nullable', 'string', 'in:conseil,experience,alerte'],
            'search' => ['nullable', 'string', 'max:100'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'sort' => ['nullable', 'string', 'in:titre,created_at,nbr_likes,nbr_signalements'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort' => $this->input('sort', 'created_at'),
            'direction' => $this->input('direction', 'desc'),
            'per_page' => $this->input('per_page', 20),
        ]);
    }
}