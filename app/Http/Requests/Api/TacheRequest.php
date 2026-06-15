<?php
// app/Http/Requests/Api/TacheRequest.php

namespace App\Http\Requests\Api;

use App\Models\Tache;
use App\Models\Elevage;
use App\Models\Animal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

/**
 * Request pour la création d'une tâche
 */
class TacheRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'elevage_id' => [
                'required',
                'integer',
                'exists:elevages,id',
                function ($attribute, $value, $fail) {
                    $elevage = Elevage::find($value);
                    if (!$elevage || $elevage->user_id !== auth()->id()) {
                        $fail('Vous devez être propriétaire de cet élevage.');
                    }
                },
            ],
            'animal_id' => [
                'nullable',
                'integer',
                'exists:animals,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $animal = Animal::find($value);
                        if ($animal && $animal->elevage_id != $this->elevage_id) {
                            $fail("L'animal n'appartient pas à cet élevage.");
                        }
                    }
                },
            ],
            'titre' => [
                'required',
                'string',
                'min:3',
                'max:200',
            ],
            'type' => [
                'required',
                'string',
                Rule::in(array_keys(Tache::TYPES)),
            ],
            'date_planifiee' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . Carbon::now()->addYear()->format('Y-m-d'),
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
            'generer_rappels' => [
                'sometimes',
                'boolean',
            ],
            'rappels' => [
                'sometimes',
                'array',
                'in:48h,24h,1h,30min,now',
            ],
        ];
    }

    /**
     * Messages d'erreur
     */
    public function messages(): array
    {
        return [
            'elevage_id.required' => 'L\'élevage est requis.',
            'elevage_id.exists' => 'L\'élevage sélectionné n\'existe pas.',
            'titre.required' => 'Le titre de la tâche est requis.',
            'titre.min' => 'Le titre doit contenir au moins 3 caractères.',
            'type.required' => 'Le type de tâche est requis.',
            'type.in' => 'Type de tâche invalide.',
            'date_planifiee.required' => 'La date planifiée est requise.',
            'date_planifiee.after_or_equal' => 'La date ne peut pas être dans le passé.',
            'rappels.in' => 'Type de rappel invalide.',
        ];
    }

    /**
     * Préparation des données
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('titre')) {
            $this->merge(['titre' => ucfirst(trim($this->titre))]);
        }
    }
}