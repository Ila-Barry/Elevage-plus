
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
        </div>
      </div>

      <!-- Container des publications -->
      <div class="posts-scroll-container" id="postsContainer">
        <div id="postsList"></div>
      </div>

      <!-- Pagination -->
        <div class="pagination" id="pagination">
            <button id="prevPage" disabled>
                <i class="fas fa-chevron-left"></i>
                <span>précédente</span>
            </button>
            <div id="pageNumbers" class="page-numbers"></div>
            <button id="nextPage">
                <span>suivante</span>
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

      <!-- ================= MODALE IMAGE ================= -->
      <div id="imageModal" class="modal-image">
        <div class="modal-image-content">
          <span class="modal-image-close" id="closeImageModal">&times;</span>
          <img id="modalImage" src="" alt="Image en grand format">
          <div class="image-nav">
            <button id="prevImageBtn"><i class="fas fa-chevron-left"></i></button>
            <span id="imageCounter">1 / 1</span>
            <button id="nextImageBtn"><i class="fas fa-chevron-right"></i></button>
          </div>
        </div>
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
    isLoading: false,
    toastTimeout: null,
    expandedPosts: new Set(),
    currentImages: [],
    currentImageIndex: 0 
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

function truncateText(text, maxLength) {
    if (!text) return '';
    return text.length <= maxLength ? text : text.substring(0, maxLength) + '...';
}

