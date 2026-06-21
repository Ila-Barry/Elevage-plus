<!-- ================= SIDEBAR ================= -->
<aside class="main-sidebar mt-5" id="sidebar">

    <!-- CLOSE MOBILE -->
    <button class="close-sidebar d-md-none" id="closeSidebar">
        <i class="fas fa-times"></i>
    </button>

    <!-- PROFIL UTILISATEUR DANS SIDEBAR -->
    <div class="sidebar-user d-none d-md-block">
        <div class="user-avatar">
            <img src="https://i.pravatar.cc/100?u=jean_diagne" alt="Jean Diagne">
        </div>
        <div class="user-info">
            <h6>Jean Diagne</h6>
            <span>Éleveur bovin</span>
        </div>
    </div>

    <!-- MENU -->
    <div class="sidebar-menu">

        <a href="<?php echo e(url('dashboard')); ?>" class="sidebar-item <?php echo e(request()->is('dashboard') ? 'active' : ''); ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <a href="<?php echo e(url('/elevages')); ?>" class="sidebar-item <?php echo e(request()->is('elevages*') ? 'active' : ''); ?>">
            <i class="fas fa-horse"></i>
            <span>Mes élevages</span>
        </a>

        <a href="<?php echo e(url('/animaux')); ?>" class="sidebar-item <?php echo e(request()->is('animaux*') ? 'active' : ''); ?>">
            <i class="fas fa-paw"></i>
            <span>Mes animaux</span>
        </a>

        <a href="<?php echo e(url('/taches')); ?>" class="sidebar-item <?php echo e(request()->is('taches*') ? 'active' : ''); ?>">
            <i class="fas fa-tasks"></i>
            <span>Mes tâches</span>
        </a>

        <a href="<?php echo e(url('/stocks')); ?>" class="sidebar-item <?php echo e(request()->is('stocks*') ? 'active' : ''); ?>">
            <i class="fas fa-box-open"></i>
            <span>Mes stocks</span>
        </a>

        <a href="<?php echo e(url('/blog')); ?>" class="sidebar-item <?php echo e(request()->is('blog*') ? 'active' : ''); ?>">
            <i class="fas fa-pen"></i>
            <span>Mon blog</span>
        </a>

        <a href="<?php echo e(url('/messages')); ?>" class="sidebar-item <?php echo e(request()->is('messages*') ? 'active' : ''); ?>">
            <i class="fas fa-comment"></i>
            <span>Messages</span>
            <span class="badge badge-light ml-auto">3</span>
        </a>

        <a href="<?php echo e(url('/notification')); ?>" class="sidebar-item <?php echo e(request()->is('notification*') ? 'active' : ''); ?>">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
            <span class="badge badge-light ml-auto">12</span>
        </a>

    </div>

    <!-- CARD APPLICATION -->
    <div class="sidebar-card">

        <!-- GRAND ÉCRAN -->
        <div class="card-full d-none d-xl-block">

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

        </div>

        <!-- PETIT ÉCRAN -->
        <div class="card-compact d-xl-none">

            <a href="#" class="download-icon" onclick="showToast('Téléchargement de l\'application mobile', 'info')">
                <i class="fas fa-download"></i>
            </a>

        </div>

    </div>

</aside>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // OPEN SIDEBAR (déjà géré par le bouton dans la navbar)
        // Mais on garde la compatibilité avec l'ancien bouton si présent
        $('#menuToggle, #menuToggleBtn').on('click', function(e) {
            e.stopPropagation();
            $('#sidebar').toggleClass('open');
        });

        // CLOSE
        $('#closeSidebar').on('click', function() {
            $('#sidebar').removeClass('open');
            $('#menuToggleBtn').find('i').removeClass('fa-times').addClass('fa-bars');
        });

        // ACTIVE LINK - Géré par les classes PHP, mais on garde le fallback
        $('.sidebar-item').on('click', function() {
            $('.sidebar-item').removeClass('active');
            $(this).addClass('active');
        });

        // Fermer la sidebar en cliquant à l'extérieur (mobile)
        $(document).on('click', function(event) {
            if ($(window).width() <= 768) {
                if (!$(event.target).closest('#sidebar').length && 
                    !$(event.target).closest('#menuToggleBtn').length &&
                    !$(event.target).closest('#menuToggle').length) {
                    $('#sidebar').removeClass('open');
                    $('#menuToggleBtn').find('i').removeClass('fa-times').addClass('fa-bars');
                }
            }
        });
    });
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\Projets\Elevage-plus\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>