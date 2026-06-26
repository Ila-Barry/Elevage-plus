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

        <div class="profile-actions" id="profileActions">
          <button class="btn btn-suivre" id="followBtn"><i class="fas fa-user-plus"></i> <span id="followBtnText">Suivre</span></button>
          <button class="btn btn-outline" id="messageBtn"><i class="far fa-comment-dots"></i> Message</button>
          <button class="btn btn-outline" id="shareProfileBtn"><i class="fas fa-share-alt"></i> Partager</button>
          <button class="btn btn-outline" id="reportBtn"><i class="fas fa-flag"></i> Signalement</button>
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
    TOKEN: (() => {
        const raw = localStorage.getItem('access_token');
        return raw ? raw.replace(/^"(.*)"$/, '$1').trim() : null;
    })(),
    USER: (() => {
        try {
            const user = localStorage.getItem('user');
            return user ? JSON.parse(user) : null;
        } catch { return null; }
    })(),
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
    displayedCount: 5,
    isFollowing: false,
    isLoading: false,
    toastTimeout: null,
    likedPosts: new Set()
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
// API CALLS
// ============================================================

async function apiCall(endpoint, options) {
    options = options || {};
    var defaultHeaders = {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN
    };

    if (CONFIG.TOKEN) {
        defaultHeaders['Authorization'] = 'Bearer ' + CONFIG.TOKEN;
    }

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
            state.isFollowing = data.user.is_following || false;
            
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
// SUIVRE/NE PLUS SUIVRE
// ============================================================

async function toggleFollow() {
    if (!CONFIG.TOKEN) {
        showToast('Connectez-vous pour suivre cet éleveur', 'warning');
        window.location.href = '/auth/login';
        return;
    }

    try {
        var result = await apiCall('/profile/' + state.profileId + '/follow', {
            method: 'POST'
        });
        
        if (result.success === true) {
            state.isFollowing = result.data.following || false;
            var followersCount = result.data.followers_count || 0;
            
            var followBtn = document.getElementById('followBtn');
            var followBtnText = document.getElementById('followBtnText');
            
            if (state.isFollowing) {
                followBtnText.textContent = 'Abonné';
                followBtn.innerHTML = '<i class="fas fa-user-check"></i> <span id="followBtnText">Abonné</span>';
                followBtn.style.background = '#6c757d';
            } else {
                followBtnText.textContent = 'Suivre';
                followBtn.innerHTML = '<i class="fas fa-user-plus"></i> <span id="followBtnText">Suivre</span>';
                followBtn.style.background = '#28a745';
            }
            
            document.getElementById('statFollowers').textContent = followersCount;
            showToast(result.message, 'success');
        } else {
            showToast(result.message || 'Erreur lors de l\'opération', 'danger');
        }
    } catch (error) {
        logError('Erreur follow', error);
        showToast('Erreur lors de l\'opération', 'danger');
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
    
    // Mettre à jour le bouton follow
    var followBtn = document.getElementById('followBtn');
    var followBtnText = document.getElementById('followBtnText');
    
    if (state.isFollowing) {
        followBtnText.textContent = 'Abonné';
        followBtn.innerHTML = '<i class="fas fa-user-check"></i> <span id="followBtnText">Abonné</span>';
        followBtn.style.background = '#6c757d';
    } else {
        followBtnText.textContent = 'Suivre';
        followBtn.innerHTML = '<i class="fas fa-user-plus"></i> <span id="followBtnText">Suivre</span>';
        followBtn.style.background = '#28a745';
    }
    
    // Couverture
    var cover = document.querySelector('.profile-cover');
    if (cover && user.cover_url) {
        cover.style.backgroundImage = 'url(' + fixImageUrl(user.cover_url) + ')';
    } else {
        cover.style.backgroundImage = 'url("https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=1600")';
    }
}

// ============================================================
// AFFICHAGE DES PUBLICATIONS
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
        var displayContent = post.resume || truncateText(post.contenu || '', 150);
        var imageUrl = post.images && post.images.length > 0 ? post.images[0] : (post.image_url || 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=400');
        var isLiked = state.likedPosts.has(post.id);
        
        html += `
        <article class="pub-card" data-post-id="` + post.id + `">
            <img src="` + fixImageUrl(imageUrl) + `" alt="` + escapeHtml(post.titre) + `" onclick="openPostModal(` + post.id + `)" style="cursor: pointer;" onerror="this.src='https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=400'">
            <div class="pub-content">
                <h3 onclick="openPostModal(` + post.id + `)" style="cursor: pointer;">` + escapeHtml(post.titre) + `</h3>
                <p>` + escapeHtml(displayContent) + `</p>
                <div class="pub-meta">
                    <span><i class="fas fa-heart ` + (isLiked ? 'text-red' : '') + `"></i> <span class="like-count-` + post.id + `">` + post.likes + `</span> likes</span>
                    <span><i class="far fa-comment-dots"></i> <span class="comment-count-` + post.id + `">` + post.comments + `</span> commentaires</span>
                    <span><i class="far fa-eye"></i> ` + post.views + ` vues</span>
                    <span><i class="far fa-clock"></i> ` + post.published_at_human + `</span>
                </div>
                <div class="post-actions-mini">
                    <button class="like-btn like-btn-` + post.id + ` ` + (isLiked ? 'liked' : '') + `" onclick="likePost(` + post.id + `)">
                        <i class="fas fa-heart"></i> ` + (isLiked ? 'Aimé' : 'Aimer') + `
                    </button>
                    <button onclick="openCommentModal(` + post.id + `, '` + escapeHtml(post.titre.replace(/'/g, "\\'")) + `')">
                        <i class="fas fa-comment"></i> Commenter
                    </button>
                    <button onclick="sharePost(` + post.id + `, '` + escapeHtml(post.titre.replace(/'/g, "\\'")) + `')">
                        <i class="fas fa-share-alt"></i> Partager
                    </button>
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
// STATISTIQUES
// ============================================================

function updateStats() {
    var totalLikes = 0;
    var totalComments = 0;
    for (var i = 0; i < state.publications.length; i++) {
        totalLikes += state.publications[i].likes;
        totalComments += state.publications[i].comments;
    }
    
    var likesEl = document.getElementById('statLikes');
    if (likesEl) {
        likesEl.textContent = totalLikes > 1000 ? (totalLikes / 1000).toFixed(1) + 'k' : totalLikes;
    }
    
    var commentsEl = document.getElementById('statComments');
    if (commentsEl) {
        commentsEl.textContent = totalComments;
    }
}

// ============================================================
// INTERACTIONS
// ============================================================

function likePost(postId) {
    if (!CONFIG.TOKEN) {
        showToast('Connectez-vous pour aimer une publication', 'warning');
        window.location.href = '/auth/login';
        return;
    }
    
    var post = null;
    for (var i = 0; i < state.publications.length; i++) {
        if (state.publications[i].id === postId) {
            post = state.publications[i];
            break;
        }
    }
    if (!post) return;
    
    var likeBtn = document.querySelector('.like-btn-' + postId);
    var likeCountSpan = document.querySelector('.like-count-' + postId);
    
    if (state.likedPosts.has(postId)) {
        state.likedPosts.delete(postId);
        post.likes--;
        if (likeBtn) likeBtn.classList.remove('liked');
        showToast('Vous n\'aimez plus cette publication', 'info');
    } else {
        state.likedPosts.add(postId);
        post.likes++;
        if (likeBtn) likeBtn.classList.add('liked');
        showToast('Publication aimée !', 'success');
    }
    
    if (likeCountSpan) likeCountSpan.textContent = post.likes;
    updateStats();
}

function sharePost(postId, postTitle) {
    var shareUrl = window.location.origin + '/post/' + postId;
    navigator.clipboard.writeText(shareUrl).then(function() {
        showToast('"' + postTitle.substring(0, 50) + '" - Lien copié !', 'success');
    }).catch(function() {
        showToast('Partagez "' + postTitle + '" avec vos amis !', 'success');
    });
}

function openCommentModal(postId, postTitle) {
    if (!CONFIG.TOKEN) {
        showToast('Connectez-vous pour commenter', 'warning');
        window.location.href = '/auth/login';
        return;
    }
    
    var comment = prompt('Laissez un commentaire sur "' + postTitle + '" :');
    if (comment && comment.trim()) {
        apiCall('/publications/' + postId + '/comments', {
            method: 'POST',
            body: { contenu: comment.trim() }
        }).then(function(result) {
            if (result.status === 'success' || result.success === true) {
                var post = null;
                for (var i = 0; i < state.publications.length; i++) {
                    if (state.publications[i].id === postId) {
                        post = state.publications[i];
                        break;
                    }
                }
                if (post) {
                    post.comments++;
                    var commentSpan = document.querySelector('.comment-count-' + postId);
                    if (commentSpan) commentSpan.textContent = post.comments;
                    updateStats();
                }
                showToast('Commentaire ajouté avec succès !', 'success');
            } else {
                showToast(result.message || 'Erreur lors de l\'ajout', 'danger');
            }
        }).catch(function(error) {
            logError('Erreur commentaire', error);
            showToast('Erreur lors de l\'ajout du commentaire', 'danger');
        });
    } else if (comment !== null) {
        showToast('Le commentaire ne peut pas être vide', 'warning');
    }
}

function openPostModal(postId) {
    var post = null;
    for (var i = 0; i < state.publications.length; i++) {
        if (state.publications[i].id === postId) {
            post = state.publications[i];
            break;
        }
    }
    if (!post) {
        showToast('Publication non trouvée', 'danger');
        return;
    }
    
    var fullContent = post.contenu || 'Contenu non disponible';
    var imageUrl = post.images && post.images.length > 0 ? post.images[0] : (post.image_url || 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=400');
    
    showModal(`
        <div class="modal-meta">
            <span><i class="fas fa-user"></i> ` + escapeHtml(state.profileData?.name || 'Utilisateur') + `</span>
            <span><i class="far fa-clock"></i> ` + post.published_at_human + `</span>
        </div>
        <div class="modal-image"><img src="` + fixImageUrl(imageUrl) + `" alt="` + escapeHtml(post.titre) + `" onerror="this.style.display='none'"></div>
        <div class="modal-content-text">
            <h3>` + escapeHtml(post.titre) + `</h3>
            <p>` + escapeHtml(fullContent.replace(/\n/g, '<br>')) + `</p>
        </div>
        <div class="modal-stats">
            <span><i class="fas fa-heart ` + (state.likedPosts.has(post.id) ? 'text-red' : '') + `"></i> ` + post.likes + ` likes</span>
            <span><i class="fas fa-comment"></i> ` + post.comments + ` commentaires</span>
            <span><i class="fas fa-eye"></i> ` + post.views + ` vues</span>
        </div>
        <div class="modal-actions">
            <button class="modal-like-btn" onclick="likePost(` + post.id + `); closeModalDialog();">
                <i class="fas fa-heart"></i> ` + (state.likedPosts.has(post.id) ? 'Je n\'aime plus' : 'J\'aime') + `
            </button>
            <button class="modal-comment-btn" onclick="openCommentModal(` + post.id + `, '` + escapeHtml(post.titre.replace(/'/g, "\\'")) + `'); closeModalDialog();">
                <i class="fas fa-comment"></i> Commenter
            </button>
            <button class="modal-share-btn" onclick="sharePost(` + post.id + `, '` + escapeHtml(post.titre.replace(/'/g, "\\'")) + `'); closeModalDialog();">
                <i class="fas fa-share-alt"></i> Partager
            </button>
        </div>
    `, post.titre);
}

// ============================================================
// MODALE
// ============================================================

function showModal(content, title) {
    title = title || 'Détails';
    var existingModal = document.querySelector('.custom-modal');
    if (existingModal) existingModal.remove();
    
    var modal = document.createElement('div');
    modal.className = 'custom-modal';
    modal.innerHTML = `
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <div class="modal-header">
                <h3>` + title + `</h3>
                <button class="modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">` + content + `</div>
        </div>
    `;
    
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    setTimeout(function() { modal.classList.add('show'); }, 10);
    
    var closeModal = function() {
        modal.classList.remove('show');
        setTimeout(function() {
            modal.remove();
            document.body.style.overflow = '';
        }, 300);
    };
    
    modal.querySelector('.modal-overlay').addEventListener('click', closeModal);
    modal.querySelector('.modal-close').addEventListener('click', closeModal);
    window.closeModalDialog = closeModal;
}

// ============================================================
// FONCTIONS D'ACTIONS PROFIL
// ============================================================

function initProfileActions() {
    document.getElementById('followBtn').addEventListener('click', toggleFollow);
    
    document.getElementById('messageBtn').addEventListener('click', function() {
        if (!CONFIG.TOKEN) {
            showToast('Connectez-vous pour envoyer un message', 'warning');
            window.location.href = '/auth/login';
            return;
        }
        window.location.href = '/messages?user=' + state.profileId;
    });
    
    document.getElementById('shareProfileBtn').addEventListener('click', function() {
        var url = window.location.href;
        navigator.clipboard.writeText(url).then(function() {
            showToast('Profil - Lien copié !', 'success');
        }).catch(function() {
            showToast('Partagez ce profil avec vos amis !', 'success');
        });
    });
    
    document.getElementById('reportBtn').addEventListener('click', function() {
        if (!CONFIG.TOKEN) {
            showToast('Connectez-vous pour signaler un profil', 'warning');
            window.location.href = '/auth/login';
            return;
        }
        var reason = prompt('Motif du signalement :', 'Contenu inapproprié');
        if (reason && reason.trim()) {
            showToast('Signalement envoyé. Merci pour votre vigilance.', 'success');
        }
    });
}

// ============================================================
// UTILITAIRES
// ============================================================

function truncateText(text, maxLength) {
    if (!text) return '';
    return text.length <= maxLength ? text : text.substring(0, maxLength) + '...';
}

// ============================================================
// INITIALISATION
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    log('🚀 Initialisation du profil');
    
    // Récupérer l'ID depuis l'URL
    var pathParts = window.location.pathname.split('/');
    var userId = pathParts[pathParts.length - 1];
    
    if (userId && !isNaN(userId)) {
        state.profileId = parseInt(userId);
        loadProfile(state.profileId);
        initProfileActions();
    } else {
        showToast('Utilisateur non trouvé', 'danger');
        window.location.href = '/';
        return;
    }
    
    // Événement de tri
    document.getElementById('sortSelect').addEventListener('change', function() {
        state.currentSort = this.value;
        state.currentPage = 1;
        loadPublications(1, state.currentSort);
    });
    
    // Événement "Afficher plus"
    document.getElementById('loadMoreBtn').addEventListener('click', function() {
        if (state.currentPage < state.totalPages) {
            state.currentPage++;
            loadPublications(state.currentPage, state.currentSort);
        }
    });
    
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
    
    .post-actions-mini {
        display: flex;
        gap: 12px;
        margin-top: 10px;
        flex-wrap: wrap;
    }
    .post-actions-mini button {
        background: none;
        border: none;
        font-size: 12px;
        color: #6c757d;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 4px;
        transition: all 0.2s;
    }
    .post-actions-mini button:hover {
        background: #e9ecef;
        color: #28a745;
    }
    .like-btn.liked {
        color: #dc3545 !important;
    }
    .like-btn.liked i {
        font-weight: 900;
    }
    .text-red {
        color: #dc3545 !important;
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
    .pub-card .pub-content .pub-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 13px;
        color: #6c757d;
    }
    .pub-card .pub-content .pub-meta i {
        margin-right: 3px;
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
    
    .custom-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10001;
        display: flex;
        align-items: center;
        justify-content: center;
        visibility: hidden;
        opacity: 0;
        transition: all 0.3s ease;
    }
    .custom-modal.show {
        visibility: visible;
        opacity: 1;
    }
    .custom-modal .modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
    }
    .custom-modal .modal-container {
        position: relative;
        background: white;
        border-radius: 12px;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow: auto;
        z-index: 10002;
        transform: scale(0.9);
        transition: transform 0.3s ease;
        padding: 20px;
    }
    .custom-modal.show .modal-container {
        transform: scale(1);
    }
    .custom-modal .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    .custom-modal .modal-header h3 {
        margin: 0;
        font-size: 18px;
        color: #1a202c;
    }
    .custom-modal .modal-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #6c757d;
        padding: 4px 8px;
    }
    .custom-modal .modal-close:hover {
        color: #dc3545;
    }
    .custom-modal .modal-body {
        max-height: 60vh;
        overflow-y: auto;
    }
    .custom-modal .modal-meta {
        display: flex;
        gap: 15px;
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
    .custom-modal .modal-image img {
        width: 100%;
        max-height: 300px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .custom-modal .modal-content-text h3 {
        font-size: 18px;
        margin-bottom: 10px;
        color: #1a202c;
    }
    .custom-modal .modal-content-text p {
        font-size: 15px;
        line-height: 1.6;
        color: #343a40;
    }
    .custom-modal .modal-stats {
        display: flex;
        gap: 20px;
        padding-top: 15px;
        border-top: 1px solid #dee2e6;
        margin-top: 15px;
        color: #6c757d;
        font-size: 13px;
    }
    .custom-modal .modal-actions {
        display: flex;
        gap: 10px;
        padding-top: 15px;
        border-top: 1px solid #dee2e6;
        margin-top: 10px;
    }
    .custom-modal .modal-actions button {
        flex: 1;
        padding: 10px;
        border: 1px solid #dee2e6;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
        font-size: 13px;
    }
    .custom-modal .modal-actions button:hover {
        background: #f8f9fa;
        border-color: #28a745;
        color: #28a745;
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
    
    @media (max-width: 768px) {
        .custom-toast {
            left: 15px;
            right: 15px;
            bottom: 15px;
            transform: translateY(100px);
        }
        .custom-toast.show { transform: translateY(0); }
        .post-actions-mini {
            gap: 8px;
        }
        .post-actions-mini button {
            font-size: 11px;
            padding: 4px 6px;
        }
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
        .pub-card img {
            height: 150px;
        }
        .custom-modal .modal-container {
            width: 95%;
            padding: 15px;
        }
        .custom-modal .modal-image img {
            max-height: 200px;
        }
        .custom-modal .modal-actions {
            flex-direction: column;
        }
    }
`;
document.head.appendChild(style);
</script>

@endsection