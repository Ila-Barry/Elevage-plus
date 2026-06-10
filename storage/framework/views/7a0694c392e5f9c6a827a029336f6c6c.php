<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'Élevage+'); ?></title>

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
    <link rel="stylesheet" href="<?php echo e(asset('css/layoutCSS/navbar.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/layoutCSS/sidebar.css')); ?>">

    <!-- CSS PAGE -->
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>

    <!-- NAVBAR -->
    <?php echo $__env->make('layouts.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="dashboard-layout" id="dashboardLayout">

        <!-- SIDEBAR -->
        <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!-- CONTENU PRINCIPAL -->
        <main class="dashboard-content" id="dashboardContent">
            <?php echo $__env->yieldContent('content'); ?>
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

        document.addEventListener('DOMContentLoaded', function() {
        // --- MODALE AJOUTER ---
        const addModal = document.getElementById('addAnimalModal');
        const editModal = document.getElementById('editAnimalModal');
        const btnAdd = document.querySelector('.btn-add-animal');
        const closeBtns = document.querySelectorAll('.modal-close');
        const cancelBtns = document.querySelectorAll('.btn-cancel');

        // Ouvrir modale Ajouter
        if(btnAdd) {
            btnAdd.addEventListener('click', function() {
                addModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            });
        }

        // Fermer les modales
        function closeModals() {
            addModal.style.display = 'none';
            editModal.style.display = 'none';
            document.body.style.overflow = '';
        }

        closeBtns.forEach(btn => btn.addEventListener('click', closeModals));
        cancelBtns.forEach(btn => btn.addEventListener('click', closeModals));

        window.addEventListener('click', function(e) {
            if(e.target === addModal) closeModals();
            if(e.target === editModal) closeModals();
        });

        // --- Gestion image Ajouter ---
        const chooseImgBtn = document.getElementById('chooseImageBtn');
        const deleteImgBtn = document.getElementById('deleteImageBtn');
        const imageInput = document.getElementById('animalImageInput');
        const photoPreview = document.getElementById('photoPreview');

        chooseImgBtn.addEventListener('click', () => imageInput.click());
        
        imageInput.addEventListener('change', function(e) {
            if(e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    photoPreview.innerHTML = `<img src="${ev.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
        
        deleteImgBtn.addEventListener('click', function() {
            imageInput.value = '';
            photoPreview.innerHTML = `<i class="fas fa-camera-retro"></i><span>Aperçu photo</span>`;
        });

        // --- Gestion image Modifier ---
        const editChooseBtn = document.getElementById('editChooseImageBtn');
        const editDeleteBtn = document.getElementById('editDeleteImageBtn');
        const editImageInput = document.getElementById('editAnimalImageInput');
        const editPreview = document.getElementById('editPhotoPreview');
        const editPreviewImg = document.getElementById('editPreviewImage');
        const editPreviewPlaceholder = document.getElementById('editPreviewPlaceholder');

        editChooseBtn.addEventListener('click', () => editImageInput.click());
        
        editImageInput.addEventListener('change', function(e) {
            if(e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    editPreviewImg.src = ev.target.result;
                    editPreviewImg.style.display = 'block';
                    editPreviewPlaceholder.style.display = 'none';
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
        
        editDeleteBtn.addEventListener('click', function() {
            editImageInput.value = '';
            editPreviewImg.style.display = 'none';
            editPreviewPlaceholder.style.display = 'flex';
            editPreviewImg.src = '';
        });

        // --- Simulation : ouvrir modale Modifier avec les données de l'animal ---
        const editBtns = document.querySelectorAll('.btn-edit');
        
        editBtns.forEach((btn, index) => {
            btn.addEventListener('click', function() {
                // Récupérer les infos de l'animal concerné (exemple: depuis la carte)
                const card = this.closest('.animal-card');
                const nomElement = card.querySelector('.animal-info h5');
                const details = card.querySelectorAll('.animal-details div');
                
                let nom = nomElement ? nomElement.innerText.replace('NOM : ', '') : '';
                let race = '';
                let poids = '';
                
                details.forEach(detail => {
                    const text = detail.innerText;
                    if(text.includes('Race :')) race = text.replace('Race : ', '');
                    if(text.includes('Poids :')) poids = text.replace('Poids : ', '').replace(' kg', '');
                });
                
                // Remplir le formulaire de modification
                document.getElementById('editNom').value = nom;
                document.getElementById('editRace').value = race;
                document.getElementById('editPoids').value = poids;
                document.getElementById('editNote').value = '';
                
                // Réinitialiser l'aperçu photo
                editDeleteBtn.click();
                
                // Ouvrir la modale modification
                editModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            });
        });

        // --- Soumission formulaire Ajouter ---
        const addForm = document.getElementById('addAnimalForm');
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Animal ajouté avec succès !');
            closeModals();
            addForm.reset();
            deleteImgBtn.click(); // Reset photo
        });

        // --- Soumission formulaire Modifier ---
        const editForm = document.getElementById('editAnimalForm');
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Animal modifié avec succès !');
            closeModals();
        });
    });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>
</html><?php /**PATH C:\Projets\Elevage-plus\resources\views/layouts/menu.blade.php ENDPATH**/ ?>