@extends('layouts.app')

@section('title', 'Profil - Élevage+')

@section('content')

<!-- style_css -->
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/profilEleveur.css') }}">

<main>
  <!-- COVER avec background image -->
  <section class="profile-cover">
    <div class="container">
      <div class="profile-card">
        <div class="profile-top">
          <div class="profile-left">
            <div class="profile-avatar">
              <img src="https://i.pravatar.cc/120?u=jean_dupont" alt="Jean Dupont" id="profileAvatar">
              <span class="badge-online"></span>
            </div>
            <div class="profile-info">
              <h1 id="profileName">Jean Dupont</h1>
              <p class="location"><i class="fas fa-map-marker-alt"></i> <span id="profileLocation">Thiès, Sénégal</span></p>
              <p class="type"><i class="fas fa-cow"></i> <span id="profileType">Élevage bovin - 45 animaux</span></p>
              <p class="member"><i class="far fa-calendar-alt"></i> Membre depuis <span id="profileMemberSince">mars 2025</span></p>
            </div>
          </div>
          <div class="profile-stats" id="profileStats">
            <div class="stat-item stat-pub">
              <strong id="statPublications">-</strong>
              <span><i class="fas fa-file-alt"></i> Publications</span>
            </div>
            <div class="stat-item stat-likes">
              <strong id="statLikes">-</strong>
              <span><i class="fas fa-heart"></i> Likes</span>
            </div>
            <div class="stat-item stat-comments">
              <strong id="statComments">-</strong>
              <span><i class="fas fa-comment"></i> Commentaires</span>
            </div>
            <div class="stat-item stat-followers">
              <strong id="statFollowers">-</strong>
              <span><i class="fas fa-users"></i> Abonnés</span>
            </div>
            <div class="stat-item stat-following">
              <strong id="statFollowing">-</strong>
              <span><i class="fas fa-user-plus"></i> Abonnements</span>
            </div>
          </div>
        </div>

        <div class="profile-bio">
          <p><i class="fas fa-circle text-green"></i> <strong>Bio :</strong> <span id="profileBio">-</span></p>
          <p><i class="fas fa-circle text-green"></i> <strong>Site web :</strong> <a href="#" id="profileWebsite" target="_blank">-</a></p>
          <p><i class="fas fa-circle text-green"></i> <strong>Email :</strong> <span id="profileEmail">-</span></p>
        </div>
      </div>
    </div>
  </section>

  <!-- PUBLICATIONS -->
  <div class="container">
    <section class="publications">
      <div class="pub-header">
        <h2><i class="fas fa-file-alt"></i> PUBLICATIONS DE <span id="userNameDisplay">-</span></h2>
        <select class="sort-select" id="sortSelect">
          <option value="recent">Trier par : plus récentes</option>
          <option value="oldest">Plus anciennes</option>
          <option value="mostLiked">Plus likées</option>
          <option value="mostViewed">Plus vues</option>
        </select>
      </div>

      <!-- Container des publications -->
      <div id="publicationsContainer">
        <div class="text-center py-5">
          <div class="spinner-border text-success" role="status">
            <span class="sr-only">Chargement...</span>
          </div>
          <p class="mt-2 text-muted">Chargement des publications...</p>
        </div>
      </div>

      <!-- Message vide -->
      <div id="emptyPublications" class="empty-posts" style="display: none;">
        <i class="fas fa-newspaper"></i>
        <h4>Aucune publication</h4>
        <p>Cet utilisateur n'a pas encore publié de contenu.</p>
      </div>

      <button class="btn-more" id="loadMoreBtn" style="display: none;"><i class="fas fa-plus"></i> Afficher plus de publications</button>
    </section>
  </div>
</main>

<script>
// ============================================================
// CONFIGURATION
// ============================================================

const CONFIG = {
    API_URL: window.location.origin + '/api',
    CSRF_TOKEN: document.querySelector('meta[name="csrf-token"]')?.content || '',
    // ✅ Page publique - Pas de token nécessaire
    TOKEN: null
};

// ============================================================
// ÉTAT DE L'APPLICATION
// ============================================================

const state = {
    profileId: null,
    profileData: null,
    publications: [],
    currentPage: 1,
    totalPages: 0,
    currentSort: 'recent',
    isLoading: false,
    toastTimeout: null
};

