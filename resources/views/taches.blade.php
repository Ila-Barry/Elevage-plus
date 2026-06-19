@extends('layouts.menu')

@section('title', 'Tâches')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/taches.css') }}">
@endpush

@section('content')

<div class="taches-container">

<!-- ========================================= -->
<!-- TITRE PAGE -->
<!-- ========================================= -->
<div class="page-header">
    <h1>CALENDRIER DES TÂCHES</h1>
</div>

<div class="tasks-grid">

    <!-- ========================================= -->
    <!-- COLONNE GAUCHE -->
    <!-- ========================================= -->
    <div class="tasks-left">

        <!-- BOUTONS VUE -->
        <div class="calendar-filters">

            <button class="filter-btn active">
                <i class="fas fa-calendar-alt"></i>
                Mois
            </button>

            <button class="filter-btn">
                <i class="far fa-calendar"></i>
                Semaines
            </button>

            <button class="filter-btn">
                <i class="far fa-calendar-check"></i>
                Jours
            </button>

        </div>

        <!-- CALENDRIER -->
        <div class="calendar-card">

            <div class="calendar-top">

                <button class="calendar-nav">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <h3>MAI 2026</h3>

                <button class="calendar-nav">
                    <i class="fas fa-chevron-right"></i>
                </button>

            </div>

            <table class="calendar-table">

                <thead>
                    <tr>
                        <th>Lun</th>
                        <th>Mar</th>
                        <th>Mer</th>
                        <th>Jeu</th>
                        <th>Ven</th>
                        <th>Sam</th>
                        <th>Dim</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>28</td>
                        <td>29</td>
                        <td>30</td>
                        <td>1</td>
                        <td>2</td>
                        <td>3</td>
                        <td>4</td>
                    </tr>

                    <tr>
                        <td>5</td>
                        <td>6</td>
                        <td>7</td>
                        <td>8</td>
                        <td>9</td>
                        <td>10</td>
                        <td>11</td>
                    </tr>

                    <tr>
                        <td>12</td>
                        <td>13</td>
                        <td class="task-day">14</td>
                        <td class="task-day">15</td>
                        <td>16</td>
                        <td>17</td>
                        <td>18</td>
                    </tr>

                    <tr>
                        <td>19</td>
                        <td class="task-day">20</td>
                        <td>21</td>
                        <td>22</td>
                        <td>23</td>
                        <td>24</td>
                        <td>25</td>
                    </tr>

                    <tr>
                        <td class="task-day">26</td>
                        <td>27</td>
                        <td>28</td>
                        <td>29</td>
                        <td>30</td>
                        <td>31</td>
                        <td>1</td>
                    </tr>

                </tbody>

            </table>

            <div class="calendar-legend">
                <span class="legend-dot"></span>
                Tâche(s) planifiée(s)
            </div>

        </div>

        <!-- TACHES A VENIR -->
        <div class="upcoming-card">

            <div class="section-header">
                <i class="far fa-calendar-alt"></i>
                TÂCHES À VENIR
            </div>

            <ul>

                <li>
                    <i class="far fa-calendar"></i>
                    <span>15/05 - Vermifuge (Marguerite)</span>
                </li>

                <li>
                    <i class="far fa-calendar"></i>
                    <span>20/05 - Contrôle vétérinaire</span>
                </li>

                <li>
                    <i class="far fa-calendar"></i>
                    <span>26/05 - Vaccination rappel (troupeau)</span>
                </li>

            </ul>

        </div>

    </div>

    <!-- ========================================= -->
    <!-- COLONNE DROITE -->
    <!-- ========================================= -->
    <div class="tasks-right">

        <button
            class="btn-add-task"
            data-bs-toggle="modal"
            data-bs-target="#addTaskModal">

            <i class="fas fa-plus"></i>
            Ajouter une tâche
        </button>

        <div class="today-card">

            <div class="section-header">
                <i class="far fa-calendar-alt"></i>
                TÂCHES DU 14 MAI 2026
            </div>

            <!-- TACHE 1 -->
            <div class="task-item">

                <div class="task-time">
                    ⏰ 09:00 - Vaccination
                </div>

                <div class="task-desc">
                    Troupeau bovin - Élevage de Thiès
                </div>

                <div class="task-actions">

                    <button class="btn-success-task">
                        <i class="fas fa-check-square"></i>
                        Masquer fait
                    </button>

                                        <button
                        class="btn-edit-task edit-task-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#editTaskModal"
                        data-type="Vaccination"
                        data-heure="09:00"
                        data-description="Troupeau bovin - Élevage de Thiès">

                        <i class="fas fa-pen"></i>
                        Modifier
                    </button>

                </div>

            </div>

            <!-- TACHE 2 -->
            <div class="task-item">

                <div class="task-time">
                    ⏰14:00 - Pesée
                </div>

                <div class="task-desc">
                    Animal : Marguerite (n°123)
                </div>

                <div class="task-actions">

                    <button class="btn-success-task">
                        <i class="fas fa-check-square"></i>
                        Masquer fait
                    </button>

                                        <button
                        class="btn-edit-task edit-task-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#editTaskModal"
                        data-type="Pesée"
                        data-heure="14:00"
                        data-description="Animal : Marguerite (n°123)">

                        <i class="fas fa-pen"></i>
                        Modifier
                    </button>
                    
                </div>

            </div>

            <!-- TACHE 3 -->
            <div class="task-item">

                <div class="task-time">
                    ⏰16:00 - Nettoyage enclos
                </div>

                <div class="task-desc">
                    Élevage bovin - Enclos nord
                </div>

                <div class="task-actions">

                    <button class="btn-success-task">
                        <i class="fas fa-check-square"></i>
                        Masquer fait
                    </button>

                                                            <button
                        class="btn-edit-task edit-task-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#editTaskModal"
                        data-type="Nettoyage enclos"
                        data-heure="16:00"
                        data-description="Élevage bovin - Enclos nord">

                        <i class="fas fa-pen"></i>
                        Modifier
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>

