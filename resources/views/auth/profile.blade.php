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
                            <div class="avatar-placeholder" id="avatarPlaceholder">
                                <i class="fas fa-user-alt"></i>
                            </div>
                            <img id="profileImage" style="display: none; width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #2e7d32; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        </div>
                        <h2 class="profile-name" id="profileDisplayName">ILIA BARRY</h2>
                        
                        <div class="photo-actions">
                            <button class="btn-photo btn-change" id="changePhotoBtn">
                                <i class="fas fa-camera"></i> Changer ma photo
                            </button>
                            <button class="btn-photo btn-delete" id="deletePhotoBtn">
                                <i class="fas fa-trash-alt"></i> Supprimer la photo
                            </button>
                        </div>
                        <div class="format-info">
                            <i class="fas fa-info-circle"></i> Formats acceptés : JPG, PNG, WEBP - Max 5 Mo
                        </div>
                        <div id="uploadProgress" style="display: none; margin-top: 10px;">
                            <div style="width: 100%; background: #e9ecef; border-radius: 10px; overflow: hidden; height: 8px;">
                                <div id="progressBar" style="width: 0%; height: 100%; background: #2e7d32; transition: width 0.3s;"></div>
                            </div>
                            <span style="font-size: 0.8rem; color: #6c757d;" id="progressText">0%</span>
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
                        <input type="text" id="mobileFullname" value="Jean Dupont" readonly>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> E-MAIL *</label>
                        <input type="email" id="mobileEmail" value="jeandupont@gmail.com" readonly>
                        <div class="email-note">
                            <i class="fas fa-shield-alt"></i> Changer d'email nécessite une vérification
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> LOCALISATION *</label>
                        <input type="text" id="mobileLocation" value="Thies, Sénégal" readonly>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-paw"></i> TYPE(s) ÉLEVAGE *</label>
                        <input type="text" id="mobileType" value="Bovins" readonly>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> BIOGRAPHIE (optionnel)</label>
                        <textarea id="mobileBio" readonly>Éleveur passionné depuis 10 ans, je partage mon expérience et mes connaissances avec la communauté. Spécialisé dans l'élevage bovin traditionnel et moderne.</textarea>
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
                            <input type="text" name="fullname" id="fullname" value="Jean Dupont">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> E-MAIL *</label>
                            <input type="email" name="email" id="email" value="jeandupont@gmail.com">
                            <div class="email-note">
                                <i class="fas fa-shield-alt"></i> Changer d'email nécessite une vérification
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> LOCALISATION *</label>
                            <input type="text" name="location" id="location" value="Thies, Sénégal">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-paw"></i> TYPE(s) ÉLEVAGE *</label>
                            <select name="livestock_type" id="livestockType">
                                <option value="bovins" selected>Bovins</option>
                                <option value="ovins">Ovins</option>
                                <option value="caprins">Caprins</option>
                                <option value="volailles">Volailles</option>
                                <option value="porcins">Porcins</option>
                                <option value="équins">Équins</option>
                                <option value="mixtes">Mixtes</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-align-left"></i> BIOGRAPHIE (optionnel)</label>
                            <textarea name="bio" id="bio" rows="4">Éleveur passionné depuis 10 ans, je partage mon expérience et mes connaissances avec la communauté. Spécialisé dans l'élevage bovin traditionnel et moderne.</textarea>
                        </div>
                        
                        <div class="save-section">
                            <button type="button" class="btn-save" id="saveProfileBtn">
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
                        <div class="stat-value" id="statPublications">48</div>
                        <div class="stat-label">Publications reçues</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-value" id="statLikes">2 345</div>
                        <div class="stat-label">Likes reçus</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-comment"></i>
                        </div>
                        <div class="stat-value" id="statComments">127</div>
                        <div class="stat-label">Commentaires reçus</div>
                    </div>
                </div>
                
                <div class="member-info">
                    <div class="member-since">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Membre depuis : 15 mars 2025</span>
                    </div>
                    <a href="#" class="profile-link" id="publicProfileLink">
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
    
