<?php
// app/Http/Requests/Api/Tache/UpdateTacheRequest.php

namespace App\Http\Requests\Api\Tache;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Tache;
use Illuminate\Validation\Rule;

class UpdateTacheRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'titre' => [
                'sometimes',
                'string',
                'min:3',
                'max:200',
            ],
            'type' => [
                'sometimes',
                Rule::in(array_keys(Tache::TYPES)),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'date_planifiee' => [
                'sometimes',
                'date',
                'after_or_equal:now',
            ],
            'priorite' => [
                'sometimes',
                Rule::in(array_keys(Tache::PRIORITES)),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
            'terminee' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'titre.min' => 'Le titre doit contenir au moins 3 caractères.',
            'titre.max' => 'Le titre ne peut pas dépasser 200 caractères.',
            'type.in' => 'Le type de tâche sélectionné n\'est pas valide.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
            'date_planifiee.after_or_equal' => 'La date planifiée ne peut pas être dans le passé.',
            'priorite.in' => 'La priorité sélectionnée n\'est pas valide.',
            'notes.max' => 'Les notes ne peuvent pas dépasser 500 caractères.',
        ];
    }
}