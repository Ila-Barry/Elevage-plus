<?php
// app/Http/Requests/Api/Tache/CreateTacheRequest.php

namespace App\Http\Requests\Api\Tache;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Tache;
use Illuminate\Validation\Rule;

class CreateTacheRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'animal_id' => [
                'nullable',
                'exists:animaux,id',
            ],
            'elevage_id' => [
                'required',
                'exists:elevages,id',
            ],
            'titre' => [
                'required',
                'string',
                'min:3',
                'max:200',
            ],
            'type' => [
                'required',
                Rule::in(array_keys(Tache::TYPES)),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'date_planifiee' => [
                'required',
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
            // 'rappel' n'existe plus car les rappels sont automatiques
        ];
    }

    public function messages(): array
    {
        return [
            'elevage_id.required' => 'L\'élevage est obligatoire.',
            'elevage_id.exists' => 'L\'élevage sélectionné n\'existe pas.',
            
            'animal_id.exists' => 'L\'animal sélectionné n\'existe pas.',
            
            'titre.required' => 'Le titre de la tâche est obligatoire.',
            'titre.min' => 'Le titre doit contenir au moins 3 caractères.',
            'titre.max' => 'Le titre ne peut pas dépasser 200 caractères.',
            
            'type.required' => 'Le type de tâche est obligatoire.',
            'type.in' => 'Le type de tâche sélectionné n\'est pas valide.',
            
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
            
            'date_planifiee.required' => 'La date planifiée est obligatoire.',
            'date_planifiee.after_or_equal' => 'La date planifiée ne peut pas être dans le passé.',
            
            'priorite.in' => 'La priorité sélectionnée n\'est pas valide.',
            
            'notes.max' => 'Les notes ne peuvent pas dépasser 500 caractères.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Vérifier que l'élevage appartient à l'utilisateur
            $elevage = \App\Models\Elevage::find($this->elevage_id);
            if ($elevage && $elevage->user_id !== auth()->id()) {
                $validator->errors()->add('elevage_id', 'Cet élevage ne vous appartient pas.');
            }
            
            // Vérifier que l'animal appartient au même élevage
            if ($this->animal_id) {
                $animal = \App\Models\Animal::find($this->animal_id);
                if ($animal && $animal->elevage_id !== $this->elevage_id) {
                    $validator->errors()->add('animal_id', 'L\'animal doit appartenir à l\'élevage sélectionné.');
                }
            }
        });
    }
}