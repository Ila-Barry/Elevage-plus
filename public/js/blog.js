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
    ITEMS_PER_PAGE: 10
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
// ÉTAT
// ============================================================

const state = {
    posts: [],
    currentPage: 1,
    totalPages: 1,
    currentCategory: 'all',
    currentScope: 'all', // 'all' ou 'mine'
    currentImages: [],
    currentImageIndex: 0,
    uploadedFiles: { images: [], videos: [], documents: [] },
    isLoading: false,
    isEditing: false,
    editId: null
};

// ============================================================
// UTILITAIRES
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

function getFileIcon(filename) {
    if (!filename) return 'fas fa-file';
    var ext = filename.split('.').pop().toLowerCase();
    var icons = {
        'pdf': 'fas fa-file-pdf',
        'doc': 'fas fa-file-word',
        'docx': 'fas fa-file-word',
        'xls': 'fas fa-file-excel',
        'xlsx': 'fas fa-file-excel',
        'ppt': 'fas fa-file-powerpoint',
        'pptx': 'fas fa-file-powerpoint',
        'txt': 'fas fa-file-alt',
        'zip': 'fas fa-file-archive',
        'rar': 'fas fa-file-archive',
        'jpg': 'fas fa-file-image',
        'jpeg': 'fas fa-file-image',
        'png': 'fas fa-file-image',
        'gif': 'fas fa-file-image',
        'webp': 'fas fa-file-image',
        'mp4': 'fas fa-file-video',
        'avi': 'fas fa-file-video',
        'mov': 'fas fa-file-video',
    };
    return icons[ext] || 'fas fa-file';
}

function detectFileType(file) {
    var type = file.type;
    var ext = file.name.split('.').pop().toLowerCase();
    
    if (type.startsWith('image/')) return 'images';
    if (type.startsWith('video/')) return 'videos';
    
    var docExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];
    if (docExts.indexOf(ext) !== -1) return 'documents';
    
    var docMimes = ['application/pdf', 'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain', 'application/zip', 'application/x-rar-compressed'];
    if (docMimes.indexOf(type) !== -1) return 'documents';
    
    return 'unknown';
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
    }, 5000);
}

