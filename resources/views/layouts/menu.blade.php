<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Élevage+')</title>

    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- FontAwesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

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
                    // Grand écran: sidebar visible, contenu décalé
                    content.css('margin-left', '260px');
                } else if (windowWidth >= 769 && windowWidth <= 1024) {
                    // Écran moyen: sidebar compacte
                    content.css('margin-left', '80px');
                } else {
                    // Mobile: sidebar cachée par défaut
                    if (sidebar.hasClass('open')) {
                        content.css('margin-left', '0px');
                    } else {
                        content.css('margin-left', '0px');
                    }
                }
            }
            
            // Appeler au chargement
            adjustContentMargin();
            
            // Réajuster lors du redimensionnement
            $(window).on('resize', function() {
                adjustContentMargin();
            });
            
            // Réajuster quand la sidebar s'ouvre/ferme sur mobile
            $(document).on('click', '#menuToggle', function() {
                setTimeout(adjustContentMargin, 50);
            });
            
            $(document).on('click', '#closeSidebar', function() {
                setTimeout(adjustContentMargin, 50);
            });
        });
    </script>

    @stack('scripts')

</body>
</html>