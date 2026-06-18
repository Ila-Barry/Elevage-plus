@extends('layouts.menu')

@section('title', 'Communauté Éleveurs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/blog.css') }}">
@endpush

@section('content')
<div class="blog-main-container p-1">
    
    <div class="blog-top-header">
        <h2 class="blog-main-title">COMMUNAUTÉ  ÉLEVEURS</h2>
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
        <a href="#" class="tab-item" data-tab="conseils"><i class="fas fa-lightbulb text-warning"></i> Conseils</a>
        <a href="#" class="tab-item" data-tab="experiences"><i class="fas fa-user-edit text-info"></i> Expériences</a>
        <a href="#" class="tab-item" data-tab="alertes"><i class="fas fa-exclamation-triangle text-danger"></i> Alertes</a>
        <!-- <a href="#" class="tab-item" data-tab="tendances"><i class="fas fa-chart-line text-primary"></i> Tendances</a> -->
    </div>

    <div class="blog-posts-feed" id="postsFeed">
        <!-- Les articles seront générés par JavaScript -->
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
            <form id="publishForm">
                <div class="form-group">
                    <label>Titre de l'article *</label>
                    <input type="text" id="postTitle" class="form-control" placeholder="Ex: Ma nouvelle méthode d'alimentation" required>
                </div>

                <div class="form-group">
                    <label>Catégorie *</label>
                    <select id="postCategory" class="form-control" required>
                        <option value="conseils">Conseils</option>
                        <option value="experiences">Expériences</option>
                        <option value="alertes">Alertes</option>
                        <option value="tendances">Tendances</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Contenu *</label>
                    <textarea id="postContent" class="form-control" rows="5" placeholder="Décrivez votre expérience..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Images (optionnel)</label>
                    <div class="image-upload-area" id="imageUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Cliquez ou glissez-déposez des images</p>
                        <input type="file" id="postImages" multiple accept="image/*" style="display: none;">
                    </div>
                    <div class="image-preview-container" id="imagePreviewContainer"></div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel-modal" id="cancelPublish">Annuler</button>
                    <button type="submit" class="btn-publish-modal">
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
// ================= DONNÉES =================
let posts = [
    {
        id: 1,
        author: "JEAN DUPONT",
        profession: "Éleveur bovin",
        avatar: "{{ asset('images/img-elevage.jpeg') }}",
        category: "experiences",
        title: "EXPERIENCE : NOUVELLE MÉTHODE D'ALIMENTATION",
        content: "Je viens de tester une nouvelle méthode d'alimentation pour mes vaches et les résultats sont encourageants. Après 3 mois d'essai, j'ai constaté une augmentation de 25% de la production laitière et une meilleure santé générale du troupeau.",
        fullContent: "Je viens de tester une nouvelle méthode d'alimentation pour mes vaches et les résultats sont encourageants. Après 3 mois d'essai, j'ai constaté une augmentation de 25% de la production laitière et une meilleure santé générale du troupeau. Cette méthode consiste à donner des compléments alimentaires naturels et à espacer les repas pour améliorer la digestion. Les vaches sont plus actives, leur pelage est plus brillant et elles produisent un lait de meilleure qualité. Je recommande cette méthode à tous les éleveurs qui souhaitent améliorer leur productivité.",
        time: "Aujourd'hui",
        likes: 12,
        comments: 5,
        views: 89,
        images: [
            "https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?w=300",
            "https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=300",
            "https://images.unsplash.com/photo-1596733430284-f7437764b1a9?w=300",
            "https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=300",
            "https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=300",
            "https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=300",
            "https://images.unsplash.com/photo-1532009877282-3340270e0529?w=300",
            "https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?w=300"
        ],
        liked: false,
        expanded: false
    },
    {
        id: 2,
        author: "AMADOU SY",
        profession: "Éleveur ovin",
        avatar: "https://i.pravatar.cc/100?u=amadou_sy",
        category: "alertes",
        title: "ALERTE : CAS DE FIÈVRE APHTEUSE À DAKAR",
        content: "⚠️ Une épidémie de fièvre aphteuse a été signalée dans la région de Dakar. Les symptômes à surveiller : fièvre, aphtes dans la bouche et sur les sabots, baisse de production laitière.",
        fullContent: "⚠️ Une épidémie de fièvre aphteuse a été signalée dans la région de Dakar. Les symptômes à surveiller : fièvre, aphtes dans la bouche et sur les sabots, baisse de production laitière. Recommandations : isolez immédiatement les animaux suspects, contactez votre vétérinaire. La situation est sous surveillance par les autorités vétérinaires. Évitez tout déplacement d'animaux et renforcez les mesures de biosécurité dans vos élevages.",
        time: "2 jours",
        likes: 34,
        comments: 18,
        views: 234,
        images: [
            "https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?w=300",
            "https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=300",
            "https://images.unsplash.com/photo-1596733430284-f7437764b1a9?w=300"
        ],
        liked: false,
        expanded: false
    },
    {
        id: 3,
        author: "FATOU DIOP",
        profession: "Éleveur caprin",
        avatar: "https://i.pravatar.cc/100?u=fatou_diop",
        category: "conseils",
        title: "CONSEIL : 5 ASTUCES POUR L'HIVERNAGE DES CAPRINS",
        content: "L'hivernage approche et il est crucial de bien préparer vos animaux. Voici mes 5 astuces pour protéger vos caprins : 1) Stockez suffisamment de fourrage, 2) Vérifiez les abris, 3) Programmez les vaccinations, 4) Augmentez les compléments énergétiques, 5) Surveillez l'état corporel chaque semaine.",
        fullContent: "L'hivernage approche et il est crucial de bien préparer vos animaux. Voici mes 5 astuces pour protéger vos caprins : 1) Stockez suffisamment de fourrage pour au moins 3 mois, 2) Vérifiez et réparez les abris avant la saison des pluies, 3) Programmez les vaccinations au moins 2 semaines avant, 4) Augmentez les compléments énergétiques dans la ration, 5) Surveillez l'état corporel de chaque animal chaque semaine. Ces conseils vous permettront de passer l'hivernage en toute sérénité.",
        time: "5 mois",
        likes: 67,
        comments: 23,
        views: 456,
        images: [
            "https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?w=300",
            "https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=300",
            "https://images.unsplash.com/photo-1596733430284-f7437764b1a9?w=300",
            "https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=300",
            "https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=300"
        ],
        liked: false,
        expanded: false
    },
    {
        id: 4,
        author: "MOUSSA DIALLO",
        profession: "Éleveur bovin",
        avatar: "https://i.pravatar.cc/100?u=moussa_diallo",
        category: "tendances",
        title: "TENDANCE : L'AGRICULTURE BIOLOGIQUE EN AFRIQUE",
        content: "L'agriculture biologique gagne du terrain en Afrique. De plus en plus d'éleveurs adoptent des méthodes naturelles pour améliorer la productivité tout en préservant l'environnement.",
        fullContent: "L'agriculture biologique gagne du terrain en Afrique. De plus en plus d'éleveurs adoptent des méthodes naturelles pour améliorer la productivité tout en préservant l'environnement. Les avantages sont nombreux : produits de meilleure qualité, réduction des coûts d'intrants, préservation des sols et de la biodiversité. La demande pour les produits bio ne cesse d'augmenter sur les marchés locaux et internationaux.",
        time: "1 semaine",
        likes: 89,
        comments: 34,
        views: 678,
        images: [
            "https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?w=300",
            "https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=300"
        ],
        liked: false,
        expanded: false
    }
];

