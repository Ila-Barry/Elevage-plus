<?php
// app/Http/Requests/Api/Animal/CreateAnimalRequest.php

namespace App\Http\Requests\Api\Animal;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Animal;
use Illuminate\Validation\Rule;

/**
 * Requête de validation pour la création d'un animal
 */
class CreateAnimalRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'elevage_id' => [
                'required',
                'exists:elevages,id',
            ],
            'nom' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],
            'espece' => [
                'required',
                'string',
                Rule::in(array_keys(Animal::ESPECES)),
            ],
            'race' => [
                'nullable',
                'string',
                'max:100',
            ],
            'date_naissance' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'poids' => [
                'required',
                'numeric',
                'min:0',
                'max:9999.99',
            ],
            'statut_sanitaire' => [
                'sometimes',
                Rule::in(array_keys(Animal::STATUTS_SANITAIRES)),
            ],
            'sexe' => [
                'sometimes',
                Rule::in(array_keys(Animal::SEXES)),
            ],
            'couleur' => [
                'nullable',
                'string',
                'max:50',
            ],
            'signes_particuliers' => [
                'nullable',
                'string',
                'max:500',
            ],
            'numero_identification' => [
                'nullable',
                'string',
                'max:50',
                'unique:animaux,numero_identification',
            ],
            'pere_id' => [
                'nullable',
                'exists:animaux,id',
            ],
            'mere_id' => [
                'nullable',
                'exists:animaux,id',
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'elevage_id.required' => 'L\'élevage est obligatoire.',
            'elevage_id.exists' => 'L\'élevage sélectionné n\'existe pas.',
            
            'nom.required' => 'Le nom de l\'animal est obligatoire.',
            'nom.min' => 'Le nom doit contenir au moins 2 caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            
            'espece.required' => 'L\'espèce est obligatoire.',
            'espece.in' => 'L\'espèce sélectionnée n\'est pas valide.',
            
            'date_naissance.required' => 'La date de naissance est obligatoire.',
            'date_naissance.before_or_equal' => 'La date de naissance ne peut pas être dans le futur.',
            
            'poids.required' => 'Le poids est obligatoire.',
            'poids.min' => 'Le poids doit être supérieur ou égal à 0.',
            
            'statut_sanitaire.in' => 'Le statut sanitaire n\'est pas valide.',
            'sexe.in' => 'Le sexe sélectionné n\'est pas valide.',
            
            'numero_identification.unique' => 'Ce numéro d\'identification est déjà utilisé.',
            
            'pere_id.exists' => 'Le père sélectionné n\'existe pas.',
            'mere_id.exists' => 'La mère sélectionnée n\'existe pas.',
            
            'image.image' => 'Le fichier doit être une image.',
            'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ];
    }

    /**
     * Validation supplémentaire après les règles de base
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Vérifier que l'élevage appartient à l'utilisateur
            $elevage = \App\Models\Elevage::find($this->elevage_id);
            if ($elevage && $elevage->user_id !== auth()->id()) {
                $validator->errors()->add('elevage_id', 'Cet élevage ne vous appartient pas.');
            }
            
            // Vérifier que le père appartient au même élevage
            if ($this->pere_id) {
                $pere = \App\Models\Animal::find($this->pere_id);
                if ($pere && $pere->elevage_id !== $this->elevage_id) {
                    $validator->errors()->add('pere_id', 'Le père doit appartenir au même élevage.');
                }
            }
            
            // Vérifier que la mère appartient au même élevage
            if ($this->mere_id) {
                $mere = \App\Models\Animal::find($this->mere_id);
                if ($mere && $mere->elevage_id !== $this->elevage_id) {
                    $validator->errors()->add('mere_id', 'La mère doit appartenir au même élevage.');
                }
            }
        });
    }
}