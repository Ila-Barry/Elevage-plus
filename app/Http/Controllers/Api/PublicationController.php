<?php
// app/Http/Controllers/PublicationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PublicationController extends Controller
{
    /**
     * Récupérer toutes les publications (mélangées)
     */
    public function index(Request $request)
    {
        $query = Publication::with(['user'])->published();

        // Filtrer par catégorie
        if ($request->has('categorie') && $request->categorie !== 'all' && !empty($request->categorie)) {
            $query->byCategory($request->categorie);
        }

        // Filtrer par auteur (mes publications)
        if ($request->has('scope') && $request->scope === 'mine') {
            $query->where('user_id', Auth::id());
        }

        // ✅ Mélange aléatoire pour un flux dynamique
        $publications = $query->inRandomOrder()->paginate(10);

        // Formatage des données
        $formatted = $publications->through(function ($post) {
            return $this->formatPublication($post);
        });

        return response()->json([
            'status' => 'success',
            'data' => $formatted,
            'meta' => [
                'current_page' => $publications->currentPage(),
                'last_page' => $publications->lastPage(),
                'total' => $publications->total(),
            ]
        ]);
    }

    /**
     * Créer une nouvelle publication
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|min:5|max:255',
            'categorie' => 'required|string|in:conseil,experience,alerte',
            'contenu' => 'required|string|min:10',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'videos.*' => 'nullable|file|mimes:mp4,mov,avi,webm|max:51200',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // Traitement des fichiers
        $images = $this->uploadMultipleFiles($request->file('images'), 'uploads/publications/images');
        $videos = $this->uploadMultipleFiles($request->file('videos'), 'uploads/publications/videos');
        $documents = $this->uploadMultipleDocuments($request->file('documents'), 'uploads/publications/documents');

        // Création
        $publication = Publication::create([
            'titre' => $request->titre,
            'categorie' => $request->categorie,
            'contenu' => $request->contenu,
            'user_id' => Auth::id(),
            'images' => $images,
            'videos' => $videos,
            'documents' => $documents,
            'published_at' => now(),
        ]);

        // Charger la relation user
        $publication->load('user');

        return response()->json([
            'status' => 'success',
            'message' => 'Article publié avec succès !',
            'data' => $this->formatPublication($publication)
        ], 201);
    }

    /**
     * Mettre à jour une publication
     */
    public function update(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);

        // Vérifier les droits
        if (!$publication->canManage(Auth::user())) {
            return response()->json([
                'status' => 'error',
                'message' => 'Action non autorisée. Vous n\'êtes pas l\'auteur de cette publication.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'titre' => 'sometimes|string|min:5|max:255',
            'categorie' => 'sometimes|string|in:conseil,experience,alerte',
            'contenu' => 'sometimes|string|min:10',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'videos.*' => 'nullable|file|mimes:mp4,mov,avi,webm|max:51200',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // Récupérer les fichiers existants
        $images = $publication->getAttributes()['images'] ?? [];
        $videos = $publication->getAttributes()['videos'] ?? [];
        $documents = $publication->getAttributes()['documents'] ?? [];

        // Supprimer des fichiers si demandé
        if ($request->has('delete_images') && $request->delete_images) {
            $this->deleteMultipleFiles($images, 'public');
            $images = [];
        }
        if ($request->has('delete_videos') && $request->delete_videos) {
            $this->deleteMultipleFiles($videos, 'public');
            $videos = [];
        }
        if ($request->has('delete_documents') && $request->delete_documents) {
            $this->deleteMultipleDocuments($documents, 'public');
            $documents = [];
        }

        // Ajouter les nouveaux fichiers
        if ($request->hasFile('images')) {
            $newImages = $this->uploadMultipleFiles($request->file('images'), 'uploads/publications/images');
            $images = array_merge($images, $newImages);
        }
        if ($request->hasFile('videos')) {
            $newVideos = $this->uploadMultipleFiles($request->file('videos'), 'uploads/publications/videos');
            $videos = array_merge($videos, $newVideos);
        }
        if ($request->hasFile('documents')) {
            $newDocuments = $this->uploadMultipleDocuments($request->file('documents'), 'uploads/publications/documents');
            $documents = array_merge($documents, $newDocuments);
        }

        // Mise à jour
        $updateData = [];
        if ($request->has('titre')) $updateData['titre'] = $request->titre;
        if ($request->has('categorie')) $updateData['categorie'] = $request->categorie;
        if ($request->has('contenu')) $updateData['contenu'] = $request->contenu;
        $updateData['images'] = $images;
        $updateData['videos'] = $videos;
        $updateData['documents'] = $documents;

        $publication->update($updateData);
        $publication->load('user');

        return response()->json([
            'status' => 'success',
            'message' => 'Publication mise à jour avec succès !',
            'data' => $this->formatPublication($publication)
        ]);
    }

    /**
     * Supprimer une publication
     */
    public function destroy($id)
    {
        $publication = Publication::findOrFail($id);

        if (!$publication->canManage(Auth::user())) {
            return response()->json([
                'status' => 'error',
                'message' => 'Action non autorisée.'
            ], 403);
        }

        // Supprimer les fichiers
        $this->deleteMultipleFiles($publication->getAttributes()['images'] ?? [], 'public');
        $this->deleteMultipleFiles($publication->getAttributes()['videos'] ?? [], 'public');
        $this->deleteMultipleDocuments($publication->getAttributes()['documents'] ?? [], 'public');

        $publication->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Publication supprimée avec succès.'
        ]);
    }

    // ============================================================
    // MÉTHODES UTILITAIRES DE FICHIERS
    // ============================================================

    /**
     * Upload multiple fichiers
     */
    private function uploadMultipleFiles($files, $directory)
    {
        if (empty($files)) {
            return [];
        }

        $uploaded = [];
        foreach ($files as $file) {
            $path = $file->store($directory, 'public');
            $uploaded[] = $path;
        }
        return $uploaded;
    }

    /**
     * Upload multiple documents (avec noms)
     */
    private function uploadMultipleDocuments($files, $directory)
    {
        if (empty($files)) {
            return [];
        }

        $uploaded = [];
        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $path = $file->store($directory, 'public');
            $uploaded[] = [
                'url' => $path,
                'nom' => $originalName
            ];
        }
        return $uploaded;
    }

    /**
     * Supprimer plusieurs fichiers
     */
    private function deleteMultipleFiles($files, $disk = 'public')
    {
        if (empty($files)) {
            return;
        }

        foreach ($files as $file) {
            if (is_array($file) && isset($file['url'])) {
                Storage::disk($disk)->delete($file['url']);
            } else {
                Storage::disk($disk)->delete($file);
            }
        }
    }

    /**
     * Supprimer plusieurs documents
     */
    private function deleteMultipleDocuments($documents, $disk = 'public')
    {
        if (empty($documents)) {
            return;
        }

        foreach ($documents as $doc) {
            if (is_array($doc) && isset($doc['url'])) {
                Storage::disk($disk)->delete($doc['url']);
            }
        }
    }

    /**
     * Formater une publication pour la réponse API
     */
    private function formatPublication(Publication $post)
    {
        $user = Auth::user();
        
        return [
            'id' => $post->id,
            'titre' => $post->titre,
            'categorie' => $post->categorie,
            'categorie_label' => $this->getCategorieLabel($post->categorie),
            'contenu' => $post->contenu,
            'resume' => $post->resume,
            'images' => $post->images ?? [],
            'videos' => $post->videos ?? [],
            'documents' => $post->documents ?? [],
            'statistiques' => [
                'likes' => $post->nbr_likes,
                'commentaires' => $post->nbr_commentaires,
                'partages' => $post->nbr_partages,
                'vues' => $post->nbr_vues,
            ],
            'interactions' => [
                'liked_by_user' => $post->isLikedByUser($user),
            ],
            'user' => [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'photo_url' => $post->user->photo_url ?? null,
                'role' => $post->user->role ?? 'user',
            ],
            'can_manage' => $post->canManage($user),
            'published_at_human' => $post->published_at?->diffForHumans(),
            'published_at' => $post->published_at?->toIso8601String(),
            'created_at' => $post->created_at?->toIso8601String(),
            'updated_at' => $post->updated_at?->toIso8601String(),
        ];
    }

    private function getCategorieLabel($categorie)
    {
        return match($categorie) {
            'experience' => '💡 Expérience',
            'conseil' => '🌾 Conseil',
            'alerte' => '⚠️ Alerte',
            default => $categorie,
        };
    }
}