let currentPage = 1;
const itemsPerPage = 3;
let currentCategory = 'all';
let currentImageIndex = 0;
let currentImages = [];
let toastTimeout = null;
let resizeTimeout = null;

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

// ================= FONCTION POUR TRONQUER LE TEXTE =================
function truncateText(text, maxLength) {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

// ================= RENDU DE LA GALERIE =================
function renderGallery(images, postId) {
    if (!images || images.length === 0) return '';
    
    const totalImages = images.length;
    const galleryId = `gallery-${postId}`;
    
    // Détecter la taille de l'écran
    const width = window.innerWidth;
    const isMobile = width <= 768;
    const isTablet = width > 768 && width <= 992;
    const displayCount = isMobile ? 2 : (isTablet ? 3 : 4);
    
    // Limiter le nombre d'images affichées
    const displayImages = images.slice(0, displayCount);
    const extraCount = totalImages - displayCount;
    
    let galleryHtml = `
        <div class="post-images-gallery" data-post="${postId}">
            <div class="image-row">
    `;
    
    displayImages.forEach((img, index) => {
        const isLast = index === displayCount - 1;
        const hasExtra = extraCount > 0;
        const realIndex = index;
        
        if (isLast && hasExtra) {
            galleryHtml += `
                <div class="grid-img-item">
                    <label for="${galleryId}" class="gallery-trigger" onclick="event.preventDefault(); document.getElementById('${galleryId}').checked = !document.getElementById('${galleryId}').checked;">
                        <img src="${img}" alt="Image ${index + 1}">
                        <span class="overlay-plus">+${extraCount}</span>
                    </label>
                </div>
            `;
        } else {
            galleryHtml += `
                <div class="grid-img-item">
                    <img src="${img}" alt="Image ${index + 1}" onclick="openImageModal(${postId}, ${realIndex})" style="cursor: pointer;">
                </div>
            `;
        }
    });
    
    galleryHtml += `
            </div>
    `;
    
    if (extraCount > 0) {
        galleryHtml += `
            <input type="checkbox" id="${galleryId}" class="gallery-checkbox">
            <div class="extra-images">
                <div class="extra-grid">
        `;
        
        images.slice(displayCount).forEach((img, idx) => {
            const realIndex = displayCount + idx;
            galleryHtml += `
                <img src="${img}" alt="Extra ${idx + 1}" onclick="openImageModal(${postId}, ${realIndex})" style="cursor: pointer;">
            `;
        });
        
        galleryHtml += `
                </div>
            </div>
        `;
    }
    
    galleryHtml += `</div>`;
    return galleryHtml;
}

// ================= RENDU DES POSTS =================
function renderPosts() {
    const container = document.getElementById('postsFeed');
    const filtered = getFilteredPosts();
    const totalPages = Math.ceil(filtered.length / itemsPerPage);
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageItems = filtered.slice(start, end);
    
    if (pageItems.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <h4>Aucun article trouvé</h4>
                <p>Aucun article ne correspond à vos critères de recherche.</p>
            </div>
        `;
        updatePagination(0);
        return;
    }
    
    container.innerHTML = pageItems.map((post) => {
        const displayContent = post.expanded ? post.fullContent || post.content : truncateText(post.content, 120);
        const isTruncated = (post.fullContent || post.content).length > 120;
        
        return `
        <article class="custom-post-card" data-id="${post.id}">
            <div class="custom-post-admin">
                <button class="action-edit" onclick="editPost(${post.id})" title="Modifier"><i class="fas fa-pencil-alt"></i></button>
                <button class="action-delete" onclick="deletePost(${post.id})" title="Supprimer"><i class="fas fa-times"></i></button>
            </div>

            <div class="custom-post-header">
                <div class="author-avatar-box">
                    <img src="${post.avatar}" alt="Avatar ${post.author}" class="rounded-circle">
                </div>
                <div class="author-meta-data">
                    <span class="user-fullname">${post.author}</span>
                    <span class="meta-separator">•</span>
                    <span class="user-profession">${post.profession}</span>
                    <span class="meta-separator">•</span>
                    <span class="published-time"><i class="far fa-calendar-alt"></i> ${post.time}</span>
                </div>
            </div>

            <div class="custom-post-body">
                <h3 class="post-title-badge color-${post.category}">
                    ${post.category === 'experiences' ? '<span class="dot-indicator"></span>' : ''}
                    ${post.category === 'alertes' ? '<i class="fas fa-exclamation-triangle"></i>' : ''}
                    ${post.category === 'conseils' ? '<i class="fas fa-lightbulb"></i>' : ''}
                    ${post.category === 'tendances' ? '<i class="fas fa-chart-line"></i>' : ''}
                    ${post.title}
                </h3>
                <p class="post-text-content">
                    ${displayContent}
                    ${isTruncated ? `
                        <span class="read-more-btn" onclick="toggleContent(${post.id})">
                            ${post.expanded ? 'moins' : 'plus...'}
                        </span>
                    ` : ''}
                </p>
                
                ${post.images && post.images.length > 0 ? renderGallery(post.images, post.id) : ''}
            </div>

            <div class="custom-post-footer">
                <div class="post-counters">
                    <span><i class="far fa-thumbs-up"></i> ${post.likes}</span>
                    <span><i class="far fa-comment"></i> ${post.comments}</span>
                    <span><i class="far fa-eye"></i> ${post.views}</span>
                </div>
                <div class="post-action-triggers">
                    <button class="trigger-btn" onclick="likePost(${post.id})">
                        <i class="${post.liked ? 'fas' : 'far'} fa-thumbs-up"></i> ${post.liked ? 'Aimé' : 'Liker'}
                    </button>
                    <button class="trigger-btn" onclick="commentPost(${post.id})">
                        <i class="far fa-comment"></i> Commenter
                    </button>
                    <button class="trigger-btn" onclick="sharePost(${post.id})">
                        <i class="fas fa-share"></i> Partager
                    </button>
                </div>
            </div>
        </article>
    `}).join('');
    
    updatePagination(totalPages);
}

// ================= TOGGLE CONTENU =================
function toggleContent(postId) {
    const post = posts.find(p => p.id === postId);
    if (!post) return;
    
    post.expanded = !post.expanded;
    renderPosts();
}

// ================= FILTRES =================
function getFilteredPosts() {
    let filtered = [...posts];
    
    if (currentCategory !== 'all') {
        filtered = filtered.filter(p => p.category === currentCategory);
    }
    
    return filtered;
}

// ================= PAGINATION =================
function updatePagination(total) {
    const totalPages = Math.ceil(total / itemsPerPage);
    const pageNumbers = document.getElementById('pageNumbers');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    
    if (totalPages <= 1) {
        pageNumbers.innerHTML = '';
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        return;
    }
    
    let html = '';
    for (let i = 1; i <= totalPages; i++) {
        html += `<span class="num-item ${i === currentPage ? 'active-num' : ''}" onclick="goToPage(${i})">${i}</span>`;
    }
    pageNumbers.innerHTML = html;
    
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;
}

function goToPage(page) {
    currentPage = page;
    renderPosts();
    document.getElementById('postsFeed').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ================= INTERACTIONS =================
function likePost(postId) {
    const post = posts.find(p => p.id === postId);
    if (!post) return;
    
    post.liked = !post.liked;
    post.likes += post.liked ? 1 : -1;
    
    renderPosts();
    showToast(post.liked ? 'Vous aimez cette publication' : 'Vous n\'aimez plus cette publication', post.liked ? 'success' : 'info');
}

function commentPost(postId) {
    const post = posts.find(p => p.id === postId);
    if (!post) return;
    
    const comment = prompt(`Laissez un commentaire sur "${post.title}" :`);
    if (comment && comment.trim()) {
        post.comments++;
        renderPosts();
        showToast('Commentaire ajouté avec succès !', 'success');
    } else if (comment !== null) {
        showToast('Le commentaire ne peut pas être vide', 'warning');
    }
}

function sharePost(postId) {
    const post = posts.find(p => p.id === postId);
    if (!post) return;
    
    const shareUrl = `${window.location.origin}/blog/${postId}`;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(shareUrl).then(() => {
            showToast('Lien copié dans le presse-papier !', 'success');
        });
    } else {
        prompt('Copiez ce lien pour partager :', shareUrl);
    }
}

function editPost(postId) {
    const post = posts.find(p => p.id === postId);
    if (!post) return;
    
    showToast(`Édition de "${post.title}" - Fonctionnalité à venir`, 'info');
}

function deletePost(postId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')) {
        posts = posts.filter(p => p.id !== postId);
        renderPosts();
        showToast('Publication supprimée avec succès', 'success');
    }
}

// ================= MODALE IMAGE =================
function openImageModal(postId, imageIndex) {
    const post = posts.find(p => p.id === postId);
    if (!post || !post.images || post.images.length === 0) return;
    
    currentImages = post.images;
    currentImageIndex = imageIndex;
    
    const modal = document.getElementById('imageModal');
    const img = document.getElementById('modalImage');
    const counter = document.getElementById('imageCounter');
    
    img.src = currentImages[currentImageIndex];
    counter.textContent = `${currentImageIndex + 1} / ${currentImages.length}`;
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
    document.body.style.overflow = '';
}

function navigateImage(direction) {
    currentImageIndex += direction;
    
    if (currentImageIndex < 0) {
        currentImageIndex = currentImages.length - 1;
    } else if (currentImageIndex >= currentImages.length) {
        currentImageIndex = 0;
    }
    
    const img = document.getElementById('modalImage');
    const counter = document.getElementById('imageCounter');
    
    img.src = currentImages[currentImageIndex];
    counter.textContent = `${currentImageIndex + 1} / ${currentImages.length}`;
}

// ================= MODALE PUBLIER =================
function openPublishModal() {
    document.getElementById('publishModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePublishModal() {
    document.getElementById('publishModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Upload d'images
document.getElementById('imageUploadArea').addEventListener('click', function() {
    document.getElementById('postImages').click();
});

document.getElementById('imageUploadArea').addEventListener('dragover', function(e) {
    e.preventDefault();
    this.style.borderColor = '#198754';
    this.style.background = '#e8f5e9';
});

document.getElementById('imageUploadArea').addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.style.borderColor = '#ced4da';
    this.style.background = '#f8f9fa';
});

document.getElementById('imageUploadArea').addEventListener('drop', function(e) {
    e.preventDefault();
    this.style.borderColor = '#ced4da';
    this.style.background = '#f8f9fa';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleImageFiles(files);
    }
});

document.getElementById('postImages').addEventListener('change', function() {
    if (this.files.length > 0) {
        handleImageFiles(this.files);
    }
});

let uploadedImages = [];

function handleImageFiles(files) {
    const container = document.getElementById('imagePreviewContainer');
    
    for (let file of files) {
        if (!file.type.startsWith('image/')) continue;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            uploadedImages.push(e.target.result);
            
            const div = document.createElement('div');
            div.className = 'image-preview-item';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Aperçu">
                <button class="remove-image" onclick="removeImage(this)"><i class="fas fa-times"></i></button>
            `;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    }
}

function removeImage(btn) {
    const item = btn.closest('.image-preview-item');
    const index = Array.from(item.parentNode.children).indexOf(item);
    uploadedImages.splice(index, 1);
    item.remove();
}

// Soumission du formulaire
document.getElementById('publishForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const title = document.getElementById('postTitle').value.trim();
    const category = document.getElementById('postCategory').value;
    const content = document.getElementById('postContent').value.trim();
    
    if (!title) {
        showToast('Veuillez saisir un titre', 'warning');
        return;
    }
    
    if (!content) {
        showToast('Veuillez saisir un contenu', 'warning');
        return;
    }
    
    const newPost = {
        id: posts.length + 1,
        author: "JEAN DIAGNE",
        profession: "Éleveur",
        avatar: "https://i.pravatar.cc/100?u=jean_diagne",
        category: category,
        title: title,
        content: content,
        fullContent: content,
        time: "À l'instant",
        likes: 0,
        comments: 0,
        views: 0,
        images: uploadedImages.length > 0 ? uploadedImages : [
            "https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?w=300",
            "https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=300"
        ],
        liked: false,
        expanded: false
    };
    
    posts.unshift(newPost);
    uploadedImages = [];
    document.getElementById('imagePreviewContainer').innerHTML = '';
    document.getElementById('postTitle').value = '';
    document.getElementById('postContent').value = '';
    document.getElementById('postImages').value = '';
    
    renderPosts();
    closePublishModal();
    showToast('Article publié avec succès !', 'success');
});

