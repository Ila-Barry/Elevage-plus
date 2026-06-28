{{-- resources/views/auth/profile.blade.php --}}

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
                            <div class="avatar-placeholder" id="avatarPlaceholder" style="{{ $user->photo_url ? 'display:none;' : 'display:flex;' }}">
                                <i class="fas fa-user-alt"></i>
                            </div>
                            <img id="profileImage" 
                                 src="{{ $user->photo_url ?? '' }}" 
                                 style="{{ $user->photo_url ? 'display:block;' : 'display:none;' }} width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #2e7d32; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        </div>
                        <h2 class="profile-name" id="profileDisplayName">{{ strtoupper($user->name) }}</h2>
                        
                        <div class="photo-actions">
                            <button class="btn-photo btn-change" id="changePhotoBtn">
                                <i class="fas fa-camera"></i> Changer ma photo
                            </button>
                            <button class="btn-photo btn-delete" id="deletePhotoBtn" {{ $user->photo_url ? '' : 'style=display:none;' }}>
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
            
            <!-- Version mobile des infos personnelles -->
            <div class="profile-card d-md-none">
                <div class="card-header">
                    <h3><i class="fas fa-address-card"></i> INFORMATIONS PERSONNELLES</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> NOM COMPLET *</label>
                        <input type="text" id="mobileFullname" value="{{ $user->name }}" readonly>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> E-MAIL *</label>
                        <input type="email" id="mobileEmail" value="{{ $user->email }}" readonly>
                        <div class="email-note">
                            <i class="fas fa-shield-alt"></i> Changer d'email nécessite une vérification
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> LOCALISATION *</label>
                        <input type="text" id="mobileLocation" value="{{ $user->localisation ?? 'Non renseignée' }}" readonly>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-paw"></i> TYPE(s) ÉLEVAGE *</label>
                        <input type="text" id="mobileType" value="{{ $user->type_elevage ?? 'Non renseigné' }}" readonly>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> BIOGRAPHIE (optionnel)</label>
                        <textarea id="mobileBio" readonly>{{ $user->bio ?? 'Aucune biographie renseignée.' }}</textarea>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- COLONNE DROITE -->
        <div class="profile-right">
            
            <!-- Carte Informations personnelles -->
            <div class="profile-card d-none d-md-block">
                <div class="card-header">
                    <h3><i class="fas fa-address-card"></i> INFORMATIONS PERSONNELLES</h3>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> NOM COMPLET *</label>
                            <input type="text" name="name" id="fullname" value="{{ $user->name }}">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> E-MAIL *</label>
                            <input type="email" name="email" id="email" value="{{ $user->email }}">
                            <div class="email-note">
                                <i class="fas fa-shield-alt"></i> Changer d'email nécessite une vérification
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> TÉLÉPHONE *</label>
                            <input type="text" name="telephone" id="telephone" value="{{ $user->telephone ?? '' }}">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> LOCALISATION *</label>
                            <input type="text" name="localisation" id="location" value="{{ $user->localisation ?? '' }}">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-paw"></i> TYPE(s) ÉLEVAGE *</label>
                            <select name="type_elevage" id="livestockType">
                                <option value="bovins" {{ ($user->type_elevage ?? '') == 'bovins' ? 'selected' : '' }}>Bovins</option>
                                <option value="ovins" {{ ($user->type_elevage ?? '') == 'ovins' ? 'selected' : '' }}>Ovins</option>
                                <option value="caprins" {{ ($user->type_elevage ?? '') == 'caprins' ? 'selected' : '' }}>Caprins</option>
                                <option value="volailles" {{ ($user->type_elevage ?? '') == 'volailles' ? 'selected' : '' }}>Volailles</option>
                                <option value="porcins" {{ ($user->type_elevage ?? '') == 'porcins' ? 'selected' : '' }}>Porcins</option>
                                <option value="equins" {{ ($user->type_elevage ?? '') == 'equins' ? 'selected' : '' }}>Équins</option>
                                <option value="mixte" {{ ($user->type_elevage ?? '') == 'mixte' ? 'selected' : '' }}>Mixte</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-align-left"></i> BIOGRAPHIE (optionnel)</label>
                            <textarea name="bio" id="bio" rows="4">{{ $user->bio ?? '' }}</textarea>
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
    
    <!-- STATISTIQUES DU COMPTE -->
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
                        <div class="stat-value" id="statPublications">{{ $stats['publications'] ?? 0 }}</div>
                        <div class="stat-label">Publications</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-value" id="statLikes">{{ number_format($stats['likes_received'] ?? 0) }}</div>
                        <div class="stat-label">Likes reçus</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-comment"></i>
                        </div>
                        <div class="stat-value" id="statComments">{{ $stats['commentaires'] ?? 0 }}</div>
                        <div class="stat-label">Commentaires</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-horse"></i>
                        </div>
                        <div class="stat-value" id="statElevages">{{ $stats['elevages'] ?? 0 }}</div>
                        <div class="stat-label">Élevages</div>
                    </div>
                </div>
                
                <div class="member-info">
                    <div class="member-since">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Membre depuis : {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="profile-link">
                        <i class="fas fa-link"></i>
                        <span>Statut : {{ $user->role === 'admin' ? 'Administrateur' : 'Éleveur' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- BOUTONS -->
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
// ================= CONFIGURATION =================
const API_URL = '/api';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

// Données initiales du profil (depuis le backend)
const initialProfile = {
    name: "{{ $user->name }}",
    email: "{{ $user->email }}",
    telephone: "{{ $user->telephone ?? '' }}",
    localisation: "{{ $user->localisation ?? '' }}",
    type_elevage: "{{ $user->type_elevage ?? 'bovins' }}",
    bio: "{{ $user->bio ?? '' }}",
    displayName: "{{ strtoupper($user->name) }}",
    photo_url: "{{ $user->photo_url ?? '' }}",
    memberSince: "{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}",
    stats: {
        publications: {{ $stats['publications'] ?? 0 }},
        likes_received: {{ $stats['likes_received'] ?? 0 }},
        commentaires: {{ $stats['commentaires'] ?? 0 }},
        elevages: {{ $stats['elevages'] ?? 0 }}
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
    document.getElementById('fullname').value = initialProfile.name;
    document.getElementById('email').value = initialProfile.email;
    document.getElementById('telephone').value = initialProfile.telephone;
    document.getElementById('location').value = initialProfile.localisation;
    document.getElementById('livestockType').value = initialProfile.type_elevage;
    document.getElementById('bio').value = initialProfile.bio;
    document.getElementById('profileDisplayName').textContent = initialProfile.displayName;
    
    // Mobile
    document.getElementById('mobileFullname').value = initialProfile.name;
    document.getElementById('mobileEmail').value = initialProfile.email;
    document.getElementById('mobileLocation').value = initialProfile.localisation || 'Non renseignée';
    document.getElementById('mobileType').value = initialProfile.type_elevage || 'Non renseigné';
    document.getElementById('mobileBio').value = initialProfile.bio || 'Aucune biographie renseignée.';
    
    // Stats
    document.getElementById('statPublications').textContent = initialProfile.stats.publications;
    document.getElementById('statLikes').textContent = initialProfile.stats.likes_received.toLocaleString();
    document.getElementById('statComments').textContent = initialProfile.stats.commentaires;
    document.getElementById('statElevages').textContent = initialProfile.stats.elevages;
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
            
            showToast('Photo de profil sélectionnée ! Cliquez sur Enregistrer.', 'success');
        };
        reader.readAsDataURL(file);
    };
    
    input.click();
});

document.getElementById('deletePhotoBtn').addEventListener('click', function() {
    if (confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')) {
        const img = document.getElementById('profileImage');
        const placeholder = document.getElementById('avatarPlaceholder');
        
        img.style.display = 'none';
        img.src = '';
        placeholder.style.display = 'flex';
        profileImageFile = null;
        
        showToast('Photo de profil supprimée. Cliquez sur Enregistrer.', 'info');
    }
});

// ================= API CALLS =================
async function updateProfile(data) {
    const token = localStorage.getItem('access_token');
    
    try {
        const response = await fetch(`${API_URL}/auth/profile`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
            throw result;
        }

        return result;
    } catch (error) {
        throw error;
    }
}

async function updatePhoto(file) {
    const token = localStorage.getItem('access_token');
    const formData = new FormData();
    formData.append('photo', file);
    formData.append('_method', 'PUT');
    
    try {
        const response = await fetch(`${API_URL}/auth/profile`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: formData
        });

        const result = await response.json();

        if (!response.ok) {
            throw result;
        }

        return result;
    } catch (error) {
        throw error;
    }
}

