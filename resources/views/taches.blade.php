@extends('layouts.menu')

@section('title', 'Tâches')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/taches.css') }}">
@endpush

@section('content')

<div class="taches-container">

```
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

        <button class="btn-add-task">
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

                    <button class="btn-edit-task">
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

                    <button class="btn-edit-task">
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

                    <button class="btn-edit-task">
                        <i class="fas fa-pen"></i>
                        Modifier
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>
```

</div>

@endsection