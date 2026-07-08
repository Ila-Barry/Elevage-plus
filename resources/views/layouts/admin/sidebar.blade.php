<!-- ================= SIDEBAR ADMIN ================= -->
<aside class="main-sidebar" id="sidebar">

    <!-- CLOSE MOBILE -->
    <button class="close-sidebar" id="closeSidebar">
        <i class="fas fa-times"></i>
    </button>

    <!-- PROFIL UTILISATEUR DANS SIDEBAR -->
    <div class="sidebar-user">
        <div class="user-avatar">
            <img src="https://i.pravatar.cc/100?u=admin" alt="Admin">
        </div>
        <div class="user-info">
            <h6>Admin Système</h6>
            <span>Super Administrateur</span>
            <div class="user-status">
                <i class="fas fa-circle"></i> En ligne
            </div>
        </div>
    </div>

    <!-- MENU -->
    <div class="sidebar-menu">

        <a href="{{ url('admin/dashboard') }}" class="sidebar-item {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ url('admin/utilisateur') }}" class="sidebar-item {{ request()->is('admin/utilisateur*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Utilisateurs</span>
        </a>

        <a href="{{ url('admin/publication') }}" class="sidebar-item {{ request()->is('admin/publication*') ? 'active' : '' }}">
            <i class="fas fa-newspaper"></i>
            <span>Publications</span>
        </a>

        <a href="{{ url('admin/signale') }}" class="sidebar-item">
            <i class="fas fa-tasks"></i>
            <span>Signalements</span>
        </a>

        <a href="{{ url('admin/statistique') }}" class="sidebar-item {{ request()->is('admin/statistique*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i>
            <span>Statistiques</span>
        </a>

        <a href="#" class="sidebar-item {{ request()->is('admin/parametre*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Paramètres</span>
        </a>

    </div>

    <!-- DIVIDER -->
    <div class="sidebar-divider"></div>

    <!-- CARD APPLICATION -->
    <!-- <div class="sidebar-card"> -->

        <!-- GRAND ÉCRAN -->
        <!-- <div class="card-full d-none d-xl-block">

            <img src="https://cdn-icons-png.flaticon.com/512/2620/2620277.png"
                 alt="mobile app">

            <h5>Application mobile</h5>

            <p>
                Gérez votre élevage partout,
                à tout moment
            </p>

            <button onclick="showToast('Téléchargement de l\'application mobile', 'info')">
                <i class="fas fa-download mr-1"></i>
                Télécharger
            </button>

        </div> -->

        <!-- PETIT ÉCRAN -->
        <!-- <div class="card-compact d-xl-none">

            <a href="#" class="download-icon" onclick="showToast('Téléchargement de l\'application mobile', 'info')">
                <i class="fas fa-download"></i>
            </a>

        </div>

    </div> -->

</aside>

@push('scripts')
<script>
    $(document).ready(function() {
        // === TOGGLE SIDEBAR ===
        // Le bouton #menuToggleBtn est dans la navbar
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

        // === FERMER EN CLIQUANT À L'EXTÉRIEUR (mobile uniquement) ===
        $(document).on('click', function(event) {
            if ($(window).width() <= 768) {
                var sidebar = $('#sidebar');
                var toggleBtn = $('#menuToggleBtn');
                var overlay = $('#sidebar-overlay');
                
                if (!$(event.target).closest('#sidebar').length && 
                    !$(event.target).closest('#menuToggleBtn').length &&
                    !$(event.target).closest('.profile-dropdown').length &&
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

        // === ACTIVE LINK ===
        $('.sidebar-item').on('click', function() {
            $('.sidebar-item').removeClass('active');
            $(this).addClass('active');
        });
    });
</script>
@endpush