async function apiCall(endpoint, options) {
    options = options || {};
    var defaultHeaders = {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + CONFIG.TOKEN,
        'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN
    };
    
    var isFormData = options.body instanceof FormData;
    
    // ✅ CRÉER UN NOUVEL OBJET HEADERS
    var headers = {};
    
    // ✅ AJOUTER LES HEADERS PAR DÉFAUT (sauf Content-Type pour FormData)
    for (var key in defaultHeaders) {
        if (defaultHeaders.hasOwnProperty(key)) {
            // ✅ Pour FormData, on NE met PAS Content-Type
            if (key === 'Content-Type' && isFormData) {
                continue;
            }
            headers[key] = defaultHeaders[key];
        }
    }
    
    // ✅ AJOUTER LES HEADERS PERSONNALISÉS
    if (options.headers) {
        for (var key in options.headers) {
            if (options.headers.hasOwnProperty(key)) {
                // ✅ Pour FormData, on NE met PAS Content-Type
                if (key === 'Content-Type' && isFormData) {
                    continue;
                }
                headers[key] = options.headers[key];
            }
        }
    }
    
    var config = {
        method: options.method || 'GET',
        headers: headers
    };
    
    // ✅ AJOUTER LE BODY (NE PAS STRINGIFIER LE FORMDATA)
    if (options.body !== undefined) {
        config.body = options.body;
    }
    
    var url = endpoint.indexOf('http') === 0 ? endpoint : CONFIG.API_URL + endpoint;
    log('🌐 Requête ' + (options.method || 'GET') + ' ' + url);
    
    // ✅ LOG POUR DÉBOGUER
    console.log('📤 CONFIG:', {
        url: url,
        method: config.method,
        headers: config.headers,
        isFormData: isFormData,
        bodyType: options.body ? (options.body instanceof FormData ? 'FormData' : typeof options.body) : 'undefined'
    });
    
    try {
        var response = await fetch(url, config);
        
        var rawText = await response.text();
        console.log('📄 RÉPONSE BRUTE (premiers 500 caractères):', rawText.substring(0, 500));
        console.log('📄 STATUS:', response.status);
        console.log('📄 HEADERS Content-Type:', response.headers.get('content-type'));
        
        var data;
        try {
            data = JSON.parse(rawText);
        } catch (parseError) {
            console.error('❌ ÉCHEC DU PARSING JSON:', parseError);
            console.error('📄 CONTENU COMPLET REÇU:', rawText);
            throw new Error('La réponse du serveur n\'est pas du JSON valide. Status: ' + response.status);
        }
        
        if (!response.ok) {
            logError('❌ Erreur HTTP ' + response.status, data);
            if (data.errors) {
                console.error('🔍 DÉTAIL DES ERREURS DE VALIDATION:');
                for (var field in data.errors) {
                    if (data.errors.hasOwnProperty(field)) {
                        console.error('   📌 ' + field + ':', data.errors[field]);
                    }
                }
            }
            var error = new Error(data.message || 'Erreur API');
            error.status = response.status;
            error.errors = data.errors;
            error.data = data;
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
// API CALLS
// ============================================================

async function fetchPosts(page, category, scope) {
    page = page || 1;
    category = category || 'all';
    scope = scope || 'all';
    log('📤 Récupération des publications page ' + page + ', catégorie ' + category + ', scope ' + scope);
    var url = '/publications?page=' + page + '&per_page=' + CONFIG.ITEMS_PER_PAGE;
    if (category !== 'all') url += '&categorie=' + category;
    if (scope === 'mine') url += '&scope=mine';
    return apiCall(url);
}

async function createPost(formData) {
    log('📤 Création d\'une nouvelle publication');
    // ✅ Vérifier que le FormData contient les données
    console.log('📤 ENVOI DU FORMDATA:');
    for (var pair of formData.entries()) {
        if (pair[1] instanceof File) {
            console.log('   📎 ' + pair[0] + ':', pair[1].name);
        } else {
            console.log('   📝 ' + pair[0] + ':', pair[1]);
        }
    }
    return apiCall('/publications', {
        method: 'POST',
        body: formData
    });
}

async function updatePost(id, formData) {
    log('📤 Mise à jour de la publication ' + id);
    formData.append('_method', 'PUT');
    return apiCall('/publications/' + id, {
        method: 'POST',
        body: formData
    });
}

async function deletePost(id) {
    log('📤 Suppression de la publication ' + id);
    return apiCall('/publications/' + id, {
        method: 'DELETE'
    });
}

// ============================================================
// CHARGEMENT DES PUBLICATIONS
// ============================================================

async function loadPosts(page, category, scope) {
    page = page || 1;
    category = category || state.currentCategory;
    scope = scope || state.currentScope;
    
    if (state.isLoading) return;
    state.isLoading = true;
    
    var container = document.getElementById('postsFeed');
    container.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2 text-muted">Chargement des publications...</p>
        </div>
    `;
    
    try {
        var result = await fetchPosts(page, category, scope);
        
        var postsData = [];
        var meta = { current_page: page, last_page: 1 };
        
        if (result.data) {
            if (Array.isArray(result.data)) {
                postsData = result.data;
            } else if (result.data.data && Array.isArray(result.data.data)) {
                postsData = result.data.data;
                meta = result.data.meta || meta;
            }
        }
        
        state.posts = postsData.map(function(post) {
            return {
                id: post.id,
                titre: post.titre || 'Sans titre',
                categorie: post.categorie || 'conseil',
                categorie_label: post.categorie_label || 'Conseil',
                contenu: post.contenu || '',
                resume: post.resume || truncateText(post.contenu || '', 200),
                images: post.images && Array.isArray(post.images) ? post.images.map(fixImageUrl).filter(function(u) { return u; }) : [],
                videos: post.videos && Array.isArray(post.videos) ? post.videos.map(fixImageUrl).filter(function(u) { return u; }) : [],
                documents: post.documents && Array.isArray(post.documents) ? post.documents.map(function(d) {
                    return { url: fixImageUrl(d.url), nom: d.nom || 'Fichier' };
                }).filter(function(d) { return d.url; }) : [],
                statistiques: post.statistiques || { likes: 0, commentaires: 0, partages: 0, vues: 0 },
                interactions: post.interactions || { liked_by_user: false },
                user: post.user || { name: 'Utilisateur', photo_url: null, role: 'user' },
                can_manage: post.can_manage || false,
                published_at_human: post.published_at_human || 'N/A',
                published_at: post.published_at,
                created_at: post.created_at,
                updated_at: post.updated_at
            };
        });
        
        state.currentPage = meta.current_page || page;
        state.totalPages = meta.last_page || 1;
        
        log('✅ ' + state.posts.length + ' publications chargées');
        renderPosts();
        updatePagination();
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
// AFFICHAGE
// ============================================================

function renderPosts() {
    var container = document.getElementById('postsFeed');
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
    
    var html = '';
    for (var i = 0; i < state.posts.length; i++) {
        var post = state.posts[i];
        var displayContent = post.resume || truncateText(post.contenu || '', 200);
        var isTruncated = (post.contenu || '').length > 200;
        
        var images = post.images || [];
        var videos = post.videos || [];
        var documents = post.documents || [];
        
        var authorName = post.user?.name || 'Utilisateur';
        var authorAvatar = getAvatarUrl(post.user?.photo_url, authorName);
        var authorRole = post.user?.role === 'admin' ? 'Administrateur' : 'Éleveur';
        
        var categoryIcon = post.categorie === 'experience' ? '💡' : 
                            post.categorie === 'alerte' ? '⚠️' : '🌾';
        
        var contentHtml = displayContent;
        if (isTruncated) {
            contentHtml = displayContent + ' <span class="read-more-btn" onclick="toggleFullContent(' + post.id + ')">plus...</span>';
        }
        
        var imagesHtml = images.length > 0 ? renderImages(images, post.id) : '';
        var videosHtml = videos.length > 0 ? renderVideos(videos) : '';
        var documentsHtml = documents.length > 0 ? renderDocuments(documents) : '';
        
        var adminButtons = post.can_manage ? `
            <button class="action-edit" onclick="openEditModal(` + post.id + `)" title="Modifier">
                <i class="fas fa-pencil-alt"></i>
            </button>
            <button class="action-delete" onclick="confirmDelete(` + post.id + `)" title="Supprimer">
                <i class="fas fa-times"></i>
            </button>
        ` : '';
        
        html += `
        <article class="custom-post-card" data-id="` + post.id + `">
            <div class="custom-post-admin">
                ` + adminButtons + `
            </div>
            <div class="custom-post-header">
                <div class="author-avatar-box">
                    <img src="` + authorAvatar + `" 
                         alt="` + authorName + `" 
                         class="rounded-circle" 
                         onerror="this.src='https://ui-avatars.com/api/?name=User&background=4F46E5&color=fff'">
                </div>
                <div class="author-meta-data">
                    <span class="user-fullname">` + escapeHtml(authorName) + `</span>
                    <span class="meta-separator">•</span>
                    <span class="user-profession">` + authorRole + `</span>
                    <span class="meta-separator">•</span>
                    <span class="published-time">
                        <i class="far fa-calendar-alt"></i> ` + (post.published_at_human || 'N/A') + `
                    </span>
                </div>
            </div>
            <div class="custom-post-body">
                <h3 class="post-title-badge color-` + post.categorie + `">
                    ` + categoryIcon + ` ` + (post.categorie_label || post.categorie) + ` - ` + escapeHtml(post.titre) + `
                </h3>
                <p class="post-text-content" data-full="false">
                    ` + contentHtml + `
                </p>
                ` + imagesHtml + `
                ` + videosHtml + `
                ` + documentsHtml + `
            </div>
            
            <div class="custom-post-footer">
                <div class="post-counters">
                    <!-- ✅ LIKES -->
                    <span class="counter-item counter-item-like" onclick="toggleLike(` + post.id + `)" style="cursor: pointer;">
                        <i class="` + (post.interactions?.liked_by_user ? 'fas fa-heart text-danger' : 'far fa-heart') + `"></i> 
                        <span class="likes-count" id="likes-count-` + post.id + `">` + (post.statistiques?.likes || 0) + `</span>
                    </span>
                    
                    <!-- ✅ COMMENTAIRES -->
                    <span class="counter-item" onclick="toggleComments(` + post.id + `)" style="cursor: pointer;">
                        <i class="far fa-comment"></i> 
                        <span class="comments-count" id="comments-count-` + post.id + `">` + (post.statistiques?.commentaires || 0) + `</span>
                    </span>
                    
                    <!-- ✅ PARTAGES -->
                    <span class="counter-item" style="cursor: default;">
                        <i class="fas fa-share-alt"></i> 
                        <span class="shares-count" id="shares-count-` + post.id + `">` + (post.statistiques?.partages || 0) + `</span>
                    </span>
                </div>
                
                <div class="post-action-triggers">
                    <!-- ✅ BOUTON LIKE -->
                    <button class="trigger-btn like-btn ` + (post.interactions?.liked_by_user ? 'liked' : '') + `" onclick="toggleLike(` + post.id + `)">
                        <i class="` + (post.interactions?.liked_by_user ? 'fas' : 'far') + ` fa-heart"></i> 
                        ` + (post.interactions?.liked_by_user ? 'Aimé' : 'Liker') + `
                    </button>
                    
                    <!-- ✅ BOUTON COMMENTAIRE -->
                    <button class="trigger-btn" onclick="toggleComments(` + post.id + `)">
                        <i class="far fa-comment"></i> Commenter
                    </button>
                    
                    <!-- ✅ BOUTON PARTAGER -->
                    <button class="trigger-btn" onclick="handleShare(` + post.id + `)">
                        <i class="fas fa-share-alt"></i> Partager
                    </button>
                </div>
            </div>

            <!-- ✅ SECTION COMMENTAIRES -->
            <div class="comments-section" id="comments-list-` + post.id + `" style="display: none; padding: 15px 20px; border-top: 1px solid #e9ecef; background: #f8f9fa; border-radius: 0 0 8px 8px;">
                <div class="comments-list" id="comments-list-inner-` + post.id + `">
                    <div class="text-center text-muted py-2" style="font-size: 13px;">
                        <i class="fas fa-spinner fa-spin"></i> Chargement des commentaires...
                    </div>
                </div>
                <form class="comment-form" id="comment-form-` + post.id + `" data-post-id="` + post.id + `" style="margin-top: 12px;" onsubmit="event.preventDefault(); addComment(` + post.id + `);">
                    <div class="d-flex gap-2" style="display: flex; gap: 8px;">
                        <input type="text" 
                            class="form-control form-control-sm" 
                            placeholder="Écrire un commentaire..." 
                            id="comment-input-` + post.id + `"
                            style="border-radius: 20px; font-size: 13px; flex: 1;">
                        <button type="submit" class="btn btn-sm btn-success" style="border-radius: 20px; white-space: nowrap;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </article>`;
    }
    container.innerHTML = html;
}

function renderImages(images, postId) {
    if (!images || images.length === 0) return '';
    var validImages = images.filter(function(img) { return img && img.trim(); });
    if (validImages.length === 0) return '';
    
    var displayImages = validImages.slice(0, 4);
    var remaining = validImages.length - 4;
    
    var html = '<div class="image-row">';
    for (var j = 0; j < displayImages.length; j++) {
        var overlay = (j === 3 && remaining > 0) ? '<div class="overlay-plus">+' + remaining + '</div>' : '';
        html += `
            <div class="grid-img-item" onclick="openImageModal(` + postId + `, ` + j + `)">
                <img src="` + displayImages[j] + `" alt="Image ` + (j + 1) + `" loading="lazy" onerror="this.style.display='none'">
                ` + overlay + `
            </div>
        `;
    }
    html += '</div>';
    if (validImages.length > 4) {
        html += '<div style="font-size: 0.7rem; color: #6c757d; margin-top: 4px;">+ ' + (validImages.length - 4) + ' autre(s) image(s)</div>';
    }
    return html;
}

function renderVideos(videos) {
    if (!videos || videos.length === 0) return '';
    var validVideos = videos.filter(function(v) { return v && v.trim(); });
    if (validVideos.length === 0) return '';
    
    var html = '<div class="post-videos-container" style="margin-top: 10px;">';
    for (var j = 0; j < validVideos.length; j++) {
        html += `
            <div style="margin-bottom: 8px;">
                <video controls class="post-video" preload="metadata" style="width: 100%; max-height: 400px; border-radius: 8px; background: #000;">
                    <source src="` + validVideos[j] + `" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture de vidéos.
                </video>
                <div style="font-size: 0.7rem; color: #6c757d; margin-top: 2px;">Vidéo ` + (j + 1) + `</div>
            </div>
        `;
    }
    html += '</div>';
    return html;
}

function renderDocuments(documents) {
    if (!documents || documents.length === 0) return '';
    var validDocs = documents.filter(function(d) { return d && d.url; });
    if (validDocs.length === 0) return '';
    
    var html = '<div class="post-files-container" style="margin-top: 10px;">';
    for (var j = 0; j < validDocs.length; j++) {
        var doc = validDocs[j];
        var icon = getFileIcon(doc.nom);
        var displayName = doc.nom || 'Fichier ' + (j + 1);
        html += `
            <a href="` + doc.url + `" target="_blank" class="file-download-link" download 
               style="display: inline-flex; align-items: center; gap: 10px; padding: 8px 15px; margin-bottom: 6px; background: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6; text-decoration: none; color: #343a40; transition: all 0.3s;">
                <i class="` + icon + `" style="font-size: 18px; color: #198754;"></i>
                <span style="font-weight: 500;">` + escapeHtml(displayName) + `</span>
                <i class="fas fa-download" style="margin-left: auto; color: #6c757d;"></i>
            </a>
        `;
    }
    html += '</div>';
    return html;
}

// ============================================================
// PAGINATION
// ============================================================

function updatePagination() {
    var pageNumbers = document.getElementById('pageNumbers');
    var prevBtn = document.getElementById('prevPage');
    var nextBtn = document.getElementById('nextPage');
    
    if (state.totalPages <= 1) {
        pageNumbers.innerHTML = '';
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        return;
    }
    
    var html = '';
    var maxVisible = 5;
    var start = Math.max(1, state.currentPage - Math.floor(maxVisible / 2));
    var end = Math.min(state.totalPages, start + maxVisible - 1);
    
    if (end - start < maxVisible - 1) {
        start = Math.max(1, end - maxVisible + 1);
    }
    
    if (start > 1) {
        html += '<span class="num-item" onclick="goToPage(1)">1</span>';
        if (start > 2) html += '<span class="num-item">...</span>';
    }
    
    for (var i = start; i <= end; i++) {
        html += '<span class="num-item ' + (i === state.currentPage ? 'active-num' : '') + '" onclick="goToPage(' + i + ')">' + i + '</span>';
    }
    
    if (end < state.totalPages) {
        if (end < state.totalPages - 1) html += '<span class="num-item">...</span>';
        html += '<span class="num-item" onclick="goToPage(' + state.totalPages + ')">' + state.totalPages + '</span>';
    }
    
    pageNumbers.innerHTML = html;
    prevBtn.disabled = state.currentPage === 1;
    nextBtn.disabled = state.currentPage === state.totalPages;
}

function goToPage(page) {
    if (page === state.currentPage || state.isLoading) return;
    state.currentPage = page;
    loadPosts(page);
    document.getElementById('postsFeed').scrollIntoView({ behavior: 'smooth', block: 'start' });
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
    
    img.src = state.currentImages[state.currentImageIndex] || '';
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
    document.getElementById('modalImage').src = state.currentImages[state.currentImageIndex] || '';
    document.getElementById('imageCounter').textContent = (state.currentImageIndex + 1) + ' / ' + state.currentImages.length;
}

// ============================================================
// GESTION DES FICHIERS
// ============================================================

function handleFiles(files) {
    var fileArray = files instanceof FileList ? Array.from(files) : files;
    var errors = [];
    var validCount = 0;
    
    // Compter les fichiers actuels par type
    var currentCounts = {
        images: state.uploadedFiles.images.length,
        videos: state.uploadedFiles.videos.length,
        documents: state.uploadedFiles.documents.length
    };
    
    for (var i = 0; i < fileArray.length; i++) {
        var file = fileArray[i];
        var type = detectFileType(file);
        
        if (type === 'unknown') {
            errors.push('❌ ' + file.name + ': Type non supporté');
            continue;
        }
        
        var limit = FILE_LIMITS[type];
        if (currentCounts[type] >= limit.max) {
            errors.push('❌ ' + file.name + ': Limite de ' + limit.max + ' ' + type + ' atteinte');
            continue;
        }
        
        if (file.size > limit.maxSize) {
            var sizeMB = (limit.maxSize / (1024 * 1024)).toFixed(0);
            errors.push('❌ ' + file.name + ': Taille max ' + sizeMB + ' Mo dépassée');
            continue;
        }
        
        // ✅ CRÉER L'OBJET FICHIER SANS LIRE LE CONTENU EN BASE64
        var fileData = {
            name: file.name,
            size: file.size,
            type: type,
            mimeType: file.type,
            file: file,  // ✅ Conserver l'objet File original
            id: Date.now() + '_' + Math.random().toString(36).substr(2, 5)
        };
        
        state.uploadedFiles[type].push(fileData);
        currentCounts[type]++;
        validCount++;
        
        // ✅ AJOUTER LA PRÉVISUALISATION
        addFilePreview(fileData);
        updateFileStats();
    }
    
    // ✅ RÉINITIALISER LE CHAMP INPUT
    document.getElementById('postFiles').value = '';
    
    if (errors.length > 0) {
        for (var j = 0; j < errors.length; j++) {
            showToast(errors[j], 'warning');
        }
    }
    
    if (validCount > 0) {
        showToast('✅ ' + validCount + ' fichier(s) ajouté(s)', 'success');
    }
}

function addFilePreview(fileData) {
    var container = document.getElementById('filePreviewContainer');
    var div = document.createElement('div');
    div.className = 'file-preview-item file-type-' + fileData.type;
    div.dataset.fileId = fileData.id;
    
    var previewContent = '';
    var file = fileData.file;
    
    // ✅ CRÉER UNE URL POUR LA PRÉVISUALISATION
    var objectUrl = URL.createObjectURL(file);
    
    if (fileData.type === 'images') {
        previewContent = '<img src="' + objectUrl + '" alt="' + fileData.name + '" class="file-preview-image" style="width:100%;height:120px;object-fit:cover;border-radius:4px;">';
    } else if (fileData.type === 'videos') {
        previewContent = `
            <div class="file-preview-video" style="width:100%;height:120px;background:#000;display:flex;flex-direction:column;align-items:center;justify-content:center;border-radius:4px;color:#fff;">
                <video src="` + objectUrl + `" style="width:100%;height:100%;object-fit:cover;" muted></video>
                <span style="font-size:11px;color:#ccc;margin-top:4px;">` + fileData.name + `</span>
            </div>
        `;
    } else {
        var icon = getFileIcon(fileData.name);
        previewContent = `
            <div class="file-preview-document" style="width:100%;height:120px;background:#f8f9fa;display:flex;flex-direction:column;align-items:center;justify-content:center;border-radius:4px;border:1px solid #dee2e6;">
                <i class="fas ` + icon + `" style="font-size:32px;color:#198754;margin-bottom:5px;"></i>
                <span class="file-name" style="font-size:11px;color:#343a40;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;padding:0 5px;">` + fileData.name + `</span>
            </div>
        `;
    }
    
    var sizeLabel = fileData.size > 1024 * 1024 
        ? (fileData.size / (1024 * 1024)).toFixed(1) + ' Mo' 
        : (fileData.size / 1024).toFixed(1) + ' Ko';
    
    div.innerHTML = `
        <div class="file-preview-content" style="display:flex;flex-direction:column;align-items:center;padding:10px;height:100%;">
            ` + previewContent + `
            <div class="file-info" style="width:100%;padding:5px 0;text-align:center;">
                <span class="file-name" style="display:block;font-size:12px;font-weight:500;color:#343a40;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">` + fileData.name + `</span>
                <span class="file-size" style="display:block;font-size:10px;color:#6c757d;">` + sizeLabel + `</span>
            </div>
            <button class="remove-file" onclick="removeFile('` + fileData.id + `')" title="Supprimer" style="position:absolute;top:5px;right:5px;background:rgba(220,53,69,0.9);border:none;color:#fff;width:24px;height:24px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;font-size:12px;opacity:0;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    
    // ✅ AFFICHER LE BOUTON SUPPRIMER AU SURVOL
    div.addEventListener('mouseenter', function() {
        var btn = this.querySelector('.remove-file');
        if (btn) btn.style.opacity = '1';
    });
    div.addEventListener('mouseleave', function() {
        var btn = this.querySelector('.remove-file');
        if (btn) btn.style.opacity = '0';
    });
}

function removeFile(fileId) {
    for (var type in state.uploadedFiles) {
        if (state.uploadedFiles.hasOwnProperty(type)) {
            var index = state.uploadedFiles[type].findIndex(function(f) { return f.id === fileId; });
            if (index > -1) {
                // ✅ NETTOYER L'URL OBJET SI ELLE EXISTE
                var file = state.uploadedFiles[type][index];
                if (file._objectUrl) {
                    URL.revokeObjectURL(file._objectUrl);
                }
                state.uploadedFiles[type].splice(index, 1);
                break;
            }
        }
    }
    var item = document.querySelector('.file-preview-item[data-file-id="' + fileId + '"]');
    if (item) item.remove();
    updateFileStats();
}

function updateFileStats() {
    var total = state.uploadedFiles.images.length + 
                state.uploadedFiles.videos.length + 
                state.uploadedFiles.documents.length;
    var stats = document.getElementById('fileStats');
    if (total === 0) {
        stats.style.display = 'none';
        return;
    }
    stats.style.display = 'block';
    document.getElementById('fileCount').textContent = total;
    var totalSize = 0;
    ['images', 'videos', 'documents'].forEach(function(type) {
        state.uploadedFiles[type].forEach(function(f) { totalSize += f.size; });
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
// MODALE PUBLICATION
// ============================================================

function openPublishModal() {
    state.isEditing = false;
    state.editId = null;
    document.getElementById('publishModalTitle').textContent = 'Publier un article';
    document.getElementById('publishSubmitBtn').innerHTML = '<i class="fas fa-paper-plane"></i> Publier';
    document.getElementById('publishForm').reset();
    resetFiles();
    document.getElementById('publishError').style.display = 'none';
    document.getElementById('publishModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function openEditModal(postId) {
    var post = state.posts.find(function(p) { return p.id === postId; });
    if (!post) {
        showToast('Publication non trouvée', 'danger');
        return;
    }
    
    state.isEditing = true;
    state.editId = postId;
    
    document.getElementById('publishModalTitle').textContent = 'Modifier l\'article';
    document.getElementById('publishSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Mettre à jour';
    document.getElementById('postTitle').value = post.titre || '';
    document.getElementById('postCategory').value = post.categorie || 'conseil';
    document.getElementById('postContent').value = post.contenu || '';
    document.getElementById('publishError').style.display = 'none';
    resetFiles();
    
    document.getElementById('publishModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePublishModal() {
    document.getElementById('publishModal').style.display = 'none';
    document.body.style.overflow = '';
    state.isEditing = false;
    state.editId = null;
}

function confirmDelete(postId) {
    var post = state.posts.find(function(p) { return p.id === postId; });
    if (!post) {
        showToast('Publication non trouvée', 'danger');
        return;
    }
    if (confirm('Êtes-vous sûr de vouloir supprimer "' + post.titre + '" ? Cette action est irréversible.')) {
        deletePost(postId)
            .then(function(result) {
                if (result.status === 'success') {
                    showToast('Publication supprimée avec succès', 'success');
                    loadPosts(state.currentPage);
                } else {
                    showToast(result.message || 'Erreur lors de la suppression', 'danger');
                }
            })
            .catch(function(error) {
                showToast(error.message || 'Erreur lors de la suppression', 'danger');
            });
    }
}

function toggleFullContent(postId) {
    var post = state.posts.find(function(p) { return p.id === postId; });
    if (!post) {
        showToast('Publication non trouvée', 'danger');
        return;
    }
    
    var postCard = document.querySelector('.custom-post-card[data-id="' + postId + '"]');
    if (!postCard) {
        showToast('Élément non trouvé', 'warning');
        return;
    }
    
    var contentParagraph = postCard.querySelector('.post-text-content');
    if (!contentParagraph) return;
    
    var isFull = contentParagraph.dataset.full === 'true';
    if (isFull) {
        var resume = post.resume || truncateText(post.contenu || '', 200);
        contentParagraph.innerHTML = resume + ' <span class="read-more-btn" onclick="toggleFullContent(' + postId + ')">plus...</span>';
        contentParagraph.dataset.full = 'false';
    } else {
        var fullContent = post.contenu || 'Contenu non disponible';
        var formattedContent = fullContent.replace(/\n/g, '<br>');
        contentParagraph.innerHTML = formattedContent + ' <span class="read-more-btn" onclick="toggleFullContent(' + postId + ')">moins...</span>';
        contentParagraph.dataset.full = 'true';
    }
}

// ============================================================
// SOUMISSION DU FORMULAIRE - CORRIGÉ
// ============================================================

function handlePublishSubmit(event) {
    event.preventDefault();
    
    console.log('🚀 FORMULAIRE SOUMIS !');
    
    var titleInput = document.getElementById('postTitle');
    var categorySelect = document.getElementById('postCategory');
    var contentTextarea = document.getElementById('postContent');
    var submitBtn = document.getElementById('publishSubmitBtn');
    var errorDiv = document.getElementById('publishError');
    
    if (!titleInput || !categorySelect || !contentTextarea) {
        console.error('❌ Éléments du formulaire manquants !');
        showToast('❌ Erreur technique: éléments manquants', 'danger');
        return;
    }
    
    var title = titleInput.value.trim();
    var category = categorySelect.value;
    var content = contentTextarea.value.trim();
    
    console.log('🔍 VALEURS RÉCUPÉRÉES:');
    console.log('   📝 Titre: "' + title + '" (longueur: ' + title.length + ')');
    console.log('   📂 Catégorie: "' + category + '"');
    console.log('   📄 Contenu: "' + content.substring(0, 30) + '..." (longueur: ' + content.length + ')');
    
    if (!title || title === '' || title.length < 5) {
        showToast('⚠️ Le titre doit contenir au moins 5 caractères', 'warning');
        titleInput.focus();
        return;
    }
    
    if (!content || content === '' || content.length < 10) {
        showToast('⚠️ Le contenu doit contenir au moins 10 caractères', 'warning');
        contentTextarea.focus();
        return;
    }
    
    // ✅ CONSTRUCTION DU FORMDATA
    var formData = new FormData();
    formData.append('titre', title);
    formData.append('categorie', category);
    formData.append('contenu', content);
    
    // ✅ AJOUT DES FICHIERS
    if (state.uploadedFiles.images.length > 0) {
        console.log('📷 Images à uploader:', state.uploadedFiles.images.length);
        state.uploadedFiles.images.forEach(function(file, index) {
            formData.append('images[]', file.file);
        });
    }
    
    if (state.uploadedFiles.videos.length > 0) {
        console.log('🎬 Vidéos à uploader:', state.uploadedFiles.videos.length);
        state.uploadedFiles.videos.forEach(function(file, index) {
            formData.append('videos[]', file.file);
        });
    }
    
    if (state.uploadedFiles.documents.length > 0) {
        console.log('📄 Documents à uploader:', state.uploadedFiles.documents.length);
        state.uploadedFiles.documents.forEach(function(file, index) {
            formData.append('documents[]', file.file);
        });
    }
    
    // ✅ AFFICHER LE CONTENU DU FORMDATA
    console.log('📦 CONTENU DU FORMDATA:');
    for (var pair of formData.entries()) {
        if (pair[1] instanceof File) {
            console.log('   📎 ' + pair[0] + ':', pair[1].name + ' (' + pair[1].size + ' bytes)');
        } else {
            console.log('   📝 ' + pair[0] + ':', pair[1]);
        }
    }
    
    // Désactiver le bouton
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
    errorDiv.style.display = 'none';
    errorDiv.className = 'alert alert-danger';
    
    // ✅ ENVOI
    var promise;
    if (state.isEditing && state.editId) {
        formData.append('_method', 'PUT');
        promise = apiCall('/publications/' + state.editId, {
            method: 'POST',
            body: formData
        });
    } else {
        promise = apiCall('/publications', {
            method: 'POST',
            body: formData
        });
    }
    
    promise
        .then(function(result) {
            console.log('✅ RÉSULTAT:', result);
            if (result.status === 'success') {
                closePublishModal();
                resetFiles();
                titleInput.value = '';
                contentTextarea.value = '';
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Publier';
                showToast(state.isEditing ? '✅ Publication mise à jour !' : '✅ Article publié !', 'success');
                loadPosts(1);
            } else {
                if (result.errors) {
                    var messages = [];
                    for (var field in result.errors) {
                        if (result.errors.hasOwnProperty(field)) {
                            messages.push('📌 ' + field + ': ' + result.errors[field].join(', '));
                        }
                    }
                    errorDiv.innerHTML = messages.join('<br>');
                    errorDiv.style.display = 'block';
                    showToast('❌ Erreur de validation', 'danger');
                } else {
                    errorDiv.textContent = result.message || 'Erreur';
                    errorDiv.style.display = 'block';
                    showToast(result.message || '❌ Erreur', 'danger');
                }
            }
        })
        .catch(function(error) {
            console.error('❌ ERREUR:', error);
            if (error.errors) {
                var messages = [];
                for (var field in error.errors) {
                    if (error.errors.hasOwnProperty(field)) {
                        messages.push('📌 ' + field + ': ' + error.errors[field].join(', '));
                    }
                }
                errorDiv.innerHTML = messages.join('<br>');
                errorDiv.style.display = 'block';
            } else {
                errorDiv.textContent = error.message || 'Erreur';
                errorDiv.style.display = 'block';
            }
            showToast(error.message || '❌ Erreur', 'danger');
        })
        .finally(function() {
            submitBtn.disabled = false;
            if (state.isEditing) {
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Mettre à jour';
            } else {
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Publier';
            }
        });
}

// ============================================================
// GESTION DES COMMENTAIRES
// ============================================================

/**
 * Basculer l'affichage des commentaires
 */
function toggleComments(postId) {
    var commentsSection = document.getElementById('comments-list-' + postId);
    if (!commentsSection) return;
    
    var isVisible = commentsSection.style.display !== 'none';
    if (isVisible) {
        commentsSection.style.display = 'none';
    } else {
        commentsSection.style.display = 'block';
        // Charger les commentaires
        loadComments(postId);
    }
}

/**
 * Charger les commentaires d'une publication
 */
async function loadComments(postId) {
    var container = document.getElementById('comments-list-inner-' + postId);
    if (!container) return;
    
    // Afficher un loader
    container.innerHTML = `
        <div class="text-center text-muted py-2" style="font-size: 13px;">
            <i class="fas fa-spinner fa-spin"></i> Chargement des commentaires...
        </div>
    `;
    
    try {
        var result = await apiCall('/publications/' + postId + '/comments', {
            method: 'GET'
        });
        
        if (result.status === 'success' && result.data) {
            var comments = result.data;
            
            if (comments.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-3" style="font-size: 13px;">
                        <i class="far fa-comment-dots"></i> Aucun commentaire pour le moment
                    </div>
                `;
                return;
            }
            
            var html = '';
            for (var i = 0; i < comments.length; i++) {
                var comment = comments[i];
                var userName = comment.user?.name || 'Utilisateur';
                var userAvatar = getAvatarUrl(comment.user?.photo_url, userName);
                
                html += `
                    <div class="comment-item d-flex gap-2 py-2" style="border-bottom: 1px solid #e9ecef; padding: 8px 0;">
                        <img src="${userAvatar}" 
                             alt="${userName}" 
                             style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0;"
                             onerror="this.src='https://ui-avatars.com/api/?name=User&background=4F46E5&color=fff'">
                        <div style="flex: 1;">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <strong style="font-size: 13px;">${escapeHtml(userName)}</strong>
                                <span style="font-size: 11px; color: #6c757d;">${comment.created_at_human || 'N/A'}</span>
                            </div>
                            <p style="font-size: 13px; margin: 2px 0 0 0; color: #343a40;">${escapeHtml(comment.contenu)}</p>
                        </div>
                    </div>
                `;
            }
            container.innerHTML = html;
        } else {
            container.innerHTML = `
                <div class="text-center text-danger py-2" style="font-size: 13px;">
                    <i class="fas fa-exclamation-circle"></i> Erreur lors du chargement
                </div>
            `;
        }
    } catch (error) {
        console.error('Erreur chargement commentaires:', error);
        container.innerHTML = `
            <div class="text-center text-danger py-2" style="font-size: 13px;">
                <i class="fas fa-exclamation-circle"></i> Erreur de chargement
            </div>
        `;
    }
}

