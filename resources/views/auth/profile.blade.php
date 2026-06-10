@extends('layouts.menu')

@section('title', 'Mon Profil - Élevage+')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/authCSS/profile.css') }}">
@endpush

@section('content')

<div class="dashboard-wrapper">
      <!-- EN-TÊTE DU PROFIL -->
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="profile-header-title">
                <i class="fas fa-user-circle"></i>
                <h1>Mon profil</h1>
            </div>
            <div class="profile-header-actions">
                <a href="#" class="header-action-link public-link" id="viewPublicProfile">
                   <i class="fas fa-eye"></i>
                    <span>Voir mon profil public</span>
                </a>
                <div class="header-separator"></div>
                 <a href="#" class="header-action-link settings-link" id="accountSettings">
                        <i class="fas fa-shield-alt"></i>

                    <span>Paramètres du compte</span>
                 </a>
            </div>
        </div>
    </div>
    
    <!-- Les deux colonnes avec hauteur égale -->
    <div class="profile-grid">
        
        <!-- COLONNE GAUCHE -->
        <div class="profile-left">
            
            <!-- Carte Photo de profil -->
            <div class="profile-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-circle"></i> PHOTO DE PROFIL</h3>
                </div>
                <div class="card-body">
                    <div class="profile-photo-section">
                        <div class="profile-avatar">
                            <div class="avatar-placeholder">
                                <i class="fas fa-user-alt"></i>
                            </div>
                        </div>
                        <h2 class="profile-name">ILIA BARRY</h2>
                        
                        <div class="photo-actions">
                            <button class="btn-photo btn-change">
                                <i class="fas fa-camera"></i> Changer ma photo
                            </button>
                            <button class="btn-photo btn-delete">
                                <i class="fas fa-trash-alt"></i> Supprimer la photo
                            </button>
                        </div>
                        <div class="format-info">
                            <i class="fas fa-info-circle"></i> Formats acceptés : JPG, PNG, WEBP - Max 5 Mo
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Version mobile des infos personnelles (cachée sur desktop) -->
            <div class="profile-card d-md-none">
                <div class="card-header">
                    <h3><i class="fas fa-address-card"></i> INFORMATIONS PERSONNELLES</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> NOM COMPLET *</label>
                        <input type="text" value="Jean Dupont" readonly>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> E-MAIL *</label>
                        <input type="email" value="jeandupont@gmail.com" readonly>
                        <div class="email-note">
                            <i class="fas fa-shield-alt"></i> Changer d'email nécessite une vérification
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> LOCALISATION *</label>
                        <input type="text" value="Thies, Sénégal" readonly>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-paw"></i> TYPE(s) ÉLEVAGE *</label>
                        <input type="text" value="Bovins" readonly>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> BIOGRAPHIE (optionnel)</label>
                        <textarea readonly>Éleveur passionné depuis 10 ans, je partage mon expérience et mes connaissances avec la communauté. Spécialisé dans l'élevage bovin traditionnel et moderne.</textarea>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- COLONNE DROITE -->
        <div class="profile-right">
            
            <!-- Carte Informations personnelles complète (visible sur desktop) -->
            <div class="profile-card d-none d-md-block">
                <div class="card-header">
                    <h3><i class="fas fa-address-card"></i> INFORMATIONS PERSONNELLES</h3>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> NOM COMPLET *</label>
                            <input type="text" name="fullname" value="Jean Dupont" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> E-MAIL *</label>
                            <input type="email" name="email" value="jeandupont@gmail.com" readonly>
                            <div class="email-note">
                                <i class="fas fa-shield-alt"></i> Changer d'email nécessite une vérification
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> LOCALISATION *</label>
                            <input type="text" name="location" value="Thies, Sénégal" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-paw"></i> TYPE(s) ÉLEVAGE *</label>
                            <select name="livestock_type" disabled>
                                <option value="bovins" selected>Bovins</option>
                                <option value="ovins">Ovins</option>
                                <option value="caprins">Caprins</option>
                                <option value="volailles">Volailles</option>
                                <option value="porcins">Porcins</option>
                                <option value="équins">Équins</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-align-left"></i> BIOGRAPHIE (optionnel)</label>
                            <textarea name="bio" readonly>Éleveur passionné depuis 10 ans, je partage mon expérience et mes connaissances avec la communauté. Spécialisé dans l'élevage bovin traditionnel et moderne.</textarea>
                        </div>
                        
                        <div class="save-section">
                            <button type="button" class="btn-save" disabled>
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            
        
            
        </div>
        
    </div>
    
    <!-- STATISTIQUES DU COMPTE - EN BAS DES DEUX COLONNES -->
    <div class="stats-wrapper">
        <div class="stats-card">
            <div class="card-header">
                <h3><i class="fas fa-chart-line"></i> STATISTIQUES DU COMPTE</h3>
            </div>
            <div class="card-body">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <div class="stat-value">48</div>
                        <div class="stat-label">Publications reçues</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-value">2 345</div>
                        <div class="stat-label">Likes reçus</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-comment"></i>
                        </div>
                        <div class="stat-value">127</div>
                        <div class="stat-label">Commentaires reçus</div>
                    </div>
                </div>
                
                <div class="member-info">
                    <div class="member-since">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Membre depuis : 15 mars 2025</span>
                    </div>
                    <a href="#" class="profile-link">
                        <i class="fas fa-link"></i>
                        <span>Lien vers mon profil public : elevageplus.com/profile/jean-d</span>
                    </a>
                </div>
            </div>
        </div>
                    <!-- BOUTONS ANNULER ET ENREGISTRER CENTRÉS -->
            <div class="action-buttons">
                <button class="btn-action btn-cancel" id="cancelButton">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button class="btn-action btn-save" id="saveButton">
                    <i class="fas fa-save"></i> Enregistrer
                </button>

    </div>
    
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Gestionnaire pour changer la photo
    $('.btn-change').on('click', function() {
        // Créer un input file invisible
        var fileInput = $('<input type="file" accept="image/jpeg,image/png,image/webp">');
        fileInput.on('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Le fichier est trop volumineux. Maximum 5 Mo.');
                    return;
                }
                alert('Fonctionnalité à venir : Upload de la photo de profil');
            }
        });
        fileInput.click();
    });
    
    // Gestionnaire pour supprimer la photo
    $('.btn-delete').on('click', function() {
        if (confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')) {
            // Réinitialiser l'avatar par défaut
            $('.avatar-placeholder').html('<i class="fas fa-user-alt"></i>');
            alert('Photo de profil supprimée');
        }
    });
    
    // Gestionnaire pour la demande d'autorisation
    $('.btn-request').on('click', function() {
        alert('Votre demande d\'autorisation a été envoyée à l\'administrateur.');
    });
    
    // Gestionnaire pour le téléchargement de l'application
    $('.btn-download').on('click', function() {
        alert('Redirection vers le téléchargement de l\'application mobile');
    });
    
    // Gestionnaire pour le lien public
    $('.profile-link').on('click', function(e) {
        e.preventDefault();
        alert('Ouverture du profil public');
    });
});
</script>
@endpush