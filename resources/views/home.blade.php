@extends('layouts.app')

@section('title', 'Accueil - Élevage+')

@section('content')

<!-- style_css -->
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/home.css') }}">

<main>

  <!-- HERO -->
  <section class="hero-jumbotron">
    <img src="{{ asset('images/bg.png') }}" alt="Élevage+" class="hero-image">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1>
        Gérez votre élevage
        <span class="text-green">facilement</span>
      </h1>
      <p class="hero-subtitle">
        <i class="fas fa-users"></i>
        Rejoignez la communauté des éleveurs
      </p>
      <p class="hero-description">
        La plateforme tout-en-un pour gérer vos animaux,
        vos tâches, vos stocks et échanger avec d'autres
        éleveurs.
      </p>
      <a href="{{ url('auth/register') }}" class="btn btn-success">
        Commencer maintenant
      </a>
    </div>
  </section>

  <!-- CONTENU -->
  <div class="container content-wrapper">

    <!-- Colonne gauche : Publications -->
    <section class="posts-section">
      <div class="section-header">
        <h2><i class="fas fa-newspaper"></i> DERNIÈRES PUBLICATIONS</h2>
      </div>

      <!-- TABS -->
      <div class="bottom-tabs">
        <h3><i class="fas fa-filter"></i> Filtrer les publications</h3>
        <div class="tabs">
          <button class="tab active" data-tab="all"><i class="fas fa-list"></i> Tous</button>
          <button class="tab" data-tab="conseil"><i class="fas fa-lightbulb"></i> Conseils</button>
          <button class="tab" data-tab="experience"><i class="fas fa-user-edit"></i> Expériences</button>
          <button class="tab" data-tab="alerte"><i class="fas fa-bell"></i> Alertes</button>
          <button class="tab" data-tab="popular"><i class="fas fa-fire"></i> Tendances</button>
        </div>
      </div>

      <!-- Container des publications -->
      <div class="posts-scroll-container" id="postsContainer">
        <div id="postsList"></div>
      </div>

      <!-- Pagination -->
      <div class="pagination" id="pagination">
        <button id="prevPage" disabled><i class="fas fa-chevron-left"></i> précédente</button>
        <div id="pageNumbers" class="page-numbers"></div>
        <button id="nextPage">suivante <i class="fas fa-chevron-right"></i></button>
      </div>
    </section>

    <!-- Sidebar droite -->
    <aside class="sidebar">

      <!-- Statistiques -->
      <div class="bottom-stats">
        <h3><i class="fas fa-chart-pie"></i> STATISTIQUES DE LA COMMUNAUTÉ</h3>
        <div class="stats-grid" id="statsGrid">
          <div class="stat-box stat-green">
            <i class="fas fa-user-friends stat-icon"></i>
            <div class="stat-num" id="statUsers">-</div>
            <div>éleveurs</div>
          </div>
          <div class="stat-box stat-blue">
            <i class="fas fa-file-alt stat-icon"></i>
            <div class="stat-num" id="statPosts">-</div>
            <div>articles</div>
          </div>
          <div class="stat-box stat-pink">
            <i class="fas fa-heart stat-icon"></i>
            <div class="stat-num" id="statLikes">-</div>
            <div>likes</div>
          </div>
          <div class="stat-box stat-mint">
            <i class="fas fa-comments stat-icon"></i>
            <div class="stat-num" id="statComments">-</div>
            <div>coms</div>
          </div>
        </div>
      </div>

      <!-- Pourquoi rejoindre -->
      <h3><i class="fas fa-question-circle"></i> POURQUOI REJOINDRE ÉLEVAGE+?</h3>
      <div class="why-list">
        <div class="why-item">
          <div class="why-icon-box icon-green"><i class="fas fa-chart-bar"></i></div>
          <div>
            <strong>Suivi professionnel</strong>
            <p>Gérez vos animaux, vos tâches, vos stocks et toutes vos activités d'élevage facilement.</p>
          </div>
        </div>
        <div class="why-item">
          <div class="why-icon-box icon-blue"><i class="fas fa-users"></i></div>
          <div>
            <strong>Communauté d'entraide</strong>
            <p>Échangez avec d'autres éleveurs, partagez vos expériences et apprenez ensemble.</p>
          </div>
        </div>
        <div class="why-item">
          <div class="why-icon-box icon-orange"><i class="fas fa-bell"></i></div>
          <div>
            <strong>Alertes intelligentes</strong>
            <p>Recevez des rappels et des alertes pour ne rien oublier et prendre les bonnes décisions.</p>
          </div>
        </div>
      </div>

      <!-- CTA -->
      <div class="cta-box">
        <h4><i class="fas fa-rocket"></i> Prête à améliorer votre élevage?</h4>
        <p>Rejoignez les centaines d'éleveurs qui nous font déjà confiance!</p>
        <a href="{{ url('auth/register') }}" class="btn btn-success w-100">
          <i class="fas fa-user-plus"></i> Créez votre compte gratuitement
        </a>
      </div>

      <!-- Images sidebar -->
      <div class="sidebar-imgs">
        <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=400" alt="Vaches">
        <img src="https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?q=80&w=400" alt="Moutons">
      </div>

    </aside>

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
    ITEMS_PER_PAGE: 6
};

