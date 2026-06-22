<?php
// app/Http/Requests/Api/Admin/UserFilterRequest.php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiRequest;

class UserFilterRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'role' => ['nullable', 'string', 'in:admin,eleveur,visiteur'],
            'status' => ['nullable', 'string', 'in:active,bannie'],
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string', 'in:name,email,created_at,role,status'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort' => $this->input('sort', 'created_at'),
            'direction' => $this->input('direction', 'desc'),
            'per_page' => $this->input('per_page', 15),
        ]);
    }
}