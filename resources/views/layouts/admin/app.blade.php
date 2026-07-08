<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Élevage+')</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('css/admin/navbar.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">

        @stack('styles')
    </head>

    <body>

        @include('layouts.admin.navbar')

        <div class="dashboard-layout" id="dashboardLayout">

            @include('layouts.admin.sidebar')

            <main class="dashboard-content" id="dashboardContent">
                @yield('content')
            </main>

        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        
<script>
    $(document).ready(function() {
        // === CORRECTION : Forcer l'initialisation des dropdowns Bootstrap ===
        if (typeof $.fn.dropdown !== 'undefined') {
            $('.dropdown-toggle').dropdown();
        }

        // === CORRECTION : Alternative manuelle pour les dropdowns ===
        $('.profile-dropdown .dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            var menu = $(this).siblings('.dropdown-menu');
            var isOpen = menu.hasClass('show');
            
            $('.profile-dropdown .dropdown-menu').removeClass('show');
            
            if (!isOpen) {
                menu.addClass('show');
            }
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.profile-dropdown').length) {
                $('.profile-dropdown .dropdown-menu').removeClass('show');
            }
        });
    });
</script>

        @stack('scripts')

    </body>
</html>