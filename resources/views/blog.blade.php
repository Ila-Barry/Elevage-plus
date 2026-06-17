@extends('layouts.menu')

@section('title', 'Communauté Éleveurs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/blog.css') }}">
@endpush

@section('content')
<div class="blog-main-container p-1">
    
    <div class="blog-top-header">
        <h2 class="blog-main-title">COMMUNAUTÉ ÉLEVEURS</h2>
        <div class="blog-header-buttons">
            <button class="btn-blog-action btn-green-publish" id="btn-trigger-publish">
                <i class="fas fa-pencil-alt"></i> Publier un article
            </button>
            <button class="btn-blog-action btn-white-trend">
                <i class="fas fa-chart-line"></i> Tendances
            </button>
        </div>
    </div>

    <div class="blog-categories-tabs">
        <a href="#" class="tab-item active-tab"><i class="fas fa-users text-success"></i> Conseils</a>
        <a href="#" class="tab-item"><i class="far fa-lightbulb text-warning"></i> Expériences</a>
        <a href="#" class="tab-item"><i class="fas fa-exclamation-triangle text-danger"></i> Alertes</a>
        <a href="#" class="tab-item"><i class="far fa-star text-primary"></i> Tendances</a>
    </div>

    <div class="blog-posts-feed">

        <div class="row">
            <div class="col-ms-12 col-md-6 col-lg-4">
                <h1>section_1</h1>

            </div>
            <div class="col-ms-12 col-md-6 col-lg-4">
                <h1>section_2</h1>

            <div class="custom-post-header">
                <div class="author-avatar-box">
                    <img src="{{ asset('images/img-elevage.jpeg') }}" alt="Avatar Jean Dupont" class="rounded-circle">
                </div>
                <div class="author-meta-data">
                    <span class="user-fullname">JEAN DUPONT</span>
                    <span class="meta-separator">•</span>
                    <span class="user-profession">Éleveur bovin</span>
                    <span class="meta-separator">•</span>
                    <span class="published-time"><i class="far fa-calendar-alt"></i> Aujourd'hui</span>
                </div>
            </div>
            <div class="custom-post-body text-center">
                <h3 class="post-title-badge color-experience">
                    <span class="dot-indicator"></span> EXPERIENCE : NOUVELLE MÉTHODE D'ALIMENTATION
                </h3>
                <p class="post-text-content">
                    Je viens de tester une nouvelle méthode d'alimentation méthode d'alimentation pour mes vaches et les résultats sont encourageants...
                </p>
            <div class="col-ms-12 col-md-6 col-lg-4">
                <h1>section_3</h1>
                
            </div>
        </div>

                <div class="post-images-grid-2x2">
                    <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?w=300" alt="1"></div>
                    <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=300" alt="2"></div>
                    <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1596733430284-f7437764b1a9?w=300" alt="3"></div>
                    <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=300" alt="4"></div>
                </div>
            </div>
            <div class="custom-post-footer">
                <div class="post-counters">
                    <span><i class="far fa-thumbs-up"></i> 0</span>
                    <span><i class="far fa-comment"></i> 0</span>
                    <span><i class="far fa-eye"></i> 0</span>
                </div>
                <div class="post-action-triggers">
                    <button class="trigger-btn"><i class="far fa-thumbs-up"></i> Liker</button>
                    <button class="trigger-btn"><i class="far fa-comment"></i> Commenter</button>
                    <button class="trigger-btn"><i class="fas fa-share"></i> Partager</button>
                </div>
            </div>
        </article>

        <article class="custom-post-card">
            <div class="custom-post-header">
                <div class="author-avatar-box">
                    <img src="{{ asset('images/img-elevage.jpeg') }}" alt="Avatar" class="rounded-circle">
                </div>
                <div class="author-meta-data">
                    <span class="user-fullname">JEAN DUPONT</span>
                    <span class="meta-separator">•</span>
                    <span class="user-profession">Éleveur bovin</span>
                    <span class="meta-separator">•</span>
                    <span class="published-time"><i class="far fa-calendar-alt"></i> 2 jours ago</span>
                </div>
            </div>
            <div class="custom-post-body text-center">
                <h3 class="post-title-badge color-alerte">
        <article class="custom-post-card">
            <div class="custom-post-admin">
                <button class="action-edit" title="Modifier"><i class="fas fa-pencil-alt"></i></button>
                <button class="action-delete" title="Supprimer"><i class="fas fa-times"></i></button>
            </div>

            <div class="custom-post-header">
                <div class="author-avatar-box">
                    <img src="{{ asset('images/img-elevage.jpeg') }}" alt="Avatar Jean Dupont" class="rounded-circle">

                </div>
                <div class="author-meta-data">
                    <span class="user-fullname">JEAN DUPONT</span>
                    <span class="meta-separator">•</span>
                    <span class="user-profession">Éleveur bovin</span>
                    <span class="meta-separator">•</span>
                    <span class="published-time"><i class="far fa-calendar-alt"></i> Aujourd'hui</span>
                </div>
            </div>

            <div class="custom-post-body ">
                <h3 class="post-title-badge color-experience">
                    <span class="dot-indicator "></span> EXPERIENCE : NOUVELLE MÉTHODE D'ALIMENTATION
                </h3>
                <p class="post-text-content">
                    Je viens de tester une nouvelle méthode d'alimentation pour mes vaches et les résultats sont encourageants...
                </p>
                
               <div class="post-images-gallery">

    <!-- Ligne des 4 images -->
    <div class="image-row">
        <!-- Image 1 -->
        <div class="grid-img-item">
            <img src="https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?w=300" alt="Alimentation vaches 1">
        </div>
        <!-- Image 2 -->
        <div class="grid-img-item">
            <img src="https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=300" alt="Alimentation vaches 2">
        </div>
        <!-- Image 3 -->
        <div class="grid-img-item">
            <img src="https://images.unsplash.com/photo-1596733430284-f7437764b1a9?w=300" alt="Alimentation vaches 3">
        </div>
        <!-- Image 4 = label cliquable -->
        <div class="grid-img-item">
            <label for="gallery-toggle-1" class="gallery-trigger">
                <img src="https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=300" alt="Voir plus d'images">
                <span class="overlay-plus">+</span>
            </label>
        </div>
    </div>

    <!-- Checkbox cachée -->
    <input type="checkbox" id="gallery-toggle-1" class="gallery-checkbox">

    <!-- Images supplémentaires (cachées par défaut) -->
    <div class="extra-images">
        <div class="extra-grid">
            <img src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=300" alt="Extra 1">
            <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=300" alt="Extra 2">
            <img src="https://images.unsplash.com/photo-1532009877282-3340270e0529?w=300" alt="Extra 3">
            <img src="https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?w=300" alt="Extra 4">
            <img src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=300" alt="Extra 5">
            <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=300" alt="Extra 6">
        </div>
    </div>

