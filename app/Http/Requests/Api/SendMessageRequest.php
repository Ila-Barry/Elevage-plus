<?php
// app/Http/Requests/Api/SendMessageRequest.php (Version mise à jour)

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request pour l'envoi de message (supporte texte, images, vidéos, fichiers, stickers)
 */
class SendMessageRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        return [
            'destinataire_id' => 'required|exists:users,id|different:user',
            
            // Soit du texte, soit un fichier, soit les deux
            'contenu' => 'nullable|string|min:1|max:5000',
            
            // Types de médias supportés
            'type' => ['nullable', Rule::in(['text', 'image', 'video', 'file', 'sticker'])],
            
            // Fichiers
            'media' => 'nullable|file',
            'media_url' => 'nullable|url|max:2048', // Pour les messages pré-uploadés
            
            // Emojis et stickers prédéfinis
            'sticker_id' => 'nullable|string|max:50', // ID d'un sticker prédéfini
            'emoji' => 'nullable|string|max:10',
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'destinataire_id.required' => 'Le destinataire est requis.',
            'destinataire_id.exists' => 'Le destinataire n\'existe pas.',
            'destinataire_id.different' => 'Vous ne pouvez pas vous envoyer un message à vous-même.',
            'contenu.max' => 'Le message ne peut pas dépasser 5000 caractères.',
            'media.file' => 'Le fichier média est invalide.',
            'type.in' => 'Le type de message est invalide.',
        ];
    }
}