function stripHtml(html) {
    var tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
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

async function loadPosts(page, category) {
    page = page || 1;
    category = category || 'all';
    
    if (state.isLoading) return;
    state.isLoading = true;
    state.currentPage = page;
    state.currentCategory = category;
    
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
        var result = await apiCall('/home/posts?page=' + page + '&per_page=' + CONFIG.ITEMS_PER_PAGE + '&category=' + category);
        
        if (result.success === true && result.data) {
            state.posts = result.data.data || [];
            var meta = result.data.meta || {};
            state.totalPages = meta.last_page || 1;
            
            state.expandedPosts = new Set();
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
// CHARGEMENT DES STATISTIQUES 
// ============================================================

async function loadStats() {
    try {
        var result = await apiCall('/home/stats');
        
        console.log('📊 Réponse stats brute:', result);
        
        if (result && result.success === true && result.data) {
            var stats = result.data;
            
            var users = stats.total_users ?? 0;
            var posts = stats.total_posts ?? 0;
            var likes = stats.total_likes ?? 0;
            var comments = stats.total_comments ?? 0;
            
            console.log('📊 Données extraites:', { users, posts, likes, comments });
            
            var usersEl = document.getElementById('statUsers');
            var postsEl = document.getElementById('statPosts');
            var likesEl = document.getElementById('statLikes');
            var commentsEl = document.getElementById('statComments');
            
            if (usersEl) usersEl.textContent = users;
            if (postsEl) postsEl.textContent = posts;
            if (likesEl) likesEl.textContent = likes > 1000 ? (likes / 1000).toFixed(1) + 'k' : likes;
            if (commentsEl) commentsEl.textContent = comments > 1000 ? (comments / 1000).toFixed(1) + 'k' : comments;
            
            log('✅ Statistiques chargées avec succès depuis l\'API');
        } else {
            useServerFallbackStats();
        }
    } catch (error) {
        logError('Erreur lors du chargement des statistiques via API', error);
        useServerFallbackStats();
    }
}

function useServerFallbackStats() {
    log('⚠️ Mode secours : Chargement des statistiques serveur (Blade)');
    var serverStats = {!! json_encode($stats ?? []) !!};
    if (serverStats) {
        var users = serverStats.total_users ?? 0;
        var posts = serverStats.total_posts ?? 0;
        var likes = serverStats.total_likes ?? 0;
        var comments = serverStats.total_comments ?? 0;

        if (document.getElementById('statUsers')) document.getElementById('statUsers').textContent = users;
        if (document.getElementById('statPosts')) document.getElementById('statPosts').textContent = posts;
        if (document.getElementById('statLikes')) document.getElementById('statLikes').textContent = likes > 1000 ? (likes / 1000).toFixed(1) + 'k' : likes;
        if (document.getElementById('statComments')) document.getElementById('statComments').textContent = comments > 1000 ? (comments / 1000).toFixed(1) + 'k' : comments;
    }
}

// ============================================================
// AFFICHAGE DES PUBLICATIONS AVEC GRILLE D'IMAGES PROFESSIONNELLE
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
        var isExpanded = state.expandedPosts.has(post.id);
        
        // Données de l'auteur
        var authorName = post.auteur?.name || post.user?.name || 'Utilisateur';
        var authorAvatar = getAvatarUrl(post.auteur?.photo_url || post.user?.photo_url, authorName);
        var authorRole = post.auteur?.role === 'admin' ? 'Administrateur' : 'Éleveur';
        var time = formatDate(post.published_at || post.created_at);
        
        // Catégorie
        var categoryIcon = post.categorie === 'experience' ? '💡' : 
                          post.categorie === 'alerte' ? '⚠️' : '🌾';
        var categoryLabel = post.categorie_label || post.categorie || 'Conseil';
        
        // Statistiques
        var likes = post.statistiques?.likes || post.nbr_likes || 0;
        var comments = post.statistiques?.commentaires || post.nbr_commentaires || 0;
        var shares = post.statistiques?.partages || post.nbr_partages || 0;
        var views = post.statistiques?.vues || post.nbr_vues || 0;
        
        // Images
        var images = post.images || [];
        var firstImage = images.length > 0 ? images[0] : (post.image_url || '');
        
        // Titre et contenu
        var title = post.titre || 'Sans titre';
        var fullContent = post.contenu || 'Contenu non disponible';
        var profileUrl = '/profilEleveur/' + (post.user_id || post.user?.id);
        
        // Contenu avec "plus..." / "moins..."
        var maxLength = 200;
        var contentExcerpt = truncateText(stripHtml(fullContent), maxLength);
        var isTruncated = fullContent.length > maxLength;
        
        var contentHtml = '';
        if (isExpanded) {
            contentHtml = fullContent.replace(/\n/g, '<br>') + 
                '<span class="read-more-btn" onclick="toggleFullContent(' + post.id + ')"> moins...</span>';
        } else if (isTruncated) {
            contentHtml = contentExcerpt + 
                ' <span class="read-more-btn" onclick="toggleFullContent(' + post.id + ')">plus...</span>';
        } else {
            contentHtml = fullContent.replace(/\n/g, '<br>');
        }
        
        // ============================================================
        // GRILLE D'IMAGES PROFESSIONNELLE
        // ============================================================
        var imagesHtml = '';
        if (images.length > 0) {
            var displayCount = Math.min(images.length, 4);
            var remaining = images.length - 4;
            var gridClass = 'grid-' + displayCount;
            
            imagesHtml = '<div class="image-grid ' + gridClass + '">';
            
            for (var j = 0; j < displayCount; j++) {
                var isLast = (j === displayCount - 1 && remaining > 0);
                var overlay = isLast ? '<div class="overlay-plus">+' + remaining + '</div>' : '';
                
                imagesHtml += `
                    <div class="grid-img-item" onclick="openImageModal(${post.id}, ${j})">
                        <img src="${fixImageUrl(images[j])}" alt="Image ${j + 1}" loading="lazy" onerror="this.style.display='none'">
                        ${overlay}
                    </div>
                `;
            }
            
            imagesHtml += '</div>';
        }
        
        // Construction de la carte
        html += `
        <article class="post-card" data-post-id="${post.id}">
            <div class="post-top">
                <img src="${authorAvatar}" class="avatar" alt="${escapeHtml(authorName)}" onerror="this.src='https://ui-avatars.com/api/?name=User&background=4F46E5&color=fff'">
                <div class="post-info">
                    <a href="${profileUrl}" style="text-decoration: none; color: inherit;">
                      <h4>${escapeHtml(authorName)} - ${authorRole} <i class="fas fa-circle-check text-info"></i> • ${time}</h4>
                    </a>
                    <div class="post-meta">
                        <span><i class="fas fa-tag"></i> ${categoryIcon} ${categoryLabel}</span>
                    </div>
                </div>
            </div>
            <div class="post-content">
                <div class="post-text">
                    <h3>${escapeHtml(title)}</h3>
                    <p class="post-text-content" data-full="${isExpanded ? 'true' : 'false'}">
                        ${contentHtml}
                    </p>
                    ${imagesHtml}
                    <div class="post-actions">
                        <span class="stat-item">
                            <i class="fas fa-heart"></i> <span class="stat-count">${likes}</span>
                        </span>
                        <span class="stat-item">
                            <i class="fas fa-comment"></i> <span class="stat-count">${comments}</span>
                        </span>
                        <span class="stat-item">
                            <i class="fas fa-share-alt"></i> <span class="stat-count">${shares}</span>
                        </span>
                        <span class="stat-item">
                            <i class="fas fa-eye"></i> <span class="stat-count">${views}</span>
                        </span>
                    </div>
                </div>
            </div>
        </article>`;
    }
    
    container.innerHTML = html;
}

function toggleFullContent(postId) {
    if (state.expandedPosts.has(postId)) {
        state.expandedPosts.delete(postId);
    } else {
        state.expandedPosts.add(postId);
    }
    renderPosts();
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
                loadPosts(page, state.currentCategory);
                document.getElementById('postsContainer').scrollTop = 0;
            }
        });
    });
}

