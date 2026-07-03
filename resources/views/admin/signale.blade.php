<!-- resources/views/admin/signale.blade.php -->
@extends('layouts.admin.app')

@section('title', 'Gestion des signalements')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/signale.css') }}">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

@section('content')
<div class="dashboard-wrapper">
    
     <!-- En-tête de la page avec les boutons d'action -->
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Gestion des signalements</h1>
            <p class="page-subtitle">Gérez et traitez les signalements de contenus de la plateforme</p>
        </div>
        <div class="page-header-right">
            <!-- Bouton Exporter -->
            <button class="btn-header btn-export" style="color: #048538;">
                <i class="fas fa-download" ></i> Exporter les signalements
            </button>
            <!-- Bouton Tout traiter -->
            <button class="btn-header btn-process-all" style="background-color: #05882a";>
                <i class="fas fa-check-double" ></i> Tout traiter
            </button>
            <!-- Bouton Paramètres -->
            <button class="btn-header btn-settings">
                <i class="fas fa-cog"></i> Paramètres
            </button>
        </div>
    </div>

    <!-- Section des filtres -->
    <div class="filters-section">
        <div class="filters-grid">
            <!-- Filtre Statut -->
            <div class="filter-group">
                <label for="filter-status" class="filter-label">
                    <i class="fas fa-filter"></i> Statut
                </label>
                <select id="filter-status" class="filter-select">
                    <option value="all">En attente</option>
                    <option value="pending">Tous</option>
                    <option value="approved">Approuvé</option>
                    <option value="rejected">Rejeté</option>
                </select>
            </div>

            <!-- Filtre Type -->
            <div class="filter-group">
                <label for="filter-type" class="filter-label">
                    <i class="fas fa-tag"></i> Type
                </label>
                <select id="filter-type" class="filter-select">
                    <option value="all">Tous</option>
                    <option value="spam">Publicité / Spam</option>
                    <option value="offensive">Langage offensant</option>
                    <option value="medical">Information médicale</option>
                </select>
            </div>

            <!-- Filtre Date -->
            <div class="filter-group">
                <label for="filter-date" class="filter-label">
                    <i class="fas fa-calendar"></i> Date
                </label>
                <select id="filter-date" class="filter-select">
                    <option value="month">Ce mois</option>
                    <option value="week">Cette semaine</option>
                    <option value="today">Aujourd'hui</option>
                    <option value="custom">Personnalisé</option>
                </select>
            </div>

            <!-- Bouton Filtrer -->
            <div class="filter-group filter-action">
                <button class="btn-filter">
                    <i class="fas fa-search"></i> Filtrer
                </button>
            </div>
        </div>
    </div>

    <!-- Section Signalements en attente -->
    <section class="reports-section">
        <h2 class="section-title">
              <i class="fas fa-triangle-exclamation" style="color: #f59e0b;"></i> Signalements en attente
        </h2>

        <!-- Carte Signalement #1 -->
        <div class="report-card">
            <div class="report-header">
                <span class="report-id">
                    <i class="fas fa-triangle-exclamation" style="color: #f59e0b;"></i> SIGNALEMENT #1
                </span>
                <span class="report-badge status-pending">
                    <i class="fas fa-hourglass-half"></i> 12/05/2026
                </span>
            </div>
            <div class="row">
                <div class="col-md-4"> 
                    <!-- Publication signalée -->
                    <div class="detail-row">
                        <span class="detail-label">
                            <i class="fas fa-file-alt"></i> Publication signalée :
                        </span>
                         <span class="detail-label">
                            <i class="fas fa-user"></i> Auteur :
                        </span>
                          <span class="detail-label">
                            <i class="fas fa-exclamation-triangle"></i> Raison :
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="detail-row-values">
                        <span class="detail-value">"Publicité non autorisée"</span>
                        <span class="detail-value">User123</span>
                        <span class="detail-value">Publicité / Spam</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Commentaire signalement -->
                    <div class="report-comment">
                        <span class="comment-label">
                            <i class="fas fa-comment"></i> Commentaire signalement :
                        </span>
                        <p class="comment-text">"Ce post fait la promotion d'un site"</p>
                    </div>
                        <!-- Métadonnées -->
                    <div class="report-meta">
                        <span class="meta-reporter">
                            <i class="fas fa-user-circle"></i> Signalé par : Marie Diop
                             <span class="meta-count">
                                <i class="fas fa-bell"></i> 3 signalements déjà
                            </span>
                        </span>
                    </div>
                </div>
            </div>
             <!-- Actions -->
            <div class="report-actions">
                <button class="btn-action btn-view">
                    <i class="fas fa-eye"></i> Voir publication
                </button>
                <button class="btn-action btn-approve">
                    <i class="fas fa-check-circle"></i> Approuver
                </button>
                <button class="btn-action btn-reject">
                    <i class="fas fa-times-circle"></i> Rejeter
                </button>
                <button class="btn-action btn-modify">
                    <i class="fas fa-edit"></i> Modifier
                </button>
            </div>
        </div>


        <!-- Carte Signalement #2 -->
        <div class="report-card">
            <div class="report-header">
                <span class="report-id">
                    <i class="fas fa-triangle-exclamation" style="color: #f59e0b;"></i> SIGNALEMENT #2
                </span>
                <span class="report-badge status-pending">
                    <i class="fas fa-hourglass-half"></i> 11/05/2026
                </span>
            </div>
            <div class="row">
                <div class="col-md-4"> 
                    <!-- Publication signalée -->
                    <div class="detail-row">
                        <span class="detail-label">
                            <i class="fas fa-file-alt"></i> Publication signalée :
                        </span>
                         <span class="detail-label">
                            <i class="fas fa-user"></i> Auteur :
                        </span>
                          <span class="detail-label">
                            <i class="fas fa-exclamation-triangle"></i> Raison :
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="detail-row-values">
                        <span class="detail-value">"Conte offensant"</span>
                        <span class="detail-value">User456</span>
                        <span class="detail-value">Langage offensant/Harcelement</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Commentaire signalement -->
                    <div class="report-comment">
                        <span class="comment-label">
                            <i class="fas fa-comment"></i> Commentaire signalement :
                        </span>
                        <p class="comment-text">"Insulte envers d'autres éleveurs"</p>
                    </div>
                        <!-- Métadonnées -->
                    <div class="report-meta">
                        <span class="meta-reporter">
                            <i class="fas fa-user-circle"></i> Signalé par : Jean Dupont
                             <span class="meta-count">
                                <i class="fas fa-bell"></i> 1 signalement
                            </span>
                        </span>
                    </div>
                </div>
            </div>
             <!-- Actions -->
            <div class="report-actions">
                <button class="btn-action btn-view">
                    <i class="fas fa-eye"></i> Voir publication
                </button>
                <button class="btn-action btn-approve">
                    <i class="fas fa-check-circle"></i> Approuver
                </button>
                <button class="btn-action btn-reject">
                    <i class="fas fa-times-circle"></i> Rejeter
                </button>
                <button class="btn-action btn-modify">
                    <i class="fas fa-edit"></i> Modifier
                </button>
            </div>
        </div>


        <!-- Carte Signalement #3 -->
        <div class="report-card">
            <div class="report-header">
                <span class="report-id">
                    <i class="fas fa-triangle-exclamation" style="color: #f59e0b;"></i> SIGNALEMENT #3
                </span>
                <span class="report-badge status-pending">
                    <i class="fas fa-hourglass-half"></i> 10/05/2026
                </span>
            </div>
            <div class="row">
                <div class="col-md-4"> 
                    <!-- Publication signalée -->
                    <div class="detail-row">
                        <span class="detail-label">
                            <i class="fas fa-file-alt"></i> Publication signalée :
                        </span>
                         <span class="detail-label">
                            <i class="fas fa-user"></i> Auteur :
                        </span>
                          <span class="detail-label">
                            <i class="fas fa-exclamation-triangle"></i> Raison :
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="detail-row-values">
                        <span class="detail-value">"Fausse information sanitaire"</span>
                        <span class="detail-value">Amadou Sy</span>
                        <span class="detail-value">Information medicale dangereuse</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Commentaire signalement -->
                    <div class="report-comment">
                        <span class="comment-label">
                            <i class="fas fa-comment"></i> Commentaire signalement :
                        </span>
                        <p class="comment-text">"Conseil vétérinaire non vérifié"</p>
                    </div>
                        <!-- Métadonnées -->
                    <div class="report-meta">
                        <span class="meta-reporter">
                            <i class="fas fa-user-circle"></i> Signalé par : Marie Diop
                             <span class="meta-count">
                                <i class="fas fa-bell"></i> 3 signalements déjà
                            </span>
                        </span>
                    </div>
                </div>
            </div>
             <!-- Actions -->
            <div class="report-actions">
                <button class="btn-action btn-view">
                    <i class="fas fa-eye"></i> Voir publication
                </button>
                <button class="btn-action btn-approve">
                    <i class="fas fa-check-circle"></i> Approuver
                </button>
                <button class="btn-action btn-reject">
                    <i class="fas fa-times-circle"></i> Rejeter
                </button>
                <button class="btn-action btn-modify">
                    <i class="fas fa-edit"></i> Modifier
                </button>
            </div>
        </div>

    <!-- Section Historique des signalements traités -->
    <section class="history-section">
        <h2 class="section-title">
            <i class="fas fa-history"></i> Signalements traités (Historique)
        </h2>
        
        <div class="history-list">
            <div class="history-item">
                <span class="history-id">
                    <i class="fas fa-flag"></i> Signalement #0
                </span>
                <span class="history-date">
                    <i ></i> - 05/05/2026
                </span>
                <span class="history-action">
                    <i></i> - Publication supprimée
                </span>
            </div>
            <div class="history-item">
                <span class="history-id">
                    <i class="fas fa-flag"></i> Signalement #0
                </span>
                <span class="history-date">
                    <i ></i> - 03/05/2026
                </span>
                <span class="history-action">
                    <i ></i> - Utilisateur averti
                </span>
            </div>
            <div class="history-item">
                <span class="history-id">
                    <i class="fas fa-flag"></i> Signalement #0
                </span>
                <span class="history-date">
                    <i ></i> - 01/05/2026
                </span>
                <span class="history-action">
                    <i ></i> - Publication modifiée
                </span>
            </div>
        </div>

        <div class="history-footer">
            <a href="#" class="link-view-all">
                <i class="fas fa-arrow-right"></i> Voir tout l'historique
            </a>
        </div>
    </section>

</div>
@endsection