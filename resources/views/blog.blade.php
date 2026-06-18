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
            <button class="btn-blog-action btn-green-publish">
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
                    <img src="{{ asset('images/img-elevage.jpeg') }}" alt="Avatar Jean Dupont" class="rounded-circle">
                </div>
                <div class="author-meta-data">
                    <span class="user-fullname">JEAN DUPONT</span>
                    <span class="meta-separator">•</span>
                    <span class="user-profession">Éleveur bovin</span>
                    <span class="meta-separator">•</span>
                    <span class="published-time"><i class="far fa-calendar-alt"></i> 5 mois ago</span>
                </div>
            </div>

            <div class="custom-post-body">
                <h3 class="post-title-badge color-conseil">
                    CONSEIL :
                     NOUVELLE MÉTHODE D'ALIMENTATION
                </h3>
                <p class="post-text-content">
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

</div>
@endsection