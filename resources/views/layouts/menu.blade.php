<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- VAPID Public Key pour les notifications push -->
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">

    <title>@yield('title', 'Élevage+')</title>

    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">
    
    <!-- LOGO -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logoE.png') }}">

    <!-- CSS GLOBAL -->
    <link rel="stylesheet" href="{{ asset('css/layoutCSS/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layoutCSS/sidebar.css') }}">

    <!-- CSS PAGE -->
    @stack('styles')
</head>

<body>

    <!-- NAVBAR -->
    @include('layouts.navbar')

    <div class="dashboard-layout" id="dashboardLayout">

        <!-- SIDEBAR -->
        @include('layouts.sidebar')

        <!-- CONTENU PRINCIPAL -->
        <main class="dashboard-content" id="dashboardContent">
            @yield('content')
        </main>

    </div>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Ajuster la marge du contenu principal en fonction de la sidebar
        function adjustContentMargin() {
            var sidebar = $('#sidebar');
            var content = $('#dashboardContent');
            var windowWidth = $(window).width();
            
            if (windowWidth > 1024) {
                content.css('margin-left', '260px');
            } else if (windowWidth >= 769 && windowWidth <= 1024) {
                content.css('margin-left', '80px');
            } else {
                content.css('margin-left', '0px');
            }
        }
        
        // Appeler au chargement
        adjustContentMargin();
        
        // Réajuster lors du redimensionnement
        $(window).on('resize', function() {
            adjustContentMargin();
        });
        
        // === CORRECTION : Utiliser le bon ID ===
        // Réajuster quand la sidebar s'ouvre/ferme sur mobile
        $(document).on('click', '#menuToggleBtn', function() {
            setTimeout(adjustContentMargin, 100);
        });
        
        $(document).on('click', '#closeSidebar', function() {
            setTimeout(adjustContentMargin, 100);
        });

        // === CORRECTION : Fermer la sidebar avec le bouton close ===
        $('#closeSidebar').on('click', function() {
            $('#sidebar').removeClass('open');
            $('#menuToggleBtn').find('i').removeClass('fa-times').addClass('fa-bars');
            $('#sidebar-overlay').fadeOut(200);
        });

        // === CORRECTION : Fermer avec overlay ===
        $(document).on('click', '#sidebar-overlay', function() {
            $('#sidebar').removeClass('open');
            $('#menuToggleBtn').find('i').removeClass('fa-times').addClass('fa-bars');
            $(this).fadeOut(200);
        });
    });

    // Enregistrement du service worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('✅ Service Worker enregistré avec succès:', registration);
                })
                .catch(function(error) {
                    console.error('❌ Erreur enregistrement Service Worker:', error);
                });
        });
    }

    // Demander la permission pour les notifications
    function requestNotificationPermission() {
        if (!('Notification' in window)) {
            console.log('🔔 Les notifications ne sont pas supportées par ce navigateur');
            return;
        }
        
        if (Notification.permission === 'granted') {
            console.log('✅ Permission notifications déjà accordée');
            subscribeToPushNotifications();
            return;
        }
        
        if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    console.log('✅ Permission notifications accordée');
                    subscribeToPushNotifications();
                } else {
                    console.log('⚠️ Permission notifications refusée');
                }
            });
        }
    }

    // S'abonner aux notifications push
    function subscribeToPushNotifications() {
        if (!('serviceWorker' in navigator)) return;
        if (!('PushManager' in window)) return;
        
        navigator.serviceWorker.ready.then(registration => {
            registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: '{{ config('webpush.vapid.public_key') }}'
            })
            .then(subscription => {
                console.log('✅ Abonnement push réussi:', subscription);
                // Envoyer la subscription au serveur
                return fetch('/api/webpush/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Authorization': 'Bearer ' + (localStorage.getItem('access_token') || '')
                    },
                    body: JSON.stringify({ subscription: subscription })
                });
            })
            .then(response => response.json())
            .then(data => {
                console.log('✅ Subscription sauvegardée sur le serveur:', data);
            })
            .catch(error => {
                console.error('❌ Erreur abonnement push:', error);
            });
        });
    }

    // Vérifier les permissions au chargement
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier si l'utilisateur est connecté
        const token = localStorage.getItem('access_token');
        if (token) {
            requestNotificationPermission();
        }
    });

    // Enregistrement du Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('✅ Service Worker enregistré avec succès:', registration);
                })
                .catch(function(error) {
                    console.error('❌ Erreur enregistrement Service Worker:', error);
                });
        });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
  
</body>
</html> 