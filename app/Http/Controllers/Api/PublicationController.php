<?php
// app/Http/Controllers/Api/PublicationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Publication\CreatePublicationRequest;
use App\Http\Requests\Api\Publication\UpdatePublicationRequest;
use App\Http\Requests\Api\Publication\ReportPublicationRequest;
use App\Http\Requests\Api\CommentaireRequest;
use App\Http\Resources\PublicationResource;
use App\Http\Resources\CommentaireResource;
use App\Models\Publication;
use App\Models\Commentaire;
use App\Models\Like;
use App\Models\Report;
use App\Models\Share;
use App\Events\PublicationViewed;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Contrôleur PublicationController
 * 
 * Gère toutes les opérations liées aux publications :
 * - CRUD des publications
 * - Gestion des likes, commentaires, partages
 * - Gestion des signalements
 * - Compteur de vues
 * - Upload de fichiers
 */
class PublicationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Constructeur avec middleware
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except([
            'index',
            'show',
            'publicIndex',
            'publicShow',
        ]);
    }

    // ========== CRUD PUBLICATIONS ==========

    /**
     * Liste des publications (publique)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Publication::with('user')
            ->published()
            ->where('statut', 'publiee');

        // Filtre par catégorie
        if ($request->filled('categorie')) {
            $query->byCategory($request->categorie);
        }

        // Filtre par recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'LIKE', "%{$search}%")
                  ->orWhere('contenu', 'LIKE', "%{$search}%");
            });
        }

        // Tri
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'popular':
                $query->orderBy('nbr_likes', 'desc');
                break;
            case 'most_viewed':
                $query->orderBy('nbr_vues', 'desc');
                break;
            case 'most_commented':
                $query->orderBy('nbr_commentaires', 'desc');
                break;
            default:
                $query->orderBy('published_at', 'desc');
        }

        $perPage = $request->get('per_page', 15);
        $publications = $query->paginate($perPage);

        return $this->successResponse([
            'data' => PublicationResource::collection($publications),
            'meta' => [
                'current_page' => $publications->currentPage(),
                'last_page' => $publications->lastPage(),
                'per_page' => $publications->perPage(),
                'total' => $publications->total(),
            ],
        ]);
    }

    /**
     * Créer une nouvelle publication
     *
     * @param CreatePublicationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePublicationRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();
            $data = $request->validated();
            $data['user_id'] = $user->id;
            $data['published_at'] = now();

            // Upload de l'image
            if ($request->hasFile('image')) {
                $data['image_url'] = $this->uploadFile($request->file('image'), 'publications/images');
            }

            // Upload de la vidéo
            if ($request->hasFile('video')) {
                $data['video_url'] = $this->uploadFile($request->file('video'), 'publications/videos');
            }

            // Upload du fichier
            if ($request->hasFile('fichier')) {
                $fichier = $request->file('fichier');
                $data['fichier_url'] = $this->uploadFile($fichier, 'publications/files');
                $data['fichier_nom'] = $fichier->getClientOriginalName();
            }

            $publication = Publication::create($data);

            DB::commit();

            return $this->successResponse(
                PublicationResource::make($publication->load('user')),
                'Publication créée avec succès.',
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur création publication: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la création de la publication.', 500);
        }
    }

    /**
     * Afficher une publication spécifique
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function show($id)
    {
        $publication = Publication::with(['user', 'commentaires.user', 'commentaires.replies.user'])
            ->where('statut', '!=', 'bloquee')
            ->findOrFail($id);

        // Si la publication est bloquée, seul l'auteur ou l'admin peut la voir
        if ($publication->statut === 'bloquee') {
            $user = auth()->user();
            if (!$user || ($user->id !== $publication->user_id && !$user->isAdmin())) {
                return $this->errorResponse('Cette publication n\'est pas disponible.', 403);
            }
        }

        // Déclencher l'event pour incrémenter les vues
        event(new PublicationViewed($publication));

        return $this->successResponse(
            PublicationResource::make($publication)
        );
    }

    /**
     * Mettre à jour une publication
     *
     * @param UpdatePublicationRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePublicationRequest $request, $id)
    {
        $publication = Publication::findOrFail($id);
        $user = $request->user();

        // Vérifier les permissions
        if ($user->id !== $publication->user_id && !$user->isAdmin()) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à modifier cette publication.');
        }

        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Gestion de l'image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image
                if ($publication->image_url) {
                    $this->deleteFile($publication->image_url);
                }
                $data['image_url'] = $this->uploadFile($request->file('image'), 'publications/images');
            } elseif ($request->input('delete_image')) {
                if ($publication->image_url) {
                    $this->deleteFile($publication->image_url);
                }
                $data['image_url'] = null;
            }

            // Gestion de la vidéo
            if ($request->hasFile('video')) {
                if ($publication->video_url) {
                    $this->deleteFile($publication->video_url);
                }
                $data['video_url'] = $this->uploadFile($request->file('video'), 'publications/videos');
            } elseif ($request->input('delete_video')) {
                if ($publication->video_url) {
                    $this->deleteFile($publication->video_url);
                }
                $data['video_url'] = null;
            }

            // Gestion du fichier
            if ($request->hasFile('fichier')) {
                if ($publication->fichier_url) {
                    $this->deleteFile($publication->fichier_url);
                }
                $fichier = $request->file('fichier');
                $data['fichier_url'] = $this->uploadFile($fichier, 'publications/files');
                $data['fichier_nom'] = $fichier->getClientOriginalName();
            } elseif ($request->input('delete_fichier')) {
                if ($publication->fichier_url) {
                    $this->deleteFile($publication->fichier_url);
                }
                $data['fichier_url'] = null;
                $data['fichier_nom'] = null;
            }

            $publication->update($data);

            DB::commit();

            return $this->successResponse(
                PublicationResource::make($publication->load('user')),
                'Publication mise à jour avec succès.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur mise à jour publication: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour de la publication.', 500);
        }
    }

    /**
     * Supprimer une publication
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);
        $user = $request->user();

        // Vérifier les permissions
        if ($user->id !== $publication->user_id && !$user->isAdmin()) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à supprimer cette publication.');
        }

        DB::beginTransaction();

        try {
            // Supprimer les fichiers associés
            if ($publication->image_url) {
                $this->deleteFile($publication->image_url);
            }
            if ($publication->video_url) {
                $this->deleteFile($publication->video_url);
            }
            if ($publication->fichier_url) {
                $this->deleteFile($publication->fichier_url);
            }

            // Supprimer les likes, commentaires, signalements, partages (cascade)
            $publication->delete();

            DB::commit();

            return $this->successResponse(null, 'Publication supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur suppression publication: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression de la publication.', 500);
        }
    }

    // ========== GESTION DES LIKES ==========

    /**
     * Liker ou unliker une publication
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleLike(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);
        $user = $request->user();

        // Vérifier que la publication n'est pas bloquée
        if ($publication->statut === 'bloquee') {
            return $this->errorResponse('Cette publication n\'est pas disponible.', 403);
        }

        DB::beginTransaction();

        try {
            $like = Like::where('publication_id', $publication->id)
                ->where('user_id', $user->id)
                ->first();

            if ($like) {
                // Unlike
                $like->delete();
                $publication->decrementLikes();
                $message = 'Like retiré.';
                $liked = false;
            } else {
                // Like
                Like::create([
                    'publication_id' => $publication->id,
                    'user_id' => $user->id,
                ]);
                $publication->incrementLikes();
                $message = 'Publication likée.';
                $liked = true;
            }

            DB::commit();

            return $this->successResponse([
                'liked' => $liked,
                'total_likes' => $publication->fresh()->nbr_likes,
            ], $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur like/unlike: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de l\'opération.', 500);
        }
    }

    // ========== GESTION DES COMMENTAIRES ==========

    /**
     * Ajouter un commentaire
     *
     * @param CommentaireRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addComment(CommentaireRequest $request, $id)
    {
        $publication = Publication::findOrFail($id);
        $user = $request->user();

        // Vérifier que la publication n'est pas bloquée
        if ($publication->statut === 'bloquee') {
            return $this->errorResponse('Cette publication n\'est pas disponible.', 403);
        }

        DB::beginTransaction();

        try {
            $commentaire = Commentaire::create([
                'publication_id' => $publication->id,
                'user_id' => $user->id,
                'parent_id' => $request->parent_id,
                'contenu' => $request->contenu,
            ]);

            $publication->incrementCommentaires();

            DB::commit();

            return $this->successResponse(
                new CommentaireResource($commentaire->load('user')),
                'Commentaire ajouté avec succès.',
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur ajout commentaire: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de l\'ajout du commentaire.', 500);
        }
    }

    /**
     * Modifier un commentaire
     *
     * @param CommentaireRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateComment(CommentaireRequest $request, $id)
    {
        $commentaire = Commentaire::findOrFail($id);
        $user = $request->user();

        // Vérifier les permissions
        if ($user->id !== $commentaire->user_id && !$user->isAdmin()) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à modifier ce commentaire.');
        }

        try {
            $commentaire->update([
                'contenu' => $request->contenu,
                'is_edited' => true,
            ]);

            return $this->successResponse(
                new CommentaireResource($commentaire->load('user')),
                'Commentaire modifié avec succès.'
            );

        } catch (\Exception $e) {
            \Log::error('Erreur modification commentaire: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la modification du commentaire.', 500);
        }
    }

    /**
     * Supprimer un commentaire
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteComment(Request $request, $id)
    {
        $commentaire = Commentaire::findOrFail($id);
        $user = $request->user();

        // Vérifier les permissions
        if ($user->id !== $commentaire->user_id && !$user->isAdmin()) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à supprimer ce commentaire.');
        }

        DB::beginTransaction();

        try {
            $publication = $commentaire->publication;
            
            // Compter le nombre de commentaires à supprimer (incluant les réponses)
            $count = 1 + $commentaire->replies()->count();
            
            $commentaire->delete();
            
            // Décrémenter le compteur de la publication
            $publication->decrement('nbr_commentaires', $count);

            DB::commit();

            return $this->successResponse(null, 'Commentaire supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur suppression commentaire: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression du commentaire.', 500);
        }
    }

    // ========== GESTION DES SIGNALEMENTS ==========

    /**
     * Signaler une publication
     *
     * @param ReportPublicationRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function report(ReportPublicationRequest $request, $id)
    {
        $publication = Publication::findOrFail($id);
        $user = $request->user();

        // Vérifier que l'utilisateur ne signale pas sa propre publication
        if ($user->id === $publication->user_id) {
            return $this->errorResponse('Vous ne pouvez pas signaler votre propre publication.', 422);
        }

        // Vérifier si l'utilisateur a déjà signalé
        if ($publication->isReportedByUser($user)) {
            return $this->errorResponse('Vous avez déjà signalé cette publication.', 422);
        }

        DB::beginTransaction();

        try {
            Report::create([
                'publication_id' => $publication->id,
                'user_id' => $user->id,
                'motif' => $request->motif,
                'commentaire' => $request->commentaire,
            ]);

            $publication->incrementSignalements();

            DB::commit();

            return $this->successResponse(null, 'Publication signalée. Notre équipe va l\'examiner.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur signalement: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du signalement.', 500);
        }
    }

    // ========== GESTION DES PARTAGES ==========

    /**
     * Enregistrer un partage
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function share(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);
        $user = $request->user();

        $request->validate([
            'plateforme' => 'required|string|in:' . implode(',', Share::PLATEFORMES),
        ]);

        DB::beginTransaction();

        try {
            Share::create([
                'publication_id' => $publication->id,
                'user_id' => $user->id,
                'plateforme' => $request->plateforme,
            ]);

            $publication->incrementPartages();

            DB::commit();

            // Générer l'URL de partage
            $shareUrl = $this->generateShareUrl($publication, $request->plateforme);

            return $this->successResponse([
                'share_url' => $shareUrl,
                'total_shares' => $publication->fresh()->nbr_partages,
            ], 'Partage enregistré.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur partage: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du partage.', 500);
        }
    }

    // ========== MÉTHODES ADMIN ==========

    /**
     * Liste des publications pour l'admin (avec toutes les statistiques)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminIndex(Request $request)
    {
        $this->authorizeAdmin();

        $query = Publication::with('user');

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par catégorie
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $perPage = $request->get('per_page', 20);
        $publications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->successResponse([
            'data' => PublicationResource::collection($publications),
            'meta' => [
                'current_page' => $publications->currentPage(),
                'last_page' => $publications->lastPage(),
                'per_page' => $publications->perPage(),
                'total' => $publications->total(),
            ],
        ]);
    }

    /**
     * Blocker une publication (admin)
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminBlock(Request $request, $id)
    {
        $this->authorizeAdmin();

        $request->validate([
            'raison' => 'required|string|min:10|max:500',
        ]);

        $publication = Publication::findOrFail($id);
        $publication->block($request->raison);

        return $this->successResponse(
            new PublicationResource($publication->load('user')),
            'Publication bloquée avec succès.'
        );
    }

    /**
     * Débloquer une publication (admin)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminUnblock($id)
    {
        $this->authorizeAdmin();

        $publication = Publication::findOrFail($id);
        $publication->unblock();

        return $this->successResponse(
            new PublicationResource($publication->load('user')),
            'Publication débloquée avec succès.'
        );
    }

    /**
     * Supprimer un signalement (admin)
     *
     * @param int $reportId
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminDeleteReport($reportId)
    {
        $this->authorizeAdmin();

        $report = Report::findOrFail($reportId);
        $publication = $report->publication;

        DB::beginTransaction();

        try {
            $report->delete();
            
            // Recalculer le nombre de signalements
            $newCount = Report::where('publication_id', $publication->id)->count();
            $publication->update([
                'nbr_signalements' => $newCount,
                'statut' => $newCount >= 5 ? 'signalee' : 'publiee',
            ]);

            DB::commit();

            return $this->successResponse(null, 'Signalement supprimé.');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de la suppression.', 500);
        }
    }

    // ========== MÉTHODES PRIVÉES ==========

    /**
     * Upload de fichier
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string
     */
    private function uploadFile($file, string $directory): string
    {
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, 'public');
        return $path;
    }

    /**
     * Suppression de fichier
     *
     * @param string|null $path
     * @return void
     */
    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Générer l'URL de partage selon la plateforme
     *
     * @param Publication $publication
     * @param string $plateforme
     * @return string
     */
    private function generateShareUrl(Publication $publication, string $plateforme): string
    {
        $url = url("/publications/{$publication->id}");
        $text = urlencode($publication->titre);

        return match($plateforme) {
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$url}",
            'twitter' => "https://twitter.com/intent/tweet?text={$text}&url={$url}",
            'whatsapp' => "https://wa.me/?text={$text}%20{$url}",
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$url}",
            default => $url,
        };
    }

    /**
     * Vérifier si l'utilisateur est admin
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function authorizeAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Accès réservé aux administrateurs.');
        }
    }
}