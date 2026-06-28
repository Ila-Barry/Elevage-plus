<?php
// app/Http/Requests/Api/Admin/ResetPasswordRequest.php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiRequest;

class ResetPasswordRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'notify_user' => ['sometimes', 'boolean'],
            'message' => ['nullable', 'string', 'max:500'],
        ];
    }
}