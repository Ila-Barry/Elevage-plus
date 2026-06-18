<!-- ================= NAVBAR ================= -->
<header class="main-navbar">

    <div class="container-fluid px-lg-4">

        <nav class="navbar navbar-expand-lg navbar-light p-0 w-100">

            <!-- LOGO -->
            <a href="{{ url('dashboard') }}" class="navbar-brand logo-wrapper d-flex align-items-center">

                <div class="logo-circle">
                    <img class="img-logo" src="{{ asset('images/logoE.png') }}" alt="Élevage+">
                </div>

                <span class="logo-text">
                    ÉLEVAGE+
                </span>

            </a>

            <!-- TOGGLER MOBILE -->
            <div class="d-flex align-items-center gap-2">
                <!-- Bouton menu mobile (sidebar) - Uniquement sur mobile/tablette -->
                <button class="menu-toggle-btn" id="menuToggleBtn" aria-label="Menu" type="button">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Bouton toggler navbar - Uniquement sur mobile -->
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

            <!-- NAVBAR CONTENT -->
            <div class="collapse navbar-collapse" id="mainNavbar">

                <!-- MENU PRINCIPAL -->
                <ul class="navbar-nav mx-auto navbar-menu">

                    <li class="nav-item">
                        <a href="{{ url('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }} titre">
                            <i class="fas fa-home"></i>
                            <span class="titre">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ url('/messages') }}" class="nav-link {{ request()->is('messages') ? 'active' : '' }} titre">
                            <i class="fas fa-comment"></i>
                            <span class="titre">Messages</span>
                            <span class="badge badge-danger badge-pill">3</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ url('/notification') }}" class="nav-link {{ request()->is('notification') ? 'active' : '' }} titre">
                            <i class="fas fa-bell"></i>
                            <span class="titre">Notifications</span>
                            <span class="badge badge-danger badge-pill">12</span>
                        </a>
                    </li>

                </ul>

                <!-- RIGHT SIDE -->
                <div class="navbar-right d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center">

                    <!-- SEARCH -->
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher..." id="navbarSearch">
                    </div>

                    <!-- PROFILE DROPDOWN -->
                    <div class="dropdown profile-dropdown">

                        <button class="btn dropdown-toggle profile-button"
                                type="button"
                                id="profileDropdown"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">

                            <img src="https://i.pravatar.cc/100?u=jean_diagne"
                                 alt="profile"
                                 class="profile-image">

                            <span class="profile-name d-none d-lg-inline">
                                Jean Diagne
                            </span>

                        </button>

                        <div class="dropdown-menu dropdown-menu-right shadow border-0">

                            <a class="dropdown-item" href="{{ url('/profil') }}">
                                <i class="fas fa-user mr-2"></i>
                                Mon profil
                            </a>

                            <a class="dropdown-item" href="{{ url('/parametres') }}">
                                <i class="fas fa-cog mr-2"></i>
                                Paramètres
                            </a>

                            <a class="dropdown-item" href="{{ url('/elevages') }}">
                                <i class="fas fa-horse mr-2"></i>
                                Mes élevages
                            </a>

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item text-danger" href="{{ url('/logout') }}">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Déconnexion
                            </a>

                        </div>

                    </div>

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
            
            console.log('Bouton cliqué !'); // Pour déboguer
            
            var sidebar = $('#sidebar');
            sidebar.toggleClass('open');
            
            // Changer l'icône
            var icon = $(this).find('i');
            if (sidebar.hasClass('open')) {
                icon.removeClass('fa-bars').addClass('fa-times');
                // Ajouter un overlay
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
                    window.location.href = '/recherche?q=' + encodeURIComponent(query);
                }
            }
        });

        // === FERMER EN CLIQUANT À L'EXTÉRIEUR ===
        $(document).on('click', function(event) {
            if ($(window).width() <= 768) {
                var sidebar = $('#sidebar');
                var toggleBtn = $('#menuToggleBtn');
                var overlay = $('#sidebar-overlay');
                
                if (!$(event.target).closest('#sidebar').length && 
                    !$(event.target).closest('#menuToggleBtn').length &&
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
    });
    
    $('#menuToggleBtn').on('click', function(e) {
    console.log('Bouton cliqué');
    
    e.preventDefault();
    e.stopPropagation();

    $('#sidebar').toggleClass('open');
});
</script>
@endpush