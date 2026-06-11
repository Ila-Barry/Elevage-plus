@extends('layouts.menu')

@section('title', 'Communauté Éleveurs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/blog.css') }}">
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="blog-container">
        
        <header class="blog-header">
            <h2 class="blog-title">COMMUNAUTÉ ÉLEVEURS</h2>
            <div class="blog-actions">
                <button class="btn-publish"><i class="fas fa-pen-nib"></i> Publier un article</button>
                <button class="btn-trending-nav"><i class="fas fa-chart-line"></i> Tendances</button>
            </div>
        </header>

        <nav class="blog-filters">
            <a href="#" class="filter-item active"><i class="fas fa-comments"></i> Conseils</a>
            <a href="#" class="filter-item"><i class="fas fa-lightbulb"></i> Expériences</a>
            <a href="#" class="filter-item"><i class="fas fa-exclamation-triangle"></i> Alertes</a>
            <a href="#" class="filter-item"><i class="fas fa-star"></i> Tendances</a>
        </nav>

        <div class="posts-list">
            
            {{-- 
                Ici, on utilise une seule structure dynamique. 
                Quand ton Controller enverra la variable $posts, il suffira de décommenter la ligne ci-dessous.
                Pour l'instant, voici le squelette unique et propre.
            --}}
            {{-- @foreach($posts as $post) --}}
            
            <article class="post-card">
                <div class="post-admin-actions">
                    <button class="btn-edit-post" title="Modifier"><i class="fas fa-pencil-alt"></i></button>
                    <button class="btn-delete-post" title="Supprimer"><i class="fas fa-times"></i></button>
                </div>

                <div class="post-header">
                    <img src="https://via.placeholder.com/50" alt="Avatar" class="author-avatar">
                    <div class="author-info">
                        <span class="author-name">JEAN DUPONT</span>
                        <span class="author-role">• Éleveur bovin</span>
                        <span class="post-date">• Aujourd'hui</span>
                    </div>
                </div>

                <div class="post-body">
                    {{-- 
                        Pour changer la couleur dynamiquement selon le type, 
                        tu appliqueras la classe correspondante : type-experience, type-alerte ou type-conseil 
                    --}}
                    <h3 class="post-category type-experience">
                        <span class="status-indicator"></span> EXPERIENCE : NOUVELLE MÉTHODE D'ALIMENTATION
                    </h3>
                    
                    <p class="post-excerpt">
                        Je viens de tester une nouvelle méthode d'alimentation pour mes vaches et les résultats sont encourageants...
                    </p>
                    
                    <div class="post-media-grid">
                        <div class="media-item"><img src="https://via.placeholder.com/300x200" alt="Média 1"></div>
                        <div class="media-item"><img src="https://via.placeholder.com/300x200" alt="Média 2"></div>
                        <div class="media-item"><img src="https://via.placeholder.com/300x200" alt="Média 3"></div>
                        <div class="media-item"><img src="https://via.placeholder.com/300x200" alt="Média 4"></div>
                    </div>
                </div>

                <div class="post-footer">
                    <div class="post-stats">
                        <span><i class="far fa-thumbs-up"></i> 0</span>
                        <span><i class="far fa-comment"></i> 0</span>
                        <span><i class="far fa-share-square"></i> 0</span>
                    </div>
                    <div class="post-interactions">
                        <button class="interaction-btn"><i class="far fa-thumbs-up"></i> Liker</button>
                        <button class="interaction-btn"><i class="far fa-comment"></i> Commenter</button>
                        <button class="interaction-btn"><i class="fas fa-share"></i> Partager</button>
                    </div>
                </div>
            </article>

            {{-- @endforeach --}}

        </div>

        <div class="blog-pagination">
            <button class="page-arrow" disabled><i class="fas fa-chevron-left"></i> Précédent</button>
            <div class="page-numbers">
                <span class="page-num active">1</span>
                <span class="page-num">2</span>
                <span class="page-num">3</span>
            </div>
            <button class="page-arrow">Suivant <i class="fas fa-chevron-right"></i></button>
        </div>

    </div>
</div>
@endsection