// ============================================================
// ÉTAT DE L'APPLICATION
// ============================================================

const state = {
    posts: [],
    currentPage: 1,
    totalPages: 0,
    currentCategory: 'all',
    currentSort: 'recent',
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
// CHARGEMENT DES PUBLICATIONS
// ============================================================

async function loadPosts(page, category, sort) {
    page = page || 1;
    category = category || 'all';
    sort = sort || 'recent';
    
    if (state.isLoading) return;
    state.isLoading = true;
    state.currentPage = page;
    state.currentCategory = category;
    state.currentSort = sort;
    
    var container = document.getElementById('postsList');
    container.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2 text-muted">Chargement des publications...</p>
        </div>
    `;
    
    try {
        var result = await apiCall('/home/posts?page=' + page + '&per_page=' + CONFIG.ITEMS_PER_PAGE + '&category=' + category + '&sort=' + sort);
        
        if (result.success === true && result.data) {
            state.posts = result.data.data || [];
            var meta = result.data.meta || {};
            state.totalPages = meta.last_page || 1;
            
            renderPosts();
            updatePagination();
            log('✅ ' + state.posts.length + ' publications chargées');
        } else {
            showToast(result.message || 'Erreur lors du chargement', 'danger');
            state.posts = [];
            renderPosts();
            updatePagination();
        }
    } catch (error) {
        logError('Erreur chargement publications', error);
        showToast('Erreur lors du chargement des publications', 'danger');
        state.posts = [];
        renderPosts();
        updatePagination();
    } finally {
        state.isLoading = false;
    }
}

// ============================================================
// CHARGEMENT DES STATISTIQUES - CORRIGÉ
// ============================================================

async function loadStats() {
    try {
        var result = await apiCall('/home/stats');
        
        console.log('📊 Réponse stats brute:', result);
        
        if (result.success === true && result.data) {
            var stats = result.data;
            
            // ✅ Vérifier que les données existent et les afficher
            var users = stats.total_users || stats.users || 0;
            var posts = stats.total_posts || stats.posts || 0;
            var likes = stats.total_likes || stats.likes || 0;
            var comments = stats.total_comments || stats.comments || 0;
            
            console.log('📊 Données extraites:', { users, posts, likes, comments });
            
            // ✅ Mettre à jour les éléments HTML
            var usersEl = document.getElementById('statUsers');
            var postsEl = document.getElementById('statPosts');
            var likesEl = document.getElementById('statLikes');
            var commentsEl = document.getElementById('statComments');
            
            if (usersEl) usersEl.textContent = users;
            if (postsEl) postsEl.textContent = posts;
            if (likesEl) likesEl.textContent = likes > 1000 ? (likes / 1000).toFixed(1) + 'k' : likes;
            if (commentsEl) commentsEl.textContent = comments > 1000 ? (comments / 1000).toFixed(1) + 'k' : comments;
            
            log('✅ Statistiques chargées', stats);
        } else {
            // Fallback avec les données du serveur
            var serverStats = {!! json_encode($stats ?? []) !!};
            if (serverStats) {
                document.getElementById('statUsers').textContent = serverStats.total_users || 0;
                document.getElementById('statPosts').textContent = serverStats.total_posts || 0;
                var likes = serverStats.total_likes || 0;
                document.getElementById('statLikes').textContent = likes > 1000 ? (likes / 1000).toFixed(1) + 'k' : likes;
                var comments = serverStats.total_comments || 0;
                document.getElementById('statComments').textContent = comments > 1000 ? (comments / 1000).toFixed(1) + 'k' : comments;
            }
        }
    } catch (error) {
        logError('Erreur chargement statistiques', error);
        // Fallback avec les données du serveur
        var serverStats = {!! json_encode($stats ?? []) !!};
        if (serverStats) {
            document.getElementById('statUsers').textContent = serverStats.total_users || 0;
            document.getElementById('statPosts').textContent = serverStats.total_posts || 0;
            var likes = serverStats.total_likes || 0;
            document.getElementById('statLikes').textContent = likes > 1000 ? (likes / 1000).toFixed(1) + 'k' : likes;
            var comments = serverStats.total_comments || 0;
            document.getElementById('statComments').textContent = comments > 1000 ? (comments / 1000).toFixed(1) + 'k' : comments;
        }
    }
}

// ============================================================
// AFFICHAGE DES PUBLICATIONS
// ============================================================

function renderPosts() {
    var container = document.getElementById('postsList');
    
    if (!state.posts || state.posts.length === 0) {
        container.innerHTML = `
            <div class="empty-posts">
                <i class="fas fa-newspaper"></i>
                <h4>Aucune publication</h4>
                <p>Aucune publication disponible dans cette catégorie pour le moment.</p>
            </div>
        `;
        return;
    }
    
    var html = '';
    for (var i = 0; i < state.posts.length; i++) {
        var post = state.posts[i];
        
        var authorName = post.auteur?.name || post.user?.name || 'Utilisateur';
        var authorAvatar = getAvatarUrl(post.auteur?.photo_url || post.user?.photo_url, authorName);
        var authorRole = post.auteur?.role === 'admin' ? 'Administrateur' : 'Éleveur';
        var time = formatDate(post.published_at || post.created_at);
        var categoryIcon = post.categorie === 'experience' ? '💡' : 
                          post.categorie === 'alerte' ? '⚠️' : '🌾';
        var categoryLabel = post.categorie_label || post.categorie || 'Conseil';
        var likes = post.statistiques?.likes || post.nbr_likes || 0;
        var comments = post.statistiques?.commentaires || post.nbr_commentaires || 0;
        var views = post.statistiques?.vues || post.nbr_vues || 0;
        var isLiked = post.interactions?.liked_by_user || false;
        var imageUrl = post.images && post.images.length > 0 ? post.images[0] : (post.image_url || 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=400');
        var content = post.resume || truncateText(post.contenu || '', 150);
        var title = post.titre || 'Sans titre';
        var profileUrl = '/profilEleveur/' + post.user_id;
        
        html += `
        <article class="post-card" data-post-id="` + post.id + `">
            <div class="post-top">
                <img src="` + authorAvatar + `" class="avatar" alt="` + escapeHtml(authorName) + `" onerror="this.src='https://ui-avatars.com/api/?name=User&background=4F46E5&color=fff'">
                <div class="post-info">
                    <a href="` + profileUrl + `" style="text-decoration: none; color: inherit;">
                      <h4>` + escapeHtml(authorName) + ` - ` + authorRole + ` <i class="fas fa-circle-check text-info"></i> • ` + time + `</h4>
                    </a>
                    <div class="post-meta">
                        <span><i class="fas fa-tag"></i> ` + categoryIcon + ` ` + categoryLabel + `</span>
                        <span><i class="far fa-thumbs-up"></i> ` + likes + ` likes</span>
                        <span><i class="far fa-comment-dots"></i> ` + comments + ` commentaires</span>
                        <span><i class="far fa-eye"></i> ` + views + ` vues</span>
                    </div>
                </div>
            </div>
            <div class="post-content">
                <img src="` + fixImageUrl(imageUrl) + `" alt="` + escapeHtml(title) + `" onerror="this.src='https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=400'">
                <div class="post-text">
                    <h3>` + escapeHtml(title) + `</h3>
                    <p>` + escapeHtml(content) + `</p>
                    <div class="post-actions">
                        <button class="like-btn ` + (isLiked ? 'liked' : '') + `" onclick="handleLike(` + post.id + `)">
                            <i class="` + (isLiked ? 'fas' : 'far') + ` fa-thumbs-up"></i> 
                            <span class="like-count">` + likes + `</span>
                        </button>
                        <button onclick="handleComment(` + post.id + `, '` + escapeHtml(title.replace(/'/g, "\\'")) + `')">
                            <i class="far fa-comment-dots"></i> Commenter
                        </button>
                        <button onclick="handleShare(` + post.id + `, '` + escapeHtml(title.replace(/'/g, "\\'")) + `')">
                            <i class="fas fa-share-alt"></i> Partager
                        </button>
                        <a href="#" class="read-more" onclick="openReadMore(` + post.id + `); return false;">
                            Lire la suite <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </article>`;
    }
    
    container.innerHTML = html;
}

// ============================================================
// PAGINATION
// ============================================================

function updatePagination() {
    var pageNumbersDiv = document.getElementById('pageNumbers');
    var prevBtn = document.getElementById('prevPage');
    var nextBtn = document.getElementById('nextPage');
    
    if (state.totalPages <= 1) {
        pageNumbersDiv.innerHTML = '';
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        return;
    }
    
    var html = '';
    var maxVisible = 5;
    var start = Math.max(1, state.currentPage - Math.floor(maxVisible / 2));
    var end = Math.min(state.totalPages, start + maxVisible - 1);
    
    if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1);
    }
    
    if (start > 1) {
        html += '<button class="page-number" data-page="1">1</button>';
        if (start > 2) html += '<span class="page-dots">...</span>';
    }
    
    for (var i = start; i <= end; i++) {
        html += '<button class="page-number ' + (i === state.currentPage ? 'active' : '') + '" data-page="' + i + '">' + i + '</button>';
    }
    
    if (end < state.totalPages) {
        if (end < state.totalPages - 1) html += '<span class="page-dots">...</span>';
        html += '<button class="page-number" data-page="' + state.totalPages + '">' + state.totalPages + '</button>';
    }
    
    pageNumbersDiv.innerHTML = html;
    prevBtn.disabled = state.currentPage === 1;
    nextBtn.disabled = state.currentPage === state.totalPages;
    
    document.querySelectorAll('.page-number').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var page = parseInt(this.dataset.page);
            if (page !== state.currentPage) {
                loadPosts(page, state.currentCategory, state.currentSort);
                document.getElementById('postsContainer').scrollTop = 0;
            }
        });
    });
}

function truncateText(text, maxLength) {
    if (!text) return '';
    return text.length <= maxLength ? text : text.substring(0, maxLength) + '...';
}

// ============================================================
// INTERACTIONS (CORRIGÉES POUR OBLIGATION DE CONNEXION)
// ============================================================

function redirectToLogin(actionMessage) {
    showToast(actionMessage + ' Redirection en cours...', 'warning');
    setTimeout(function() {
        window.location.href = "{{ url('auth/register') }}"; // Ou '/login' selon votre système
    }, 2000);
}

async function handleLike(postId) {
    // Vérification obligatoire de la connexion
    if (!CONFIG.TOKEN) {
        redirectToLogin('Connectez-vous pour aimer une publication.');
        return;
    }
    
    try {
        var result = await apiCall('/publications/' + postId + '/like', {
            method: 'POST'
        });
        
        if (result.status === 'success' || result.success === true) {
            var postCard = document.querySelector('.post-card[data-post-id="' + postId + '"]');
            if (postCard) {
                var likeBtn = postCard.querySelector('.like-btn');
                var likeCount = postCard.querySelector('.like-count');
                
                if (likeBtn) {
                    var isLiked = result.data?.liked || false;
                    likeBtn.innerHTML = '<i class="' + (isLiked ? 'fas' : 'far') + ' fa-thumbs-up"></i> <span class="like-count">' + (result.data?.total_likes || 0) + '</span>';
                    likeBtn.classList.toggle('liked', isLiked);
                }
                if (likeCount) {
                    likeCount.textContent = result.data?.total_likes || 0;
                }
            }
            showToast(result.message || 'Opération réussie', 'success');
        } else {
            showToast(result.message || 'Erreur', 'danger');
        }
    } catch (error) {
        logError('Erreur like', error);
        showToast('Erreur lors du like', 'danger');
    }
}

function handleComment(postId, postTitle) {
    // Vérification obligatoire de la connexion
    if (!CONFIG.TOKEN) {
        redirectToLogin('Connectez-vous pour laisser un commentaire.');
        return;
    }
    
    var comment = prompt('Laissez un commentaire sur "' + postTitle + '" :');
    if (comment && comment.trim()) {
        apiCall('/publications/' + postId + '/comments', {
            method: 'POST',
            body: { contenu: comment.trim() }
        }).then(function(result) {
            if (result.status === 'success' || result.success === true) {
                showToast('Commentaire ajouté !', 'success');
                loadPosts(state.currentPage, state.currentCategory, state.currentSort);
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

function handleShare(postId, postTitle) {
    // Vérification obligatoire de la connexion (Ajoutée ici !)
    if (!CONFIG.TOKEN) {
        redirectToLogin('Connectez-vous pour partager cette publication.');
        return;
    }

    var url = window.location.origin + '/post/' + postId;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function() {
            showToast('Lien copié dans le presse-papier !', 'success');
        }).catch(function() {
            showToast('Partagez "' + postTitle + '" avec vos amis !', 'success');
        });
    } else {
        var copyInput = document.createElement('input');
        copyInput.value = url;
        document.body.appendChild(copyInput);
        copyInput.select();
        document.execCommand('copy');
        copyInput.remove();
        showToast('Lien copié !', 'success');
    }
}

function openReadMore(postId) {
    var post = null;
    for (var i = 0; i < state.posts.length; i++) {
        if (state.posts[i].id === postId) {
            post = state.posts[i];
            break;
        }
    }
    if (!post) {
        showToast('Publication non trouvée', 'danger');
        return;
    }
    
    var authorName = post.auteur?.name || post.user?.name || 'Utilisateur';
    var authorAvatar = getAvatarUrl(post.auteur?.photo_url || post.user?.photo_url, authorName);
    var authorRole = post.auteur?.role === 'admin' ? 'Administrateur' : 'Éleveur';
    var time = formatDate(post.published_at || post.created_at);
    var fullContent = post.contenu || 'Contenu non disponible';
    var imageUrl = post.images && post.images.length > 0 ? post.images[0] : (post.image_url || 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=400');
    
    showModal(`
        <div class="modal-meta">
            <img src="` + authorAvatar + `" class="modal-avatar" onerror="this.src='https://ui-avatars.com/api/?name=User&background=4F46E5&color=fff'">
            <div>
                <strong>` + escapeHtml(authorName) + `</strong>
                <span>` + authorRole + `</span>
                <span>• ` + time + `</span>
            </div>
        </div>
        <div class="modal-image">
            <img src="` + fixImageUrl(imageUrl) + `" alt="` + escapeHtml(post.titre) + `" onerror="this.style.display='none'">
        </div>
        <div class="modal-content-text">
            <h3>` + escapeHtml(post.titre) + `</h3>
            <p>` + escapeHtml(fullContent.replace(/\n/g, '<br>')) + `</p>
        </div>
        <div class="modal-stats">
            <span><i class="fas fa-heart"></i> ` + (post.statistiques?.likes || post.nbr_likes || 0) + ` likes</span>
            <span><i class="fas fa-comment"></i> ` + (post.statistiques?.commentaires || post.nbr_commentaires || 0) + ` commentaires</span>
            <span><i class="fas fa-eye"></i> ` + (post.statistiques?.vues || post.nbr_vues || 0) + ` vues</span>
        </div>
    `, 'Publication complète');
}

// ============================================================
// MODALE
// ============================================================

function showModal(content, title) {
    title = title || 'Détails';
    var existingModal = document.querySelector('.custom-modal-home');
    if (existingModal) existingModal.remove();
    
    var modal = document.createElement('div');
    modal.className = 'custom-modal-home';
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
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    }, { once: true });
}

// ============================================================
// FILTRES
// ============================================================

function setupTabs() {
    document.querySelectorAll('.tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.tab').forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            
            var category = this.dataset.tab;
            var sort = 'recent';
            if (category === 'popular') {
                sort = 'popular';
                category = 'all';
            }
            
            loadPosts(1, category, sort);
        });
    });
}

// ============================================================
// INITIALISATION
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    log('🚀 Initialisation de la page d\'accueil');
    
    // Charger les statistiques
    loadStats();
    
    // Charger les publications
    loadPosts(1, 'all', 'recent');
    
    // Configurer les filtres
    setupTabs();
    
    // Pagination
    document.getElementById('prevPage').addEventListener('click', function() {
        if (state.currentPage > 1) {
            loadPosts(state.currentPage - 1, state.currentCategory, state.currentSort);
            document.getElementById('postsContainer').scrollTop = 0;
        }
    });
    
    document.getElementById('nextPage').addEventListener('click', function() {
        if (state.currentPage < state.totalPages) {
            loadPosts(state.currentPage + 1, state.currentCategory, state.currentSort);
            document.getElementById('postsContainer').scrollTop = 0;
        }
    });
    
    log('✅ Page d\'accueil initialisée avec succès');
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
    
    .custom-modal-home {
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
    .custom-modal-home.show {
        visibility: visible;
        opacity: 1;
    }
    .custom-modal-home .modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
    }
    .custom-modal-home .modal-container {
        position: relative;
        background: white;
        border-radius: 12px;
        max-width: 700px;
        width: 90%;
        max-height: 80vh;
        overflow: auto;
        z-index: 10002;
        transform: scale(0.9);
        transition: transform 0.3s ease;
        padding: 20px;
    }
    .custom-modal-home.show .modal-container {
        transform: scale(1);
    }
    .custom-modal-home .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    .custom-modal-home .modal-header h3 {
        margin: 0;
        font-size: 18px;
        color: #1a202c;
    }
    .custom-modal-home .modal-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #6c757d;
        padding: 4px 8px;
    }
    .custom-modal-home .modal-close:hover {
        color: #dc3545;
    }
    .custom-modal-home .modal-body {
        max-height: 60vh;
        overflow-y: auto;
    }
    .custom-modal-home .modal-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        font-size: 13px;
        color: #6c757d;
    }
    .custom-modal-home .modal-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .custom-modal-home .modal-image img {
        width: 100%;
        max-height: 300px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .custom-modal-home .modal-content-text h3 {
        font-size: 18px;
        margin-bottom: 10px;
        color: #1a202c;
    }
    .custom-modal-home .modal-content-text p {
        font-size: 15px;
        line-height: 1.6;
        color: #343a40;
    }
    .custom-modal-home .modal-stats {
        display: flex;
        gap: 20px;
        padding-top: 15px;
        border-top: 1px solid #dee2e6;
        margin-top: 15px;
        color: #6c757d;
        font-size: 13px;
    }
    
    #postsList {
        transition: opacity 0.3s ease;
    }
    .empty-posts {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        border: 1px solid #dee2e6;
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
    
    .like-btn.liked {
        color: #e83e8c;
    }
    .like-btn.liked i {
        font-weight: 900;
    }
    
    .pagination .page-numbers {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
    }
    .page-number {
        padding: 7px 12px;
        border: 1px solid #dee2e6;
        background: white;
        cursor: pointer;
        border-radius: 4px;
        font-size: 12px;
        transition: all 0.2s;
    }
    .page-number:hover {
        background: #e9ecef;
    }
    .page-number.active {
        background: #28a745;
        color: white;
        border-color: #28a745;
    }
    .page-dots {
        padding: 0 5px;
        color: #6c757d;
    }
    #prevPage:disabled, #nextPage:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    #pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 20px;
    }
    
    .posts-scroll-container {
        max-height: 700px;
        overflow-y: auto;
        padding-right: 10px;
    }
    .posts-scroll-container::-webkit-scrollbar {
        width: 8px;
    }
    .posts-scroll-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .posts-scroll-container::-webkit-scrollbar-thumb {
        background: #28a745;
        border-radius: 10px;
    }
    .posts-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #146c43;
    }
    
    .post-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        overflow: hidden;
        background: white;
    }
    .post-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .post-top {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px 20px;
    }
    .post-top .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .post-top .post-info h4 {
        margin: 0 0 4px 0;
        font-size: 14px;
        font-weight: 600;
        color: #1a202c;
    }
    .post-top .post-info .post-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 12px;
        color: #6c757d;
    }
    .post-top .post-info .post-meta i {
        margin-right: 3px;
    }
    
    .post-content {
        display: flex;
        flex-direction: column;
    }
    .post-content img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .post-content .post-text {
        padding: 15px 20px;
    }
    .post-content .post-text h3 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #1a202c;
    }
    .post-content .post-text p {
        font-size: 14px;
        line-height: 1.6;
        color: #4a5568;
        margin-bottom: 12px;
    }
    .post-content .post-text .post-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }
    .post-content .post-text .post-actions button,
    .post-content .post-text .post-actions .read-more {
        background: none;
        border: none;
        font-size: 13px;
        color: #6c757d;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
    }
    .post-content .post-text .post-actions button:hover,
    .post-content .post-text .post-actions .read-more:hover {
        background: #f0f0f0;
        color: #28a745;
    }
    .post-content .post-text .post-actions .read-more {
        margin-left: auto;
        color: #28a745;
        font-weight: 600;
    }
    .post-content .post-text .post-actions .read-more:hover {
        color: #146c43;
    }
    
    .posts-section {
        flex: 2;
    }
    
    @media (max-width: 768px) {
        .post-content {
            flex-direction: column;
        }
        .post-content img {
            height: 150px;
        }
        .post-top .post-info .post-meta {
            gap: 8px;
        }
        .post-content .post-text .post-actions {
            gap: 8px;
        }
        .post-content .post-text .post-actions .read-more {
            margin-left: 0;
        }
        .posts-scroll-container {
            max-height: 500px;
        }
        .custom-modal-home .modal-container {
            width: 95%;
            padding: 15px;
        }
        .custom-modal-home .modal-image img {
            max-height: 200px;
        }
    }
`;
document.head.appendChild(style);
</script>

@endsection