// ============================================================
// MODALE IMAGE
// ============================================================

function openImageModal(postId, imageIndex) {
    var post = state.posts.find(function(p) { return p.id === postId; });
    if (!post || !post.images || post.images.length === 0) {
        showToast('Aucune image à afficher', 'info');
        return;
    }
    
    state.currentImages = post.images;
    state.currentImageIndex = Math.min(imageIndex, state.currentImages.length - 1);
    
    var modal = document.getElementById('imageModal');
    var img = document.getElementById('modalImage');
    var counter = document.getElementById('imageCounter');
    
    img.src = fixImageUrl(state.currentImages[state.currentImageIndex]) || '';
    counter.textContent = (state.currentImageIndex + 1) + ' / ' + state.currentImages.length;
    
    document.getElementById('prevImageBtn').style.display = state.currentImages.length > 1 ? 'flex' : 'none';
    document.getElementById('nextImageBtn').style.display = state.currentImages.length > 1 ? 'flex' : 'none';
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
    document.body.style.overflow = '';
}

function navigateImage(direction) {
    if (state.currentImages.length === 0) return;
    state.currentImageIndex += direction;
    if (state.currentImageIndex < 0) state.currentImageIndex = state.currentImages.length - 1;
    if (state.currentImageIndex >= state.currentImages.length) state.currentImageIndex = 0;
    document.getElementById('modalImage').src = fixImageUrl(state.currentImages[state.currentImageIndex]) || '';
    document.getElementById('imageCounter').textContent = (state.currentImageIndex + 1) + ' / ' + state.currentImages.length;
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
            loadPosts(1, category);
        });
    });
}

// ============================================================
// INITIALISATION
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    log('🚀 Initialisation de la page d\'accueil');
    
    loadStats();
    loadPosts(1, 'all');
    setupTabs();
    
    document.getElementById('prevPage').addEventListener('click', function() {
        if (state.currentPage > 1) {
            loadPosts(state.currentPage - 1, state.currentCategory);
            document.getElementById('postsContainer').scrollTop = 0;
        }
    });
    
    document.getElementById('nextPage').addEventListener('click', function() {
        if (state.currentPage < state.totalPages) {
            loadPosts(state.currentPage + 1, state.currentCategory);
            document.getElementById('postsContainer').scrollTop = 0;
        }
    });
    
    document.getElementById('closeImageModal').addEventListener('click', closeImageModal);
    document.getElementById('prevImageBtn').addEventListener('click', function() { navigateImage(-1); });
    document.getElementById('nextImageBtn').addEventListener('click', function() { navigateImage(1); });
    
    window.addEventListener('click', function(e) {
        if (e.target === document.getElementById('imageModal')) {
            closeImageModal();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (document.getElementById('imageModal').style.display === 'flex') {
                closeImageModal();
            }
        }
        if (e.key === 'ArrowLeft' && document.getElementById('imageModal').style.display === 'flex') {
            navigateImage(-1);
        }
        if (e.key === 'ArrowRight' && document.getElementById('imageModal').style.display === 'flex') {
            navigateImage(1);
        }
    });
    
    log('✅ Page d\'accueil initialisée avec succès');
});

