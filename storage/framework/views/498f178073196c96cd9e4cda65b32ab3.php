<?php $__env->startSection('title', 'Dashboard - Élevage+'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/eleveurCSS/dashboard.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-wrapper">

    
    <div class="welcome-section">
        <div class="welcome-text">
            <h1><i class="fas fa-hand-peace"></i> Bonjour Jean !</h1>
            <p>voici ce qui se passe aujourd'hui</p>
        </div>
    </div>

    
    <div class="stats-grid">
        
        <div class="stat-card animals-card1">
            <div class="stat-icon">
                <i class="fas fa-paw"></i>
            </div>
            <div class="stat-value">45</div>
            <div class="stat-label">Animaux</div>
            <a href="#" class="stat-link">voir détail <i class="fas fa-arrow-right"></i></a>
        </div>

        
        <div class="stat-card animals-card2">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value">12</div>
            <div class="stat-label">Tâches du mois</div>
            <a href="#" class="stat-link">voir calendrier <i class="fas fa-arrow-right"></i></a>
        </div>

        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-value">3</div>
            <div class="stat-label">Alertes actives</div>
            <a href="#" class="stat-link">voir série <i class="fas fa-arrow-right"></i></a>
        </div>

        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-heartbeat"></i>
            </div>
            <div class="stat-value">89%</div>
            <div class="stat-label">Santé troupeau</div>
            <a href="#" class="stat-link">voir rapports <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>

    
    <div class="two-columns">
        
        <div class="card tasks-card">
            <div class="card-header">
                <h3><i class="fas fa-tasks"></i> Tâches du jour</h3>
                <a href="#" class="see-all">Voir tout <i class="fas fa-chevron-right"></i></a>
            </div>
            <div class="card-body">
                <div class="task-item">
                    <div class="task-info">
                        <span class="task-title">Vaccination - Triageau évolu</span>
                    </div>
                    <span class="task-status completed">Terminé</span>
                </div>
                <div class="task-item">
                    <div class="task-info">
                        <span class="task-title">Pesée - Vache #123</span>
                    </div>
                    <span class="task-status pending">À faire</span>
                </div>
                <div class="task-item">
                    <div class="task-info">
                        <span class="task-title">Nettoyage enclos</span>
                    </div>
                    <span class="task-status pending">À faire</span>
                </div>
            </div>
        </div>

        
        <div class="card alerts-card">
            <div class="card-header">
                <h3><i class="fas fa-bell"></i> Alertes actives</h3>
                <a href="#" class="see-all">Voir tout <i class="fas fa-chevron-right"></i></a>
            </div>
            <div class="card-body">
                <div class="alert-item">
                    <div class="alert-icon">
                        <i class="fas fa-capsules"></i>
                    </div>
                    <div class="alert-details">
                        <div class="alert-title">Stock de médicaments faible</div>
                        <div class="alert-time">Il reste 5 minutes en stock.</div>
                    </div>
                </div>
                <div class="alert-item">
                    <div class="alert-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="alert-details">
                        <div class="alert-title">Animal #127 perte de poids suspecte</div>
                        <div class="alert-time">Il y a 5 minutes.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="two-columns">
        
        <div class="card chart-card">
            <div class="card-header">
                <h3><i class="fas fa-chart-line"></i> Evolution du poids moyen</h3>
            </div>
            <div class="card-body chart-body">
                
                <div class="bar-chart">
                    <div class="bar-item" data-value="160"><span class="bar-label">Jan</span><div class="bar" style="height: 60px;"></div><span class="bar-value">160kg</span></div>
                    <div class="bar-item" data-value="180"><span class="bar-label">Fév</span><div class="bar" style="height: 70px;"></div><span class="bar-value">180kg</span></div>
                    <div class="bar-item" data-value="210"><span class="bar-label">Mar</span><div class="bar" style="height: 85px;"></div><span class="bar-value">210kg</span></div>
                    <div class="bar-item" data-value="240"><span class="bar-label">Avr</span><div class="bar" style="height: 100px;"></div><span class="bar-value">240kg</span></div>
                    <div class="bar-item" data-value="260"><span class="bar-label">Mai</span><div class="bar" style="height: 110px;"></div><span class="bar-value">260kg</span></div>
                    <div class="bar-item" data-value="275"><span class="bar-label">Juin</span><div class="bar" style="height: 115px;"></div><span class="bar-value">275kg</span></div>
                </div>
            </div>
        </div>

        
        <div class="card activity-card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> Activité récente</h3>
                <a href="#" class="see-all">Voir tout l'historique <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="card-body">
                <div class="activity-item">
                    <i class="fas fa-grain"></i>
                    <div class="activity-details">
                        <span class="activity-text">Votre pétouflon a reçu 12 kilos</span>
                        <span class="activity-time">Il y a 2 heures</span>
                    </div>
                </div>
                <div class="activity-item">
                    <i class="fas fa-comment"></i>
                    <div class="activity-details">
                        <span class="activity-text">Merci Clop a commenté votre article</span>
                        <span class="activity-time">Il y a 5 heures</span>
                    </div>
                </div>
                <div class="activity-item">
                    <i class="fas fa-boxes"></i>
                    <div class="activity-details">
                        <span class="activity-text">Stock d'aliments mis à jour</span>
                        <span class="activity-time">Mais - 50kg ajouté • Il y a 1 jour</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card summary-card">
        <div class="card-header">
            <h3><i class="fas fa-chart-pie"></i> Résumé par espèce</h3>
        </div>
        <div class="card-body species-summary">
            <div class="species-item">
                <div class="species-info">
                    <i class="fas fa-dog"></i>
                    <span class="species-name">Bovins</span>
                </div>
                <div class="species-stats">
                    <div class="progress-bar">
                        <div class="progress-fill bovine-fill" style="width: 100%"></div>
                    </div>
                    <span class="species-count">280/280</span>
                </div>
            </div>
            <div class="species-item">
                <div class="species-info">
                    <i class="fas fa-sheep"></i>
                    <span class="species-name">Ovis</span>
                </div>
                <div class="species-stats">
                    <div class="progress-bar">
                        <div class="progress-fill ovis-fill" style="width: 36%"></div>
                    </div>
                    <span class="species-count">100/280</span>
                </div>
            </div>
            <div class="species-item">
                <div class="species-info">
                    <i class="fas fa-goat"></i>
                    <span class="species-name">Caprins</span>
                </div>
                <div class="species-stats">
                    <div class="progress-bar">
                        <div class="progress-fill caprin-fill" style="width: 36%"></div>
                    </div>
                    <span class="species-count">100/280</span>
                </div>
            </div>
            <div class="species-item">
                <div class="species-info">
                    <i class="fas fa-dove"></i>
                    <span class="species-name">Volailles</span>
                </div>
                <div class="species-stats">
                    <div class="progress-bar">
                        <div class="progress-fill volaille-fill" style="width: 100%"></div>
                    </div>
                    <span class="species-count">280/280</span>
                </div>
            </div>
        </div>
        <div class="total-animals">
            TOTAL = 45
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Projets\Elevage-plus\resources\views/dashboard.blade.php ENDPATH**/ ?>