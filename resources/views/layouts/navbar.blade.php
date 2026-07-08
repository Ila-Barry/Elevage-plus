<!-- ================= NAVBAR ================= -->
<header class="main-navbar">
    <div class="container-fluid px-lg-4">
        <nav class="navbar navbar-expand-lg navbar-light p-0 w-100">

            <!-- LOGO -->
            <a href="{{ url('dashboard') }}" class="navbar-brand logo-wrapper d-flex align-items-center">
                <div class="logo-circle">
                    <img class="img-logo" src="{{ asset('images/logoE.png') }}" alt="Élevage+">
                </div>
                <span class="logo-text">ÉLEVAGE+</span>
            </a>

            <!-- TOGGLER MOBILE -->
            <div class="d-flex align-items-center gap-2">
                <button class="menu-toggle-btn" id="menuToggleBtn" aria-label="Menu" type="button">
                    <i class="fas fa-bars"></i>
                </button>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- NAVBAR CONTENT -->
            <div class="collapse navbar-collapse" id="mainNavbar">

                <!-- MENU PRINCIPAL -->
                <ul class="navbar-nav mx-auto navbar-menu">
                    <li class="nav-item">
                        <a href="{{ url('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span class="titre">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/messages') }}" class="nav-link {{ request()->is('messages') ? 'active' : '' }}">
                            <i class="fas fa-comment"></i>
                            <span class="titre">Messages</span>
                            <span id="navbarMessageBadge" class="badge badge-danger badge-pill"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/notification') }}" class="nav-link {{ request()->is('notification') ? 'active' : '' }}">
                            <i class="fas fa-bell"></i>
                            <span class="titre">Notifications</span>
                            <span id="navbarNotificationBadge" class="badge badge-danger badge-pill"></span>
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
                        <button class="btn dropdown-toggle profile-button" type="button" id="profileDropdown" data-toggle="dropdown">
                            @php
                                $user = auth()->user();
                                $avatar = $user?->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=2e7d32&color=fff';
                            @endphp
                            <img src="{{ $avatar }}" alt="profile" class="profile-image">
                            <span class="profile-name d-none d-lg-inline">{{ $user?->name ?? 'Utilisateur' }}</span>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right shadow border-0">
                            
                            <a class="dropdown-item" href="{{ url('../auth/profile') }}">
                                <i class="fas fa-user mr-2"></i>
                                Mon profil
                            </a>

                            <a class="dropdown-item" href="{{ url('../auth/parametre') }}">
                                <i class="fas fa-cog mr-2"></i>
                                Paramètres
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="#" id="logoutBtn">
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