// ================= ENREGISTREMENT DU PROFIL =================
async function saveProfile() {
    // Récupérer les valeurs
    const fullname = document.getElementById('fullname').value.trim();
    const email = document.getElementById('email').value.trim();
    const telephone = document.getElementById('telephone').value.trim();
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
    
    // Désactiver les boutons
    document.getElementById('saveButton').disabled = true;
    document.querySelector('.btn-save').disabled = true;
    document.getElementById('saveButton').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
    
    try {
        let result;
        
        // Si une photo a été sélectionnée, l'uploader d'abord
        if (profileImageFile) {
            result = await updatePhoto(profileImageFile);
            if (result.status === 'success' && result.data) {
                // Mettre à jour la photo dans l'UI
                const img = document.getElementById('profileImage');
                const placeholder = document.getElementById('avatarPlaceholder');
                if (result.data.photo_url) {
                    img.src = result.data.photo_url;
                    img.style.display = 'block';
                    placeholder.style.display = 'none';
                    document.getElementById('deletePhotoBtn').style.display = 'inline-block';
                }
                profileImageFile = null;
            }
        }
        
        // Mettre à jour les informations du profil
        const updateData = {
            name: fullname,
            email: email,
            telephone: telephone,
            localisation: location,
            type_elevage: livestockType,
            bio: bio
        };
        
        result = await updateProfile(updateData);
        
        if (result.status === 'success') {
            // Mettre à jour les données locales
            initialProfile.name = fullname;
            initialProfile.email = email;
            initialProfile.telephone = telephone;
            initialProfile.localisation = location;
            initialProfile.type_elevage = livestockType;
            initialProfile.bio = bio;
            initialProfile.displayName = fullname.toUpperCase();
            
            // Mettre à jour l'UI
            updateFields();
            hasChanges = false;
            
            // Mettre à jour le localStorage
            const userStr = localStorage.getItem('user');
            if (userStr) {
                const userData = JSON.parse(userStr);
                userData.name = fullname;
                userData.email = email;
                userData.telephone = telephone;
                if (result.data?.photo_url) {
                    userData.photo_url = result.data.photo_url;
                }
                localStorage.setItem('user', JSON.stringify(userData));
            }
            
            showToast('Profil mis à jour avec succès !', 'success');
        } else {
            showToast(result.message || 'Erreur lors de la mise à jour', 'danger');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast(error.message || 'Erreur lors de la mise à jour du profil', 'danger');
    } finally {
        document.getElementById('saveButton').disabled = false;
        document.querySelector('.btn-save').disabled = false;
        document.getElementById('saveButton').innerHTML = '<i class="fas fa-save"></i> Enregistrer';
    }
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
    const telephone = document.getElementById('telephone').value.trim();
    const location = document.getElementById('location').value.trim();
    const livestockType = document.getElementById('livestockType').value;
    const bio = document.getElementById('bio').value.trim();
    
    const hasChanged = 
        fullname !== initialProfile.name ||
        email !== initialProfile.email ||
        telephone !== initialProfile.telephone ||
        location !== initialProfile.localisation ||
        livestockType !== initialProfile.type_elevage ||
        bio !== initialProfile.bio ||
        profileImageFile !== null;
    
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
    document.getElementById('telephone').addEventListener('input', detectChanges);
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
    });
    
    // Paramètres du compte
    document.getElementById('accountSettings').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = '/auth/parametre';
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
    `;
    document.head.appendChild(style);
});

$(document).ready(function() {
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
@endsection