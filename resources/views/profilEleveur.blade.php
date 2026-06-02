@extends('layouts.app')

@section('title', 'Profil - Élevage+')

@section('content')

    <!-- style_css -->
    <link rel="stylesheet" href="{{ asset('css/eleveurCSS/profilEleveur.css') }}">

    <!-- contenue de la page profilEleveur -->
    <h1>Profil de l'éleveur</h1>
    <p>Bienvenue sur votre profil, éleveur ! Ici, vous pouvez gérer vos informations personnelles, vos annonces d'animaux à vendre, et suivre vos transactions.</p>

    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Élevage+ - Profil public de Jean Dupont</title>

    <!-- Bootstrap 4.6 CSS (pour la grille responsive) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome 6 (gratuit) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts : Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <!-- Fichier CSS personnalisé -->
    <link rel="stylesheet" href="profil-public.css">
</head>
<body>

    <!-- ================= HEADER (identique à la maquette) ================= -->
    <header class="main-header shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light px-0">
                <!-- Logo -->
                <a class="navbar-brand d-flex align-items-center logo-brand" href="#">
                    <i class="fas fa-paw mr-2"></i>
                    <span>ÉLEVAGE+</span>
                </a>

                <!-- Toggler mobile -->
                <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#mainNavbar"
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Contenu navbar -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <!-- Barre de recherche centrale -->
                    <ul class="navbar-nav mx-auto align-items-lg-center w-100 justify-content-center">
                        <li class="nav-item search-item w-100">
                            <form class="search-form w-100">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control border-left-0" placeholder="Rechercher un élevage, une publication...">
                                </div>
                            </form>
                        </li>
                    </ul>

                    <!-- Boutons connexion / inscription -->
                    <div class="navbar-actions d-flex flex-column flex-lg-row align-items-stretch ml-3 align-items-lg-center">
                        <a href="#" class="btn btn-outline-success mr-lg-2 mb-2 mb-lg-0">
                            <i class="fas fa-sign-in-alt mr-1"></i> Connexion
                        </a>
                        <a href="#" class="btn btn-success">
                            <i class="fas fa-user-plus mr-1"></i> Inscription
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main class="container my-5">
        <div class="row">
            <!-- ================= COLONNE GAUCHE : INFOS PROFIL ================= -->
            <div class="col-lg-4 mb-5 mb-lg-0">
                <div class="profile-card text-center text-lg-left p-4 shadow-sm rounded bg-white">
                    <!-- Avatar / Photo de profil -->
                    <div class="profile-avatar mx-auto mx-lg-0 mb-3">
                        <i class="fas fa-user-circle fa-5x text-success"></i>
                    </div>
                    <h2 class="h3 font-weight-bold">Jean Dupont</h2>
                    <p class="text-muted mb-2">
                        <i class="fas fa-map-marker-alt mr-1 text-success"></i> Thies, Sénégal
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-tractor mr-1 text-success"></i> Élevage bovin - 45 animaux
                    </p>
                    <p class="mb-3 small text-muted">
                        <i class="far fa-calendar-alt mr-1"></i> Membre depuis mars 2025
                    </p>

                    <!-- Statistiques rapides (ligne) -->
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="stat-number font-weight-bold">48</div>
                            <div class="stat-label">Publications</div>
                        </div>
                        <div class="col-4">
                            <div class="stat-number font-weight-bold">2.3k</div>
                            <div class="stat-label">Likes</div>
                        </div>
                        <div class="col-4">
                            <div class="stat-number font-weight-bold">127</div>
                            <div class="stat-label">Commentaires</div>
                        </div>
                    </div>

                    <!-- Bio -->
                    <div class="bio-section mt-3 pt-2 border-top">
                        <p class="mb-2"><strong>Bio :</strong></p>
                        <p class="text-muted small">Éleveur passionné depuis 10 ans, je partage mon expérience pour aider la communauté agricole.</p>
                        <p class="mb-0">
                            <i class="fas fa-globe mr-1 text-success"></i>
                            <a href="#" class="text-success">www.jean-elevage.com</a>
                        </p>
                    </div>

                    <!-- Boutons d'action : Suivre / Commenter / Partager -->
                    <div class="action-buttons d-flex flex-wrap justify-content-between mt-4 pt-2">
                        <a href="#" class="btn btn-success btn-sm rounded-pill px-4 mb-2"><i class="fas fa-user-plus mr-1"></i> Suivre</a>
                        <a href="#" class="btn btn-outline-secondary btn-sm rounded-pill px-4 mb-2"><i class="far fa-comment mr-1"></i> Commenter</a>
                        <a href="#" class="btn btn-outline-secondary btn-sm rounded-pill px-4 mb-2"><i class="fas fa-share-alt mr-1"></i> Partager</a>
                    </div>
                </div>
            </div>

            <!-- ================= COLONNE DROITE : PUBLICATIONS ================= -->
            <div class="col-lg-8">
                <!-- En-tête des publications avec tri -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                    <h3 class="section-title h4 mb-0">PUBLICATIONS DE JEAN DUPONT</h3>
                    <div class="dropdown mt-2 mt-sm-0">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-pill" type="button" id="sortMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Trier par : <span class="font-weight-bold">plus récentes</span> <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="sortMenu">
                            <a class="dropdown-item" href="#">Plus récentes</a>
                            <a class="dropdown-item" href="#">Plus anciennes</a>
                            <a class="dropdown-item" href="#">Les plus likées</a>
                        </div>
                    </div>
                </div>

                <!-- Carte publication 1 -->
                <div class="card post-card mb-4 shadow-sm border-0 rounded-lg">
                    <div class="card-body">
                        <h4 class="post-title h5 font-weight-bold">COMMENT J'AI AUGMENTÉ MA PRODUCTION LAITIÈRE</h4>
                        <p class="text-muted">Le mois dernier, j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...</p>
                        <div class="d-flex flex-wrap align-items-center text-secondary small">
                            <span class="mr-3"><i class="fas fa-heart text-danger"></i> 45 likes</span>
                            <span class="mr-3"><i class="far fa-comment-dots"></i> 12 commentaires</span>
                            <span><i class="far fa-eye"></i> 230 vues</span>
                        </div>
                    </div>
                </div>

                <!-- Carte publication 2 -->
                <div class="card post-card mb-4 shadow-sm border-0 rounded-lg">
                    <div class="card-body">
                        <h4 class="post-title h5 font-weight-bold">ALERTE : FIÈVRE APHITEUSE DANS LA RÉGION</h4>
                        <p class="text-muted">Le mois dernier, j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...</p>
                        <div class="d-flex flex-wrap align-items-center text-secondary small">
                            <span class="mr-3"><i class="fas fa-heart text-danger"></i> 45 likes</span>
                            <span class="mr-3"><i class="far fa-comment-dots"></i> 12 commentaires</span>
                            <span><i class="far fa-eye"></i> 230 vues</span>
                        </div>
                    </div>
                </div>

                <!-- Carte publication 3 -->
                <div class="card post-card mb-4 shadow-sm border-0 rounded-lg">
                    <div class="card-body">
                        <h4 class="post-title h5 font-weight-bold">5 ASTUCES POUR L'HIVERNAGE DES BOVINS</h4>
                        <p class="text-muted">Le mois dernier, j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...</p>
                        <div class="d-flex flex-wrap align-items-center text-secondary small">
                            <span class="mr-3"><i class="fas fa-heart text-danger"></i> 45 likes</span>
                            <span class="mr-3"><i class="far fa-comment-dots"></i> 12 commentaires</span>
                            <span><i class="far fa-eye"></i> 230 vues</span>
                        </div>
                    </div>
                </div>

                <!-- Lien "Afficher plus de publications" -->
                <div class="text-center mt-4">
                    <a href="#" class="btn btn-link text-success font-weight-bold">
                        Afficher plus de publications <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="main-footer mt-5">
        <div class="container">
            <div class="footer-content">
                <div class="footer-copyright">
                    © 2026 Élevage+
                </div>
                <div class="footer-links">
                    <a href="#">Mentions légales</a>
                    <span class="text-muted">|</span>
                    <a href="#">Contact</a>
                    <span class="text-muted">|</span>
                    <a href="#">CGU</a>
                </div>
                <div class="footer-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts nécessaires (Bootstrap, jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    
@endsection
