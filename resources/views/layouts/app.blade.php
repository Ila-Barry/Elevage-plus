<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Élevage+ - Gestion intégrée d\'élevage & Communauté agricole')</title>
    <link rel="icon" href="{{ asset('images/logoE.png') }}" type="image/png">

    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layoutCSS/app.css') }}">
    
    @stack('styles')
</head>
<body>

    <!-- ================= HEADER ================= -->
    <header class="main-header shadow-sm">

        <div class="container">

            <nav class="navbar navbar-expand-lg navbar-light px-0">

                <!-- ================= LOGO ================= -->
                <a class="navbar-brand d-flex align-items-center logo-brand" href="{{ url('/') }}">
                    <img class="img-logo" src="{{ asset('images/logoE.png') }}" alt="Élevage+ Logo">
                    <span class="ml-2 brand-text">ÉLEVAGE+</span>
                </a>

                <!-- ================= TOGGLER MOBILE ================= -->
                <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- ================= NAVBAR CONTENT ================= -->
                <div class="collapse navbar-collapse" id="mainNavbar">

                    <!-- ===== CENTRE ===== -->
                    <ul class="navbar-nav mx-auto align-items-lg-center w-100 justify-content-center">

                        <!-- Recherche -->
                        <li class="nav-item search-item">
                            <form class="search-form" action="#" method="GET">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control border-left-0" placeholder="Rechercher un élevage, une publication..." id="globalSearch" aria-label="Recherche">
                                    <div class="input-group-append">
                                        <button class="btn btn-success" type="submit" style="border-radius: 0 12px 12px 0;">
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </li>

                    </ul>

                    <!-- ===== DROITE ===== -->
                    <div class="navbar-actions d-flex flex-column flex-lg-row align-items-stretch ml-3 align-items-lg-center">

                        @auth
                            <!-- Utilisateur connecté -->
                            <div class="dropdown">
                                <button class="btn btn-outline-success dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user-circle mr-2"></i>
                                    <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Utilisateur' }}</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="{{ url('../auth/profile') }}">
                                        <i class="fas fa-user mr-2"></i> Mon profil
                                    </a>
                                    <a class="dropdown-item" href="{{ url('../auth/parametre') }}">
                                        <i class="fas fa-cog mr-2"></i> Paramètres
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="{{ url('/logout') }}">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                                    </a>
                                </div>
                            </div>
                        @else
                            <!-- Utilisateur non connecté -->
                            <a href="{{ url('/auth/login') }}" class="btn btn-outline-success mr-lg-2 mb-2 mb-lg-0">
                                <i class="fas fa-sign-in-alt mr-1"></i>
                                Connexion
                            </a>
                            <a href="{{ url('/auth/register') }}" class="btn btn-success">
                                <i class="fas fa-user-plus mr-1"></i>
                                Inscription
                            </a>
                        @endauth

                    </div>

                </div>

            </nav>

        </div>

    </header>
    
    <!-- ================= CONTENU PRINCIPAL ================= -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- ================= TOAST CONTAINER ================= -->
    <div id="toastContainer" style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 350px; width: 100%;"></div>

    <!-- ================= FOOTER ================= -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-copyright">
                    <i class="fas fa-copyright"></i> 2026 Élevage+ - Tous droits réservés
                </div>
                <div class="footer-links">
                    <a href="#" class="footer-link"><i class="fas fa-file-alt"></i> Mentions légales</a>
                    <span class="footer-separator">|</span>
                    <a href="#" class="footer-link"><i class="fas fa-envelope"></i> Contact</a>
                    <span class="footer-separator">|</span>
                    <a href="#" class="footer-link"><i class="fas fa-file-contract"></i> CGU</a>
                    <span class="footer-separator">|</span>
                    <a href="#" class="footer-link"><i class="fas fa-question-circle"></i> Aide</a>
                </div>
                <div class="footer-social">
                    <a href="#" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ================= SCRIPTS ================= -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script global pour les toasts et fonctionnalités communes -->
    <script>
        // ================= FONCTION TOAST GLOBALE =================
        function showToast(message, type = 'info', duration = 3000) {
            const container = document.getElementById('toastContainer');
            if (!container) return;
            
            const toastId = 'toast-' + Date.now();
            const icons = {
                success: 'fa-check-circle',
                danger: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            const colors = {
                success: '#28a745',
                danger: '#dc3545',
                warning: '#ffc107',
                info: '#17a2b8'
            };
            
            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = 'custom-toast';
            toast.style.cssText = `
                background: #fff;
                border-radius: 12px;
                padding: 14px 18px;
                margin-bottom: 12px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                display: flex;
                align-items: center;
                gap: 12px;
                border-left: 4px solid ${colors[type] || '#6c757d'};
                transform: translateX(120%);
                transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                animation: toastSlideIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
                max-width: 100%;
                position: relative;
            `;
            
            toast.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
                    <i class="fas ${icons[type] || 'fa-info-circle'}" style="color: ${colors[type] || '#6c757d'}; font-size: 20px;"></i>
                    <span style="font-size: 0.9rem; color: #1a202c; flex: 1;">${message}</span>
                </div>
                <button onclick="this.closest('.custom-toast').remove()" style="background: none; border: none; color: #999; cursor: pointer; font-size: 16px; padding: 0 4px;">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Auto-suppression
            setTimeout(() => {
                const el = document.getElementById(toastId);
                if (el) {
                    el.style.transform = 'translateX(120%)';
                    el.style.opacity = '0';
                    setTimeout(() => {
                        if (el.parentNode) el.remove();
                    }, 400);
                }
            }, duration);
        }

        // ================= RECHERCHE GLOBALE =================
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('globalSearch');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const query = this.value.trim();
                        if (query) {
                            window.location.href = `/recherche?q=${encodeURIComponent(query)}`;
                        } else {
                            showToast('Veuillez saisir un terme de recherche', 'warning');
                        }
                    }
                });
            }
            
            // Ajouter les styles pour les animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes toastSlideIn {
                    from {
                        transform: translateX(120%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                .custom-toast {
                    animation: toastSlideIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
                }
                .custom-toast-removing {
                    animation: toastSlideOut 0.3s ease forwards;
                }
                @keyframes toastSlideOut {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(120%);
                        opacity: 0;
                    }
                }
                .main-content {
                    min-height: calc(100vh - 200px);
                }
            `;
            document.head.appendChild(style);
        });
    </script>
    
    @stack('scripts')
</body>
</html>