/**
 * Ajouter un commentaire
 */
async function addComment(postId) {
    var input = document.getElementById('comment-input-' + postId);
    var content = input.value.trim();
    
    if (!content) {
        showToast('Veuillez écrire un commentaire', 'warning');
        return;
    }
    
    if (content.length < 1) {
        showToast('Le commentaire est trop court', 'warning');
        return;
    }
    
    var submitBtn = document.querySelector('#comment-form-' + postId + ' button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    try {
        var result = await apiCall('/publications/' + postId + '/comments', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ contenu: content })
        });
        
        if (result.status === 'success') {
            showToast('💬 Commentaire ajouté !', 'success');
            input.value = '';
            
            // Mettre à jour le compteur
            var countSpan = document.getElementById('comments-count-' + postId);
            if (countSpan) {
                var currentCount = parseInt(countSpan.textContent, 10) || 0;
                countSpan.textContent = currentCount + 1;
            }
            
            // Recharger les commentaires
            await loadComments(postId);
        } else {
            showToast(result.message || 'Erreur', 'danger');
        }
    } catch (error) {
        console.error('Erreur ajout commentaire:', error);
        showToast(error.message || 'Erreur lors de l\'ajout', 'danger');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
    }
}

/**
 * Supprimer un commentaire
 */
async function deleteComment(commentId) {
    if (!confirm('Supprimer ce commentaire ?')) return;
    
    try {
        var result = await apiCall('/publications/comments/' + commentId, {
            method: 'DELETE'
        });
        
        if (result.status === 'success') {
            showToast('Commentaire supprimé', 'success');
            // Recharger la page ou les commentaires
            location.reload();
        } else {
            showToast(result.message || 'Erreur', 'danger');
        }
    } catch (error) {
        showToast(error.message || 'Erreur', 'danger');
    }
}