</div>
            </div>

            <div class="custom-post-footer">
                <div class="post-counters">
                    <span><i class="far fa-thumbs-up"></i> 0</span>
                    <span><i class="far fa-comment"></i> 0</span>
                    <span><i class="far fa-eye"></i> 0</span>
                </div>
                <div class="post-action-triggers">
                    <button class="trigger-btn"><i class="far fa-thumbs-up"></i> Liker</button>
                    <button class="trigger-btn"><i class="far fa-comment"></i> Commenter</button>
                    <button class="trigger-btn"><i class="fas fa-share"></i> Partager</button>
                </div>
            </div>
        </article>

        <article class="custom-post-card">
            <div class="custom-post-header">
                <div class="author-avatar-box">
                    <img src="{{ asset('images/img-elevage.jpeg') }}" alt="Avatar Jean Dupont" class="rounded-circle">
                </div>
                <div class="author-meta-data">
                    <span class="user-fullname">JEAN DUPONT</span>
                    <span class="meta-separator">•</span>
                    <span class="user-profession">Éleveur bovin</span>
                    <span class="meta-separator">•</span>
                    <span class="published-time"><i class="far fa-calendar-alt"></i> 2 jours ago</span>
                </div>
            </div>

            <div class="custom-post-body">
                <h3 class="post-title-badge color-alerte">
                    <i class="fas fa-exclamation-triangle"></i> ALERTE : CAS DE FIÈVRE APHTEUSE À DAKAR
                </h3>
                <p class="post-text-content">
                    Je viens de tester une nouvelle méthode d'alimentation pour mes vaches et les résultats sont encourageants...
                </p>
                <div class="post-images-grid-2x2">
                    <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?w=300" alt="1"></div>
                    <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=300" alt="2"></div>
                    <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1596733430284-f7437764b1a9?w=300" alt="3"></div>
                    <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=300" alt="4"></div>
                </div>
                
                <div class="post-images-gallery">
    <!-- Les 4 images en ligne -->
    <div class="image-row">
        <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?w=300" alt="Image 1"></div>
        <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=300" alt="Image 2"></div>
        <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1596733430284-f7437764b1a9?w=300" alt="Image 3"></div>
        
        <!-- La 4ème image est un label -->
        <div class="grid-img-item">
            <label for="toggle-gallery-1" class="gallery-trigger">
                <img src="https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=300" alt="Cliquez pour plus d'images">
                <span class="overlay-text">+ Voir plus</span>
            </label>
        </div>
    </div>

    <!-- Checkbox cachée -->
    <input type="checkbox" id="toggle-gallery-1" class="gallery-checkbox">

    <!-- Conteneur d'images supplémentaires (caché par défaut) -->
    <div class="extra-images">
        <div class="extra-grid">
            <img src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=300" alt="Extra 1">
            <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=300" alt="Extra 2">
            <img src="https://images.unsplash.com/photo-1532009877282-3340270e0529?w=300" alt="Extra 3">
            <img src="https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?w=300" alt="Extra 4">
            <img src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=300" alt="Extra 5">
            <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=300" alt="Extra 6">
        </div>
    </div>
