@extends('layouts.menu')

@section('title', 'Dashboard - Élevage+')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/dashboard.css') }}">
@endpush

@section('content')

<div class="dashboard-container">

    <!-- BANNIERE -->
    <div class="welcome-banner">
        <div>
            <h1>BONJOUR JEAN ! 👋</h1>
            <p>Voici ce qu'il se passe aujourd'hui</p>
        </div>
    </div>

    <!-- KPI -->
    <div class="stats-row">

        <div class="stat-card green">
            <div class="number">45</div>
            <div class="label">Animaux</div>
            <a href="#">voir détail →</a>
        </div>

        <div class="stat-card blue">
            <div class="number">12</div>
            <div class="label">Tâches ce mois</div>
            <a href="#">voir calendrier →</a>
        </div>

        <div class="stat-card yellow">
            <div class="number">3</div>
            <div class="label">Alertes actives</div>
            <a href="#">voir alertes →</a>
        </div>

        <div class="stat-card red">
            <div class="number">89%</div>
            <div class="label">Santé troupeau</div>
            <a href="#">voir rapports →</a>
        </div>

    </div>

    <!-- GRID -->
    <div class="dashboard-grid">

        <!-- TACHES -->
        <div class="card-box">

            <div class="box-header">
                📋 TÂCHES DU JOUR
            </div>

            <div class="task-row">
                <span>✅ Vaccination - Troupeau bovin</span>
                <span class="done">Terminée</span>
            </div>

            <div class="task-row">
                <span>⏰ Pesée - Vache #123</span>
                <span class="todo">À faire</span>
            </div>

            <div class="task-row">
                <span>🧹 Nettoyage enclos</span>
                <span class="todo">À faire</span>
            </div>

        </div>

        <!-- GRAPH -->
        <div class="card-box">

            <div class="box-header between">
                <span>📈 ÉVOLUTION DU POIDS MOYEN</span>

                <select>
                    <option>6 derniers mois</option>
                </select>
            </div>

            <!-- Ajout d'une hauteur forcée pour éviter que le graphique s'écrase -->
            <canvas id="weightChart" style="height: 250px; width: 100%;"></canvas>

        </div>

        <!-- ALERTES -->
        <div class="card-box">

            <div class="box-header">
                🔔 ALERTES ACTIVES
            </div>

            <div class="alert-card warning">
                <h5>Stock de médicaments faible</h5>
                <p>Il reste très peu de doses en stock.</p>
            </div>

            <div class="alert-card danger">
                <h5>Animal #127 : Perte de poids suspecte</h5>
                <p>Baisse importante détectée cette semaine.</p>
            </div>

        </div>

        <!-- DONUT -->
        <div class="card-box">

            <div class="box-header">
                Résumé par espèce
            </div>

            <div class="species-wrapper">

                <canvas id="speciesChart" style="max-height: 200px;"></canvas>

                <div class="species-legend">

                    <div><span class="dot green"></span> Bovins 28 (62%)</div>

                    <div><span class="dot yellow"></span> Ovins 10 (22%)</div>

                    <div><span class="dot blue"></span> Caprins 5 (11%)</div>

                    <div><span class="dot red"></span> Volailles 2 (5%)</div>

                </div>

            </div>

            <h3 class="total">
                TOTAL = 45
            </h3>

        </div>

        <!-- ACTIVITE -->
        <div class="card-box activity-box">

            <div class="box-header between">

                <span>🏆 ACTIVITÉ RÉCENTE</span>

                <span>suivante ▶</span>

            </div>

            <div class="activity-item">
                👍 Votre publication a reçu 12 likes
                <small>Il y a 2 heures</small>
            </div>

            <div class="activity-item">
                💬 Marie Diop a commenté votre article
                <small>Il y a 5 heures</small>
            </div>

            <div class="activity-item">
                🚜 Stock d'aliments mis à jour
                <small>Il y a 1 jour</small>
            </div>

            <a href="#" class="history-link">
                Voir tout l'historique →
            </a>

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('weightChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
        datasets: [{
            label: 'Poids moyen (kg)',
            data: [420, 435, 448, 462, 480, 495],
            borderColor: '#22c55e',
            backgroundColor: 'rgba(34,197,94,0.15)',
            borderWidth: 3,
            pointRadius: 5,
            pointHoverRadius: 7,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,

        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.parsed.y + ' kg';
                    }
                }
            }
        },

        scales: {
            y: {
                min: 400,
                max: 520,
                ticks: {
                    callback: function(value) {
                        return value + ' kg';
                    }
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

const species = document.getElementById('speciesChart');

new Chart(species,{
    type:'doughnut',
    data:{
        labels:['Bovins','Ovins','Caprins','Volailles'],
        datasets:[{
            data:[28,10,5,2],
            backgroundColor:[
                '#22c55e',
                '#f59e0b',
                '#2563eb',
                '#ef4444'
            ]
        }]
    },
    options:{
        responsive:true,
        plugins:{
            legend:{
                display:false
            }
        }
    }
});

</script>

@endpush