// ============================================================
// FONCTIONS UTILITAIRES
// ============================================================

function log(message, data) {
    data = data || null;
    const timestamp = new Date().toISOString();
    if (data) {
        console.log('[' + timestamp + '] 📝 ' + message, data);
    } else {
        console.log('[' + timestamp + '] 📝 ' + message);
    }
}

function logError(message, error) {
    error = error || null;
    const timestamp = new Date().toISOString();
    if (error) {
        console.error('[' + timestamp + '] ❌ ' + message, error);
    } else {
        console.error('[' + timestamp + '] ❌ ' + message);
    }
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    var date = new Date(dateString);
    var now = new Date();
    var diff = now - date;
    
    if (diff < 60000) return 'À l\'instant';
    if (diff < 3600000) return 'Il y a ' + Math.floor(diff / 60000) + ' min';
    if (diff < 86400000) return 'Il y a ' + Math.floor(diff / 3600000) + 'h';
    if (diff < 604800000) return 'Il y a ' + Math.floor(diff / 86400000) + 'j';
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function formatDateLong(dateString) {
    if (!dateString) return 'N/A';
    var date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
}

function fixImageUrl(url) {
    if (!url) return '';
    var clean = url.trim();
    if (clean.indexOf('http://') === 0 || clean.indexOf('https://') === 0) {
        return clean;
    }
    clean = clean.replace(/\/storage\/\/storage\//g, '/storage/');
    clean = clean.replace(/storage\/\/storage\//g, 'storage/');
    clean = clean.replace(/\/\/storage\//g, '/storage/');
    clean = clean.replace(/storage\/storage\//g, 'storage/');
    if (clean.indexOf('storage/') === 0) {
        clean = window.location.origin + '/' + clean;
    } else if (clean.indexOf('/storage/') === 0) {
        clean = window.location.origin + clean;
    } else if (clean && clean.indexOf('http') !== 0) {
        clean = window.location.origin + '/storage/' + clean;
    }
    return clean;
}

function getAvatarUrl(photoUrl, name) {
    if (photoUrl && photoUrl.trim()) {
        var fixed = fixImageUrl(photoUrl);
        if (fixed) return fixed;
    }
    var defaultName = name || 'User';
    return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(defaultName) + '&background=4F46E5&color=fff';
}

function truncateText(text, maxLength) {
    if (!text) return '';
    return text.length <= maxLength ? text : text.substring(0, maxLength) + '...';
}

function stripHtml(html) {
    var tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
}

// ============================================================
// TOAST
// ============================================================

function showToast(message, type) {
    type = type || 'info';
    var existing = document.querySelector('.custom-toast');
    if (existing) existing.remove();
    if (state.toastTimeout) clearTimeout(state.toastTimeout);
    
    var toast = document.createElement('div');
    toast.className = 'custom-toast ' + type;
    
    var icons = {
        success: 'fa-check-circle',
        danger: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas ` + (icons[type] || icons.info) + `"></i>
            <span>` + message + `</span>
        </div>
    `;
    document.body.appendChild(toast);
    
    requestAnimationFrame(function() { toast.classList.add('show'); });
    
    state.toastTimeout = setTimeout(function() {
        toast.classList.remove('show');
        setTimeout(function() { toast.remove(); }, 300);
    }, 3000);
}

// ============================================================
// API CALLS - PUBLIQUES (SANS TOKEN)
// ============================================================

async function apiCall(endpoint, options) {
    options = options || {};
    var defaultHeaders = {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN
    };

    // ✅ Page publique - Pas de token
    // if (CONFIG.TOKEN) {
    //     defaultHeaders['Authorization'] = 'Bearer ' + CONFIG.TOKEN;
    // }

    var config = {
        method: options.method || 'GET',
        headers: Object.assign({}, defaultHeaders, options.headers || {})
    };

    if (config.body && typeof config.body === 'object') {
        config.body = JSON.stringify(config.body);
    }

    var url = endpoint.indexOf('http') === 0 ? endpoint : CONFIG.API_URL + endpoint;
    log('🌐 Requête ' + (options.method || 'GET') + ' ' + url);

    try {
        var response = await fetch(url, config);
        var data = await response.json();

        if (!response.ok) {
            logError('Erreur HTTP ' + response.status, data);
            var error = new Error(data.message || 'Erreur API');
            error.status = response.status;
            error.errors = data.errors;
            throw error;
        }

        log('✅ Réponse reçue', { status: response.status });
        return data;
    } catch (error) {
        logError('Erreur API', error);
        throw error;
    }
}

// ============================================================
// CHARGEMENT DU PROFIL
// ============================================================

async function loadProfile(userId) {
    if (state.isLoading) return;
    state.isLoading = true;
    state.profileId = userId;

    try {
        var result = await apiCall('/profile/' + userId);
        
        if (result.success === true && result.data) {
            var data = result.data;
            state.profileData = data.user;
            
            // Mettre à jour l'UI du profil
            updateProfileUI(data.user, data.stats);
            
            // Charger les publications
            await loadPublications(1, state.currentSort);
            
            log('✅ Profil chargé', data.user);
        } else {
            showToast(result.message || 'Erreur lors du chargement du profil', 'danger');
        }
    } catch (error) {
        logError('Erreur chargement profil', error);
        showToast('Erreur lors du chargement du profil', 'danger');
    } finally {
        state.isLoading = false;
    }
}

// ============================================================
// CHARGEMENT DES PUBLICATIONS
// ============================================================

async function loadPublications(page, sort) {
    page = page || 1;
    sort = sort || 'recent';
    
    if (!state.profileId) return;
    
    var container = document.getElementById('publicationsContainer');
    container.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2 text-muted">Chargement des publications...</p>
        </div>
    `;

    try {
        var result = await apiCall('/profile/' + state.profileId + '/posts?page=' + page + '&per_page=5&sort=' + sort);
        
        if (result.success === true && result.data) {
            state.publications = result.data.data || [];
            var meta = result.data.meta || {};
            state.currentPage = meta.current_page || 1;
            state.totalPages = meta.last_page || 1;
            
            renderPublications();
            updateStats();
            log('✅ ' + state.publications.length + ' publications chargées');
        } else {
            showToast(result.message || 'Erreur lors du chargement des publications', 'danger');
            state.publications = [];
            renderPublications();
        }
    } catch (error) {
        logError('Erreur chargement publications', error);
        showToast('Erreur lors du chargement des publications', 'danger');
        state.publications = [];
        renderPublications();
    }
}

// ============================================================
// AFFICHAGE DU PROFIL
// ============================================================

function updateProfileUI(user, stats) {
    document.getElementById('profileName').textContent = user.name || 'Utilisateur';
    document.getElementById('profileLocation').textContent = user.location || 'Non renseignée';
    document.getElementById('profileType').textContent = user.type_elevage || 'Non spécifié';
    document.getElementById('profileMemberSince').textContent = formatDateLong(user.member_since);
    document.getElementById('profileBio').textContent = user.bio || 'Aucune biographie';
    document.getElementById('profileWebsite').textContent = user.website || '-';
    document.getElementById('profileWebsite').href = user.website || '#';
    document.getElementById('profileEmail').textContent = user.email || '-';
    document.getElementById('userNameDisplay').textContent = (user.name || 'Utilisateur').toUpperCase();
    
    var avatar = getAvatarUrl(user.photo_url, user.name);
    document.getElementById('profileAvatar').src = avatar;
    
    // Mettre à jour les stats
    document.getElementById('statPublications').textContent = stats.publications || 0;
    document.getElementById('statLikes').textContent = stats.likes || 0;
    document.getElementById('statComments').textContent = stats.comments || 0;
    document.getElementById('statFollowers').textContent = stats.followers || 0;
    document.getElementById('statFollowing').textContent = stats.following || 0;
    
    // Couverture
    var cover = document.querySelector('.profile-cover');
    if (cover && user.cover_url) {
        cover.style.backgroundImage = 'url(' + fixImageUrl(user.cover_url) + ')';
    } else {
        cover.style.backgroundImage = 'url("https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=1600")';
    }
}

// ============================================================
// AFFICHAGE DES PUBLICATIONS - VERSION PUBLIQUE (SANS INTERACTIONS)
// ============================================================

function renderPublications() {
    var container = document.getElementById('publicationsContainer');
    var emptyDiv = document.getElementById('emptyPublications');
    var loadMoreBtn = document.getElementById('loadMoreBtn');
    
    if (state.publications.length === 0) {
        container.innerHTML = '';
        emptyDiv.style.display = 'flex';
        loadMoreBtn.style.display = 'none';
        return;
    }
    
    emptyDiv.style.display = 'none';
    
    var html = '';
    for (var i = 0; i < state.publications.length; i++) {
        var post = state.publications[i];
        
        // ✅ Gestion du contenu avec "plus..." et "moins..."
        var fullContent = post.contenu || 'Contenu non disponible';
        var contentExcerpt = truncateText(stripHtml(fullContent), 200);
        var isTruncated = fullContent.length > 200;
        
        var contentHtml = '';
        if (isTruncated) {
            contentHtml = contentExcerpt + 
                ' <span class="read-more-btn" onclick="toggleFullContent(' + post.id + ')">plus...</span>';
        } else {
            contentHtml = fullContent.replace(/\n/g, '<br>');
        }
        
        var imageUrl = post.images && post.images.length > 0 ? post.images[0] : (post.image_url || 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=400');
        var isExpanded = state.expandedPosts && state.expandedPosts.has(post.id);
        
        if (isExpanded) {
            contentHtml = fullContent.replace(/\n/g, '<br>') + 
                ' <span class="read-more-btn" onclick="toggleFullContent(' + post.id + ')">moins...</span>';
        }
        
        html += `
        <article class="pub-card" data-post-id="` + post.id + `">
            <img src="` + fixImageUrl(imageUrl) + `" alt="` + escapeHtml(post.titre) + `" onerror="this.src='https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=400'">
            <div class="pub-content">
                <h3>` + escapeHtml(post.titre) + `</h3>
                <p class="post-text-content" data-full="` + (isExpanded ? 'true' : 'false') + `">
                    ` + contentHtml + `
                </p>
                <div class="pub-meta">
                    <!-- ✅ STATISTIQUES UNIQUEMENT (SANS INTERACTIONS) -->
                    <span><i class="fas fa-heart"></i> ` + post.likes + ` likes</span>
                    <span><i class="far fa-comment-dots"></i> ` + post.comments + ` commentaires</span>
                    <span><i class="fas fa-share-alt"></i> ` + (post.shares || 0) + ` partages</span>
                    <span><i class="far fa-eye"></i> ` + post.views + ` vues</span>
                    <span><i class="far fa-clock"></i> ` + post.published_at_human + `</span>
                </div>
            </div>
            <div class="pub-badge">` + (post.categorie_label || post.categorie) + `</div>
        </article>`;
    }
    
    container.innerHTML = html;
    
    // Afficher ou cacher le bouton "Afficher plus"
    if (state.currentPage >= state.totalPages) {
        loadMoreBtn.style.display = 'none';
    } else {
        loadMoreBtn.style.display = 'block';
    }
}

// ============================================================
// TOGGLE DU CONTENU COMPLET
// ============================================================

// ✅ Initialiser le Set pour suivre les publications développées
if (!state.expandedPosts) {
    state.expandedPosts = new Set();
}

function toggleFullContent(postId) {
    if (!state.expandedPosts) {
        state.expandedPosts = new Set();
    }
    
    if (state.expandedPosts.has(postId)) {
        state.expandedPosts.delete(postId);
    } else {
        state.expandedPosts.add(postId);
    }
    renderPublications();
}

// ============================================================
// STATISTIQUES
// ============================================================

function updateStats() {
    var totalLikes = 0;
    var totalComments = 0;
    var totalShares = 0;
    
    for (var i = 0; i < state.publications.length; i++) {
        totalLikes += state.publications[i].likes || 0;
        totalComments += state.publications[i].comments || 0;
        totalShares += state.publications[i].shares || 0;
    }
    
    var likesEl = document.getElementById('statLikes');
    if (likesEl) {
        likesEl.textContent = totalLikes > 1000 ? (totalLikes / 1000).toFixed(1) + 'k' : totalLikes;
    }
    
    var commentsEl = document.getElementById('statComments');
    if (commentsEl) {
        commentsEl.textContent = totalComments > 1000 ? (totalComments / 1000).toFixed(1) + 'k' : totalComments;
    }
}

// ============================================================
// TRI
// ============================================================

document.getElementById('sortSelect').addEventListener('change', function() {
    state.currentSort = this.value;
    state.currentPage = 1;
    loadPublications(1, state.currentSort);
});

// ============================================================
// "AFFICHER PLUS"
// ============================================================

document.getElementById('loadMoreBtn').addEventListener('click', function() {
    if (state.currentPage < state.totalPages) {
        state.currentPage++;
        loadPublications(state.currentPage, state.currentSort);
    }
});

// ============================================================
// INITIALISATION
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    log('🚀 Initialisation du profil (page publique)');
    
    // Récupérer l'ID depuis l'URL
    var pathParts = window.location.pathname.split('/');
    var userId = pathParts[pathParts.length - 1];
    
    if (userId && !isNaN(userId)) {
        state.profileId = parseInt(userId);
        loadProfile(state.profileId);
    } else {
        showToast('Utilisateur non trouvé', 'danger');
        window.location.href = '/';
        return;
    }
    
    log('✅ Profil initialisé avec succès');
});

// ============================================================
// STYLES DYNAMIQUES
// ============================================================

var style = document.createElement('style');
style.textContent = `
    .custom-toast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 10000;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        max-width: 90%;
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
        font-size: 14px;
    }
    .custom-toast.success .toast-content { background: #198754; }
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
    
    .empty-posts {
        text-align: center;
        padding: 60px 20px;
        background: #fafafa;
        border-radius: 12px;
        margin: 20px 0;
    }
    .empty-posts i {
        font-size: 48px;
        color: #6c757d;
        margin-bottom: 15px;
    }
    .empty-posts h4 {
        font-size: 18px;
        margin-bottom: 8px;
        color: #343a40;
    }
    .empty-posts p {
        font-size: 14px;
        color: #6c757d;
    }
    
    .pub-card {
        border: 1px solid #dee2e6;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
        background: white;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }
    .pub-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .pub-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .pub-card .pub-content {
        padding: 15px 20px;
    }
    .pub-card .pub-content h3 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #1a202c;
    }
    .pub-card .pub-content p {
        font-size: 14px;
        line-height: 1.6;
        color: #4a5568;
        margin-bottom: 12px;
    }
    
    /* ✅ Style pour le bouton "plus..." et "moins..." */
    .read-more-btn {
        color: #28a745;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s;
        display: inline-block;
    }
    .read-more-btn:hover {
        color: #146c43;
        text-decoration: underline;
    }
    
    .pub-card .pub-content .pub-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        font-size: 13px;
        color: #6c757d;
        padding-top: 10px;
        border-top: 1px solid #e9ecef;
        margin-top: 10px;
    }
    .pub-card .pub-content .pub-meta i {
        margin-right: 4px;
    }
    .pub-card .pub-content .pub-meta .fa-heart {
        color: #dc3545;
    }
    .pub-card .pub-content .pub-meta .fa-comment-dots {
        color: #0dcaf0;
    }
    .pub-card .pub-content .pub-meta .fa-share-alt {
        color: #6c757d;
    }
    .pub-card .pub-content .pub-meta .fa-eye {
        color: #6c757d;
    }
    .pub-card .pub-content .pub-meta .fa-clock {
        color: #6c757d;
    }
    
    .pub-card .pub-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .btn-more {
        display: block;
        width: 100%;
        padding: 12px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-weight: 600;
        color: #28a745;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 10px;
    }
    .btn-more:hover {
        background: #f0fdf4;
        border-color: #28a745;
    }
    
    .profile-stats {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    .profile-stats .stat-item {
        text-align: center;
        padding: 8px 12px;
        background: rgba(255,255,255,0.15);
        border-radius: 8px;
        min-width: 70px;
    }
    .profile-stats .stat-item strong {
        display: block;
        font-size: 20px;
        color: white;
    }
    .profile-stats .stat-item span {
        font-size: 11px;
        color: rgba(255,255,255,0.8);
    }
    
    @media (max-width: 768px) {
        .custom-toast {
            left: 15px;
            right: 15px;
            bottom: 15px;
            transform: translateY(100px);
        }
        .custom-toast.show { transform: translateY(0); }
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
        .pub-card img {
            height: 150px;
        }
        .pub-card .pub-content .pub-meta {
            gap: 10px;
            font-size: 12px;
        }
    }
`;
document.head.appendChild(style);
</script>

@endsection