// ============================================================
// STYLES DYNAMIQUES
// ============================================================

var style = document.createElement('style');
style.textContent = `
    /* ============================================================
       TOAST
    ============================================================ */
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
    
    /* ============================================================
       SCROLL CONTAINER
    ============================================================ */
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
    
    /* ============================================================
       POST CARDS
    ============================================================ */
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
    
    .post-content .post-text .post-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid #e9ecef;
        margin-top: 10px;
    }
    .post-content .post-text .post-actions .stat-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 13px;
        color: #6c757d;
        padding: 4px 8px;
        border-radius: 4px;
        background: #f8f9fa;
    }
    .post-content .post-text .post-actions .stat-item i {
        font-size: 14px;
    }
    .post-content .post-text .post-actions .stat-item .stat-count {
        font-weight: 600;
        color: #343a40;
    }
    .post-content .post-text .post-actions .stat-item .fa-heart {
        color: #dc3545;
    }
    .post-content .post-text .post-actions .stat-item .fa-comment {
        color: #0dcaf0;
    }
    .post-content .post-text .post-actions .stat-item .fa-share-alt {
        color: #6c757d;
    }
    .post-content .post-text .post-actions .stat-item .fa-eye {
        color: #6c757d;
    }
    
    /* ============================================================
       PAGINATION PROFESSIONNELLE
    ============================================================ */
    #pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 25px;
        padding: 15px 0;
        flex-wrap: wrap;
    }

    #pagination button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 16px;
        border: 1px solid #dee2e6;
        background: white;
        color: #495057;
        cursor: pointer;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.25s ease;
        min-height: 38px;
        min-width: 38px;
    }

    #pagination button:hover:not(:disabled) {
        background: #198754;
        color: white;
        border-color: #198754;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.2);
    }

    #pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    #pagination button:active:not(:disabled) {
        transform: scale(0.95);
    }

    .page-numbers {
        display: flex;
        gap: 4px;
        align-items: center;
    }

    .page-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 14px;
        border: 1px solid #dee2e6;
        background: white;
        cursor: pointer;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        color: #495057;
        transition: all 0.25s ease;
        min-width: 38px;
        min-height: 38px;
    }

    .page-number:hover {
        background: #f8f9fa;
        border-color: #198754;
        color: #198754;
    }

    .page-number.active {
        background: #198754;
        color: white;
        border-color: #198754;
        box-shadow: 0 2px 8px rgba(25, 135, 84, 0.25);
    }

    .page-number.active:hover {
        background: #146c43;
        border-color: #146c43;
    }

    .page-dots {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
        min-width: 30px;
    }

    /* ============================================================
       EMPTY POSTS
    ============================================================ */
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
    
    /* ============================================================
       MODALE IMAGE
    ============================================================ */
    .modal-image {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.85);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-image-content {
        position: relative;
        max-width: 90%;
        max-height: 90vh;
        background: transparent;
    }
    .modal-image-content img {
        width: 100%;
        max-height: 85vh;
        object-fit: contain;
        border-radius: 8px;
    }
    .modal-image-close {
        position: absolute;
        top: -40px;
        right: 0;
        color: white;
        font-size: 32px;
        cursor: pointer;
        transition: all 0.3s;
        text-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    .modal-image-close:hover {
        color: #dc3545;
        transform: rotate(90deg);
    }
    .image-nav {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin-top: 16px;
    }
    .image-nav button {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.3s;
        color: #1a202c;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .image-nav button:hover {
        background: white;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    .image-nav span {
        color: white;
        font-size: 14px;
        background: rgba(0, 0, 0, 0.5);
        padding: 4px 14px;
        border-radius: 20px;
    }
    
    /* ============================================================
       GRILLE D'IMAGES PROFESSIONNELLE
    ============================================================ */
    .image-grid {
        display: grid;
        gap: 6px;
        margin-top: 10px;
        margin-bottom: 6px;
    }
    
    .image-grid .grid-img-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        cursor: pointer;
        aspect-ratio: 16/10;
        background: #f0f2f5;
    }
    
    .image-grid .grid-img-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .image-grid .grid-img-item:hover img {
        transform: scale(1.03);
    }
    
    .image-grid .overlay-plus {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        font-size: 24px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        backdrop-filter: blur(2px);
    }
    
    /* Grille 1 image */
    .image-grid.grid-1 {
        grid-template-columns: 1fr;
        max-width: 100%;
    }
    
    .image-grid.grid-1 .grid-img-item {
        aspect-ratio: 16/9;
        max-height: 300px;
    }
    
    /* Grille 2 images */
    .image-grid.grid-2 {
        grid-template-columns: 1fr 1fr;
    }
    
    .image-grid.grid-2 .grid-img-item {
        aspect-ratio: 1/1;
    }
    
    /* Grille 3 images */
    .image-grid.grid-3 {
        grid-template-columns: 2fr 1fr;
        grid-template-rows: 1fr 1fr;
    }
    
    .image-grid.grid-3 .grid-img-item:first-child {
        grid-row: 1 / 3;
    }
    
    .image-grid.grid-3 .grid-img-item {
        aspect-ratio: auto;
    }
    
    /* Grille 4 images */
    .image-grid.grid-4 {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
    }
    
    .image-grid.grid-4 .grid-img-item {
        aspect-ratio: 1/1;
    }
    
    /* ============================================================
       RESPONSIVE PAGINATION
    ============================================================ */
    @media (max-width: 768px) {
        #pagination {
            gap: 5px;
            padding: 10px 0;
        }
        
        #pagination button {
            padding: 6px 12px;
            font-size: 12px;
            min-height: 32px;
            min-width: 32px;
            border-radius: 6px;
        }
        
        .page-number {
            padding: 6px 10px;
            font-size: 12px;
            min-width: 32px;
            min-height: 32px;
            border-radius: 6px;
        }
        
        .page-dots {
            font-size: 12px;
            min-width: 20px;
        }
        
        .image-grid .grid-img-item {
            aspect-ratio: 4/3;
        }
        
        .image-grid.grid-1 .grid-img-item {
            aspect-ratio: 16/9;
            max-height: 200px;
        }
        
        .image-grid.grid-3 {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto;
        }
        
        .image-grid.grid-3 .grid-img-item:first-child {
            grid-row: 1 / 2;
            grid-column: 1 / 3;
            aspect-ratio: 16/9;
        }
    }
    
    @media (max-width: 480px) {
        #pagination {
            gap: 3px;
        }
        
        #pagination button {
            padding: 4px 8px;
            font-size: 11px;
            min-height: 28px;
            min-width: 28px;
            border-radius: 4px;
        }
        
        .page-number {
            padding: 4px 8px;
            font-size: 11px;
            min-width: 28px;
            min-height: 28px;
            border-radius: 4px;
        }
        
        /* Cache le texte des boutons précédent/suivant sur très petits écrans */
        #pagination button span {
            display: none;
        }
        
        #pagination button i {
            font-size: 12px;
        }
        
        .image-grid {
            gap: 4px;
        }
        
        .image-grid .grid-img-item {
            aspect-ratio: 4/3;
            border-radius: 6px;
        }
        
        .image-grid.grid-1 .grid-img-item {
            aspect-ratio: 16/9;
            max-height: 160px;
        }
        
        .image-grid.grid-3 {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto;
        }
        
        .image-grid.grid-3 .grid-img-item:first-child {
            grid-row: 1 / 2;
            grid-column: 1 / 3;
            aspect-ratio: 16/9;
        }
        
        .image-grid .overlay-plus {
            font-size: 18px;
        }
    }
`;
document.head.appendChild(style);
</script>

@endsection