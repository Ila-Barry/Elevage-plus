<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait ApiResponseTrait
 * 
 * Standardise toutes les réponses API de l'application.
 * Fournit une structure cohérente pour les succès et les erreurs.
 */
trait ApiResponseTrait
{
    /**
     * Réponse de succès
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = 'Opération réussie', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String()
        ], $code);
    }

    /**
     * Réponse d'erreur
     *
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message = 'Erreur serveur', int $code = 500, $errors = null): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String()
        ], $code);
    }

    /**
     * Réponse de validation échouée
     *
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function validationErrorResponse($errors): JsonResponse
    {
        return $this->errorResponse('Erreur de validation des données', 422, $errors);
    }

    /**
     * Réponse non autorisé
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Non authentifié'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Réponse accès interdit
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = 'Accès interdit'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Réponse ressource non trouvée
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Ressource non trouvée'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Réponse créé (201)
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    protected function createdResponse($data = null, string $message = 'Ressource créée avec succès'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Réponse sans contenu (204)
     *
     * @return JsonResponse
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }
}