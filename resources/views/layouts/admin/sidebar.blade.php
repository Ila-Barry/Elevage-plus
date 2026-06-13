<!-- BTN MOBILE -->
<button class="menu-toggle" id="menuToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- ================= SIDEBAR ================= -->
<aside class="main-sidebar mt-5" id="sidebar">

    <!-- CLOSE MOBILE -->
    <button class="close-sidebar d-md-none" id="closeSidebar">
        <i class="fas fa-times"></i>
    </button>

    <!-- MENU -->
    <div class="sidebar-menu">

        <a href="#" class="sidebar-item active">
            <i class="fa-solid fa-bars"></i>
            <span>Menu</span>
        </a>

        <a href="{{ url('admin/dashboard') }}" class="sidebar-item">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ url('admin/utilisateur') }}" class="sidebar-item">
            <i class="fas fa-horse"></i>
            <span>Utilisateurs</span>
        </a>

        <a href="{{ url('admin/publication') }}" class="sidebar-item">
            <i class="fas fa-paw"></i>
            <span>Publications</span>
        </a>

        <a href="{{ url('admin/signale') }}" class="sidebar-item">
            <i class="fas fa-tasks"></i>
            <span>Signallements</span>
        </a>

        <a href="{{ url('admin/statistique') }}" class="sidebar-item">
            <i class="fas fa-box-open"></i>
            <span>Statistique</span>
        </a>

    </div>

    <!-- CARD -->
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

            <button>
                Télécharger
                <i class="fas fa-download ml-1"></i>
            </button>

        </div>

        <!-- PETIT ÉCRAN -->
        <div class="card-compact d-xl-none">

            <a href="#" class="download-icon">
                <i class="fas fa-download"></i>
            </a>

        </div>

    </div>

</aside>

@push('scripts')
<script>

    $(document).ready(function () {

        // OPEN
        $('#menuToggle').on('click', function (e) {

            e.stopPropagation();

            $('#sidebar').toggleClass('open');

        });

        // CLOSE
        $('#closeSidebar').on('click', function () {

            $('#sidebar').removeClass('open');

        });

        // CLOSE OUTSIDE
        $(document).on('click', function (event) {

            if ($(window).width() <= 768) {

                if (
                    !$(event.target).closest('#sidebar').length &&
                    !$(event.target).closest('#menuToggle').length
                ) {

                    $('#sidebar').removeClass('open');

                }

            }

        });

        // ACTIVE LINK
        $('.sidebar-item').on('click', function () {

            $('.sidebar-item').removeClass('active');

            $(this).addClass('active');

        });

    });

</script>
@endpush