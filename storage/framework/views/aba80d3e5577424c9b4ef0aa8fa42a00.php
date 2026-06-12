<!-- ================= NAVBAR ================= -->
<header class="main-navbar">

    <div class="container-fluid px-lg-4">

        <nav class="navbar navbar-expand-lg navbar-light p-0 w-100">

            <!-- LOGO -->
            <a href="#" class="navbar-brand logo-wrapper d-flex align-items-center">

                <div class="logo-circle">
                    Logo
                </div>

                <span class="logo-text">
                    ÉLEVAGE+
                </span>

            </a>

            <!-- TOGGLER -->
            <button class="navbar-toggler"
                    type="button"
                    data-toggle="collapse"
                    data-target="#mainNavbar">

                <span class="navbar-toggler-icon"></span>

            </button>

            <!-- NAVBAR CONTENT -->
            <div class="collapse navbar-collapse" id="mainNavbar">

                <!-- MENU -->
                <ul class="navbar-nav mx-auto navbar-menu">

                    <li class="nav-item">
                        <a href="<?php echo e(url('dashboard')); ?>" class="nav-link active">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo e(url('/messages')); ?>" class="nav-link">
                            <i class="fas fa-comment"></i>
                            <span>Messages</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo e(url('/notification')); ?>" class="nav-link">
                            <i class="fas fa-bell"></i>
                            <span>Notification</span>
                        </a>
                    </li>

                </ul>

                <!-- RIGHT -->
                <div class="navbar-right d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center">

                    <!-- SEARCH -->
                    <div class="search-box">

                        <i class="fas fa-search"></i>

                        <input type="text" placeholder="Rechercher...">

                    </div>

                    <!-- PROFILE -->
                    <div class="dropdown profile-dropdown">

                        <button class="btn dropdown-toggle profile-button"
                                type="button"
                                id="profileDropdown"
                                data-toggle="dropdown">

                            <img src="https://i.pravatar.cc/100"
                                 alt="profile"
                                 class="profile-image">

                            <span class="profile-name">
                                Jean Diagne
                            </span>

                        </button>

                        <div class="dropdown-menu dropdown-menu-right shadow border-0">

                            <a class="dropdown-item" href="<?php echo e(url('/auth/profile')); ?>">
                                <i class="fas fa-user mr-2"></i>
                                Profil
                            </a>

                            <a class="dropdown-item" href="<?php echo e(url('/auth/parametre')); ?>">
                                <i class="fas fa-cog mr-2"></i>
                                Paramètres
                            </a>

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item text-danger" href="#">
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

<?php $__env->startPush('scripts'); ?>
<script>

    $(document).ready(function () {

        $('.navbar-toggler').on('click', function () {

            $('#mainNavbar').toggleClass('show');

        });

    });

</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\Projets\Elevage-plus\resources\views/layouts/navbar.blade.php ENDPATH**/ ?>