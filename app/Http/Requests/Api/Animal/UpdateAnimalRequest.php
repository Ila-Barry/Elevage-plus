<?php
// app/Http/Requests/Api/Animal/UpdateAnimalRequest.php

namespace App\Http\Requests\Api\Animal;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Animal;
use Illuminate\Validation\Rule;

/**
 * Requête de validation pour la mise à jour d'un animal
 */
class UpdateAnimalRequest extends ApiRequest
{
    /**
     * Règles de validation
     */
    public function rules(): array
    {
        $animalId = $this->route('animal');
        
        return [
            'nom' => [
                'sometimes',
                'string',
                'min:2',
                'max:100',
            ],
            'espece' => [
                'sometimes',
                Rule::in(array_keys(Animal::ESPECES)),
            ],
            'race' => [
                'nullable',
                'string',
                'max:100',
            ],
            'date_naissance' => [
                'sometimes',
                'date',
                'before_or_equal:today',
            ],
            'poids' => [
                'sometimes',
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
                Rule::unique('animaux', 'numero_identification')->ignore($animalId),
            ],
            'statut' => [
                'sometimes',
                Rule::in(array_keys(Animal::STATUTS)),
            ],
            'date_deces' => [
                'nullable',
                'date',
                'before_or_equal:today',
                'required_if:statut,decede',
            ],
            'motif_deces' => [
                'nullable',
                'string',
                'max:500',
                'required_if:statut,decede',
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
                'max:5048',
            ],
            'delete_image' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'nom.min' => 'Le nom doit contenir au moins 2 caractères.',
            'espece.in' => 'L\'espèce sélectionnée n\'est pas valide.',
            'date_naissance.before_or_equal' => 'La date de naissance ne peut pas être dans le futur.',
            'poids.min' => 'Le poids doit être supérieur ou égal à 0.',
            'statut_sanitaire.in' => 'Le statut sanitaire n\'est pas valide.',
            'sexe.in' => 'Le sexe sélectionné n\'est pas valide.',
            'numero_identification.unique' => 'Ce numéro d\'identification est déjà utilisé.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
            'date_deces.required_if' => 'La date de décès est obligatoire pour un animal décédé.',
            'motif_deces.required_if' => 'Le motif du décès est obligatoire pour un animal décédé.',
            'pere_id.exists' => 'Le père sélectionné n\'existe pas.',
            'mere_id.exists' => 'La mère sélectionnée n\'existe pas.',
            'image.image' => 'Le fichier doit être une image.',
            'image.max' => 'L\'image ne doit pas dépasser 5 Mo.',
        ];
    }
}