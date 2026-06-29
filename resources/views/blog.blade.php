{{-- resources/views/blog.blade.php --}}

@extends('layouts.menu')

@section('title', 'Communauté Éleveurs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/blog.css') }}">
@endpush

@section('content')
<div class="blog-main-container p-4">
    
    <div class="blog-top-header">
        <h2 class="blog-main-title">COMMUNAUTÉ ÉLEVEURS</h2>
        <div class="blog-header-buttons">
            <button class="btn-blog-action btn-green-publish" id="openPublishModal">
                <i class="fas fa-pencil-alt"></i> Publier un article
            </button>
        </div>
    </div>

    <div class="blog-categories-tabs">
        <a href="#" class="tab-item active-tab" data-tab="all"><i class="fas fa-users text-success"></i> Tous</a>
        <a href="#" class="tab-item" data-tab="mine"><i class="fas fa-user text-primary"></i> Mes articles</a>
        <a href="#" class="tab-item" data-tab="conseil"><i class="fas fa-lightbulb text-warning"></i> Conseils</a>
        <a href="#" class="tab-item" data-tab="experience"><i class="fas fa-user-edit text-info"></i> Expériences</a>
        <a href="#" class="tab-item" data-tab="alerte"><i class="fas fa-exclamation-triangle text-danger"></i> Alertes</a>
    </div>

    <div class="blog-posts-feed" id="postsFeed">
        <div class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2 text-muted">Chargement des publications...</p>
        </div>
    </div>

    <div class="custom-blog-pagination">
        <button class="pag-arrow-btn" id="prevPage" disabled><i class="fas fa-caret-left"></i> précédente</button>
        <div class="pag-numbers-list" id="pageNumbers"></div>
        <button class="pag-arrow-btn" id="nextPage">suivante <i class="fas fa-caret-right"></i></button>
    </div>
</div>

<!-- ================= MODALE PUBLIER ================= -->
<div id="publishModal" class="modal-blog">
    <div class="modal-blog-content">
        <div class="modal-blog-header">
            <h3 id="publishModalTitle"><i class="fas fa-pencil-alt"></i> Publier un article</h3>
            <span class="modal-blog-close" id="closePublishModal">&times;</span>
        </div>
        <div class="modal-blog-body">
            <div id="publishError" class="alert alert-danger" style="display: none;"></div>
            <form id="publishForm" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Titre de l'article *</label>
                    <input type="text" id="postTitle" class="form-control" placeholder="Ex: Ma nouvelle méthode d'alimentation" required>
                </div>

                <div class="form-group">
                    <label>Catégorie *</label>
                    <select id="postCategory" class="form-control" required>
                        <option value="conseil">🌾 Conseils</option>
                        <option value="experience">💡 Expériences</option>
                        <option value="alerte">⚠️ Alertes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Contenu (optionnel)</label>
                    <textarea id="postContent" class="form-control" rows="5" placeholder="Décrivez votre expérience..." ></textarea>
                </div>

                
                <div class="form-group">
                    <label>Fichiers joints (optionnel)</label>
                    <div class="file-upload-area" id="fileUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>
                            <strong>Cliquez ou glissez-déposez</strong> vos fichiers
                        </p>

                        <p class="file-hint">
                            <small>Images (5 max), Vidéos (2 max), Documents (3 max)</small>
                        </p>
                    </div>

                    <input
                        type="file"
                        id="postFiles"
                        multiple
                        accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar"
                        style="display:none;"
                    >
                </div>
                <div class="file-preview-container" id="filePreviewContainer"></div>
                <div class="file-stats" id="fileStats" style="display: none; margin-top: 10px; font-size: 13px; color: #6c757d;">
                    <span id="fileCount">0</span> fichier(s) sélectionné(s)
                    <span class="badge badge-secondary ml-2" id="fileSize">0 Mo</span>
                </div>
            </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel-modal" id="cancelPublish">Annuler</button>
                    <button type="submit" class="btn-publish-modal" id="publishSubmitBtn">
                        <i class="fas fa-paper-plane"></i> Publier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODALE IMAGE ================= -->
<div id="imageModal" class="modal-blog image-modal">
    <div class="modal-blog-content image-modal-content">
        <span class="modal-blog-close" id="closeImageModal">&times;</span>
        <img id="modalImage" src="" alt="Image en grand format">
        <div class="image-nav">
            <button id="prevImageBtn"><i class="fas fa-chevron-left"></i></button>
            <span id="imageCounter">1 / 1</span>
            <button id="nextImageBtn"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/blog.js') }}"></script>
@endpush