// ============================================================
// GESTION DES LIKES
// ============================================================

/**
 * Ajouter ou retirer un like
 */
async function toggleLike(postId) {
    try {
        var result = await apiCall('/publications/' + postId + '/like', {
            method: 'POST'
        });
        
        if (result.status === 'success') {
            // Mettre à jour le compteur
            var countSpan = document.getElementById('likes-count-' + postId);
            if (countSpan && result.data) {
                countSpan.textContent = result.data.total_likes || 0;
            }
            
            // Mettre à jour l'état du bouton
            var likeBtn = document.querySelector('.custom-post-card[data-id="' + postId + '"] .like-btn');
            var counterIcon = document.querySelector('.custom-post-card[data-id="' + postId + '"] .counter-item-like i');
            
            if (likeBtn) {
                if (result.data.liked) {
                    likeBtn.innerHTML = '<i class="fas fa-heart"></i> Aimé';
                    likeBtn.classList.add('liked');
                } else {
                    likeBtn.innerHTML = '<i class="far fa-heart"></i> Liker';
                    likeBtn.classList.remove('liked');
                }
            }
            
            if (counterIcon) {
                counterIcon.className = result.data.liked ? 'fas fa-heart text-danger' : 'far fa-heart';
            }
            
            showToast(result.message, result.data.liked ? 'success' : 'info');
        } else {
            showToast(result.message || 'Erreur', 'danger');
        }
    } catch (error) {
        console.error('Erreur like:', error);
        showToast(error.message || 'Erreur lors du like', 'danger');
    }
}

