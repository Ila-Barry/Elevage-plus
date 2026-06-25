{{-- resources/views/blog.blade.php --}}

@extends('layouts.menu')

@section('title', 'Communauté Éleveurs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/blog.css') }}">
@endpush

@section('content')
<div class="blog-main-container p-4">
    
    <div class="blog-top-header">
        <h2 class="blog-main-title">COMMUNAUTÉ ÉLEVEURS</h2>
        <div class="blog-header-buttons">
            <button class="btn-blog-action btn-green-publish" id="openPublishModal">
                <i class="fas fa-pencil-alt"></i> Publier un article
            </button>
            <button class="btn-blog-action btn-white-trend" id="showTrends" data-tab="tendances">
                <i class="fas fa-chart-line"></i> Tendances
            </button>
        </div>
    </div>

    <div class="blog-categories-tabs">
        <a href="#" class="tab-item active-tab" data-tab="all"><i class="fas fa-users text-success"></i> Tous</a>
        <a href="#" class="tab-item" data-tab="conseil"><i class="fas fa-lightbulb text-warning"></i> Conseils</a>
        <a href="#" class="tab-item" data-tab="experience"><i class="fas fa-user-edit text-info"></i> Expériences</a>
        <a href="#" class="tab-item" data-tab="alerte"><i class="fas fa-exclamation-triangle text-danger"></i> Alertes</a>
    </div>

    <div class="blog-posts-feed" id="postsFeed">
        <div class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2 text-muted">Chargement des publications...</p>
        </div>
    </div>

    <div class="custom-blog-pagination">
        <button class="pag-arrow-btn" id="prevPage" disabled><i class="fas fa-caret-left"></i> précédente</button>
        <div class="pag-numbers-list" id="pageNumbers"></div>
        <button class="pag-arrow-btn" id="nextPage">suivante <i class="fas fa-caret-right"></i></button>
    </div>
</div>

<!-- ================= MODALE PUBLIER ================= -->
<div id="publishModal" class="modal-blog">
    <div class="modal-blog-content">
        <div class="modal-blog-header">
            <h3><i class="fas fa-pencil-alt"></i> Publier un article</h3>
            <span class="modal-blog-close" id="closePublishModal">&times;</span>
        </div>
        <div class="modal-blog-body">
            <div id="publishError" class="alert alert-danger" style="display: none;"></div>
            <form id="publishForm" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Titre de l'article *</label>
                    <input type="text" id="postTitle" class="form-control" placeholder="Ex: Ma nouvelle méthode d'alimentation" required>
                </div>

                <div class="form-group">
                    <label>Catégorie *</label>
                    <select id="postCategory" class="form-control" required>
                        <option value="conseil">🌾 Conseils</option>
                        <option value="experience">💡 Expériences</option>
                        <option value="alerte">⚠️ Alertes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Contenu *</label>
                    <textarea id="postContent" class="form-control" rows="5" placeholder="Décrivez votre expérience..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Fichiers joints (optionnel)</label>
                    <div class="file-upload-area" id="fileUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p><strong>Cliquez ou glissez-déposez</strong> vos fichiers</p>
                        <input type="file" id="postFiles" multiple accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar" style="display: none;">
                    </div>
                    
                    <div class="file-preview-container" id="filePreviewContainer"></div>
                    
                    <div class="file-stats" id="fileStats" style="display: none; margin-top: 10px; font-size: 13px; color: #6c757d;">
                        <span id="fileCount">0</span> fichier(s) sélectionné(s)
                        <span class="badge badge-secondary ml-2" id="fileSize">0 Mo</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel-modal" id="cancelPublish">Annuler</button>
                    <button type="submit" class="btn-publish-modal" id="publishSubmitBtn">
                        <i class="fas fa-paper-plane"></i> Publier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODALE IMAGE ================= -->
<div id="imageModal" class="modal-blog image-modal">
    <div class="modal-blog-content image-modal-content">
        <span class="modal-blog-close" id="closeImageModal">&times;</span>
        <img id="modalImage" src="" alt="Image en grand format">
        <div class="image-nav">
            <button id="prevImageBtn"><i class="fas fa-chevron-left"></i></button>
            <span id="imageCounter">1 / 1</span>
            <button id="nextImageBtn"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</div>

<script>
// ============================================================
// CONFIGURATION & CONSTANTES
// ============================================================

const CONFIG = {
    API_URL: window.location.origin + '/api',
    CSRF_TOKEN: document.querySelector('meta[name="csrf-token"]')?.content || '',
    TOKEN: (() => {
        const raw = localStorage.getItem('access_token');
        return raw ? raw.replace(/^"(.*)"$/, '$1').trim() : null;
    })(),
    ITEMS_PER_PAGE: 4
};

// Limites des fichiers
const FILE_LIMITS = {
    images: { max: 5, maxSize: 5 * 1024 * 1024, types: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'] },
    videos: { max: 2, maxSize: 50 * 1024 * 1024, types: ['video/mp4', 'video/avi', 'video/quicktime'] },
    documents: { max: 3, maxSize: 10 * 1024 * 1024, types: [
        'application/pdf', 'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain', 'application/zip', 'application/x-rar-compressed'
    ]}
};

// ============================================================
// ÉTAT DE L'APPLICATION
// ============================================================

const state = {
    posts: [],
    currentPage: 1,
    totalPages: 0,
    currentCategory: 'all',
    currentImageIndex: 0,
    currentImages: [],
    uploadedFiles: { images: [], videos: [], documents: [] },
    isLoading: false,
    toastTimeout: null
};

// ============================================================
// FONCTIONS UTILITAIRES
// ============================================================

function log(message, data = null) {
    const timestamp = new Date().toISOString();
    if (data) {
        console.log(`[${timestamp}] 📝 ${message}`, data);
    } else {
        console.log(`[${timestamp}] 📝 ${message}`);
    }
}

function logError(message, error = null) {
    const timestamp = new Date().toISOString();
    if (error) {
        console.error(`[${timestamp}] ❌ ${message}`, error);
    } else {
        console.error(`[${timestamp}] ❌ ${message}`);
    }
}

function truncateText(text, maxLength) {
    if (!text) return '';
    return text.length <= maxLength ? text : text.substring(0, maxLength) + '...';
}

function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    const icons = {
        'pdf': 'fas fa-file-pdf', 'doc': 'fas fa-file-word', 'docx': 'fas fa-file-word',
        'xls': 'fas fa-file-excel', 'xlsx': 'fas fa-file-excel',
        'ppt': 'fas fa-file-powerpoint', 'pptx': 'fas fa-file-powerpoint',
        'txt': 'fas fa-file-alt', 'zip': 'fas fa-file-archive', 'rar': 'fas fa-file-archive',
        'jpg': 'fas fa-file-image', 'jpeg': 'fas fa-file-image', 'png': 'fas fa-file-image',
        'gif': 'fas fa-file-image', 'webp': 'fas fa-file-image',
        'mp4': 'fas fa-file-video', 'avi': 'fas fa-file-video', 'mov': 'fas fa-file-video'
    };
    return icons[ext] || 'fas fa-file';
}

function detectFileType(file) {
    const type = file.type;
    const ext = file.name.split('.').pop().toLowerCase();
    
    if (type.startsWith('image/')) return 'images';
    if (type.startsWith('video/')) return 'videos';
    
    const docExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];
    if (docExts.includes(ext)) return 'documents';
    
    const docMimes = ['application/pdf', 'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain', 'application/zip', 'application/x-rar-compressed'];
    if (docMimes.includes(type)) return 'documents';
    
    return 'unknown';
}

