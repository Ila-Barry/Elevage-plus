@extends('layouts.admin.app')

@section('title', 'dashboard-admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endpush

@section('content')

<div class="dashboard-wrapper">

<body>
    <div class="container-fluid dashboard-wrapper">

        <!-- ========== EN-TÊTE AVEC FOND ÉLEVAGE ========== -->
        <header class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="fas fa-tachometer-alt text-success"></i> Dashboard Administrateur</h1>
                    <p class="text-muted">Admin 3 - Bienvenue, Administrateur</p>
                </div>
                <div class="col-md-6 text-md-right">
                    <span class="badge badge-pill badge-success px-3 py-2">
                        <i class="fas fa-clock"></i> Dernière connexion : aujourd'hui 14:30
                    </span>
                </div>
            </div>
        </header>

        <!-- ========== INDICATEURS CLÉS ========== -->
        <section class="key-indicators my-4">
            <div class="row">
                <div class="col-md-3 col-6 mb-3">
                    <div class="indicator-card card-stat-users">
                        <div class="indicator-icon"><i class="fas fa-users"></i></div>
                        <div class="indicator-content">
                            <span class="indicator-number">127</span>
                            <span class="indicator-label">Utilisateurs</span>
                            <span class="indicator-change text-success"><i class="fas fa-arrow-up"></i> +12 ce mois</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="indicator-card card-stat-posts">
                        <div class="indicator-icon"><i class="fas fa-newspaper"></i></div>
                        <div class="indicator-content">
                            <span class="indicator-number">345</span>
                            <span class="indicator-label">Publications</span>
                            <span class="indicator-change text-success"><i class="fas fa-arrow-up"></i> +23 ce mois</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="indicator-card card-stat-reports">
                        <div class="indicator-icon"><i class="fas fa-flag"></i></div>
                        <div class="indicator-content">
                            <span class="indicator-number">12</span>
                            <span class="indicator-label">Signalements</span>
                            <span class="indicator-change text-danger"><i class="fas fa-arrow-up"></i> +5 ce mois</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="indicator-card card-stat-rating">
                        <div class="indicator-icon"><i class="fas fa-star"></i></div>
                        <div class="indicator-content">
                            <span class="indicator-number">4.8</span>
                            <span class="indicator-label">Note moyenne</span>
                            <span class="indicator-change text-info"><i class="fas fa-chart-line"></i> 15 ateliers</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== GRAPHIQUE + ALERTES ========== -->
        <div class="row my-4">
            <!-- Évolution mensuelle -->
           <!-- Évolution mensuelle -->
<div class="col-lg-12 mb-4">
    <div class="card chart-card">
        <div class="card-header bg-white">
            <h5><i class="fas fa-chart-bar text-success"></i> Évolution mensuelle</h5>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <!-- Axe Y (vertical) avec graduations -->
                <div class="chart-y-axis">
                    <span>150</span>
                    <span>100</span>
                    <span>50</span>
                    <span>0</span>
                </div>
                <!-- Zone du graphique (barres + grille) -->
                <div class="chart-area">
                    <!-- Barres -->
                    <div class="bar-wrapper">
                        <div class="bar" style="height: 30%;"><span>J</span></div>
                        <div class="bar" style="height: 50%;"><span>F</span></div>
                        <div class="bar" style="height: 70%;"><span>M</span></div>
                        <div class="bar" style="height: 45%;"><span>A</span></div>
                        <div class="bar" style="height: 85%;"><span>M</span></div>
                        <div class="bar" style="height: 60%;"><span>I</span></div>
                        <div class="bar" style="height: 90%;"><span>J</span></div>
                    </div>
                    <!-- Lignes de la grille horizontale -->
                    <div class="grid-lines">
                        <div class="grid-line" style="bottom: 0%;"></div>
                        <div class="grid-line" style="bottom: 33.33%;"></div>
                        <div class="grid-line" style="bottom: 66.66%;"></div>
                        <div class="grid-line" style="bottom: 100%;"></div>
                    </div>
                </div>
            </div>
            <!-- Axe X (horizontal) -->
            <div class="chart-x-axis">
                <span>j</span>
                <span>f</span>
                <span>m</span>
                <span>a</span>
                <span>m</span>
                <span>i</span>
                <span>j</span>
            </div>
        </div>
    </div>
</div>
            <!-- Alertes et activités récentes -->
            <div class="col-lg-12 mb-4">
                <div class="card alert-card">
                    <div class="card-header bg-white">
                        <h5><i class="fas fa-exclamation-triangle text-warning"></i> Alertes et activités récentes</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled alert-list">
                            <li class="alert-item">
                                <span class="alert-icon"><i class="fas fa-flag text-danger"></i></span>
                                <span class="alert-text">5 nouveaux signalements à traiter</span>
                                <a href="#" class="btn btn-sm btn-outline-danger">Traiter</a>
                            </li>
                            <li class="alert-item">
                                <span class="alert-icon"><i class="fas fa-user-plus text-success"></i></span>
                                <span class="alert-text">2 nouveaux utilisateurs inscrits aujourd'hui</span>
                                <a href="#" class="btn btn-sm btn-outline-success">Voir</a>
                            </li>
                            <li class="alert-item">
                                <span class="alert-icon"><i class="fas fa-chart-line text-info"></i></span>
                                <span class="alert-text">Taux d'engagement du blog : +15% cette semaine</span>
                                <span class="badge badge-info">+15%</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== DERNIERS UTILISATEURS INSCRITS ========== -->
        <section class="users-table my-4">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-user-plus text-success"></i> Derniers utilisateurs inscrits</h5>
                    <a href="#" class="btn btn-sm btn-outline-success">Voir tous les utilisateurs →</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-user-circle text-success"></i> Jean Dupont</td>
                                    <td>jean@email.com</td>
                                    <td>15/05/2026</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary btn-action btn-view" data-toggle="modal" data-target="#userModal" data-name="Jean Dupont" data-email="jean@email.com" data-date="15/05/2026"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-warning btn-action btn-edit" data-toggle="modal" data-target="#editModal" data-name="Jean Dupont" data-email="jean@email.com" data-date="15/05/2026"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-action btn-delete" data-toggle="modal" data-target="#deleteModal" data-name="Jean Dupont"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-user-circle text-success"></i> Marie Diop</td>
                                    <td>marie@email.com</td>
                                    <td>14/05/2026</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary btn-action btn-view" data-toggle="modal" data-target="#userModal" data-name="Marie Diop" data-email="marie@email.com" data-date="14/05/2026"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-warning btn-action btn-edit" data-toggle="modal" data-target="#editModal" data-name="Marie Diop" data-email="marie@email.com" data-date="14/05/2026"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-action btn-delete" data-toggle="modal" data-target="#deleteModal" data-name="Marie Diop"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-user-circle text-success"></i> Amadou Sy</td>
                                    <td>amadou@email.com</td>
                                    <td>13/05/2026</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary btn-action btn-view" data-toggle="modal" data-target="#userModal" data-name="Amadou Sy" data-email="amadou@email.com" data-date="13/05/2026"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-warning btn-action btn-edit" data-toggle="modal" data-target="#editModal" data-name="Amadou Sy" data-email="amadou@email.com" data-date="13/05/2026"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-action btn-delete" data-toggle="modal" data-target="#deleteModal" data-name="Amadou Sy"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- ========== MODAL VOIR (détails) ========== -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user text-success"></i> Détails utilisateur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Nom :</strong> <span id="modalUserName">Jean Dupont</span></p>
                    <p><strong>Email :</strong> <span id="modalUserEmail">jean@email.com</span></p>
                    <p><strong>Date d'inscription :</strong> <span id="modalUserDate">15/05/2026</span></p>
                    <p><strong>Rôle :</strong> Éleveur</p>
                    <p><strong>Statut :</strong> <span class="badge badge-success">Actif</span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== MODAL MODIFIER ========== -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit text-warning"></i> Modifier l'utilisateur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm">
                    <div class="modal-body">
                        <input type="hidden" id="editOriginalName">
                        <div class="form-group">
                            <label>Nom</label>
                            <input type="text" class="form-control" id="editName" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" id="editEmail" required>
                        </div>
                        <div class="form-group">
                            <label>Date d'inscription</label>
                            <input type="date" class="form-control" id="editDate">
                        </div>
                        <div class="form-group">
                            <label>Rôle</label>
                            <select class="form-control" id="editRole">
                                <option>Éleveur</option>
                                <option>Administrateur</option>
                                <option>Modérateur</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========== MODAL SUPPRIMER (confirmation) ========== -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-trash-alt"></i> Confirmer la suppression</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer l'utilisateur :</p>
                    <p class="font-weight-bold text-danger" id="deleteUserName">Jean Dupont</p>
                    <p class="text-muted small">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== SCRIPTS ========== -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {

            // --- Modal Voir ---
            $('#userModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                modal.find('#modalUserName').text(button.data('name'));
                modal.find('#modalUserEmail').text(button.data('email'));
                modal.find('#modalUserDate').text(button.data('date'));
            });

            // --- Modal Modifier ---
            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                modal.find('#editOriginalName').val(button.data('name'));
                modal.find('#editName').val(button.data('name'));
                modal.find('#editEmail').val(button.data('email'));
                modal.find('#editDate').val(button.data('date'));
                modal.find('#editRole').val('Éleveur');
            });

            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                var originalName = $('#editOriginalName').val();
                var newName = $('#editName').val();
                var newEmail = $('#editEmail').val();
                var newDate = $('#editDate').val();
                var newRole = $('#editRole').val();
                alert('Utilisateur "' + originalName + '" modifié :\nNom : ' + newName + '\nEmail : ' + newEmail + '\nDate : ' + newDate + '\nRôle : ' + newRole);
                $('#editModal').modal('hide');
            });

            // --- Modal Supprimer ---
            $('#deleteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                modal.find('#deleteUserName').text(button.data('name'));
            });

            $('#confirmDeleteBtn').on('click', function() {
                var userName = $('#deleteUserName').text();
                alert('Utilisateur "' + userName + '" supprimé (simulation).');
                $('#deleteModal').modal('hide');
            });

            // --- Intégration API Admin Stats ---
            const token = localStorage.getItem('access_token');

            if (!token) {
                window.location.href = '/auth/login';
                return;
            }

            $.ajax({
                url: '/api/admin/dashboard/stats',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;

                        // Utilisateurs
                        $('.card-stat-users .indicator-number').text(data.utilisateurs.total);
                        const mois = data.utilisateurs.nouveaux_par_mois;
                        if (mois.length > 0) {
                            const dernierMois = mois[mois.length - 1];
                            $('.card-stat-users .indicator-change')
                                .html('<i class="fas fa-arrow-up"></i> +' + dernierMois.nombre + ' ce mois');
                        }

                        // Publications
                        $('.card-stat-posts .indicator-number').text(data.publications.total);

                        // Signalements
                        $('.card-stat-reports .indicator-number').text(data.stocks.produits_critiques);
                    }
                },
                error: function(xhr) {
                    console.error('Erreur API:', xhr.responseJSON);
                    if (xhr.status === 401) {
                        window.location.href = '/auth/login';
                    }
                }
            });

        });
    </script>
</body>

</div>

@endsection