@extends('layouts.app')

@section('title', 'Accueil - Élevage+')

@section('content')

    <!-- style_css -->
    <link rel="stylesheet" href="{{ asset('css/eleveurCSS/home.css') }}">

    <!-- contenue de la page home -->
    <h1>Bienvenue sur Élevage+ !</h1>
    <p>Gérez vos élevages et animaux en toute simplicité.</p>

@endsection
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Élevage+ - Accueil | Gestion & communauté pour éleveurs</title>

    <!-- Bootstrap 4.6 CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome 6 (gratuit) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts : Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <!-- Custom CSS (séparé) -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ================= HEADER (identique à la maquette + menu fourni) ================= -->
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

    <main>
        <!-- ================= SECTION HERO ================= -->
        <section class="hero-section bg-gradient-light">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7 hero-content text-center text-lg-left">
                        <h1 class="display-4 font-weight-bold">Gérez votre élevage <span class="text-success">facilement</span></h1>
                        <p class="lead mt-3 mb-4">Rejoignez la communauté éleveurs — la plateforme tout-en-un pour gérer vos animaux, vos tâches, vos stocks et échanger avec d'autres éleveurs.</p>
                        <a href="#" class="btn btn-success btn-lg px-5 py-3 rounded-pill shadow-sm">
                            <i class="fas fa-play mr-2"></i> Commencez gratuitement
                        </a>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block text-center">
                        <i class="fas fa-tractor fa-8x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= SECTION : DERNIÈRES PUBLICATIONS + STATS (2 colonnes) ================= -->
        <div class="container mt-5 pt-3">
            <div class="row">
                <!-- Colonne principale : publications -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="section-title">📰 DERNIÈRES PUBLICATIONS</h2>
                        <span class="badge badge-pill badge-success px-3 py-2">Conseils & retours d'expérience</span>
                    </div>

                    <!-- Carte 1 (exacte à la description : Jean Dupont) -->
                    <div class="card post-card mb-4 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-placeholder rounded-circle bg-success text-white mr-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold">Jean Dupont</h5>
                                        <p class="text-muted small mb-0">Éleveur bovin <i class="fas fa-certificate text-info ml-1"></i></p>
                                    </div>
                                </div>
                                <small class="text-muted"><i class="far fa-clock"></i> 2 jours ago</small>
                            </div>

                            <h3 class="post-title h4 mt-2">COMMENT J'AI AUGMENTÉ MA PRODUCTION LAITIÈRE DE 30%</h3>
                            <p class="text-muted">Le mois dernier, j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches basée sur l'ajout de compléments naturels et une meilleure gestion du pâturage tournant...</p>
                            
                            <!-- Stats de l'article : likes, coms, vues -->
                            <div class="d-flex flex-wrap align-items-center gap-3 mt-3 text-secondary">
                                <span><i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="far fa-star"></i> <span class="ml-1">(45 likes)</span></span>
                                <span><i class="far fa-comment-dots"></i> 12 commentaires</span>
                                <span><i class="far fa-eye"></i> 230 vues</span>
                            </div>

                            <div class="mt-4 d-flex flex-wrap align-items-center justify-content-between">
                                <a href="#" class="btn btn-sm btn-outline-success rounded-pill px-4">Lire la suite <i class="fas fa-arrow-right ml-1"></i></a>
                                <a href="#" class="text-success ml-3"><i class="fas fa-share-alt mr-1"></i> Partager</a>
                            </div>
                        </div>
                    </div>

                    <!-- Carte 2 (deuxième publication pour illustrer le flux) -->
                    <div class="card post-card mb-4 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-placeholder rounded-circle bg-success text-white mr-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold">Marie Lambert</h5>
                                        <p class="text-muted small mb-0">Éleveuse caprine</p>
                                    </div>
                                </div>
                                <small class="text-muted"><i class="far fa-clock"></i> 5 jours ago</small>
                            </div>
                            <h3 class="post-title h4">LES 5 ERREURS À ÉVITER EN ÉLEVAGE DE CHÈVRES LAITIÈRES</h3>
                            <p class="text-muted">Après 10 ans d'expérience, je partage les erreurs courantes qui peuvent ruiner une saison de traite et comment les anticiper grâce au suivi quotidien...</p>
                            <div class="d-flex flex-wrap align-items-center gap-3 mt-3 text-secondary">
                                <span><i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="fas fa-star-half-alt text-warning"></i> <i class="far fa-star"></i> <span class="ml-1">(32 likes)</span></span>
                                <span><i class="far fa-comment-dots"></i> 8 commentaires</span>
                                <span><i class="far fa-eye"></i> 145 vues</span>
                            </div>
                            <div class="mt-4 d-flex flex-wrap align-items-center justify-content-between">
                                <a href="#" class="btn btn-sm btn-outline-success rounded-pill px-4">Lire la suite <i class="fas fa-arrow-right ml-1"></i></a>
                                <a href="#" class="text-success ml-3"><i class="fas fa-share-alt mr-1"></i> Partager</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite : Statistiques communauté (carte) -->
                <div class="col-lg-4">
                    <div class="stats-card text-center p-4 shadow-sm rounded-lg bg-white border">
                        <h3 class="h5 border-bottom pb-2 mb-3"><i class="fas fa-chart-line text-success mr-2"></i> Statistiques de la communauté</h3>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="stat-item">
                                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                                    <div class="stat-number">127</div>
                                    <div class="stat-label">éleveurs</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="stat-item">
                                    <i class="fas fa-newspaper fa-2x text-success mb-2"></i>
                                    <div class="stat-number">345</div>
                                    <div class="stat-label">articles</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="stat-item">
                                    <i class="fas fa-heart fa-2x text-danger mb-2"></i>
                                    <div class="stat-number">2.5k</div>
                                    <div class="stat-label">likes</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="stat-item">
                                    <i class="fas fa-comments fa-2x text-info mb-2"></i>
                                    <div class="stat-number">890</div>
                                    <div class="stat-label">coms</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= POURQUOI REJOINDRE ÉLEVAGE+ (3 RAISONS) ================= -->
        <section class="why-join-section mt-5 py-5 bg-light">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title">Pourquoi rejoindre <span class="text-success">Élevage+</span> ?</h2>
                    <p class="lead text-muted">La plateforme pensée par et pour les éleveurs</p>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="feature-card text-center p-4 h-100 bg-white rounded shadow-sm">
                            <div class="feature-icon bg-success text-white rounded-circle mb-3">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <h4>Suivi professionnel</h4>
                            <p class="text-muted">Gérez vos animaux, vos tâches, vos stocks et toutes vos activités d'élevage facilement.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-card text-center p-4 h-100 bg-white rounded shadow-sm">
                            <div class="feature-icon bg-success text-white rounded-circle mb-3">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <h4>Communauté d'entraide</h4>
                            <p class="text-muted">Échangez avec d'autres éleveurs, partagez vos expériences et apprenez ensemble.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-card text-center p-4 h-100 bg-white rounded shadow-sm">
                            <div class="feature-icon bg-success text-white rounded-circle mb-3">
                                <i class="fas fa-bell"></i>
                            </div>
                            <h4>Alertes intelligentes</h4>
                            <p class="text-muted">Recevez des rappels et des alertes pour ne rien oublier et prendre les bonnes décisions.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= SECTION SUPPLÉMENTAIRE : AUTRES CONSEILS + PAGINATION ================= -->
        <div class="container mt-5">
            <h3 class="section-title mb-4"><i class="fas fa-newspaper text-success mr-2"></i> À découvrir également</h3>
            <div class="row">
                <!-- troisième publication (identique au modèle pour illustrer la répétition) -->
                <div class="col-md-6 mb-4">
                    <div class="card post-card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar-placeholder-sm rounded-circle bg-success text-white d-flex align-items-center justify-content-center mr-2">
                                    <i class="fas fa-user fa-sm"></i>
                                </div>
                                <div>
                                    <span class="font-weight-bold">Pierre Martin</span>
                                    <span class="text-muted small">· Éleveur ovin</span>
                                </div>
                                <small class="text-muted ml-auto"><i class="far fa-clock"></i> 1 semaine</small>
                            </div>
                            <h4 class="h6 font-weight-bold">COMMENT J'AI AUGMENTÉ MA PRODUCTION LAITIÈRE DE 30%</h4>
                            <p class="small text-muted">Le mois dernier, j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <a href="#" class="small text-success font-weight-bold">Lire la suite →</a>
                                <a href="#" class="small text-success"><i class="fas fa-share-alt"></i> Partager</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card post-card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar-placeholder-sm rounded-circle bg-success text-white d-flex align-items-center justify-content-center mr-2">
                                    <i class="fas fa-user fa-sm"></i>
                                </div>
                                <div>
                                    <span class="font-weight-bold">Sophie Dubois</span>
                                    <span class="text-muted small">· Éleveuse bovine</span>
                                </div>
                                <small class="text-muted ml-auto"><i class="far fa-clock"></i> 3 jours</small>
                            </div>
                            <h4 class="h6 font-weight-bold">LES NOUVELLES MÉTHODES D'ALIMENTATION BIO</h4>
                            <p class="small text-muted">Découvrez comment j'ai réduit mes coûts tout en augmentant la qualité du lait...</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <a href="#" class="small text-success font-weight-bold">Lire la suite →</a>
                                <a href="#" class="small text-success"><i class="fas fa-share-alt"></i> Partager</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination (Préalablement 1 2 3 4 suivante) -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mt-4 pt-2 border-top">
                <span class="text-muted small">Préalablement</span>
                <nav aria-label="Navigation des publications">
                    <ul class="pagination pagination-md mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">4</a></li>
                        <li class="page-item"><a class="page-link" href="#">suivante <i class="fas fa-chevron-right ml-1"></i></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </main>

    <!-- ================= FOOTER (identique à la maquette) ================= -->
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

    <!-- Scripts obligatoires (Bootstrap, jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>