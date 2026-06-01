<?php
// app/Http/Requests/ApiRequest.php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiResponseTrait;

/**
 * Classe de base ApiRequest
 * 
 * Toutes les requêtes API doivent étendre cette classe
 * pour bénéficier d'une gestion unifiée des erreurs de validation.
 */
abstract class ApiRequest extends FormRequest
{
    use ApiResponseTrait;

    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     * Par défaut, toutes les requêtes API sont accessibles,
     * mais les classes filles peuvent surcharger cette méthode.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Gère l'échec de validation.
     * Au lieu de rediriger vers une page, retourne une réponse JSON.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->validationErrorResponse($validator->errors())
        );
    }

    /**
     * Gère l'échec d'autorisation.
     *
     * @throws HttpResponseException
     */
    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            $this->forbiddenResponse('Vous n\'êtes pas autorisé à effectuer cette action')
        );
    }

    /**
     * Nettoie et filtre les données entrantes.
     * Les classes filles peuvent surcharger cette méthode.
     */
    protected function prepareForValidation(): void
    {
        // Trim des champs string
        $this->merge(array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $this->all()));
    }
}