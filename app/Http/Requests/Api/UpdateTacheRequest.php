<?php
// app/Http/Requests/Api/UpdateTacheRequest.php

namespace App\Http\Requests\Api;

use App\Models\Tache;
use App\Models\Animal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

/**
 * Request pour la mise à jour d'une tâche
 */
class UpdateTacheRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

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
                'string',
                Rule::in(array_keys(Tache::TYPES)),
            ],
            'date_planifiee' => [
                'sometimes',
                'date',
                'after_or_equal:today',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
            'animal_id' => [
                'nullable',
                'exists:animals,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'titre.min' => 'Le titre doit contenir au moins 3 caractères.',
            'type.in' => 'Type de tâche invalide.',
            'date_planifiee.after_or_equal' => 'La date ne peut pas être dans le passé.',
        ];
    }
}