</div>

<script>
// ================= DONNÉES DU PROFIL =================
const profileData = {
    fullname: "Jean Dupont",
    email: "jeandupont@gmail.com",
    location: "Thies, Sénégal",
    livestockType: "bovins",
    bio: "Éleveur passionné depuis 10 ans, je partage mon expérience et mes connaissances avec la communauté. Spécialisé dans l'élevage bovin traditionnel et moderne.",
    displayName: "ILIA BARRY",
    memberSince: "15 mars 2025",
    profileLink: "elevageplus.com/profile/jean-d",
    stats: {
        publications: 48,
        likes: 2345,
        comments: 127
    }
};

let hasChanges = false;
let toastTimeout = null;
let profileImageFile = null;

// ================= FONCTIONS TOAST =================
function showToast(message, type = 'info') {
    const existingToast = document.querySelector('.custom-toast');
    if (existingToast) existingToast.remove();
    if (toastTimeout) clearTimeout(toastTimeout);
    
    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`;
    
    let icon = 'fa-info-circle';
    if (type === 'success') icon = 'fa-check-circle';
    else if (type === 'danger') icon = 'fa-exclamation-circle';
    else if (type === 'warning') icon = 'fa-exclamation-triangle';
    
    toast.innerHTML = `<div class="toast-content"><i class="fas ${icon}"></i><span>${message}</span></div>`;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 10);
    
    toastTimeout = setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ================= GESTION DES CHAMPS =================
function updateFields() {
    // Desktop
    document.getElementById('fullname').value = profileData.fullname;
    document.getElementById('email').value = profileData.email;
    document.getElementById('location').value = profileData.location;
    document.getElementById('livestockType').value = profileData.livestockType;
    document.getElementById('bio').value = profileData.bio;
    document.getElementById('profileDisplayName').textContent = profileData.displayName;
    
    // Mobile
    document.getElementById('mobileFullname').value = profileData.fullname;
    document.getElementById('mobileEmail').value = profileData.email;
    document.getElementById('mobileLocation').value = profileData.location;
    document.getElementById('mobileType').value = 
        profileData.livestockType.charAt(0).toUpperCase() + profileData.livestockType.slice(1);
    document.getElementById('mobileBio').value = profileData.bio;
    
    // Stats
    document.getElementById('statPublications').textContent = profileData.stats.publications;
    document.getElementById('statLikes').textContent = profileData.stats.likes.toLocaleString();
    document.getElementById('statComments').textContent = profileData.stats.comments;
    
    // Lien public
    document.querySelector('.profile-link span').textContent = `Lien vers mon profil public : ${profileData.profileLink}`;
    document.querySelector('.member-since span').textContent = `Membre depuis : ${profileData.memberSince}`;
}

// ================= GESTION DE LA PHOTO =================
document.getElementById('changePhotoBtn').addEventListener('click', function() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/webp';
    
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        if (file.size > 5 * 1024 * 1024) {
            showToast('Le fichier est trop volumineux. Maximum 5 Mo.', 'danger');
            return;
        }
        
        profileImageFile = file;
        
        // Afficher l'aperçu
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = document.getElementById('profileImage');
            const placeholder = document.getElementById('avatarPlaceholder');
            
            img.src = event.target.result;
            img.style.display = 'block';
            placeholder.style.display = 'none';
            
            showToast('Photo de profil mise à jour !', 'success');
            
            // Simuler un upload
            simulateUpload();
        };
        reader.readAsDataURL(file);
    };
    
    input.click();
});

function simulateUpload() {
    const progressContainer = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    
    progressContainer.style.display = 'block';
    let progress = 0;
    
    const interval = setInterval(() => {
        progress += Math.random() * 15 + 5;
        if (progress > 100) progress = 100;
        
        progressBar.style.width = progress + '%';
        progressText.textContent = Math.round(progress) + '%';
        
        if (progress >= 100) {
            clearInterval(interval);
            setTimeout(() => {
                progressContainer.style.display = 'none';
                progressBar.style.width = '0%';
                progressText.textContent = '0%';
                showToast('Photo de profil téléchargée avec succès !', 'success');
            }, 500);
        }
    }, 200);
}

document.getElementById('deletePhotoBtn').addEventListener('click', function() {
    if (confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')) {
        const img = document.getElementById('profileImage');
        const placeholder = document.getElementById('avatarPlaceholder');
        
        img.style.display = 'none';
        img.src = '';
        placeholder.style.display = 'flex';
        profileImageFile = null;
        
        showToast('Photo de profil supprimée', 'info');
    }
});

// ================= ENREGISTREMENT DU PROFIL =================
function saveProfile() {
    // Récupérer les valeurs
    const fullname = document.getElementById('fullname').value.trim();
    const email = document.getElementById('email').value.trim();
    const location = document.getElementById('location').value.trim();
    const livestockType = document.getElementById('livestockType').value;
    const bio = document.getElementById('bio').value.trim();
    
    // Validation
    if (!fullname) {
        showToast('Veuillez saisir votre nom complet', 'warning');
        return;
    }
    
    if (!email) {
        showToast('Veuillez saisir votre email', 'warning');
        return;
    }
    
    if (!email.includes('@')) {
        showToast('Veuillez saisir un email valide', 'warning');
        return;
    }
    
    if (!location) {
        showToast('Veuillez saisir votre localisation', 'warning');
        return;
    }
    
    // Mettre à jour les données
    profileData.fullname = fullname;
    profileData.email = email;
    profileData.location = location;
    profileData.livestockType = livestockType;
    profileData.bio = bio;
    profileData.displayName = fullname.toUpperCase();
    
    // Mettre à jour l'UI
    updateFields();
    hasChanges = false;
    
    // Désactiver les boutons
    document.getElementById('saveButton').disabled = true;
    document.querySelector('.btn-save').disabled = true;
    
    showToast('Profil mis à jour avec succès !', 'success');
}

// ================= ANNULATION DES MODIFICATIONS =================
function cancelChanges() {
    if (!hasChanges) {
        showToast('Aucune modification à annuler', 'info');
        return;
    }
    
    if (confirm('Voulez-vous vraiment annuler toutes les modifications ?')) {
        updateFields();
        hasChanges = false;
        document.getElementById('saveButton').disabled = true;
        document.querySelector('.btn-save').disabled = true;
        showToast('Modifications annulées', 'info');
    }
}

// ================= DÉTECTION DES CHANGEMENTS =================
function detectChanges() {
    const fullname = document.getElementById('fullname').value.trim();
    const email = document.getElementById('email').value.trim();
    const location = document.getElementById('location').value.trim();
    const livestockType = document.getElementById('livestockType').value;
    const bio = document.getElementById('bio').value.trim();
    
    const hasChanged = 
        fullname !== profileData.fullname ||
        email !== profileData.email ||
        location !== profileData.location ||
        livestockType !== profileData.livestockType ||
        bio !== profileData.bio;
    
    hasChanges = hasChanged;
    
    const saveBtn = document.getElementById('saveButton');
    const saveBtn2 = document.querySelector('.btn-save');
    if (saveBtn) saveBtn.disabled = !hasChanged;
    if (saveBtn2) saveBtn2.disabled = !hasChanged;
}

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
    updateFields();
    
    // Écouteurs d'événements pour la détection de changements
    document.getElementById('fullname').addEventListener('input', detectChanges);
    document.getElementById('email').addEventListener('input', detectChanges);
    document.getElementById('location').addEventListener('input', detectChanges);
    document.getElementById('livestockType').addEventListener('change', detectChanges);
    document.getElementById('bio').addEventListener('input', detectChanges);
    
    // Sauvegarde
    document.getElementById('saveProfileBtn').addEventListener('click', saveProfile);
    document.getElementById('saveButton').addEventListener('click', saveProfile);
    
    // Annulation
    document.getElementById('cancelButton').addEventListener('click', cancelChanges);
    
    // Voir profil public
    document.getElementById('viewPublicProfile').addEventListener('click', function(e) {
        e.preventDefault();
        showToast('Ouverture de votre profil public', 'info');
        window.open('#', '_blank');
    });
    
    // Paramètres du compte
    document.getElementById('accountSettings').addEventListener('click', function(e) {
        e.preventDefault();
        showToast('Paramètres du compte (fonctionnalité à venir)', 'info');
    });
    
    // Lien profil public
    document.getElementById('publicProfileLink').addEventListener('click', function(e) {
        e.preventDefault();
        showToast('Ouverture de votre profil public', 'info');
    });
    
    // Ajouter les styles manquants
    const style = document.createElement('style');
    style.textContent = `
        .custom-toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 10000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }
        .custom-toast.show { transform: translateX(0); }
        .custom-toast .toast-content {
            background: #343a40;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .custom-toast.success .toast-content { background: #2e7d32; }
        .custom-toast.danger .toast-content { background: #dc3545; }
        .custom-toast.warning .toast-content { background: #ffc107; color: #343a40; }
        .custom-toast.info .toast-content { background: #0dcaf0; color: #343a40; }
        
        @media (max-width: 768px) {
            .custom-toast {
                left: 15px;
                right: 15px;
                bottom: 15px;
                transform: translateY(100px);
            }
            .custom-toast.show { transform: translateY(0); }
        }
        
        .btn-save:disabled, .btn-cancel:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .profile-avatar {
            position: relative;
        }
        
        #uploadProgress {
            margin-top: 10px;
        }
        
        #progressBar {
            transition: width 0.3s ease;
        }
    `;
    document.head.appendChild(style);
});

// ================= TOAST SUPPLÉMENTAIRE POUR LE BOUTON ENREGISTRER =================
document.querySelector('.btn-save')?.addEventListener('click', function() {
    if (!this.disabled) {
        saveProfile();
    }
});
</script>

<style>
/* Styles supplémentaires pour le toast et les modals */
.custom-toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 10000;
    transform: translateX(400px);
    transition: transform 0.3s ease;
}

.custom-toast.show {
    transform: translateX(0);
}

.custom-toast .toast-content {
    background: #343a40;
    color: white;
    padding: 12px 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.custom-toast.success .toast-content {
    background: #2e7d32;
}

.custom-toast.danger .toast-content {
    background: #dc3545;
}

.custom-toast.warning .toast-content {
    background: #ffc107;
    color: #343a40;
}

.custom-toast.info .toast-content {
    background: #0dcaf0;
    color: #343a40;
}

/* Responsive pour mobile */
@media (max-width: 576px) {
    .custom-toast {
        left: 15px;
        right: 15px;
        bottom: 15px;
        transform: translateY(100px);
    }
    .custom-toast.show {
        transform: translateY(0);
    }
}

/* Animation de la barre de progression */
@keyframes progressPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

#uploadProgress {
    animation: progressPulse 1.5s ease-in-out infinite;
}

/* Style pour les champs modifiés */
.form-group input.modified,
.form-group select.modified,
.form-group textarea.modified {
    border-color: #2e7d32;
    background: #f0f7f0;
}

/* Animation des stats */
.stat-value {
    transition: all 0.3s ease;
}

.stat-item:hover .stat-value {
    transform: scale(1.05);
    color: #2e7d32;
}

/* Animation de la photo */
.profile-avatar img {
    transition: all 0.3s ease;
}

.profile-avatar img:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(46, 125, 50, 0.3);
}
</style>

@endsection

@push('scripts')
<script>
// Script supplémentaire pour les interactions avancées
$(document).ready(function() {
    // Gestionnaire pour les champs en lecture seule avec double-clic pour modifier
    $('.form-group input[readonly]').dblclick(function() {
        $(this).prop('readonly', false);
        $(this).css('background', 'white');
        $(this).focus();
        showToast('Vous pouvez maintenant modifier ce champ', 'info');
    });
    
    // Animation de la carte de statistiques au survol
    $('.stat-item').hover(
        function() {
            $(this).css('transform', 'translateY(-4px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );
});
</script>
@endpush