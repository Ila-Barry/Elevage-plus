<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Élevage+ - Gestion intégrée d\'élevage & Communauté agricole')</title>

    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layoutCSS/sidebar.css') }}">
</head>
<body>

    <!-- btn dynamique-->
    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>

<div class="app-wrapper">
    
    <!-- ================= SIDEBAR ================= -->
    <aside class="main-sidebar mt-5" id="sidebar">
        <button class="close-sidebar d-md-none" id="closeSidebar">
            <i class="fas fa-times"></i>
        </button>
        
        <!-- MENU -->
        <div class="sidebar-menu">
            <a href="#" class="sidebar-item active">
                <i class="fa-solid fa-bars"></i>
                <span>Menu</span>
            </a>

            <a href="{{ url('dashboard') }}" class="sidebar-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ url('/elevages') }}" class="sidebar-item">
                <i class="fas fa-horse"></i>
                <span>Mes élevages</span>
            </a>
            <a href="{{ url('/animaux') }}" class="sidebar-item">
                <i class="fas fa-paw"></i>
                <span>Mes animaux</span>
            </a>
            <a href="{{ url('/taches') }}" class="sidebar-item">
                <i class="fas fa-tasks"></i>
                <span>Mes tâches</span>
            </a>
            <a href="{{ url('/stocks') }}" class="sidebar-item">
                <i class="fas fa-box-open"></i>
                <span>Mes stocks</span>
            </a>
            <a href="{{ url('/blog') }}" class="sidebar-item">
                <i class="fas fa-pen"></i>
                <span>Mon blog</span>
            </a>
        </div>

        <!-- CARD -->
        <div class="sidebar-card">
            <!-- Version complète (grands écrans seulement) -->
            <div class="card-full d-none d-xl-block">
                <img src="https://cdn-icons-png.flaticon.com/512/2620/2620277.png" alt="mobile app">
                <h5>Application mobile</h5>
                <p>Gérez votre élevage partout, à tout moment</p>
                <button>
                    Télécharger
                    <i class="fas fa-download ml-1"></i>
                </button>
            </div>
            
            <!-- Version compacte (tous les écrans sauf très grands) -->
            <div class="card-compact d-xl-none">
                <a href="#" class="download-icon">
                    <i class="fas fa-download"></i>
                </a>
            </div>
        </div>
    </aside>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // Toggle sidebar on mobile
        $('#menuToggle').on('click', function(e) {
            e.stopPropagation();
            $('#sidebar').toggleClass('open');
        });
        
        // Close sidebar with close button (mobile)
        $('#closeSidebar').on('click', function() {
            $('#sidebar').removeClass('open');
        });

        // Close sidebar when clicking outside on mobile
        $(document).on('click', function(event) {
            if ($(window).width() <= 768) {
                if (!$(event.target).closest('#sidebar').length && 
                    !$(event.target).closest('#menuToggle').length) {
                    $('#sidebar').removeClass('open');
                }
            }
        });

        // Active link management
        $('.sidebar-item').on('click', function() {
            $('.sidebar-item').removeClass('active');
            $(this).addClass('active');
        });
        
        // Pour les écrans moyens, les tooltips peuvent être utiles
        if ($(window).width() >= 769 && $(window).width() <= 1024) {
            $('.sidebar-item').each(function() {
                var text = $(this).find('span').text();
                $(this).attr('title', text);
            });
        }
    });
</script>