/**
 * Récupérer la liste des likes
 */
async function loadLikesList(postId) {
    try {
        var result = await apiCall('/publications/' + postId + '/likes', {
            method: 'GET'
        });
        
        if (result.status === 'success' && result.data) {
            var likes = result.data;
            var container = document.getElementById('likes-list-' + postId);
            
            if (!container) return;
            
            if (likes.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-2" style="font-size: 13px;">
                        <i class="far fa-heart"></i> Soyez le premier à aimer !
                    </div>
                `;
                return;
            }
            
            var html = '<div class="d-flex flex-wrap gap-2 py-2">';
            for (var i = 0; i < Math.min(likes.length, 10); i++) {
                var like = likes[i];
                var avatar = getAvatarUrl(like.photo_url, like.name);
                html += `
                    <div class="d-flex align-items-center gap-2" style="background: #f8f9fa; padding: 4px 12px 4px 4px; border-radius: 20px;">
                        <img src="${avatar}" 
                             alt="${like.name}" 
                             style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;"
                             onerror="this.src='https://ui-avatars.com/api/?name=User&background=4F46E5&color=fff'">
                        <span style="font-size: 12px; font-weight: 500;">${escapeHtml(like.name)}</span>
                    </div>
                `;
            }
            if (likes.length > 10) {
                html += `<span style="font-size: 12px; color: #6c757d;">+ ${likes.length - 10} autres</span>`;
            }
            html += '</div>';
            container.innerHTML = html;
        }
    } catch (error) {
        console.error('Erreur chargement likes:', error);
    }
}

// ============================================================
// GESTION DES PARTAGES
// ============================================================

/**
 * Partager une publication
 */
async function handleShare(postId) {
    // Afficher une modale ou un menu de choix
    var choix = prompt(
        'Choisissez une plateforme de partage :\n\n' +
        '1. 📋 Copier le lien\n' +
        '2. 📘 Facebook\n' +
        '3. 🐦 Twitter\n' +
        '4. 💬 WhatsApp\n\n' +
        'Entrez le numéro (1-4) :'
    );
    
    if (choix === null) {
        showToast('Partage annulé', 'info');
        return;
    }
    
    var plateformes = {
        '1': 'copie_lien',
        '2': 'facebook',
        '3': 'twitter',
        '4': 'whatsapp'
    };
    
    var plateforme = plateformes[choix.trim()];
    if (!plateforme) {
        showToast('❌ Option invalide. Veuillez choisir entre 1 et 4.', 'warning');
        return;
    }
    
    try {
        var result = await apiCall('/publications/' + postId + '/share', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ plateforme: plateforme })
        });
        
        if (result.status === 'success') {
            var url = result.data?.share_url || '';
            
            if (plateforme === 'copie_lien') {
                // Copier le lien dans le presse-papier
                try {
                    await navigator.clipboard.writeText(url);
                    showToast('📋 Lien copié dans le presse-papier !', 'success');
                } catch {
                    // Fallback pour les navigateurs sans Clipboard API
                    var copyInput = document.createElement('input');
                    copyInput.value = url;
                    document.body.appendChild(copyInput);
                    copyInput.select();
                    document.execCommand('copy');
                    copyInput.remove();
                    showToast('📋 Lien copié !', 'success');
                }
            } else {
                // Ouvrir dans une nouvelle fenêtre
                window.open(url, '_blank');
                showToast('🔗 Partagé sur ' + plateforme + ' !', 'success');
            }
            
            // Mettre à jour le compteur
            var countSpan = document.getElementById('shares-count-' + postId);
            if (countSpan && result.data) {
                countSpan.textContent = result.data.total_shares || 0;
            }
            
            // Recharger les publications pour mettre à jour les statistiques
            setTimeout(function() {
                loadPosts(state.currentPage, state.currentCategory, state.currentScope);
            }, 1000);
        } else {
            showToast(result.message || 'Erreur lors du partage', 'danger');
        }
    } catch (error) {
        console.error('Erreur partage:', error);
        showToast(error.message || 'Erreur lors du partage', 'danger');
    }
}

/**
 * Copier le lien d'une publication (raccourci)
 */
async function copyLink(postId) {
    var post = state.posts.find(function(p) { return p.id === postId; });
    if (!post) {
        showToast('Publication non trouvée', 'danger');
        return;
    }
    
    try {
        var url = window.location.origin + '/blog/' + postId;
        await navigator.clipboard.writeText(url);
        showToast('📋 Lien copié !', 'success');
    } catch {
        var copyInput = document.createElement('input');
        copyInput.value = window.location.origin + '/blog/' + postId;
        document.body.appendChild(copyInput);
        copyInput.select();
        document.execCommand('copy');
        copyInput.remove();
        showToast('📋 Lien copié !', 'success');
    }
}

// ============================================================
// INITIALISATION 
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    log('🚀 Initialisation du blog');
    
    if (!CONFIG.TOKEN) {
        showToast('Non connecté. Redirection...', 'danger');
        setTimeout(function() {
            window.location.href = '/auth/login';
        }, 2000);
        return;
    }
    
    // ✅ RÉFÉRENCES
    var publishForm = document.getElementById('publishForm');
    var fileUploadArea = document.getElementById('fileUploadArea');
    var postFiles = document.getElementById('postFiles');
    
    // ✅ ATTACHER L'ÉCOUTEUR DU FORMULAIRE DIRECTEMENT
    publishForm.addEventListener('submit', handlePublishSubmit);
    
    // ✅ ATTACHER LES ÉVÉNEMENTS D'UPLOAD
    if (fileUploadArea && postFiles) {
        // Clic sur la zone d'upload
        fileUploadArea.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🖱️ Clic sur la zone d\'upload');
            postFiles.click();
        });
        
        // Drag & Drop
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
    
    // ✅ ÉVÉNEMENT DE CHANGEMENT DU INPUT FILE
    if (postFiles) {
        postFiles.addEventListener('change', function() {
            console.log('📁 Fichiers sélectionnés:', this.files.length);
            if (this.files.length > 0) {
                handleFiles(this.files);
            }
            this.value = '';
        });
    }
    
    // Chargement initial
    loadPosts(1, 'all', 'all');
    
    // Onglets de catégorie
    document.querySelectorAll('.tab-item').forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.tab-item').forEach(function(t) { t.classList.remove('active-tab'); });
            this.classList.add('active-tab');
            
            var tabType = this.dataset.tab;
            if (tabType === 'mine') {
                state.currentScope = 'mine';
                state.currentCategory = 'all';
                document.querySelectorAll('.tab-item').forEach(function(t) {
                    if (t.dataset.tab !== 'mine') t.classList.remove('active-tab');
                });
            } else {
                state.currentScope = 'all';
                state.currentCategory = tabType;
            }
            state.currentPage = 1;
            loadPosts(1, state.currentCategory, state.currentScope);
        });
    });
    
    // Boutons modale
    document.getElementById('openPublishModal').addEventListener('click', openPublishModal);
    document.getElementById('closePublishModal').addEventListener('click', closePublishModal);
    document.getElementById('cancelPublish').addEventListener('click', closePublishModal);
    document.getElementById('closeImageModal').addEventListener('click', closeImageModal);
    
    document.getElementById('prevImageBtn').addEventListener('click', function() { navigateImage(-1); });
    document.getElementById('nextImageBtn').addEventListener('click', function() { navigateImage(1); });
    
    window.addEventListener('click', function(e) {
        if (e.target === document.getElementById('publishModal')) closePublishModal();
        if (e.target === document.getElementById('imageModal')) closeImageModal();
    });
    
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
    
    document.getElementById('prevPage').addEventListener('click', function() {
        if (state.currentPage > 1) goToPage(state.currentPage - 1);
    });
    document.getElementById('nextPage').addEventListener('click', function() {
        goToPage(state.currentPage + 1);
    });
    
    log('✅ Blog initialisé avec succès');
});