</div>
            </div>
            <div class="custom-post-footer">
                <div class="post-counters">
                    <span><i class="far fa-thumbs-up"></i> 0</span>
                    <span><i class="far fa-comment"></i> 0</span>
                    <span><i class="far fa-eye"></i> 0</span>
                </div>
                <div class="post-action-triggers">
                    <button class="trigger-btn"><i class="far fa-thumbs-up"></i> Liker</button>
                    <button class="trigger-btn"><i class="far fa-comment"></i> Commenter</button>
                    <button class="trigger-btn"><i class="fas fa-share"></i> Partager</button>
                </div>
            </div>
        </article>

        <article class="custom-post-card">
            <div class="custom-post-header">
                <div class="author-avatar-box">
                    <img src="{{ asset('images/img-elevage.jpeg') }}" alt="Avatar" class="rounded-circle">
                </div>
                <div class="author-meta-data">
                    <span class="user-fullname">JEAN DUPONT</span>
                    <span class="meta-separator">•</span>
                    <span class="user-profession">Éleveur bovin</span>
                    <span class="meta-separator">•</span>
                    <span class="published-time"><i class="far fa-calendar-alt"></i> 5 mois ago</span>
                </div>
            </div>
            <div class="custom-post-body text-center">
                <h3 class="post-title-badge color-conseil">CONSEIL : NOUVELLE MÉTHODE D'ALIMENTATION</h3>
                <p class="post-text-content">
    Je viens de tester une nouvelle méthode d'alimentation pour mes vaches et les résultats sont encourageants...
</p>
                
                    Je viens de tester une nouvelle méthode d'alimentation<br>pour mes vaches et les résultats sont encourageants...
                </p>
                <div class="post-images-grid-2x2">
                    <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?w=300" alt="1"></div>
                    <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=300" alt="2"></div>
                    <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1596733430284-f7437764b1a9?w=300" alt="3"></div>
                    <div class="grid-img-item"><img src="https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=300" alt="4"></div>
                </div>
    Je viens de tester une nouvelle méthode d'alimentation pour mes vaches et les résultats sont encourageants...
</p>
                
                <div class="post-images-gallery">

    <!-- Ligne des 4 images alignées -->
    <div class="image-row">
        <div class="grid-img-item">
            <img src="https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?w=300" alt="Conseil élevage 1">
        </div>
        <div class="grid-img-item">
            <img src="https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=300" alt="Conseil élevage 2">
        </div>
        <div class="grid-img-item">
            <img src="https://images.unsplash.com/photo-1596733430284-f7437764b1a9?w=300" alt="Conseil élevage 3">
        </div>
        <!-- La 4ème image est un label cliquable -->
        <div class="grid-img-item">
            <label for="gallery-1" class="gallery-trigger">
                <img src="https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=300" alt="Voir plus">
                <span class="overlay-plus">+</span>
            </label>
        </div>
    </div>

    <!-- Checkbox cachée -->
    <input type="checkbox" id="gallery-1" class="gallery-checkbox">

    <!-- Images supplémentaires (apparaissent au clic) -->
    <div class="extra-images">
        <div class="extra-grid">
            <img src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=300" alt="Extra 1">
            <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=300" alt="Extra 2">
            <img src="https://images.unsplash.com/photo-1532009877282-3340270e0529?w=300" alt="Extra 3">
            <img src="https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?w=300" alt="Extra 4">
            <img src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=300" alt="Extra 5">
            <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=300" alt="Extra 6">
        </div>
    </div>

