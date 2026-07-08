<header class="main-navbar">
    <div class="container-fluid px-lg-4">
        <nav class="navbar navbar-expand-lg navbar-light p-0 w-100">

            <a href="{{ url('admin/dashboard') }}" class="navbar-brand logo-wrapper d-flex align-items-center">
                <div class="logo-circle">
                    <img class="img-logo" src="{{ asset('images/logoE.png') }}" alt="Élevage+">
                </div>
                <span class="logo-text">ÉLEVAGE+</span>
            </a>

            <div class="d-flex align-items-center gap-2">
                <button class="menu-toggle-btn" id="menuToggleBtn" aria-label="Menu" type="button">
                    <i class="fas fa-bars"></i>
                </button>

                <button class="navbar-toggler"
                        type="button"
                        data-toggle="collapse"
                        data-target="#mainNavbar"
                        aria-controls="mainNavbar"
                        aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="mainNavbar">

                <div class="navbar-search-center">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher..." id="navbarSearch">
                    </div>
                </div>

                <div class="navbar-right d-flex align-items-center">

                    <div class="dropdown profile-dropdown">
                        <button class="btn dropdown-toggle profile-button"
                                type="button"
                                id="profileDropdown"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            <img src="https://i.pravatar.cc/100?u=admin" alt="profile" class="profile-image">
                            <span class="profile-name d-none d-lg-inline">Admin Système</span>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right shadow border-0" aria-labelledby="profileDropdown">
                            <a class="dropdown-item" href="{{ url('/admin/profil') }}">
                                <i class="fas fa-user mr-2"></i> Mon profil
                            </a>
                            <a class="dropdown-item" href="{{ url('/admin/parametres') }}">
                                <i class="fas fa-cog mr-2"></i> Paramètres
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="{{ url('/admin/logout') }}">
                                <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                            </a>
                        </div>
                    </div>

                    <a href="{{ url('/admin/logout') }}" class="btn btn-danger btn-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="d-none d-lg-inline">Déconnexion</span>
                    </a>

                </div>
            </div>

        </nav>
    </div>
</header>

@push('scripts')
<script>
    $(document).ready(function() {
        // === TOGGLE SIDEBAR SUR MOBILE ===
        $('#menuToggleBtn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var sidebar = $('#sidebar');
            sidebar.toggleClass('open');
            
            var icon = $(this).find('i');
            if (sidebar.hasClass('open')) {
                icon.removeClass('fa-bars').addClass('fa-times');
                if (!$('#sidebar-overlay').length) {
                    $('body').append('<div id="sidebar-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1020;display:none;"></div>');
                }
                $('#sidebar-overlay').fadeIn(200);
            } else {
                icon.removeClass('fa-times').addClass('fa-bars');
                $('#sidebar-overlay').fadeOut(200);
            }
        });

        // === FERMER LA SIDEBAR ===
        $('#closeSidebar').on('click', function() {
            $('#sidebar').removeClass('open');
            $('#menuToggleBtn').find('i').removeClass('fa-times').addClass('fa-bars');
            $('#sidebar-overlay').fadeOut(200);
        });

        // === FERMER AVEC OVERLAY ===
        $(document).on('click', '#sidebar-overlay', function() {
            $('#sidebar').removeClass('open');
            $('#menuToggleBtn').find('i').removeClass('fa-times').addClass('fa-bars');
            $(this).fadeOut(200);
        });

        // === RECHERCHE AVEC ENTRÉE ===
        $('#navbarSearch').on('keypress', function(e) {
            if (e.which === 13) {
                const query = $(this).val().trim();
                if (query) {
                    window.location.href = '/admin/recherche?q=' + encodeURIComponent(query);
                }
            }
        });

        // === FERMER LA SIDEBAR EN CLIQUANT À L'EXTÉRIEUR (CORRIGÉ POUR LE PROFIL) ===
        $(document).on('click', function(event) {
            if ($(window).width() <= 768) {
                var sidebar = $('#sidebar');
                var toggleBtn = $('#menuToggleBtn');
                var overlay = $('#sidebar-overlay');
                
                // On ignore le clic si c'est sur la sidebar, le bouton menu, OU le dropdown du profil
                if (!$(event.target).closest('#sidebar').length && 
                    !$(event.target).closest('#menuToggleBtn').length &&
                    !$(event.target).closest('#profileDropdown').length && 
                    sidebar.hasClass('open')) {
                    
                    sidebar.removeClass('open');
                    toggleBtn.find('i').removeClass('fa-times').addClass('fa-bars');
                    if (overlay.length) {
                        overlay.fadeOut(200);
                    }
                }
            }
        });

        // === GÉRER LE REDIMENSIONNEMENT ===
        $(window).on('resize', function() {
            if ($(window).width() > 768) {
                $('#sidebar').removeClass('open');
                $('#menuToggleBtn').find('i').removeClass('fa-times').addClass('fa-bars');
                $('#sidebar-overlay').remove();
            }
        });

        // === NAVBAR TOGGLER (CORRIGÉ POUR UTILISER LE COMPORTEMENT NATIF BOOTSTRAP) ===
        $('.navbar-toggler').on('click', function() {
            $('#mainNavbar').toggleClass('show');
        });
    });
</script>
@endpush