<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'destinataire_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if ($value == auth()->id()) {
                        $fail('Vous ne pouvez pas vous envoyer un message à vous-même.');
                    }
                },
            ],
            'contenu'    => 'nullable|string|min:1|max:5000',
            'type'       => ['nullable', Rule::in(['text', 'image', 'video', 'file', 'sticker'])],
            'media'      => 'nullable|file',
            'media_url'  => 'nullable|url|max:2048',
            'sticker_id' => 'nullable|string|max:50',
            'emoji'      => 'nullable|string|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'destinataire_id.required' => 'Le destinataire est requis.',
            'destinataire_id.exists'   => 'Le destinataire n\'existe pas.',
            'contenu.max'              => 'Le message ne peut pas dépasser 5000 caractères.',
            'media.file'               => 'Le fichier média est invalide.',
            'type.in'                  => 'Le type de message est invalide.',
        ];
    }
}