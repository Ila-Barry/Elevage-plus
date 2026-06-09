<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Élevage+ - Gestion intégrée d\'élevage & Communauté agricole'); ?></title>

    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/layoutCSS/app.css')); ?>">
</head>
<body>

    <!-- ================= HEADER ================= -->
    <header class="main-header shadow-sm">

        <div class="container">

            <nav class="navbar navbar-expand-lg navbar-light px-0">

                <!-- ================= LOGO ================= -->
                <a class="navbar-brand d-flex align-items-center logo-brand"
                href="<?php echo e(url('/')); ?>">

                    <i class="fas fa-paw mr-2"></i>
                    <span>ÉLEVAGE+</span>

                </a>

                <!-- ================= TOGGLER MOBILE ================= -->
                <button class="navbar-toggler border-0"
                        type="button"
                        data-toggle="collapse"
                        data-target="#mainNavbar"
                        aria-controls="mainNavbar"
                        aria-expanded="false"
                        aria-label="Menu">

                    <span class="navbar-toggler-icon"></span>

                </button>

                <!-- ================= NAVBAR CONTENT ================= -->
                <div class="collapse navbar-collapse" id="mainNavbar">

                    <!-- ===== CENTRE ===== -->
                    <ul class="navbar-nav mx-auto align-items-lg-center w-100 justify-content-center">

                        <!-- Recherche -->
                        <li class="nav-item search-item">

                            <form class="search-form">

                                <div class="input-group">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                    </div>

                                    <input type="text"
                                        class="form-control border-left-0"
                                        placeholder="Rechercher un élevage, une publication...">

                                </div>

                            </form>

                        </li>

                    </ul>

                    <!-- ===== DROITE ===== -->
                    <div class="navbar-actions d-flex flex-column flex-lg-row align-items-stretch ml-3 align-items-lg-center">

                            <a href="<?php echo e(url('/auth/login')); ?>"
                            class="btn btn-outline-success mr-lg-2 mb-2 mb-lg-0">

                                <i class="fas fa-sign-in-alt mr-1"></i>
                                Connexion

                            </a>

                            <a href="<?php echo e(url('/auth/register')); ?>"
                            class="btn btn-success">

                                <i class="fas fa-user-plus mr-1"></i>
                                Inscription

                            </a>
                    </div>

                </div>

            </nav>

        </div>

    </header>
    
    <!-- Contenu principal -->
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- ============ FOOTER ============ -->
    <footer class="main-footer">
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\USER\Desktop\Projet\Elevage-plus\resources\views/layouts/app.blade.php ENDPATH**/ ?>