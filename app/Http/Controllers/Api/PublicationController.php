<?php
// app/Http/Controllers/PublicationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\Commentaire;
use App\Models\Like;
use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Notifications\PublicationNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PublicationController extends Controller
{
    /**
     * Récupérer toutes les publications (mélangées)
     */
    public function index(Request $request)
    {
        $query = Publication::with(['user'])->published();

        if ($request->has('categorie') && $request->categorie !== 'all' && !empty($request->categorie)) {
            $query->byCategory($request->categorie);
        }

        if ($request->has('scope') && $request->scope === 'mine') {
            $query->where('user_id', Auth::id());
        }

        $publications = $query->inRandomOrder()->paginate(10);

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

    public function store(CreateAnimalRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $user = $request->user();
            $data = $request->validated();
            
            // ✅ LOG pour déboguer
            \Log::info('📝 Création animal', [
                'user_id' => $user->id,
                'data' => $data,
                'elevage_id' => $data['elevage_id'] ?? null
            ]);
            
            $elevage = Elevage::where('id', $data['elevage_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
            
            \Log::info('✅ Élevage trouvé', ['elevage_id' => $elevage->id]);
            
            if ($request->hasFile('image')) {
                $data['img_url'] = $this->uploadImage($request->file('image'));
            }
            
            $animal = Animal::create($data);
            \Log::info('✅ Animal créé', ['animal_id' => $animal->id]);
            
            DB::commit();
            
            return $this->successResponse(
                new AnimalResource($animal->load(['elevage', 'pere', 'mere'])),
                'Animal créé avec succès.',
                201
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ Erreur création animal: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Erreur lors de la création de l\'animal: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Mettre à jour une publication
     */
    public function update(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);

        if (!$publication->canManage(Auth::user())) {
            return response()->json([
                'status' => 'error',
                'message' => 'Action non autorisée. Vous n\'êtes pas l\'auteur de cette publication.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'titre' => 'sometimes|string|min:5|max:255',
            'categorie' => 'sometimes|string|in:conseil,experience,alerte',
            'contenu' => 'nullable|string|min:2',
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

        $user = Auth::user();
        $oldTitre = $publication->titre;

        // Gestion des fichiers (comme avant)
        $images = is_array($publication->images) ? $publication->images : [];
        $videos = is_array($publication->videos) ? $publication->videos : [];
        $documents = is_array($publication->documents) ? $publication->documents : [];

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

        $updateData = [];
        if ($request->has('titre')) $updateData['titre'] = $request->titre;
        if ($request->has('categorie')) $updateData['categorie'] = $request->categorie;
        if ($request->has('contenu')) $updateData['contenu'] = $request->contenu;
        $updateData['images'] = $images;
        $updateData['videos'] = $videos;
        $updateData['documents'] = $documents;

        $publication->update($updateData);
        $publication->load('user');

        // 🔔 NOTIFICATION DE MODIFICATION
        try {
            Log::info('📤 Envoi notification modification publication', [
                'user_id' => $user->id,
                'publication_id' => $publication->id,
                'old_titre' => $oldTitre,
                'new_titre' => $publication->titre
            ]);

            $user->notify(new PublicationNotification($publication, PublicationNotification::TYPE_UPDATED));

            Log::info('✅ Notification modification publication envoyée');
        } catch (\Exception $e) {
            Log::error('❌ Erreur notification modification publication', [
                'error' => $e->getMessage()
            ]);
        }

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

        $user = Auth::user();
        $titre = $publication->titre;

        $this->deleteMultipleFiles($publication->getAttributes()['images'] ?? [], 'public');
        $this->deleteMultipleFiles($publication->getAttributes()['videos'] ?? [], 'public');
        $this->deleteMultipleDocuments($publication->getAttributes()['documents'] ?? [], 'public');

        // 🔔 NOTIFICATION DE SUPPRESSION (avant la suppression)
        try {
            Log::info('📤 Envoi notification suppression publication', [
                'user_id' => $user->id,
                'publication_id' => $publication->id,
                'titre' => $titre
            ]);

            $publicationClone = clone $publication;
            $user->notify(new PublicationNotification($publicationClone, PublicationNotification::TYPE_DELETED));

            Log::info('✅ Notification suppression publication envoyée');
        } catch (\Exception $e) {
            Log::error('❌ Erreur notification suppression publication', [
                'error' => $e->getMessage()
            ]);
        }

        $publication->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Publication supprimée avec succès.'
        ]);
    }

    // ============================================================
    // MÉTHODES UTILITAIRES DE FICHIERS (AVEC CRÉATION AUTO DES DOSSIERS)
    // ============================================================

    /**
     * Upload multiple fichiers avec création automatique du dossier
     */
    private function uploadMultipleFiles($files, $directory)
    {
        if (empty($files)) {
            return [];
        }

        try {
            // ✅ CRÉER LE DOSSIER AUTOMATIQUEMENT
            $fullPath = storage_path('app/public/' . $directory);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
                \Log::info('📁 Dossier créé: ' . $fullPath);
            }

            $uploaded = [];
            foreach ($files as $file) {
                \Log::info('📤 Upload du fichier: ' . $file->getClientOriginalName());
                $path = $file->store($directory, 'public');
                if ($path) {
                    $uploaded[] = $path;
                    \Log::info('✅ Fichier uploadé: ' . $path);
                }
            }
            return $uploaded;
        } catch (\Exception $e) {
            \Log::error('❌ Erreur upload: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Upload multiple documents avec création automatique du dossier
     */
    private function uploadMultipleDocuments($files, $directory)
    {
        if (empty($files)) {
            return [];
        }

        // ✅ CRÉER LE DOSSIER AUTOMATIQUEMENT (SOLUTION DÉFINITIVE)
        $fullPath = storage_path('app/public/' . $directory);
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
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

    // ============================================================
    // COMMENTAIRES
    // ============================================================

    public function addComment(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'contenu' => 'required|string|min:1|max:5000',
            'parent_id' => 'nullable|exists:commentaires,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $commentaire = Commentaire::create([
            'publication_id' => $publication->id,
            'user_id' => $user->id,
            'parent_id' => $request->parent_id,
            'contenu' => $request->contenu,
        ]);

        $publication->increment('nbr_commentaires');
        $commentaire->load('user');

        // 🔔 NOTIFICATION DE COMMENTAIRE (uniquement si ce n'est pas l'auteur)
        if ($publication->user_id !== $user->id) {
            try {
                Log::info('📤 Envoi notification commentaire publication', [
                    'publication_id' => $publication->id,
                    'commentateur_id' => $user->id,
                    'auteur_id' => $publication->user_id
                ]);

                $auteur = User::find($publication->user_id);
                if ($auteur) {
                    $auteur->notify(new PublicationNotification(
                        $publication,
                        PublicationNotification::TYPE_COMMENTED,
                        $user,
                        ['comment_content' => $request->contenu]
                    ));
                    Log::info('✅ Notification commentaire envoyée à ' . $auteur->name);
                }
            } catch (\Exception $e) {
                Log::error('❌ Erreur notification commentaire', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Commentaire ajouté avec succès',
            'data' => [
                'id' => $commentaire->id,
                'contenu' => $commentaire->contenu,
                'created_at' => $commentaire->created_at->toIso8601String(),
                'created_at_human' => $commentaire->created_at->diffForHumans(),
                'user' => [
                    'id' => $commentaire->user->id,
                    'name' => $commentaire->user->name,
                    'photo_url' => $commentaire->user->photo_url,
                ]
            ]
        ], 201);
    }


    public function getComments($id)
    {
        $publication = Publication::findOrFail($id);
        
        $commentaires = Commentaire::with(['user'])
            ->where('publication_id', $id)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        $formatted = $commentaires->map(function($comment) {
            return [
                'id' => $comment->id,
                'contenu' => $comment->contenu,
                'created_at' => $comment->created_at->toIso8601String(),
                'created_at_human' => $comment->created_at->diffForHumans(),
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'photo_url' => $comment->user->photo_url,
                ]
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formatted
        ]);
    }

    public function deleteComment($id)
    {
        $commentaire = Commentaire::findOrFail($id);
        
        if (Auth::id() !== $commentaire->user_id && Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Action non autorisée'
            ], 403);
        }

        $publication_id = $commentaire->publication_id;
        $commentaire->delete();
        Publication::where('id', $publication_id)->decrement('nbr_commentaires');

        return response()->json([
            'status' => 'success',
            'message' => 'Commentaire supprimé'
        ]);
    }

    // ============================================================
    // LIKES
    // ============================================================

    public function toggleLike($id)
    {
        $publication = Publication::findOrFail($id);
        $user = Auth::user();
        
        $existingLike = Like::where('publication_id', $publication->id)
                            ->where('user_id', $user->id)
                            ->first();
        
        if ($existingLike) {
            $existingLike->delete();
            $publication->decrement('nbr_likes');
            $liked = false;
            $message = 'Like retiré';
        } else {
            Like::create([
                'publication_id' => $publication->id,
                'user_id' => $user->id,
            ]);
            $publication->increment('nbr_likes');
            $liked = true;
            $message = 'Like ajouté';

            // 🔔 NOTIFICATION DE LIKE (uniquement si ce n'est pas l'auteur)
            if ($publication->user_id !== $user->id) {
                try {
                    Log::info('📤 Envoi notification like publication', [
                        'publication_id' => $publication->id,
                        'likeur_id' => $user->id,
                        'auteur_id' => $publication->user_id
                    ]);

                    $auteur = User::find($publication->user_id);
                    if ($auteur) {
                        $auteur->notify(new PublicationNotification(
                            $publication,
                            PublicationNotification::TYPE_LIKED,
                            $user
                        ));
                        Log::info('✅ Notification like envoyée à ' . $auteur->name);
                    }
                } catch (\Exception $e) {
                    Log::error('❌ Erreur notification like', [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        $publication->refresh();
        
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => [
                'liked' => $liked,
                'total_likes' => $publication->nbr_likes
            ]
        ]);
    }


    public function checkLike($id)
    {
        $publication = Publication::findOrFail($id);
        $user = Auth::user();
        
        $liked = Like::where('publication_id', $publication->id)
                    ->where('user_id', $user->id)
                    ->exists();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'liked' => $liked,
                'total_likes' => $publication->nbr_likes
            ]
        ]);
    }

    public function getLikes($id)
    {
        $publication = Publication::findOrFail($id);
        
        $likes = Like::with('user')
            ->where('publication_id', $publication->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($like) {
                return [
                    'id' => $like->user->id,
                    'name' => $like->user->name,
                    'photo_url' => $like->user->photo_url,
                    'liked_at' => $like->created_at->diffForHumans(),
                ];
            });
        
        return response()->json([
            'status' => 'success',
            'data' => $likes
        ]);
    }

    // ============================================================
    // PARTAGES
    // ============================================================

    public function share(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'plateforme' => 'required|string|in:facebook,twitter,whatsapp,copie_lien',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plateforme de partage invalide',
                'errors' => $validator->errors()
            ], 422);
        }

        Share::create([
            'publication_id' => $publication->id,
            'user_id' => $user->id,
            'plateforme' => $request->plateforme,
        ]);

        $publication->increment('nbr_partages');

        // 🔔 NOTIFICATION DE PARTAGE (uniquement si ce n'est pas l'auteur)
        if ($publication->user_id !== $user->id) {
            try {
                Log::info('📤 Envoi notification partage publication', [
                    'publication_id' => $publication->id,
                    'partageur_id' => $user->id,
                    'auteur_id' => $publication->user_id,
                    'plateforme' => $request->plateforme
                ]);

                $auteur = User::find($publication->user_id);
                if ($auteur) {
                    $auteur->notify(new PublicationNotification(
                        $publication,
                        PublicationNotification::TYPE_SHARED,
                        $user,
                        ['platform' => $request->plateforme]
                    ));
                    Log::info('✅ Notification partage envoyée à ' . $auteur->name);
                }
            } catch (\Exception $e) {
                Log::error('❌ Erreur notification partage', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        $shareUrl = url('/blog/' . $publication->id);
        
        $platformUrls = [
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareUrl),
            'twitter' => 'https://twitter.com/intent/tweet?url=' . urlencode($shareUrl) . '&text=' . urlencode($publication->titre),
            'whatsapp' => 'https://api.whatsapp.com/send?text=' . urlencode($publication->titre . ' - ' . $shareUrl),
            'copie_lien' => $shareUrl,
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Partage effectué avec succès',
            'data' => [
                'share_url' => $platformUrls[$request->plateforme] ?? $shareUrl,
                'total_shares' => $publication->nbr_partages,
                'plateforme' => $request->plateforme,
            ]
        ]);
    }

    public function getShares($id)
    {
        $publication = Publication::findOrFail($id);
        
        $shares = Share::with('user')
            ->where('publication_id', $publication->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($share) {
                return [
                    'id' => $share->id,
                    'plateforme' => $share->plateforme,
                    'user' => [
                        'id' => $share->user->id,
                        'name' => $share->user->name,
                        'photo_url' => $share->user->photo_url,
                    ],
                    'shared_at' => $share->created_at->diffForHumans(),
                ];
            });
        
        $stats = Share::where('publication_id', $publication->id)
            ->select('plateforme', \DB::raw('count(*) as total'))
            ->groupBy('plateforme')
            ->get()
            ->pluck('total', 'plateforme')
            ->toArray();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $publication->nbr_partages,
                'shares' => $shares,
                'stats' => $stats,
            ]
        ]);
    }
}