// ================= GESTION DU REDIMENSIONNEMENT =================
function handleResize() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        renderPosts();
    }, 300);
}

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
    renderPosts();
    
    // Onglets
    document.querySelectorAll('.tab-item').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active-tab'));
            this.classList.add('active-tab');
            
            currentCategory = this.dataset.tab;
            currentPage = 1;
            renderPosts();
            
            const categoryName = this.textContent.trim();
            showToast(`Filtre: ${categoryName}`, 'info');
        });
    });
    
    // Modal Publier
    document.getElementById('openPublishModal').addEventListener('click', openPublishModal);
    document.getElementById('closePublishModal').addEventListener('click', closePublishModal);
    document.getElementById('cancelPublish').addEventListener('click', closePublishModal);
    
    // Modal Image
    document.getElementById('closeImageModal').addEventListener('click', closeImageModal);
    document.getElementById('prevImageBtn').addEventListener('click', () => navigateImage(-1));
    document.getElementById('nextImageBtn').addEventListener('click', () => navigateImage(1));
    
    // Fermer modales en cliquant à l'extérieur
    window.addEventListener('click', function(e) {
        const publishModal = document.getElementById('publishModal');
        const imageModal = document.getElementById('imageModal');
        
        if (e.target === publishModal) closePublishModal();
        if (e.target === imageModal) closeImageModal();
    });
    
    // Navigation au clavier pour les images
    document.addEventListener('keydown', function(e) {
        const imageModal = document.getElementById('imageModal');
        if (imageModal.style.display === 'flex') {
            if (e.key === 'ArrowLeft') navigateImage(-1);
            if (e.key === 'ArrowRight') navigateImage(1);
            if (e.key === 'Escape') closeImageModal();
        }
        if (e.key === 'Escape') {
            if (document.getElementById('publishModal').style.display === 'flex') {
                closePublishModal();
            }
        }
    });
    
    // Pagination
    document.getElementById('prevPage').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            renderPosts();
        }
    });
    
    document.getElementById('nextPage').addEventListener('click', function() {
        const filtered = getFilteredPosts();
        const totalPages = Math.ceil(filtered.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderPosts();
        }
    });
    
    // Tendances
    document.getElementById('showTrends').addEventListener('click', function() {
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active-tab'));
        document.querySelector('[data-tab="tendances"]').classList.add('active-tab');
        currentCategory = 'tendances';
        currentPage = 1;
        renderPosts();
        showToast('Affichage des tendances', 'info');
    });
    
    // Redimensionnement
    window.addEventListener('resize', handleResize);
});

// ================= STYLES ADDITIONNELS =================
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
    
    .read-more-btn {
        color: #198754;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        margin-left: 4px;
        transition: all 0.3s;
        display: inline-block;
    }
    .read-more-btn:hover {
        color: #146c43;
        text-decoration: underline;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
    }
    .empty-state i {
        font-size: 48px;
        color: #6c757d;
        margin-bottom: 15px;
    }
    .empty-state h4 {
        font-size: 18px;
        color: #1a202c;
        margin-bottom: 8px;
    }
    .empty-state p {
        color: #6c757d;
        font-size: 14px;
    }
`;
document.head.appendChild(style);
</script>

@endsection