<!-- Toast pour les messages -->
<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
@push('scripts')
<script>
$(document).ready(function() {
    // ================= SIDEBAR TOGGLE =================
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

    // ================= FERMER LA SIDEBAR =================
    $(document).on('click', '#sidebar-overlay', function() {
        $('#sidebar').removeClass('open');
        $('#menuToggleBtn').find('i').removeClass('fa-times').addClass('fa-bars');
        $(this).fadeOut(200);
    });

    // ================= RECHERCHE =================
    $('#navbarSearch').on('keypress', function(e) {
        if (e.which === 13) {
            const query = $(this).val().trim();
            if (query) {
                window.location.href = '/recherche?q=' + encodeURIComponent(query);
            }
        }
    });

    // ================= FERMER EN CLIQUANT À L'EXTÉRIEUR =================
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

    // ================= REDIMENSIONNEMENT =================
    $(window).on('resize', function() {
        if ($(window).width() > 768) {
            $('#sidebar').removeClass('open');
            $('#menuToggleBtn').find('i').removeClass('fa-times').addClass('fa-bars');
            $('#sidebar-overlay').remove();
        }
    });

    // ================= 🚀 DÉCONNEXION =================
    $('#logoutBtn').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
            return;
        }
        
        const logoutBtn = $(this);
        const originalText = logoutBtn.html();
        
        logoutBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Déconnexion...');
        logoutBtn.css('opacity', '0.7');
        
        const token = localStorage.getItem('access_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        fetch('/api/auth/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('✅ API Logout réussi:', data);
            
            localStorage.removeItem('access_token');
            localStorage.removeItem('token_expiry');
            localStorage.removeItem('user');
            localStorage.removeItem('remember_login');
            
            return fetch('/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
        })
        .then(response => {
            console.log('✅ Web Logout réussi');
            showToast('Déconnexion réussie !', 'success');
            
            setTimeout(() => {
                window.location.href = '/auth/login';
            }, 500);
        })
        .catch(error => {
            console.error('❌ Erreur lors de la déconnexion:', error);
            
            localStorage.removeItem('access_token');
            localStorage.removeItem('token_expiry');
            localStorage.removeItem('user');
            
            showToast('Déconnexion réussie !', 'success');
            
            setTimeout(() => {
                window.location.href = '/auth/login';
            }, 500);
        })
        .finally(() => {
            logoutBtn.html(originalText);
            logoutBtn.css('opacity', '1');
        });
    });

    // ================= FONCTIONS TOAST =================
    function showToast(message, type = 'info') {
        const existingToast = document.querySelector('.custom-toast');
        if (existingToast) existingToast.remove();
        
        const toast = document.createElement('div');
        toast.className = `custom-toast ${type}`;
        
        let icon = 'fa-info-circle';
        if (type === 'success') icon = 'fa-check-circle';
        else if (type === 'danger') icon = 'fa-exclamation-circle';
        else if (type === 'warning') icon = 'fa-exclamation-triangle';
        
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas ${icon}"></i>
                <span>${message}</span>
                <button class="toast-close"><i class="fas fa-times"></i></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        
        toast.querySelector('.toast-close').addEventListener('click', function() {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        });
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // ================= RAFRAÎCHISSEMENT DES BADGES =================
    async function refreshBadges() {
        try {
            const token = localStorage.getItem('access_token');
            if (!token) return;

            // ================= MESSAGES =================
            try {
                const response = await fetch('/api/messaging/unread-count', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    const totalMessages = data.data?.unread_count || data.unread_count || 0;

                    const msgBadgeNavbar = document.getElementById('navbarMessageBadge');
                    const msgBadgeSidebar = document.getElementById('sidebarMessageBadge');

                    if (msgBadgeNavbar) {
                        msgBadgeNavbar.textContent = totalMessages > 0 ? totalMessages : '';
                        msgBadgeNavbar.style.display = totalMessages > 0 ? 'inline-block' : 'none';
                    }

                    if (msgBadgeSidebar) {
                        msgBadgeSidebar.textContent = totalMessages > 0 ? totalMessages : '';
                        msgBadgeSidebar.style.display = totalMessages > 0 ? 'inline-block' : 'none';
                    }
                }
            } catch (e) {
                console.warn('⚠️ Erreur chargement messages:', e);
            }

            // ================= NOTIFICATIONS =================
            try {
                const notifResponse = await fetch('/api/notifications/unread', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if (notifResponse.ok) {
                    const notifData = await notifResponse.json();
                    
                    // 🔥 CORRECTION: Vérifier toutes les structures possibles
                    let totalNotif = 0;
                    
                    // Structure 1: { data: { count: X } }
                    if (notifData.data?.count !== undefined) {
                        totalNotif = notifData.data.count;
                    }
                    // Structure 2: { data: { meta: { unread_count: X } } }
                    else if (notifData.data?.meta?.unread_count !== undefined) {
                        totalNotif = notifData.data.meta.unread_count;
                    }
                    // Structure 3: { data: { unread_count: X } }
                    else if (notifData.data?.unread_count !== undefined) {
                        totalNotif = notifData.data.unread_count;
                    }
                    // Structure 4: { data: [...] } (tableau direct)
                    else if (Array.isArray(notifData.data)) {
                        totalNotif = notifData.data.length;
                    }
                    // Structure 5: { count: X }
                    else if (notifData.count !== undefined) {
                        totalNotif = notifData.count;
                    }
                    // Structure 6: { data: { data: [...] } }
                    else if (notifData.data?.data && Array.isArray(notifData.data.data)) {
                        totalNotif = notifData.data.data.filter(n => !n.read_at && !n.is_read).length;
                    }

                    console.log('📊 Notifications non lues:', totalNotif);

                    const notifBadgeNavbar = document.getElementById('navbarNotificationBadge');
                    const notifBadgeSidebar = document.getElementById('sidebarNotificationBadge');

                    if (notifBadgeNavbar) {
                        notifBadgeNavbar.textContent = totalNotif > 0 ? totalNotif : '';
                        notifBadgeNavbar.style.display = totalNotif > 0 ? 'inline-block' : 'none';
                        
                        // Animation pulse
                        if (totalNotif > 0) {
                            notifBadgeNavbar.classList.remove('pulse');
                            void notifBadgeNavbar.offsetWidth;
                            notifBadgeNavbar.classList.add('pulse');
                        }
                    }

                    if (notifBadgeSidebar) {
                        notifBadgeSidebar.textContent = totalNotif > 0 ? totalNotif : '';
                        notifBadgeSidebar.style.display = totalNotif > 0 ? 'inline-block' : 'none';
                    }

                    // Mettre à jour le titre de la page
                    if (totalNotif > 0) {
                        document.title = `(${totalNotif}) Notifications - Élevage+`;
                    }
                }
            } catch (e) {
                console.warn('⚠️ Erreur chargement notifications:', e);
            }

        } catch (error) {
            console.error('❌ Erreur refreshBadges:', error);
        }
    }

    // ================= LANCER LE RAFRAÎCHISSEMENT =================
    refreshBadges();
    setInterval(refreshBadges, 10000); // Toutes les 10 secondes

    // ================= RAFRAÎCHIR QUAND LA PAGE REVIENT AU PREMIER PLAN =================
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            refreshBadges();
        }
    });

    // ================= RAFRAÎCHIR QUAND LE RÉSEAU REVIENT =================
    window.addEventListener('online', function() {
        refreshBadges();
    });
});
</script>
@endpush