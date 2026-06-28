<?php
// app/Http/Requests/Api/Admin/ReportFilterRequest.php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiRequest;

class ReportFilterRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'statut' => ['nullable', 'string', 'in:en_attente,traite,ignore'],
            'motif' => ['nullable', 'string', 'in:spam,offensant,fausse_info,contenu_inapproprie,autre'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'sort' => ['nullable', 'string', 'in:created_at,motif,statut'],
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