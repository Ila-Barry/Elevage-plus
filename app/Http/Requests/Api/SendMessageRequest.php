<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return true; // Assurez-vous que c'est bien à true si géré par middleware
    }

    /**
     * Obtenir les règles de validation qui s'appliquent à la requête.
     */
    public function rules(): array
    {
        return [
            // 🔴 CORRECTION : On s'assure de cibler simplement la table 'users' et la colonne 'id'
            'destinataire_id' => 'required|integer|exists:users,id',
            'contenu'         => 'nullable|string',
            'type'            => 'nullable|string|in:text,image,video,audio,file,sticker',
            'sticker_id'      => 'nullable|string',
            'emoji'           => 'nullable|string',
        ];
    }

    /**
     * Personnalisation des messages d'erreur.
     */
    public function messages(): array
    {
        return [
            'destinataire_id.required' => "L'identifiant du destinataire est obligatoire.",
            'destinataire_id.exists'   => "Le destinataire n'existe pas.",
        ];
    }
}