</div>
<!-- ========================================= -->
<!-- MODALE AJOUTER UNE TÂCHE -->
<!-- ========================================= -->

<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <!-- En-tête -->
            <div class="modal-header">

                <h2 class="modal-title fw-bold" id="addTaskModalLabel">
                    AJOUTER UNE TÂCHE
                </h2>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Fermer">
                </button>

            </div>

            <!-- Corps -->
            <div class="modal-body">

                <form action="#" method="POST">

                    @csrf

                    <!-- Animal concerné -->
                    <div class="mb-3">

                        <label class="form-label">
                            🐄 Animal concerné <span class="text-danger">*</span>
                        </label>

                        <select class="form-select" name="animal_id" required>

                            <option value="">
                                Sélectionner un animal
                            </option>

                            <option value="1">
                                Marguerite (n°123)
                            </option>

                            <option value="2">
                                Bella (n°124)
                            </option>

                        </select>

                    </div>

                    <!-- Type de tâche -->
                    <div class="mb-3">

                        <label class="form-label">
                            🏠 Type de tâche <span class="text-danger">*</span>
                        </label>

                                                <select
                            class="form-select"
                            name="type_tache"
                            required>

                            <option value="">
                                Choisir un type
                            </option>

                            <option value="Vaccination">
                                Vaccination
                            </option>

                            <option value="Vermifuge">
                                Vermifuge
                            </option>

                            <option value="Pesée">
                                Pesée
                            </option>

                            <option value="Nettoyage enclos">
                                Nettoyage enclos
                            </option>

                            <option value="Contrôle vétérinaire">
                                Contrôle vétérinaire
                            </option>

                        </select>

                    </div>

                    <!-- Date -->
                    <div class="mb-3">

                        <label class="form-label">
                            📅 Date planifiée <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            class="form-control"
                            name="date_planifiee"
                            required>

                    </div>

                    <!-- Heure -->
                    <div class="mb-3">

                        <label class="form-label">
                            ⏰ Heure (optionnel)
                        </label>

                        <input
                            type="time"
                            class="form-control"
                            name="heure">

                    </div>

                    <!-- Notes -->
                    <div class="mb-3">

                        <label class="form-label">
                            📝 Notes (optionnelle)
                        </label>

                        <textarea
                            class="form-control"
                            name="notes"
                            rows="4"
                            placeholder="Ajouter une description ou une remarque..."></textarea>

                    </div>

                    <!-- Boutons -->
                    <div class="d-flex justify-content-center gap-3">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            ❌ Annuler

                        </button>

                        <button
                            type="submit"
                            class="btn btn-success">

                            ✅ Ajouter

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>
<!-- ========================================= -->
<!-- MODALE MODIFIER UNE TÂCHE -->
<!-- ========================================= -->

<div class="modal fade"
     id="editTaskModal"
     tabindex="-1"
     aria-labelledby="editTaskModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">

                <h2 class="modal-title fw-bold"
                    id="editTaskModalLabel">

                    MODIFIER UNE TÂCHE

                </h2>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <!-- BODY -->
            <div class="modal-body">

                <form action="#" method="POST">

                    @csrf
                    @method('PUT')

                    <!-- Animal -->

                    <div class="mb-3">

                        <label class="form-label">
                            🐄 Animal concerné
                        </label>

                        <select class="form-select" name="animal_id">

                            <option value="1" selected>
                                Marguerite (n°123)
                            </option>

                            <option value="2">
                                Bella (n°124)
                            </option>

                        </select>

                    </div>

                    <!-- Type tâche -->

                    <div class="mb-3">

                        <label class="form-label">
                            📋 Type de tâche
                        </label>

                        <select class="form-select"
                                name="type_tache">

                            <option>
                                Vaccination
                            </option>

                            <option>
                                Vermifuge
                            </option>

                            <option>
                                Pesée
                            </option>

                        </select>

                    </div>

                    <!-- Date -->

                    <div class="mb-3">

                        <label class="form-label">
                            📅 Date planifiée
                        </label>

                        <input type="date"
                               class="form-control"
                               value="2026-05-14">
                    </div>

                    <!-- Heure -->

                    <div class="mb-3">

                        <label class="form-label">
                            ⏰ Heure
                        </label>

                                                <input
                            type="time"
                            class="form-control"
                            id="editHeure"
                            name="heure">

                    <!-- Notes -->

                    <div class="mb-3">

                        <label class="form-label">
                            📝 Notes
                        </label>

                                                <textarea
                            class="form-control"
                            id="editDescription"
                            name="notes"
                            rows="4">
                        </textarea>

                    </div>

                    <!-- Boutons -->

                    <div class="d-flex justify-content-center gap-3">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Annuler

                        </button>

                        <button
                            type="submit"
                            class="btn btn-success">

                            Enregistrer

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const editButtons = document.querySelectorAll('.edit-task-btn');

    const typeField = document.getElementById('editTypeTache');
    const heureField = document.getElementById('editHeure');
    const descriptionField = document.getElementById('editDescription');

    editButtons.forEach(button => {

        button.addEventListener('click', function () {

            typeField.value = this.dataset.type;
            heureField.value = this.dataset.heure;
            descriptionField.value = this.dataset.description;

        });

    });

});
</script>
@endsection