</div>
            </div>
            <div class="custom-post-footer">
                <div class="post-counters">
                    <span><i class="far fa-thumbs-up"></i> 0</span>
                    <span><i class="far fa-comment"></i> 0</span>
                    <span><i class="far fa-eye"></i> 0</span>
                </div>
                <div class="post-action-triggers">
                    <button class="trigger-btn"><i class="far fa-thumbs-up"></i> Liker</button>
                    <button class="trigger-btn"><i class="far fa-comment"></i> Commenter</button>
                    <button class="trigger-btn"><i class="fas fa-share"></i> Partager</button>
                </div>
            </div>
        </article>

    </div>

    <div class="custom-blog-pagination">
        <button class="pag-arrow-btn" disabled><i class="fas fa-caret-left"></i> précédente</button>
        <div class="pag-numbers-list">
            <span class="num-item active-num">1</span>
            <span class="num-item">2</span>
            <span class="num-item">3</span>
            <span class="num-item">4</span>
        </div>
        <button class="pag-arrow-btn">suivante <i class="fas fa-caret-right"></i></button>
    </div>

    <div id="publish-article-modal" class="custom-modal-overlay">
        <div class="custom-modal-container">
            
            <h2 class="modal-form-title">PUBLIER UN ARTICLE</h2>
            
            <form action="#" method="POST" enctype="multipart/form-data" class="modal-publish-form">
                @csrf
                
                <div class="form-input-group">
                    <label class="form-custom-label">
                        <i class="fas fa-edit text-muted"></i> Titre de l'article <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="title" class="form-custom-control" placeholder="Comment j'ai sauvé mon troupeau de la fièvre aphteuse" required>
                </div>

                <div class="form-input-group">
                    <label class="form-custom-label">
                        <i class="fas fa-tags text-muted"></i> Catégorie <span class="text-danger">*</span>
                    </label>
                    <select name="category" class="form-custom-select" required>
                        <option value="Experience">Expérience</option>
                        <option value="Conseil">Conseil</option>
                        <option value="Alerte">Alerte</option>
                        <option value="Tendance">Tendance</option>
                    </select>
                </div>

                <div class="form-input-group">
                    <label class="form-custom-label">
                        <i class="fas fa-align-left text-muted"></i> Description <span class="text-danger">*</span>
                    </label>
                    <div class="custom-editor-box">
                        <div class="editor-toolbar">
                            <button type="button" class="tool-btn font-weight-bold">B</button>
                            <button type="button" class="tool-btn font-style-italic">I</button>
                            <button type="button" class="tool-btn font-underline">U</button>
                            <span class="tool-separator">|</span>
                            <button type="button" class="tool-btn"><i class="fas fa-link"></i></button>
                            <button type="button" class="tool-btn"><i class="far fa-image"></i></button>
                        </div>
                        <textarea name="description" class="editor-textarea" placeholder="Le mois dernier, j'ai remarqué que 3 de mes vaches..." required></textarea>
                    </div>
                </div>

                <div class="form-input-group">
                    <label class="form-custom-label">
                        <i class="far fa-image text-muted"></i> Image (optionnelle)
                    </label>
                    <div class="custom-file-upload-row">
                        <label for="file-upload-input" class="btn-choose-file">
                            <i class="far fa-folder-open"></i> Choisir un fichier
                        </label>
                        <input id="file-upload-input" type="file" name="image" style="display: none;">
                        <span id="file-name-placeholder" class="file-status-text">Aucun fichier choisi</span>
                    </div>
                </div>

                <div class="modal-form-actions-row">
                    <button type="button" class="btn-form-cancel" id="btn-close-modal">
                        <i class="fas fa-times-circle text-danger"></i> Annuler
                    </button>
                    <button type="submit" class="btn-form-submit">
                        <i class="fas fa-check-circle"></i> Publier
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('publish-article-modal');
        const openBtn = document.getElementById('btn-trigger-publish');
        const closeBtn = document.getElementById('btn-close-modal');
        const fileInput = document.getElementById('file-upload-input');
        const filePlaceholder = document.getElementById('file-name-placeholder');

        // Ouvrir la modale
        openBtn.addEventListener('click', () => {
            modal.classList.add('modal-visible');
        });

        // Fermer la modale au clic sur Annuler
        closeBtn.addEventListener('click', () => {
            modal.classList.remove('modal-visible');
        });

        // Fermer si l'utilisateur clique en dehors de la boîte blanche
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('modal-visible');
            }
        });

        // Afficher le nom du fichier sélectionné
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                filePlaceholder.textContent = this.files[0].name;
                filePlaceholder.style.color = "#212529";
            } else {
                filePlaceholder.textContent = "Aucun fichier choisi";
            }
        });
    });
</script>
@endsection