// ============================================================
// ✅ FONCTION DE NORMALISATION DES URLs D'IMAGES - CORRIGÉE
// ============================================================

function fixImageUrl(url) {
    if (!url) return null;
    
    let clean = url.trim();
    
    // Si l'URL est déjà complète (http ou https)
    if (clean.startsWith('http://') || clean.startsWith('https://')) {
        return clean;
    }
    
    // ✅ Supprimer les doubles 'storage/' et les chemins corrompus
    clean = clean.replace(/\/storage\/\/storage\//g, '/storage/');
    clean = clean.replace(/storage\/\/storage\//g, 'storage/');
    clean = clean.replace(/\/\/storage\//g, '/storage/');
    clean = clean.replace(/storage\/storage\//g, 'storage/');
    
    // ✅ Si l'URL commence par 'storage/' ou '/storage/'
    if (clean.startsWith('storage/')) {
        clean = window.location.origin + '/' + clean;
    } else if (clean.startsWith('/storage/')) {
        clean = window.location.origin + clean;
    } else {
        // Fallback: ajouter le préfixe storage
        clean = window.location.origin + '/storage/' + clean;
    }
    
    return clean;
}

// ============================================================
// FONCTIONS TOAST
// ============================================================

function showToast(message, type = 'info') {
    const existing = document.querySelector('.custom-toast');
    if (existing) existing.remove();
    if (state.toastTimeout) clearTimeout(state.toastTimeout);
    
    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`;
    
    const icons = {
        success: 'fa-check-circle',
        danger: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas ${icons[type] || icons.info}"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(toast);
    
    requestAnimationFrame(() => toast.classList.add('show'));
    
    state.toastTimeout = setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ============================================================
// API - COMMUNICATION AVEC LE BACKEND
// ============================================================

async function apiCall(endpoint, options = {}) {
    const defaultHeaders = {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + CONFIG.TOKEN,
        'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN
    };

    const isFormData = options.body instanceof FormData;
    
    if (!isFormData) {
        defaultHeaders['Content-Type'] = 'application/json';
    }

    const config = {
        ...options,
        headers: {
            ...defaultHeaders,
            ...options.headers
        }
    };

    if (config.body && !(config.body instanceof FormData) && typeof config.body === 'object') {
        config.body = JSON.stringify(config.body);
    }

    const url = endpoint.startsWith('http') ? endpoint : `${CONFIG.API_URL}${endpoint}`;
    log(`🌐 Requête ${options.method || 'GET'} ${url}`, { isFormData });

    try {
        const response = await fetch(url, config);
        const data = await response.json();

        if (!response.ok) {
            logError(`Erreur HTTP ${response.status}`, data);
            const error = new Error(data.message || 'Erreur API');
            error.status = response.status;
            error.errors = data.errors;
            throw error;
        }

        log(`✅ Réponse reçue`, { status: response.status });
        return data;
    } catch (error) {
        logError('Erreur API', error);
        throw error;
    }
}

// ============================================================
// GESTION DES PUBLICATIONS
// ============================================================

async function fetchPosts(page = 1, category = 'all') {
    log(`📤 Récupération des publications page ${page}, catégorie ${category}`);
    
    let url = `/publications?page=${page}&per_page=${CONFIG.ITEMS_PER_PAGE}`;
    if (category !== 'all') url += `&categorie=${category}`;
    
    const result = await apiCall(url);
    log('📦 Structure de la réponse:', result);
    return result;
}

async function createPost(formData) {
    log('📤 Création d\'une nouvelle publication');
    
    const hasTitre = formData.has('titre');
    const hasCategorie = formData.has('categorie');
    const hasContenu = formData.has('contenu');
    
    if (!hasTitre || !hasCategorie || !hasContenu) {
        throw new Error('Champs obligatoires manquants');
    }
    
    return apiCall('/publications', {
        method: 'POST',
        body: formData
    });
}

async function updatePost(id, formData) {
    log(`📤 Mise à jour de la publication ${id}`);
    return apiCall(`/publications/${id}`, {
        method: 'POST',
        body: formData,
        headers: { 'X-HTTP-Method-Override': 'PUT' }
    });
}

async function deletePost(id) {
    log(`📤 Suppression de la publication ${id}`);
    return apiCall(`/publications/${id}`, {
        method: 'DELETE'
    });
}

async function toggleLike(id) {
    log(`📤 Like/Unlike publication ${id}`);
    return apiCall(`/publications/${id}/like`, {
        method: 'POST'
    });
}

async function addComment(id, content) {
    log(`📤 Ajout commentaire publication ${id}`);
    return apiCall(`/publications/${id}/comments`, {
        method: 'POST',
        body: { contenu: content }
    });
}

async function sharePost(id, plateforme) {
    log(`📤 Partage publication ${id} sur ${plateforme}`);
    return apiCall(`/publications/${id}/share`, {
        method: 'POST',
        body: { plateforme }
    });
}

// ============================================================
// CHARGEMENT DES PUBLICATIONS - CORRIGÉ AVEC fixImageUrl
// ============================================================

async function loadPosts(page = 1, category = 'all') {
    if (state.isLoading) return;
    state.isLoading = true;
    
    const container = document.getElementById('postsFeed');
    container.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2 text-muted">Chargement des publications...</p>
        </div>
    `;
    
    try {
        const result = await fetchPosts(page, category);
        
        // ✅ DEBUG DÉTAILLÉ - Afficher toute la structure
        console.log('🔍 === DÉBOGAGE COMPLET DE LA RÉPONSE ===');
        console.log('1. result complet:', result);
        console.log('2. result.status:', result.status);
        console.log('3. result.data:', result.data);
        console.log('4. Type de result.data:', typeof result.data);
        console.log('5. result.data est un tableau?', Array.isArray(result.data));
        console.log('6. result.data.data:', result.data?.data);
        console.log('7. result.data.meta:', result.data?.meta);
        console.log('8. Clés de result.data:', result.data ? Object.keys(result.data) : 'null');
        
        let postsData = [];
        let meta = {};
        
        // ✅ Vérifier si result.data existe
        if (result.data) {
            // Cas 1: result.data.data est un tableau
            if (result.data.data && Array.isArray(result.data.data)) {
                postsData = result.data.data;
                meta = result.data.meta || {};
                console.log('✅ Cas 1 - result.data.data est un tableau de', postsData.length, 'éléments');
            }
            // Cas 2: result.data est un tableau
            else if (Array.isArray(result.data)) {
                postsData = result.data;
                meta = { current_page: page, last_page: 1 };
                console.log('✅ Cas 2 - result.data est un tableau de', postsData.length, 'éléments');
            }
            // Cas 3: result.data a une propriété qui contient un tableau
            else if (typeof result.data === 'object') {
                let found = false;
                for (const key of Object.keys(result.data)) {
                    if (Array.isArray(result.data[key]) && result.data[key].length > 0) {
                        postsData = result.data[key];
                        meta = result.data.meta || {};
                        console.log(`✅ Cas 3 - result.data.${key} est un tableau de`, postsData.length, 'éléments');
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    // ✅ Essayer de récupérer depuis result.data.data
                    if (result.data.data) {
                        postsData = Array.isArray(result.data.data) ? result.data.data : [];
                        meta = result.data.meta || {};
                        console.log('✅ Cas 3b - result.data.data (fallback) de', postsData.length, 'éléments');
                    } else {
                        console.warn('⚠️ Aucun tableau trouvé dans result.data');
                    }
                }
            }
        } else {
            // ✅ Si result.data est null ou undefined
            console.warn('⚠️ result.data est null ou undefined');
        }
        
        // ✅ Si toujours vide, essayer de récupérer depuis result directement
        if (postsData.length === 0 && result.data) {
            // Vérifier si result a une propriété qui contient un tableau
            for (const key of Object.keys(result)) {
                if (Array.isArray(result[key]) && result[key].length > 0) {
                    postsData = result[key];
                    meta = { current_page: page, last_page: 1 };
                    console.log(`✅ Récupéré depuis result.${key} de`, postsData.length, 'éléments');
                    break;
                }
            }
        }
        
        console.log('📋 Données finales extraites:', postsData.length, 'éléments');
        
        // ✅ Transformation des données
        if (postsData.length > 0) {
            state.posts = postsData.map(post => {
                if (!post) return null;
                
                // ✅ Normaliser les images
                let images = [];
                if (post.images && Array.isArray(post.images)) {
                    images = post.images.map(img => fixImageUrl(img)).filter(u => u);
                } else if (post.image_url) {
                    if (typeof post.image_url === 'string') {
                        images = post.image_url.split(',').map(url => fixImageUrl(url.trim())).filter(u => u);
                    } else if (Array.isArray(post.image_url)) {
                        images = post.image_url.map(url => fixImageUrl(url)).filter(u => u);
                    }
                }
                
                let videos = [];
                if (post.videos && Array.isArray(post.videos)) {
                    videos = post.videos.map(v => fixImageUrl(v)).filter(u => u);
                } else if (post.video_url) {
                    if (typeof post.video_url === 'string') {
                        videos = post.video_url.split(',').map(url => fixImageUrl(url.trim())).filter(u => u);
                    } else if (Array.isArray(post.video_url)) {
                        videos = post.video_url.map(url => fixImageUrl(url)).filter(u => u);
                    }
                }
                
                let fichiers = [];
                if (post.fichiers && Array.isArray(post.fichiers)) {
                    fichiers = post.fichiers.map(f => ({
                        ...f,
                        url: fixImageUrl(f.url)
                    })).filter(f => f.url);
                } else if (post.fichier_url) {
                    const urls = typeof post.fichier_url === 'string' ? post.fichier_url.split(',').map(u => u.trim()).filter(u => u) : [];
                    const names = post.fichier_nom ? (typeof post.fichier_nom === 'string' ? post.fichier_nom.split(',').map(n => n.trim()) : []) : [];
                    fichiers = urls.map((url, index) => ({
                        url: fixImageUrl(url),
                        nom: names[index] || 'Fichier ' + (index + 1)
                    })).filter(f => f.url);
                }
                
                return {
                    id: post.id || 0,
                    titre: post.titre || post.title || 'Sans titre',
                    categorie: post.categorie || post.category || 'conseil',
                    categorie_label: post.categorie_label || post.category_label || post.categorie || 'Conseil',
                    contenu: post.contenu || post.content || '',
                    resume: post.resume || truncateText(post.contenu || post.content || '', 200),
                    images: images,
                    image_url: images.length > 0 ? images[0] : null,
                    videos: videos,
                    video_url: videos.length > 0 ? videos[0] : null,
                    fichiers: fichiers,
                    statistiques: {
                        likes: post.nbr_likes || post.likes_count || post.likes || 0,
                        commentaires: post.nbr_commentaires || post.comments_count || post.comments || 0,
                        vues: post.nbr_vues || post.views_count || post.views || 0
                    },
                    interactions: {
                        liked_by_user: post.liked_by_user || post.is_liked || false
                    },
                    auteur: post.user || post.auteur || post.author || {
                        name: 'Utilisateur',
                        photo_url: null,
                        role: 'user'
                    },
                    published_at_human: post.published_at_human || post.created_at_human || post.date_human || 'N/A',
                    published_at: post.published_at || post.created_at,
                    created_at: post.created_at,
                    updated_at: post.updated_at
                };
            }).filter(post => post !== null);
        } else {
            state.posts = [];
        }
        
        state.currentPage = meta.current_page || page;
        state.totalPages = meta.last_page || 1;
        
        console.log(`✅ ${state.posts.length} publications chargées`);
        
        if (state.posts.length > 0) {
            console.log('📋 Première publication:', state.posts[0]);
        }
        
        renderPosts();
        updatePagination();
        
    } catch (error) {
        console.error('❌ Erreur chargement publications:', error);
        showToast('Erreur lors du chargement des publications', 'danger');
        state.posts = [];
        renderPosts();
        updatePagination();
    } finally {
        state.isLoading = false;
    }
}

// ============================================================
// AFFICHAGE DES PUBLICATIONS - AVEC FORMULAIRE DE COMMENTAIRE
// ============================================================

function renderPosts() {
    const container = document.getElementById('postsFeed');
    
    if (!state.posts || state.posts.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <h4>Aucun article trouvé</h4>
                <p>Commencez à partager votre expérience avec la communauté !</p>
            </div>
        `;
        return;
    }
    
    log(`📋 Rendu de ${state.posts.length} publications`);
    
    container.innerHTML = state.posts.map((post, index) => {
        const displayContent = post.resume || truncateText(post.contenu || '', 200);
        const isTruncated = (post.contenu || '').length > 200;
        
        const images = post.images && post.images.length > 0 ? post.images : [];
        const videos = post.videos && post.videos.length > 0 ? post.videos : [];
        const fichiers = post.fichiers && post.fichiers.length > 0 ? post.fichiers : [];
        
        const authorName = post.auteur?.name || 'Utilisateur';
        const authorAvatar = post.auteur?.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(authorName)}&background=4F46E5&color=fff`;
        const authorRole = post.auteur?.role === 'admin' ? 'Administrateur' : 'Éleveur';
        
        const categoryIcon = post.categorie === 'experience' ? '💡' : 
                            post.categorie === 'alerte' ? '⚠️' : '🌾';
        
        const likes = post.statistiques?.likes || 0;
        const comments = post.statistiques?.commentaires || 0;
        const views = post.statistiques?.vues || 0;
        const isLiked = post.interactions?.liked_by_user || false;
        
        // ✅ Préparer le contenu avec le bouton "plus..."
        let contentHtml = displayContent;
        if (isTruncated) {
            contentHtml = `
                ${displayContent}
                <span class="read-more-btn" onclick="toggleFullContent(${post.id})">
                    plus...
                </span>
            `;
        }
        
        // ✅ Générer un ID unique pour le formulaire de commentaire
        const commentFormId = `comment-form-${post.id}`;
        const commentsListId = `comments-list-${post.id}`;
        
        return `
        <article class="custom-post-card" data-id="${post.id}" data-index="${index}">
            <div class="custom-post-admin">
                <button class="action-edit" onclick="editPost(${post.id})" title="Modifier">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <button class="action-delete" onclick="deletePostConfirm(${post.id})" title="Supprimer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="custom-post-header">
                <div class="author-avatar-box">
                    <img src="${authorAvatar}" 
                         alt="${authorName}" 
                         class="rounded-circle" 
                         onerror="this.src='https://ui-avatars.com/api/?name=User&background=4F46E5&color=fff'">
                </div>
                <div class="author-meta-data">
                    <span class="user-fullname">${authorName}</span>
                    <span class="meta-separator">•</span>
                    <span class="user-profession">${authorRole}</span>
                    <span class="meta-separator">•</span>
                    <span class="published-time">
                        <i class="far fa-calendar-alt"></i> ${post.published_at_human || 'N/A'}
                    </span>
                </div>
            </div>

            <div class="custom-post-body">
                <h3 class="post-title-badge color-${post.categorie}">
                    ${categoryIcon} ${post.categorie_label || post.categorie} - ${post.titre || 'Sans titre'}
                </h3>
                <p class="post-text-content" data-full="false">
                    ${contentHtml}
                </p>
                
                ${images.length > 0 ? renderImages(images, post.id) : ''}
                ${videos.length > 0 ? renderVideos(videos) : ''}
                ${fichiers.length > 0 ? renderFichiers(fichiers) : ''}
            </div>

            <div class="custom-post-footer">
                <div class="post-counters">
                    <span class="counter-item" onclick="handleLike(${post.id})" style="cursor: pointer;">
                        <i class="${isLiked ? 'fas' : 'far'} fa-thumbs-up"></i> 
                        <span class="likes-count" id="likes-count-${post.id}">${likes}</span>
                    </span>
                    <span class="counter-item" onclick="toggleComments(${post.id})" style="cursor: pointer;">
                        <i class="far fa-comment"></i> 
                        <span class="comments-count" id="comments-count-${post.id}">${comments}</span>
                    </span>
                    <span class="counter-item">
                        <i class="far fa-eye"></i> ${views}
                    </span>
                </div>
                <div class="post-action-triggers">
                    <button class="trigger-btn ${isLiked ? 'liked' : ''}" onclick="handleLike(${post.id})">
                        <i class="${isLiked ? 'fas' : 'far'} fa-thumbs-up"></i> 
                        ${isLiked ? 'Aimé' : 'Liker'}
                    </button>
                    <button class="trigger-btn" onclick="toggleComments(${post.id})">
                        <i class="far fa-comment"></i> Commenter
                    </button>
                    <button class="trigger-btn" onclick="handleShare(${post.id})">
                        <i class="fas fa-share"></i> Partager
                    </button>
                </div>
            </div>

            <!-- ================= SECTION COMMENTAIRES ================= -->
            <div class="comments-section" id="${commentsListId}" style="display: none; padding: 15px 20px; border-top: 1px solid #e9ecef; background: #f8f9fa; border-radius: 0 0 8px 8px;">
                <!-- Liste des commentaires -->
                <div class="comments-list" id="comments-list-inner-${post.id}">
                    <div class="text-center text-muted py-2" style="font-size: 13px;">
                        <i class="fas fa-spinner fa-spin"></i> Chargement des commentaires...
                    </div>
                </div>
                
                <!-- Formulaire d'ajout de commentaire -->
                <form class="comment-form" id="${commentFormId}" data-post-id="${post.id}" style="margin-top: 12px;">
                    <div class="d-flex gap-2">
                        <input type="text" 
                               class="form-control form-control-sm" 
                               placeholder="Écrire un commentaire..." 
                               id="comment-input-${post.id}"
                               style="border-radius: 20px; font-size: 13px;">
                        <button type="submit" class="btn btn-sm btn-success" style="border-radius: 20px; white-space: nowrap;">
                            <i class="fas fa-paper-plane"></i> Envoyer
                        </button>
                    </div>
                </form>
            </div>
        </article>
    `}).join('');
    
    // ✅ Attacher les événements des formulaires de commentaire après le rendu
    attachCommentFormEvents();
}

// ============================================================
// ATTACHER LES ÉVÉNEMENTS DES FORMULAIRES DE COMMENTAIRE
// ============================================================

function attachCommentFormEvents() {
    document.querySelectorAll('.comment-form').forEach(form => {
        // Supprimer les anciens événements pour éviter les doublons
        form.removeEventListener('submit', handleCommentSubmit);
        form.addEventListener('submit', handleCommentSubmit);
    });
}

async function handleCommentSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const postId = parseInt(form.dataset.postId);
    const input = document.getElementById(`comment-input-${postId}`);
    const content = input.value.trim();
    
    if (!content) {
        showToast('Veuillez écrire un commentaire', 'warning');
        return;
    }
    
    // Désactiver le bouton
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    try {
        const result = await addComment(postId, content);
        
        if (result.status === 'success') {
            showToast('Commentaire ajouté avec succès !', 'success');
            input.value = '';
            
            // ✅ Mettre à jour le compteur de commentaires
            const countSpan = document.getElementById(`comments-count-${postId}`);
            if (countSpan) {
                const currentCount = parseInt(countSpan.textContent) || 0;
                countSpan.textContent = currentCount + 1;
            }
            
            // ✅ Recharger les commentaires
            await loadComments(postId);
        } else {
            showToast(result.message || 'Erreur lors de l\'ajout du commentaire', 'danger');
        }
    } catch (error) {
        console.error('Erreur commentaire:', error);
        showToast('Erreur lors de l\'ajout du commentaire', 'danger');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer';
    }
}

// ============================================================
// CHARGER LES COMMENTAIRES D'UNE PUBLICATION
// ============================================================

async function loadComments(postId) {
    try {
        // ✅ Appel API pour récupérer les commentaires
        const result = await apiCall(`/publications/${postId}`, {
            method: 'GET'
        });
        
        const commentsContainer = document.getElementById(`comments-list-inner-${postId}`);
        if (!commentsContainer) return;
        
        if (result.status === 'success' && result.data) {
            const publication = result.data;
            const comments = publication.commentaires || [];
            
            if (comments.length === 0) {
                commentsContainer.innerHTML = `
                    <div class="text-center text-muted py-2" style="font-size: 13px;">
                        <i class="far fa-comment-dots"></i> Aucun commentaire pour le moment
                    </div>
                `;
                return;
            }
            
            // ✅ Afficher les commentaires
            commentsContainer.innerHTML = comments.map(comment => {
                const userName = comment.user?.name || 'Utilisateur';
                const userAvatar = comment.user?.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=4F46E5&color=fff`;
                const date = comment.created_at ? new Date(comment.created_at).toLocaleDateString('fr-FR') : 'N/A';
                
                return `
                    <div class="comment-item d-flex gap-2 py-2" style="border-bottom: 1px solid #e9ecef;">
                        <img src="${userAvatar}" 
                             alt="${userName}" 
                             style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0;"
                             onerror="this.src='https://ui-avatars.com/api/?name=User&background=4F46E5&color=fff'">
                        <div style="flex: 1;">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <strong style="font-size: 13px;">${userName}</strong>
                                <span style="font-size: 11px; color: #6c757d;">${date}</span>
                            </div>
                            <p style="font-size: 13px; margin: 2px 0 0 0; color: #343a40;">${comment.contenu || ''}</p>
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            commentsContainer.innerHTML = `
                <div class="text-center text-muted py-2" style="font-size: 13px;">
                    <i class="fas fa-exclamation-triangle"></i> Erreur lors du chargement des commentaires
                </div>
            `;
        }
    } catch (error) {
        console.error('Erreur chargement commentaires:', error);
        const commentsContainer = document.getElementById(`comments-list-inner-${postId}`);
        if (commentsContainer) {
            commentsContainer.innerHTML = `
                <div class="text-center text-danger py-2" style="font-size: 13px;">
                    <i class="fas fa-exclamation-circle"></i> Erreur de chargement
                </div>
            `;
        }
    }
}

// ============================================================
// AFFICHER/MASQUER LA SECTION COMMENTAIRES
// ============================================================

function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-list-${postId}`);
    if (!commentsSection) return;
    
    const isVisible = commentsSection.style.display !== 'none';
    
    if (isVisible) {
        commentsSection.style.display = 'none';
    } else {
        commentsSection.style.display = 'block';
        // ✅ Charger les commentaires si la section est vide
        const innerContainer = document.getElementById(`comments-list-inner-${postId}`);
        if (innerContainer && innerContainer.querySelector('.fa-spinner')) {
            loadComments(postId);
        }
    }
}

// ============================================================
// AFFICHER LE CONTENU COMPLET D'UNE PUBLICATION
// ============================================================

function toggleFullContent(postId) {
    const post = state.posts.find(p => p.id === postId);
    if (!post) {
        showToast('Publication non trouvée', 'danger');
        return;
    }
    
    // Trouver l'élément du post dans le DOM
    const postCard = document.querySelector(`.custom-post-card[data-id="${postId}"]`);
    if (!postCard) {
        showToast('Élément non trouvé', 'warning');
        return;
    }
    
    const contentParagraph = postCard.querySelector('.post-text-content');
    if (!contentParagraph) return;
    
    // Vérifier si le contenu est déjà complet
    const isFull = contentParagraph.dataset.full === 'true';
    
    if (isFull) {
        // Réduire le contenu
        const resume = post.resume || truncateText(post.contenu || '', 200);
        contentParagraph.innerHTML = `
            ${resume}
            <span class="read-more-btn" onclick="toggleFullContent(${postId})">
                plus...
            </span>
        `;
        contentParagraph.dataset.full = 'false';
    } else {
        // Afficher le contenu complet
        const fullContent = post.contenu || 'Contenu non disponible';
        // ✅ Remplacer les sauts de ligne par des <br> pour l'affichage
        const formattedContent = fullContent.replace(/\n/g, '<br>');
        contentParagraph.innerHTML = `
            ${formattedContent}
            <span class="read-more-btn" onclick="toggleFullContent(${postId})">
                moins...
            </span>
        `;
        contentParagraph.dataset.full = 'true';
    }
}

// ============================================================
// RENDU DES IMAGES, VIDÉOS ET FICHIERS - AVEC NORMALISATION
// ============================================================

function renderImages(images, postId) {
    if (!images || images.length === 0) return '';
    
    // ✅ Les images sont déjà normalisées par fixImageUrl, mais on vérifie
    const validImages = images.filter(img => img && img.trim() !== '');
    if (validImages.length === 0) return '';
    
    const displayImages = validImages.slice(0, 4);
    const remaining = validImages.length - 4;
    
    return `
        <div class="image-row">
            ${displayImages.map((img, index) => `
                <div class="grid-img-item" onclick="openImageModal(${postId}, ${index})">
                    <img src="${img}" alt="Image ${index + 1}" loading="lazy" onerror="this.style.display='none'">
                    ${index === 3 && remaining > 0 ? `<div class="overlay-plus">+${remaining}</div>` : ''}
                </div>
            `).join('')}
        </div>
    `;
}

function renderVideos(videos) {
    if (!videos || videos.length === 0) return '';
    
    const validVideos = videos.filter(v => v && v.trim() !== '');
    if (validVideos.length === 0) return '';
    
    return `
        <div class="post-videos-container">
            ${validVideos.map(video => `
                <video controls class="post-video" preload="metadata">
                    <source src="${video}" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture de vidéos.
                </video>
            `).join('')}
        </div>
    `;
}

function renderFichiers(fichiers) {
    if (!fichiers || fichiers.length === 0) return '';
    
    const validFichiers = fichiers.filter(f => f && f.url);
    if (validFichiers.length === 0) return '';
    
    return `
        <div class="post-files-container">
            ${validFichiers.map(fichier => {
                const icon = getFileIcon(fichier.nom || '');
                return `
                    <a href="${fichier.url}" target="_blank" class="file-download-link" download>
                        <i class="${icon}"></i>
                        <span>${fichier.nom || 'Télécharger'}</span>
                        <i class="fas fa-download"></i>
                    </a>
                `;
            }).join('')}
        </div>
    `;
}

// ============================================================
// PAGINATION
// ============================================================

function updatePagination() {
    const pageNumbers = document.getElementById('pageNumbers');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    
    if (state.totalPages <= 1) {
        pageNumbers.innerHTML = '';
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        return;
    }
    
    let html = '';
    const maxVisible = 5;
    let start = Math.max(1, state.currentPage - Math.floor(maxVisible / 2));
    let end = Math.min(state.totalPages, start + maxVisible - 1);
    
    if (end - start < maxVisible - 1) {
        start = Math.max(1, end - maxVisible + 1);
    }
    
    if (start > 1) {
        html += `<span class="num-item" onclick="goToPage(1)">1</span>`;
        if (start > 2) html += `<span class="num-item">...</span>`;
    }
    
    for (let i = start; i <= end; i++) {
        html += `<span class="num-item ${i === state.currentPage ? 'active-num' : ''}" onclick="goToPage(${i})">${i}</span>`;
    }
    
    if (end < state.totalPages) {
        if (end < state.totalPages - 1) html += `<span class="num-item">...</span>`;
        html += `<span class="num-item" onclick="goToPage(${state.totalPages})">${state.totalPages}</span>`;
    }
    
    pageNumbers.innerHTML = html;
    prevBtn.disabled = state.currentPage === 1;
    nextBtn.disabled = state.currentPage === state.totalPages;
}

function goToPage(page) {
    if (page === state.currentPage || state.isLoading) return;
    state.currentPage = page;
    loadPosts(page, state.currentCategory);
    document.getElementById('postsFeed').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ============================================================
// HANDLE LIKE - AVEC MISE À JOUR UI
// ============================================================

async function handleLike(postId) {
    try {
        const result = await toggleLike(postId);
        if (result.status === 'success') {
            // ✅ Mettre à jour le compteur de likes dans l'UI
            const countSpan = document.getElementById(`likes-count-${postId}`);
            if (countSpan && result.data) {
                countSpan.textContent = result.data.total_likes || 0;
            }
            
            // ✅ Mettre à jour le bouton like
            const postCard = document.querySelector(`.custom-post-card[data-id="${postId}"]`);
            if (postCard) {
                const likeBtn = postCard.querySelector('.trigger-btn:first-child');
                const counterItem = postCard.querySelector('.counter-item:first-child');
                
                if (likeBtn) {
                    const isLiked = result.data.liked || false;
                    likeBtn.innerHTML = `
                        <i class="${isLiked ? 'fas' : 'far'} fa-thumbs-up"></i> 
                        ${isLiked ? 'Aimé' : 'Liker'}
                    `;
                    likeBtn.classList.toggle('liked', isLiked);
                }
                
                if (counterItem) {
                    const icon = counterItem.querySelector('i');
                    if (icon) {
                        icon.className = result.data.liked ? 'fas fa-thumbs-up' : 'far fa-thumbs-up';
                    }
                }
            }
            
            showToast(result.message, 'success');
        } else {
            showToast(result.message || 'Erreur lors du like', 'danger');
        }
    } catch (error) {
        logError('Erreur like', error);
        showToast('Erreur lors du like', 'danger');
    }
}

function handleComment(postId) {
    const post = state.posts.find(p => p.id === postId);
    if (!post) {
        showToast('Publication non trouvée', 'danger');
        return;
    }
    
    const content = prompt(`Laissez un commentaire sur "${post.titre}" :`);
    if (!content || !content.trim()) {
        if (content !== null) showToast('Le commentaire ne peut pas être vide', 'warning');
        return;
    }
    
    addComment(postId, content.trim())
        .then(result => {
            if (result.status === 'success') {
                showToast('Commentaire ajouté avec succès !', 'success');
                loadPosts(state.currentPage, state.currentCategory);
            } else {
                showToast(result.message || 'Erreur lors de l\'ajout', 'danger');
            }
        })
        .catch(() => showToast('Erreur lors de l\'ajout du commentaire', 'danger'));
}

async function handleShare(postId) {
    const post = state.posts.find(p => p.id === postId);
    if (!post) {
        showToast('Publication non trouvée', 'danger');
        return;
    }
    
    const choix = prompt(
        'Choisissez une plateforme de partage :\n\n' +
        '1. WhatsApp\n2. Facebook\n3. Twitter\n4. Copier le lien\n\n' +
        'Entrez le numéro (1-4) :'
    );
    
    const plateformes = { '1': 'whatsapp', '2': 'facebook', '3': 'twitter', '4': 'copie_lien' };
    const plateforme = plateformes[choix];
    
    if (!plateforme) {
        if (choix !== null) showToast('Option invalide', 'warning');
        return;
    }
    
    try {
        const result = await sharePost(postId, plateforme);
        if (result.status === 'success') {
            const url = result.data?.share_url || window.location.href;
            if (plateforme === 'copie_lien') {
                try {
                    await navigator.clipboard.writeText(url);
                    showToast('Lien copié dans le presse-papier !', 'success');
                } catch {
                    prompt('Copiez ce lien pour partager :', url);
                }
            } else {
                window.open(url, '_blank');
                showToast('Partage effectué !', 'success');
            }
            await loadPosts(state.currentPage, state.currentCategory);
        } else {
            showToast(result.message || 'Erreur lors du partage', 'danger');
        }
    } catch (error) {
        logError('Erreur partage', error);
        showToast('Erreur lors du partage', 'danger');
    }
}

// ============================================================
// GESTION DES PUBLICATIONS (CRUD)
// ============================================================

function editPost(postId) {
    const post = state.posts.find(p => p.id === postId);
    if (!post) {
        showToast('Publication non trouvée', 'danger');
        return;
    }
    
    document.getElementById('postTitle').value = post.titre || '';
    document.getElementById('postCategory').value = post.categorie || 'conseil';
    document.getElementById('postContent').value = post.contenu || '';
    document.getElementById('publishError').style.display = 'none';
    
    resetFiles();
    
    const submitBtn = document.getElementById('publishSubmitBtn');
    submitBtn.innerHTML = '<i class="fas fa-save"></i> Mettre à jour';
    submitBtn.dataset.editId = postId;
    
    openPublishModal();
}

function deletePostConfirm(postId) {
    const post = state.posts.find(p => p.id === postId);
    if (!post) {
        showToast('Publication non trouvée', 'danger');
        return;
    }
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer "${post.titre}" ?`)) {
        deletePost(postId)
            .then(result => {
                if (result.status === 'success') {
                    showToast('Publication supprimée avec succès', 'success');
                    loadPosts(state.currentPage, state.currentCategory);
                } else {
                    showToast(result.message || 'Erreur lors de la suppression', 'danger');
                }
            })
            .catch(() => showToast('Erreur lors de la suppression', 'danger'));
    }
}

// ============================================================
// GESTION DES FICHIERS (UPLOAD)
// ============================================================

function handleFiles(files) {
    const fileArray = files instanceof FileList ? Array.from(files) : files;
    let errors = [];
    let validCount = 0;
    
    const currentCounts = {
        images: state.uploadedFiles.images.length,
        videos: state.uploadedFiles.videos.length,
        documents: state.uploadedFiles.documents.length
    };
    
    for (const file of fileArray) {
        const type = detectFileType(file);
        
        if (type === 'unknown') {
            errors.push(`❌ ${file.name}: Type non supporté`);
            continue;
        }
        
        const limit = FILE_LIMITS[type];
        if (currentCounts[type] >= limit.max) {
            errors.push(`❌ ${file.name}: Limite de ${limit.max} ${type} atteinte`);
            continue;
        }
        
        if (file.size > limit.maxSize) {
            const sizeMB = (limit.maxSize / (1024 * 1024)).toFixed(0);
            errors.push(`❌ ${file.name}: Taille max ${sizeMB} Mo dépassée`);
            continue;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const fileData = {
                name: file.name,
                size: file.size,
                type: type,
                mimeType: file.type,
                data: e.target.result,
                file: file,
                id: Date.now() + '_' + Math.random().toString(36).substr(2, 5)
            };
            
            state.uploadedFiles[type].push(fileData);
            currentCounts[type]++;
            validCount++;
            addFilePreview(fileData);
            updateFileStats();
        };
        reader.readAsDataURL(file);
    }
    
    errors.forEach(err => showToast(err, 'warning'));
    if (validCount === 0 && fileArray.length > 0) {
        showToast('⚠️ Aucun fichier valide ajouté', 'warning');
    }
}

function addFilePreview(fileData) {
    const container = document.getElementById('filePreviewContainer');
    
    const div = document.createElement('div');
    div.className = `file-preview-item file-type-${fileData.type}`;
    div.dataset.fileId = fileData.id;
    
    let previewContent = '';
    if (fileData.type === 'images') {
        previewContent = `<img src="${fileData.data}" alt="${fileData.name}" class="file-preview-image">`;
    } else if (fileData.type === 'videos') {
        previewContent = `
            <div class="file-preview-video">
                <i class="fas fa-play-circle"></i>
                <span>${fileData.name}</span>
            </div>
        `;
    } else {
        const icon = getFileIcon(fileData.name);
        previewContent = `
            <div class="file-preview-document">
                <i class="fas ${icon}"></i>
                <span class="file-name">${fileData.name}</span>
            </div>
        `;
    }
    
    const sizeLabel = fileData.size > 1024 * 1024 
        ? (fileData.size / (1024 * 1024)).toFixed(1) + ' Mo' 
        : (fileData.size / 1024).toFixed(1) + ' Ko';
    
    div.innerHTML = `
        <div class="file-preview-content">
            ${previewContent}
            <div class="file-info">
                <span class="file-name">${fileData.name}</span>
                <span class="file-size">${sizeLabel}</span>
            </div>
            <button class="remove-file" onclick="removeFile('${fileData.id}')" title="Supprimer">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    container.appendChild(div);
}

function removeFile(fileId) {
    for (const type of ['images', 'videos', 'documents']) {
        const index = state.uploadedFiles[type].findIndex(f => f.id === fileId);
        if (index > -1) {
            state.uploadedFiles[type].splice(index, 1);
            break;
        }
    }
    
    const item = document.querySelector(`.file-preview-item[data-file-id="${fileId}"]`);
    if (item) item.remove();
    updateFileStats();
}

function updateFileStats() {
    const total = state.uploadedFiles.images.length + 
                  state.uploadedFiles.videos.length + 
                  state.uploadedFiles.documents.length;
    
    const stats = document.getElementById('fileStats');
    if (total === 0) {
        stats.style.display = 'none';
        return;
    }
    
    stats.style.display = 'block';
    document.getElementById('fileCount').textContent = total;
    
    let totalSize = 0;
    ['images', 'videos', 'documents'].forEach(type => {
        state.uploadedFiles[type].forEach(f => totalSize += f.size);
    });
    document.getElementById('fileSize').textContent = (totalSize / (1024 * 1024)).toFixed(1) + ' Mo';
}

function resetFiles() {
    state.uploadedFiles = { images: [], videos: [], documents: [] };
    document.getElementById('filePreviewContainer').innerHTML = '';
    document.getElementById('fileStats').style.display = 'none';
    document.getElementById('postFiles').value = '';
}

// ============================================================
// MODALE IMAGE
// ============================================================

function openImageModal(postId, imageIndex) {
    const post = state.posts.find(p => p.id === postId);
    if (!post) return;
    
    state.currentImages = post.images || [];
    if (state.currentImages.length === 0) {
        showToast('Aucune image à afficher', 'info');
        return;
    }
    
    state.currentImageIndex = Math.min(imageIndex, state.currentImages.length - 1);
    
    const modal = document.getElementById('imageModal');
    document.getElementById('modalImage').src = state.currentImages[state.currentImageIndex] || '';
    document.getElementById('imageCounter').textContent = `${state.currentImageIndex + 1} / ${state.currentImages.length}`;
    
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
    
    document.getElementById('modalImage').src = state.currentImages[state.currentImageIndex] || '';
    document.getElementById('imageCounter').textContent = `${state.currentImageIndex + 1} / ${state.currentImages.length}`;
}

// ============================================================
// MODALE PUBLIER
// ============================================================

function openPublishModal() {
    document.getElementById('publishModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePublishModal() {
    document.getElementById('publishModal').style.display = 'none';
    document.body.style.overflow = '';
    
    const submitBtn = document.getElementById('publishSubmitBtn');
    submitBtn.dataset.editId = '';
    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Publier';
    document.getElementById('publishError').style.display = 'none';
}

// ============================================================
// SOUMISSION DU FORMULAIRE
// ============================================================

document.getElementById('publishForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('publishSubmitBtn');
    const errorDiv = document.getElementById('publishError');
    errorDiv.style.display = 'none';
    errorDiv.className = 'alert alert-danger';
    
    const title = document.getElementById('postTitle').value.trim();
    const category = document.getElementById('postCategory').value;
    const content = document.getElementById('postContent').value.trim();
    const editId = submitBtn.dataset.editId;
    
    if (!title) {
        showToast('⚠️ Veuillez saisir un titre', 'warning');
        document.getElementById('postTitle').focus();
        return;
    }
    
    if (title.length < 5) {
        showToast('⚠️ Le titre doit contenir au moins 5 caractères', 'warning');
        document.getElementById('postTitle').focus();
        return;
    }
    
    if (!content) {
        showToast('⚠️ Veuillez saisir un contenu', 'warning');
        document.getElementById('postContent').focus();
        return;
    }
    
    if (content.length < 10) {
        showToast('⚠️ Le contenu doit contenir au moins 10 caractères', 'warning');
        document.getElementById('postContent').focus();
        return;
    }
    
    const formData = new FormData();
    formData.append('titre', title);
    formData.append('categorie', category);
    formData.append('contenu', content);
    
    if (state.uploadedFiles.images.length > 0) {
        state.uploadedFiles.images.forEach(file => {
            formData.append('images[]', file.file);
        });
    }
    
    if (state.uploadedFiles.videos.length > 0) {
        state.uploadedFiles.videos.forEach(file => {
            formData.append('videos[]', file.file);
        });
    }
    
    if (state.uploadedFiles.documents.length > 0) {
        state.uploadedFiles.documents.forEach(file => {
            formData.append('documents[]', file.file);
        });
    }
    
    if (editId) {
        formData.append('_method', 'PUT');
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
    
    try {
        let endpoint = editId ? `/publications/${editId}` : '/publications';
        
        const result = await apiCall(endpoint, {
            method: 'POST',
            body: formData
        });
        
        if (result.status === 'success') {
            closePublishModal();
            resetFiles();
            document.getElementById('postTitle').value = '';
            document.getElementById('postContent').value = '';
            submitBtn.dataset.editId = '';
            
            const msg = editId ? '✅ Publication mise à jour avec succès !' : '✅ Article publié avec succès !';
            showToast(msg, 'success');
            
            setTimeout(() => loadPosts(1, state.currentCategory), 500);
        } else {
            if (result.errors) {
                let messages = [];
                for (const [field, errors] of Object.entries(result.errors)) {
                    messages.push(`📌 ${field}: ${errors.join(', ')}`);
                }
                errorDiv.innerHTML = messages.join('<br>');
                errorDiv.style.display = 'block';
                showToast('❌ Erreur de validation - Vérifiez les champs', 'danger');
                
                if (result.errors.titre) {
                    document.getElementById('postTitle').style.borderColor = '#dc3545';
                }
                if (result.errors.contenu) {
                    document.getElementById('postContent').style.borderColor = '#dc3545';
                }
            } else {
                errorDiv.textContent = result.message || 'Erreur lors de la publication';
                errorDiv.style.display = 'block';
                showToast(result.message || '❌ Erreur lors de la publication', 'danger');
            }
        }
    } catch (error) {
        logError('Erreur publication', error);
        
        if (error.errors) {
            let messages = [];
            for (const [field, errors] of Object.entries(error.errors)) {
                messages.push(`📌 ${field}: ${errors.join(', ')}`);
            }
            errorDiv.innerHTML = messages.join('<br>');
            errorDiv.style.display = 'block';
        } else {
            errorDiv.textContent = error.message || 'Erreur lors de la publication';
            errorDiv.style.display = 'block';
        }
        showToast(error.message || '❌ Erreur lors de la publication', 'danger');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = editId ? '<i class="fas fa-save"></i> Mettre à jour' : '<i class="fas fa-paper-plane"></i> Publier';
        
        document.getElementById('postTitle').style.borderColor = '';
        document.getElementById('postContent').style.borderColor = '';
    }
});

// ============================================================
// INITIALISATION
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    log('🚀 Initialisation du blog');
    
    if (!CONFIG.TOKEN) {
        showToast('Non connecté. Redirection...', 'danger');
        setTimeout(() => window.location.href = '/auth/login', 2000);
        return;
    }
    
    loadPosts(1, 'all');
    
    // Événements des onglets
    document.querySelectorAll('.tab-item').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active-tab'));
            this.classList.add('active-tab');
            
            state.currentCategory = this.dataset.tab;
            state.currentPage = 1;
            loadPosts(1, state.currentCategory);
        });
    });
    
    // Événements des modales
    document.getElementById('openPublishModal').addEventListener('click', openPublishModal);
    document.getElementById('closePublishModal').addEventListener('click', closePublishModal);
    document.getElementById('cancelPublish').addEventListener('click', closePublishModal);
    document.getElementById('closeImageModal').addEventListener('click', closeImageModal);
    
    // Navigation image
    document.getElementById('prevImageBtn').addEventListener('click', () => navigateImage(-1));
    document.getElementById('nextImageBtn').addEventListener('click', () => navigateImage(1));
    
    // Fermeture modales sur clic externe
    window.addEventListener('click', function(e) {
        if (e.target === document.getElementById('publishModal')) closePublishModal();
        if (e.target === document.getElementById('imageModal')) closeImageModal();
    });
    
    // Raccourcis clavier
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (document.getElementById('publishModal').style.display === 'flex') closePublishModal();
            if (document.getElementById('imageModal').style.display === 'flex') closeImageModal();
        }
        if (e.key === 'ArrowLeft' && document.getElementById('imageModal').style.display === 'flex') {
            navigateImage(-1);
        }
        if (e.key === 'ArrowRight' && document.getElementById('imageModal').style.display === 'flex') {
            navigateImage(1);
        }
    });
    
    // Pagination
    document.getElementById('prevPage').addEventListener('click', () => {
        if (state.currentPage > 1) goToPage(state.currentPage - 1);
    });
    document.getElementById('nextPage').addEventListener('click', () => {
        goToPage(state.currentPage + 1);
    });
    
    // Upload de fichiers
    const fileUploadArea = document.getElementById('fileUploadArea');
    const postFiles = document.getElementById('postFiles');
    
    if (fileUploadArea) {
        fileUploadArea.addEventListener('click', () => postFiles.click());
        fileUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#198754';
            this.style.background = '#e8f5e9';
        });
        fileUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#ced4da';
            this.style.background = '#f8f9fa';
        });
        fileUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#ced4da';
            this.style.background = '#f8f9fa';
            if (e.dataTransfer.files.length > 0) {
                handleFiles(e.dataTransfer.files);
            }
        });
    }
    
    if (postFiles) {
        postFiles.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleFiles(this.files);
            }
            this.value = '';
        });
    }
    
    // ✅ Fonction de debug dans la console
    window.testFetchPosts = async function() {
        console.log('🧪 TEST DE RÉCUPÉRATION DES PUBLICATIONS');
        try {
            const result = await fetchPosts(1, 'all');
            console.log('📦 Résultat complet:', result);
            
            console.log('🔍 Structures possibles:');
            console.log('  - result.data:', result.data);
            console.log('  - result.data.data:', result.data?.data);
            console.log('  - result.data.data[0]:', result.data?.data?.[0]);
            console.log('  - result.data[0]:', result.data?.[0]);
            
            let count = 0;
            if (Array.isArray(result.data)) {
                count = result.data.length;
            } else if (result.data && typeof result.data === 'object') {
                for (const key of Object.keys(result.data)) {
                    if (Array.isArray(result.data[key])) {
                        console.log(`  - result.data.${key}: ${result.data[key].length} éléments`);
                        count += result.data[key].length;
                    }
                }
            }
            console.log(`📊 Total éléments trouvés: ${count}`);
            
            return result;
        } catch (error) {
            console.error('❌ Erreur test:', error);
        }
    };
    
    log('✅ Blog initialisé avec succès');
});

// ============================================================
// STYLES DYNAMIQUES
// ============================================================

const style = document.createElement('style');
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
    
    .file-upload-area {
        border: 2px dashed #ced4da;
        border-radius: 8px;
        padding: 30px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }
    .file-upload-area:hover {
        border-color: #198754;
        background: #f0f8f0;
    }
    .file-upload-area i { font-size: 32px; color: #6c757d; margin-bottom: 10px; }
    .file-upload-area p { margin: 5px 0; color: #343a40; }
    .file-upload-area .file-hint { margin-top: 8px; }
    .file-upload-area .file-hint small { font-size: 12px; }
    
    .file-preview-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        margin-top: 15px;
    }
    .file-preview-item {
        position: relative;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .file-preview-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .file-preview-item .file-preview-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px;
        height: 100%;
    }
    .file-preview-item .file-preview-image {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 4px;
    }
    .file-preview-item .file-preview-video {
        width: 100%;
        height: 120px;
        background: #000;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        color: #fff;
    }
    .file-preview-item .file-preview-video i { font-size: 32px; margin-bottom: 5px; }
    .file-preview-item .file-preview-video span { font-size: 11px; color: #ccc; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding: 0 5px; }
    .file-preview-item .file-preview-document {
        width: 100%;
        height: 120px;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
    .file-preview-item .file-preview-document i { font-size: 32px; color: #198754; margin-bottom: 5px; }
    .file-preview-item .file-preview-document .file-name { font-size: 11px; color: #343a40; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding: 0 5px; }
    .file-preview-item .file-info { width: 100%; padding: 5px 0; text-align: center; }
    .file-preview-item .file-info .file-name { display: block; font-size: 12px; font-weight: 500; color: #343a40; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .file-preview-item .file-info .file-size { display: block; font-size: 10px; color: #6c757d; }
    .file-preview-item .remove-file {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 53, 69, 0.9);
        border: none;
        color: #fff;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        font-size: 12px;
        opacity: 0;
    }
    .file-preview-item:hover .remove-file { opacity: 1; }
    .file-preview-item .remove-file:hover { background: #dc3545; transform: scale(1.1); }
    .file-type-images .file-preview-content { background: #f0f8ff; }
    .file-type-videos .file-preview-content { background: #f8f0ff; }
    .file-type-documents .file-preview-content { background: #f0fff4; }
    
    .file-stats {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 6px;
    }
    .file-stats .badge { background: #198754; color: #fff; padding: 2px 10px; border-radius: 12px; font-size: 11px; }
    
    .post-video { width: 100%; max-height: 400px; border-radius: 8px; margin-top: 10px; background: #000; }
    .file-download-link {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 15px;
        margin-bottom: 8px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        text-decoration: none;
        color: #343a40;
        transition: all 0.3s;
    }
    .file-download-link:hover { background: #e9ecef; border-color: #198754; color: #198754; }
    
    .read-more-btn {
        color: #198754;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        margin-left: 4px;
        transition: all 0.3s;
        display: inline-block;
    }
    .read-more-btn:hover { color: #146c43; text-decoration: underline; }
    
    .empty-state { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; }
    .empty-state i { font-size: 48px; color: #6c757d; margin-bottom: 15px; }
    .empty-state h4 { font-size: 18px; color: #1a202c; margin-bottom: 8px; }
    .empty-state p { color: #6c757d; font-size: 14px; }
    
    .color-experience { color: #0dcaf0; }
    .color-conseil { color: #ffc107; }
    .color-alerte { color: #dc3545; }
    
    .post-title-badge {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .post-title-badge i { margin-right: 6px; }
    
    .overlay-plus {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        color: white;
        font-size: 24px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
    
    .grid-img-item {
        position: relative;
        flex: 1;
        min-width: 0;
        aspect-ratio: 4/3;
        overflow: hidden;
        border-radius: 8px;
        cursor: pointer;
    }
    .grid-img-item img { width: 100%; height: 100%; object-fit: cover; }
    .image-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 10px; }
    
    .action-edit, .action-delete {
        background: none;
        border: none;
        padding: 4px 8px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 13px;
        border-radius: 4px;
    }
    .action-edit { color: #856404; background: #fff3cd; }
    .action-edit:hover { background: #ffe69c; }
    .action-delete { color: #dc3545; background: #f8d7da; }
    .action-delete:hover { background: #f5c6cb; }
    
    .custom-post-admin {
        display: flex;
        gap: 6px;
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
    }
    .custom-post-card { position: relative; }
    
    .liked { color: #dc3545 !important; }
    .liked i { font-weight: 900 !important; }
    .trigger-btn.liked { background: rgba(220, 53, 69, 0.1); }
    
    @media (max-width: 768px) {
        .image-row { grid-template-columns: repeat(2, 1fr); }
        .blog-main-title { font-size: 1.2rem; }
        .blog-header-buttons { flex-wrap: wrap; gap: 8px; }
        .custom-post-admin { top: 6px; right: 6px; }
        .action-edit, .action-delete { padding: 2px 6px; font-size: 11px; }
        .file-preview-container { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
    }
    @media (max-width: 480px) {
        .image-row { grid-template-columns: 1fr 1fr; gap: 4px; }
    }
        
    .comments-section {
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .comment-item:last-child {
        border-bottom: none !important;
    }

    .comment-item:hover {
        background: rgba(0,0,0,0.02);
    }

    .counter-item {
        cursor: pointer;
        transition: all 0.2s;
        padding: 2px 6px;
        border-radius: 4px;
    }

    .counter-item:hover {
        background: rgba(0,0,0,0.05);
    }

    .counter-item i {
        transition: all 0.2s;
    }

    .counter-item:hover i {
        transform: scale(1.1);
    }

    .comment-form input:focus {
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.1);
    }
`;
document.head.appendChild(style);
</script>

@endsection