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
            // Gestion de l'upload et de l'aperçu
        const photoInput = document.getElementById('photoInput');

        if (photoInput) {
            photoInput.addEventListener('change', function (e) {

                const file = e.target.files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function (event) {

                        const preview = document.getElementById('imagePreview');
                        const placeholder = document.querySelector('.image-preview-placeholder');

                        if (preview) {
                            preview.src = event.target.result;
                            preview.style.display = 'block';
                        }

                        if (placeholder) {
                            placeholder.style.display = 'none';
                        }
                    };

                    reader.readAsDataURL(file);
                }

            });
        }

        // Gestion de la suppression
        const deleteBtn = document.getElementById('deletePhotoBtn');

        if (deleteBtn) {
            deleteBtn.addEventListener('click', function () {

                const fileInput = document.getElementById('photoInput');
                const preview = document.getElementById('imagePreview');
                const placeholder = document.querySelector('.image-preview-placeholder');

                if (fileInput) fileInput.value = '';

                if (preview) {
                    preview.src = '';
                    preview.style.display = 'none';
                }

                if (placeholder) {
                    placeholder.style.display = 'flex';
                }

                showTemporaryMessage('Photo supprimée avec succès');
            });
        }

        // Fonction utilitaire
        function showTemporaryMessage(message) {

            const infoDiv = document.querySelector('.photo-info');

            if (!infoDiv) return;

            const originalContent = infoDiv.innerHTML;

            infoDiv.innerHTML = `
                <small class="text-success">
                    <i class="fas fa-check-circle mr-1"></i>
                    ${message}
                </small>
            `;

            setTimeout(() => {
                infoDiv.innerHTML = originalContent;
            }, 2000);
        }
           
        </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    });
</script>

        @stack('scripts')

    </body>
</html>