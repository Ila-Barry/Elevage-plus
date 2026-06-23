@extends('layouts.app')

@section('title', 'Accueil - Élevage+')

@section('content')

<!-- style_css -->
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/home.css') }}">

<main>
  <!-- HERO  + CARTE CENTRÉE -->
<section class="hero-jumbotron">

    <img
        src="{{ asset('images/bg.png') }}"
        alt="Élevage+"
        class="hero-image"
    >

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
          <button class="tab" data-tab="conseils"><i class="fas fa-lightbulb"></i> Conseils</button>
          <button class="tab" data-tab="experiences"><i class="fas fa-user-edit"></i> Expériences</button>
          <button class="tab" data-tab="alertes"><i class="fas fa-bell"></i> Alertes</button>
          <button class="tab" data-tab="tendances"><i class="fas fa-fire"></i> Tendances</button>
        </div>
      </div>

      <!-- Container des publications avec hauteur fixe -->
      <div class="posts-scroll-container">
        <div id="posts-container"></div>
      </div>

      <!-- Pagination -->
      <div class="pagination" id="pagination">
        <button id="prevPage" disabled><i class="fas fa-chevron-left"></i> précédente</button>
        <div id="pageNumbers" class="page-numbers"></div>
        <button id="nextPage">suivante <i class="fas fa-chevron-right"></i></button>
      </div>
    </section>

    <!-- Sidebar droite - Statistiques indépendante -->
    <aside class="sidebar">
      <div class="bottom-stats">
        <h3><i class="fas fa-chart-pie"></i> STATISTIQUES DE LA COMMUNAUTÉ</h3>
        <div class="stats-grid" id="statsGrid">
          <div class="stat-box stat-green">
            <i class="fas fa-user-friends stat-icon"></i>
            <div class="stat-num" id="statUsers">127</div>
            <div>éleveurs</div>
          </div>
          <div class="stat-box stat-blue">
            <i class="fas fa-file-alt stat-icon"></i>
            <div class="stat-num" id="statPosts">345</div>
            <div>articles</div>
          </div>
          <div class="stat-box stat-pink">
            <i class="fas fa-heart stat-icon"></i>
            <div class="stat-num" id="statLikes">2.5k</div>
            <div>likes</div>
          </div>
          <div class="stat-box stat-mint">
            <i class="fas fa-comments stat-icon"></i>
            <div class="stat-num" id="statComments">890</div>
            <div>coms</div>
          </div>
        </div>
      </div>

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

      <div class="cta-box">
        <h4><i class="fas fa-rocket"></i> Prête à améliorer votre élevage?</h4>
        <p>rejoignez les centaines d'éleveurs qui nous font déjà confiance!</p>
        <a href="{{ url('auth/register') }}" class="btn btn-success w-100"><i class="fas fa-user-plus"></i> Créez votre compte gratuitement</a>
      </div>

      <div class="sidebar-imgs">
        <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=400" alt="Vaches">
        <img src="https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?q=80&w=400" alt="Moutons">
      </div>
    </aside>
  </div>
</main>

<script>
// ================= GÉNÉRATION DE 20+ PUBLICATIONS =================
function generatePublications() {
  const basePublications = [
    {
      id: 1,
      author: "Jean Dupont",
      role: "Éleveur bovin",
      avatar: "https://i.pravatar.cc/40?u=jean1",
      time: "2 jours",
      rating: 4.0,
      likes: 45,
      comments: 12,
      views: 230,
      image: "https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=250",
      title: "COMMENT J'AI AUGMENTÉ MA PRODUCTION LAITIÈRE DE 30%",
      content: "Le mois dernier, j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...",
      fullContent: "Le mois dernier, j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches. Après avoir consulté un nutritionniste animalier, j'ai modifié la ration quotidienne en ajoutant des compléments protéinés naturels. Les résultats sont spectaculaires : augmentation de 30% de la production laitière, meilleure santé générale des animaux et réduction des coûts d'alimentation de 15%.",
      userLiked: false,
      type: "conseils"
    },
    {
      id: 2,
      author: "Marie Diop",
      role: "Éleveur ovin",
      avatar: "https://i.pravatar.cc/40?u=marie1",
      time: "5 jours",
      rating: 4.5,
      likes: 78,
      comments: 23,
      views: 450,
      image: "https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?q=80&w=250",
      title: "5 ASTUCES POUR PRÉPARER L'HIVERNAGE",
      content: "L'hivernage approche et il est crucial de bien préparer vos animaux...",
      fullContent: "L'hivernage approche et il est crucial de bien préparer vos animaux. Voici mes 5 astuces : 1) Stockez suffisamment de fourrage, 2) Vérifiez les abris, 3) Programmez les vaccinations avant la saison des pluies, 4) Augmentez les compléments énergétiques, 5) Surveillez l'état corporel chaque semaine.",
      userLiked: false,
      type: "conseils"
    },
    {
      id: 3,
      author: "Amadou Sy",
      role: "Éleveur bovin",
      avatar: "https://i.pravatar.cc/40?u=amadou1",
      time: "1 semaine",
      rating: 4.8,
      likes: 120,
      comments: 45,
      views: 890,
      image: "https://images.unsplash.com/photo-1589923188651-268a9765e432?q=80&w=250",
      title: "MON EXPÉRIENCE AVEC LA TRANSITION VERS L'AGRICULTURE BIOLOGIQUE",
      content: "Après 5 ans d'élevage conventionnel, j'ai décidé de passer au bio...",
      fullContent: "Après 5 ans d'élevage conventionnel, j'ai décidé de passer au bio. Les défis étaient nombreux : trouver des aliments certifiés, adapter les traitements vétérinaires, former le personnel. Mais les résultats en valent la peine : animaux plus résistants, qualité de lait supérieure, et un prix de vente augmenté de 40%.",
      userLiked: false,
      type: "experiences"
    },
    {
      id: 4,
      author: "Fatou Ndiaye",
      role: "Éleveur caprin",
      avatar: "https://i.pravatar.cc/40?u=fatou1",
      time: "3 jours",
      rating: 4.2,
      likes: 56,
      comments: 18,
      views: 310,
      image: "https://images.unsplash.com/photo-1516467508483-72145faca6d0?q=80&w=250",
      title: "ALERTE - FIEVRE APHTEUSE DANS LA RÉGION DE THIÈS",
      content: "⚠️ Une épidémie de fièvre aphteuse a été signalée dans la région de Thiès...",
      fullContent: "Une épidémie de fièvre aphteuse a été signalée dans la région de Thiès. Les symptômes à surveiller : fièvre, aphtes dans la bouche et sur les sabots, baisse de production laitière. Recommandations : isolez immédiatement les animaux suspects, contactez votre vétérinaire, évitez tout déplacement d'animaux.",
      userLiked: false,
      type: "alertes"
    },
    {
      id: 5,
      author: "Mamadou Diallo",
      role: "Éleveur bovin",
      avatar: "https://i.pravatar.cc/40?u=mamadou1",
      time: "2 jours",
      rating: 4.9,
      likes: 234,
      comments: 67,
      views: 1200,
      image: "https://images.unsplash.com/photo-1559839734-2b71ea197ec2?q=80&w=250",
      title: "TOP 5 DES ALIMENTS QUI BOOSTENT LA PRODUCTION LAITIÈRE",
      content: "Après des années d'expérimentation, voici mon top 5 des aliments...",
      fullContent: "Après des années d'expérimentation, voici mon top 5 des aliments qui augmentent significativement la production laitière : 1) Le tourteau de coton, 2) La drèche de brasserie, 3) Le son de blé, 4) Les feuilles de moringa, 5) Les compléments minéraux. Attention aux quantités et à l'introduction progressive !",
      userLiked: false,
      type: "tendances"
    },
    {
      id: 6,
      author: "Aissatou Sow",
      role: "Éleveur bovin",
      avatar: "https://i.pravatar.cc/40?u=aissatou1",
      time: "4 jours",
      rating: 4.3,
      likes: 89,
      comments: 31,
      views: 520,
      image: "https://images.unsplash.com/photo-1546448396-6aef80193ceb?q=80&w=250",
      title: "MON TROUPEAU VIENT DE PASSER À 100 TÊTES !",
      content: "Une étape importante franchie cette semaine, mon élevage compte désormais 100 animaux...",
      fullContent: "Une étape importante franchie cette semaine, mon élevage compte désormais 100 animaux. Retour sur mon parcours : j'ai commencé avec 15 vaches il y a 3 ans. Grâce aux conseils de la communauté, à une bonne gestion et à beaucoup de travail, j'ai pu agrandir progressivement mon troupeau.",
      userLiked: false,
      type: "experiences"
    }
  ];

  // Générer 20+ publications (jusqu'à 25 pour avoir plusieurs pages)
  let allPublications = [...basePublications];
  
  // Ajouter des publications supplémentaires pour atteindre 25
  const types = ["conseils", "experiences", "alertes", "tendances"];
  const titles = [
    "NOUVELLE MÉTHODE D'ALIMENTATION BIO", 
    "COMMENT PRÉVENIR LES MALADIES EN SAISON DES PLUIES",
    "LES MEILLEURES PRATIQUES POUR L'ÉLEVAGE MODERNE",
    "RETOUR D'EXPÉRIENCE SUR L'AGRICULTURE DURABLE",
    "ASTUCES POUR RÉDUIRE LES COÛTS D'ALIMENTATION",
    "IMPORTANCE DE LA TRACEABILITÉ DANS L'ÉLEVAGE",
    "FORMATION GRATUITE SUR LA GESTION D'ÉLEVAGE",
    "LES NOUVELLES TECHNOLOGIES AU SERVICE DE L'ÉLEVAGE"
  ];
  const authors = [
    "Ousmane Barry", "Aminata Diallo", "Moussa Cissé", "Rokhaya Fall", 
    "Cheikh Dieng", "Fatimata Sow", "Babacar Ndiaye", "Mariama Sall"
  ];
  const roles = ["Éleveur bovin", "Éleveur ovin", "Éleveur caprin", "Éleveur mixte"];

  for (let i = basePublications.length + 1; i <= 25; i++) {
    const randomType = types[Math.floor(Math.random() * types.length)];
    const randomTitle = titles[Math.floor(Math.random() * titles.length)];
    const randomAuthor = authors[Math.floor(Math.random() * authors.length)];
    const randomRole = roles[Math.floor(Math.random() * roles.length)];
    
    allPublications.push({
      id: i,
      author: randomAuthor,
      role: randomRole,
      avatar: `https://i.pravatar.cc/40?u=${randomAuthor.replace(/\s/g, '')}`,
      time: `${Math.floor(Math.random() * 15) + 1} jours`,
      rating: (Math.random() * 1.5 + 3.5).toFixed(1),
      likes: Math.floor(Math.random() * 200) + 20,
      comments: Math.floor(Math.random() * 50) + 5,
      views: Math.floor(Math.random() * 1000) + 100,
      image: `https://picsum.photos/id/${100 + i}/250/150`,
      title: randomTitle,
      content: "Découvrez cette nouvelle publication passionnante sur l'élevage moderne. Des conseils pratiques et des astuces pour améliorer votre production...",
      fullContent: "Découvrez cette nouvelle publication passionnante sur l'élevage moderne. Des conseils pratiques et des astuces pour améliorer votre production. Nous partageons notre expérience et nos connaissances pour aider toute la communauté à progresser.",
      userLiked: false,
      type: randomType
    });
  }
  
  return allPublications;
}

const allGeneratedPublications = generatePublications();

// ================= DONNÉES DES PUBLICATIONS =================
const publicationsData = {
  all: [...allGeneratedPublications],
  conseils: allGeneratedPublications.filter(p => p.type === "conseils"),
  experiences: allGeneratedPublications.filter(p => p.type === "experiences"),
  alertes: allGeneratedPublications.filter(p => p.type === "alertes"),
  tendances: allGeneratedPublications.filter(p => p.type === "tendances")
};

// Variables d'état
let currentTab = 'all';
let currentPage = 1;
const itemsPerPage = 20; 
let likedPosts = new Set();
let toastTimeout = null;

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
  
  toast.innerHTML = `
    <div class="toast-content">
      <i class="fas ${icon}"></i>
      <span>${message}</span>
    </div>
  `;
  
  document.body.appendChild(toast);
  setTimeout(() => toast.classList.add('show'), 10);
  
  toastTimeout = setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// ================= FONCTIONS D'INTERACTION =================
function likePost(postId) {
  let post = null;
  for (const tab in publicationsData) {
    const found = publicationsData[tab].find(p => p.id === postId);
    if (found) {
      post = found;
      break;
    }
  }
  if (!post) return;
  
  const likeBtn = document.querySelector(`.like-btn-${postId}`);
  const likeCountSpan = document.querySelector(`.like-count-${postId}`);
  
  if (likedPosts.has(postId)) {
    likedPosts.delete(postId);
    post.likes--;
    if (likeBtn) likeBtn.classList.remove('liked');
    showToast('Vous n\'aimez plus cette publication', 'info');
  } else {
    likedPosts.add(postId);
    post.likes++;
    if (likeBtn) likeBtn.classList.add('liked');
    showToast('Publication aimée !', 'success');
  }
  
  if (likeCountSpan) likeCountSpan.textContent = post.likes;
  updateStats();
}

function sharePost(postId, postTitle) {
  const shareUrl = `${window.location.origin}/post/${postId}`;
  navigator.clipboard.writeText(shareUrl).then(() => {
    showToast(`"${postTitle}" - Lien copié dans le presse-papier !`, 'success');
  }).catch(() => {
    showToast(`Partagez "${postTitle}" avec vos amis !`, 'success');
  });
}

function openCommentModal(postId, postTitle) {
  const comment = prompt(`Laissez un commentaire sur "${postTitle}" :`);
  if (comment && comment.trim()) {
    showToast(`Commentaire ajouté : "${comment.substring(0, 50)}${comment.length > 50 ? '...' : ''}"`, 'success');
    let post = null;
    for (const tab in publicationsData) {
      const found = publicationsData[tab].find(p => p.id === postId);
      if (found) {
        post = found;
        break;
      }
    }
    if (post) {
      post.comments++;
      const commentSpan = document.querySelector(`.comment-count-${postId}`);
      if (commentSpan) commentSpan.textContent = post.comments;
      updateStats();
    }
  } else if (comment !== null) {
    showToast('Le commentaire ne peut pas être vide', 'warning');
  }
}

function openReadMore(postId) {
  let post = null;
  for (const tab in publicationsData) {
    const found = publicationsData[tab].find(p => p.id === postId);
    if (found) {
      post = found;
      break;
    }
  }
  if (post) {
    showModal(`
      <h3>${post.title}</h3>
      <div class="modal-meta">
        <span><i class="fas fa-user"></i> ${post.author} (${post.role})</span>
        <span><i class="far fa-clock"></i> ${post.time}</span>
      </div>
      <div class="modal-image">
        <img src="${post.image}" alt="${post.title}">
      </div>
      <div class="modal-content-text">
        <p>${post.fullContent || post.content}</p>
      </div>
      <div class="modal-stats">
        <span><i class="fas fa-heart"></i> ${post.likes} likes</span>
        <span><i class="fas fa-comment"></i> ${post.comments} commentaires</span>
        <span><i class="fas fa-eye"></i> ${post.views} vues</span>
      </div>
    `, "Publication complète");
  }
}

function showModal(content, title = "Détails") {
  const existingModal = document.querySelector('.custom-modal');
  if (existingModal) existingModal.remove();
  
  const modal = document.createElement('div');
  modal.className = 'custom-modal';
  modal.innerHTML = `
    <div class="modal-overlay"></div>
    <div class="modal-container">
      <div class="modal-header">
        <h3>${title}</h3>
        <button class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        ${content}
      </div>
    </div>
  `;
  
  document.body.appendChild(modal);
  document.body.style.overflow = 'hidden';
  setTimeout(() => modal.classList.add('show'), 10);
  
  const closeModal = () => {
    modal.classList.remove('show');
    setTimeout(() => {
      modal.remove();
      document.body.style.overflow = '';
    }, 300);
  };
  
  modal.querySelector('.modal-overlay').addEventListener('click', closeModal);
  modal.querySelector('.modal-close').addEventListener('click', closeModal);
}

// ================= AFFICHAGE DES PUBLICATIONS =================
function renderPosts() {
  const container = document.getElementById('posts-container');
  if (!container) return;
  
  const currentPosts = publicationsData[currentTab] || [];
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const paginatedPosts = currentPosts.slice(startIndex, endIndex);
  
  if (paginatedPosts.length === 0) {
    container.innerHTML = `
      <div class="empty-posts">
        <i class="fas fa-newspaper"></i>
        <h4>Aucune publication</h4>
        <p>Aucune publication disponible dans cette catégorie pour le moment.</p>
      </div>
    `;
    return;
  }
  
  container.innerHTML = paginatedPosts.map(post => `
    <article class="post-card" data-post-id="${post.id}">
      <div class="post-top">
        <img src="${post.avatar}" class="avatar" alt="${post.author}">
        <div class="post-info">
          <a href="{{ url('/profilEleveur') }}">
            <h4>${post.author} - ${post.role} <i class="fas fa-circle-check text-info"></i> • ${post.time}</h4>
          </a>
          <div class="post-meta">
            <span><i class="fas fa-star text-warning"></i> ${post.rating} (${post.likes} likes)</span>
            <span><i class="far fa-comment-dots"></i> ${post.comments} commentaires</span>
            <span><i class="far fa-eye"></i> ${post.views} vues</span>
          </div>
        </div>
      </div>
      <div class="post-content">
        <img src="${post.image}" alt="${post.title}">
        <div class="post-text">
          <h3>${post.title}</h3>
          <p>${post.content}</p>
          <div class="post-actions">
            <button class="like-btn like-btn-${post.id} ${likedPosts.has(post.id) ? 'liked' : ''}" onclick="likePost(${post.id})">
              <i class="far fa-thumbs-up"></i> Liker <span class="like-count-${post.id}">${post.likes}</span>
            </button>
            <button onclick="openCommentModal(${post.id}, '${post.title.replace(/'/g, "\\'")}')">
              <i class="far fa-comment-dots"></i> Commenter <span class="comment-count-${post.id}">${post.comments}</span>
            </button>
            <button onclick="sharePost(${post.id}, '${post.title.replace(/'/g, "\\'")}')">
              <i class="fas fa-share-alt"></i> Partager
            </button>
            <a href="#" class="read-more" onclick="openReadMore(${post.id}); return false;">
              Lire la suite <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </article>
  `).join('');
}

// ================= PAGINATION =================
function updatePagination() {
  const currentPosts = publicationsData[currentTab] || [];
  const totalPages = Math.ceil(currentPosts.length / itemsPerPage);
  const pageNumbersDiv = document.getElementById('pageNumbers');
  const prevBtn = document.getElementById('prevPage');
  const nextBtn = document.getElementById('nextPage');
  
  if (!pageNumbersDiv) return;
  
  if (totalPages <= 1) {
    pageNumbersDiv.innerHTML = '';
    if (prevBtn) prevBtn.disabled = true;
    if (nextBtn) nextBtn.disabled = true;
    return;
  }
  
  let pagesHtml = '';
  const maxVisiblePages = 5;
  let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
  let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
  
  if (endPage - startPage + 1 < maxVisiblePages) {
    startPage = Math.max(1, endPage - maxVisiblePages + 1);
  }
  
  if (startPage > 1) {
    pagesHtml += `<button class="page-number" data-page="1">1</button>`;
    if (startPage > 2) pagesHtml += `<span class="page-dots">...</span>`;
  }
  
  for (let i = startPage; i <= endPage; i++) {
    pagesHtml += `<button class="page-number ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
  }
  
  if (endPage < totalPages) {
    if (endPage < totalPages - 1) pagesHtml += `<span class="page-dots">...</span>`;
    pagesHtml += `<button class="page-number" data-page="${totalPages}">${totalPages}</button>`;
  }
  
  pageNumbersDiv.innerHTML = pagesHtml;
  
  if (prevBtn) prevBtn.disabled = currentPage === 1;
  if (nextBtn) nextBtn.disabled = currentPage === totalPages;
  
  document.querySelectorAll('.page-number').forEach(btn => {
    btn.addEventListener('click', () => {
      currentPage = parseInt(btn.dataset.page);
      renderPosts();
      updatePagination();
      document.querySelector('.posts-scroll-container').scrollTop = 0;
    });
  });
}

// ================= STATISTIQUES DYNAMIQUES =================
function updateStats() {
  const totalUsers = 127;
  let totalPosts = publicationsData.all.length;
  let totalLikes = 0;
  let totalComments = 0;
  
  publicationsData.all.forEach(post => {
    totalLikes += post.likes;
    totalComments += post.comments;
  });
  
  const statUsers = document.getElementById('statUsers');
  const statPosts = document.getElementById('statPosts');
  const statLikes = document.getElementById('statLikes');
  const statComments = document.getElementById('statComments');
  
  if (statUsers) statUsers.textContent = totalUsers;
  if (statPosts) statPosts.textContent = totalPosts;
  if (statLikes) statLikes.textContent = totalLikes > 1000 ? `${(totalLikes / 1000).toFixed(1)}k` : totalLikes;
  if (statComments) statComments.textContent = totalComments > 1000 ? `${(totalComments / 1000).toFixed(1)}k` : totalComments;
}

// ================= FILTRAGE PAR TAB =================
function switchTab(tabName) {
  currentTab = tabName;
  currentPage = 1;
  renderPosts();
  updatePagination();
  
  const container = document.getElementById('posts-container');
  container.style.opacity = '0';
  setTimeout(() => {
    container.style.opacity = '1';
  }, 150);
  
  let tabDisplayName = '';
  switch(tabName) {
    case 'all': tabDisplayName = 'Toutes les publications'; break;
    case 'conseils': tabDisplayName = 'Conseils'; break;
    case 'experiences': tabDisplayName = 'Expériences'; break;
    case 'alertes': tabDisplayName = 'Alertes'; break;
    case 'tendances': tabDisplayName = 'Tendances'; break;
    default: tabDisplayName = tabName;
  }
  showToast(`Affichage : ${tabDisplayName}`, 'info');
}

// ================= CARROUSEL =================
function initCarousel() {
  const slides = document.querySelectorAll('.carousel-slide');
  const dots = document.querySelectorAll('.dot');
  let currentSlide = 0;
  let carouselInterval;
  
  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
      if (dots[i]) dots[i].classList.toggle('active', i === index);
    });
    currentSlide = index;
  }
  
  function nextSlide() {
    const next = (currentSlide + 1) % slides.length;
    showSlide(next);
  }
  
  function startAutoPlay() {
    if (carouselInterval) clearInterval(carouselInterval);
    carouselInterval = setInterval(nextSlide, 5000);
  }
  
  function stopAutoPlay() {
    if (carouselInterval) {
      clearInterval(carouselInterval);
      carouselInterval = null;
    }
  }
  
  startAutoPlay();
  
  dots.forEach(dot => {
    dot.addEventListener('click', () => {
      stopAutoPlay();
      showSlide(parseInt(dot.dataset.slide));
      startAutoPlay();
    });
  });
  
  const carousel = document.querySelector('.hero-carousel');
  if (carousel) {
    carousel.addEventListener('mouseenter', stopAutoPlay);
    carousel.addEventListener('mouseleave', startAutoPlay);
  }
}

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
  initCarousel();
  updateStats();
  renderPosts();
  updatePagination();
  
  document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function() {
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      const tabName = this.dataset.tab;
      switchTab(tabName);
    });
  });
  
  const prevBtn = document.getElementById('prevPage');
  const nextBtn = document.getElementById('nextPage');
  
  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage--;
        renderPosts();
        updatePagination();
        document.querySelector('.posts-scroll-container').scrollTop = 0;
      }
    });
  }
  
  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      const currentPosts = publicationsData[currentTab] || [];
      const totalPages = Math.ceil(currentPosts.length / itemsPerPage);
      if (currentPage < totalPages) {
        currentPage++;
        renderPosts();
        updatePagination();
        document.querySelector('.posts-scroll-container').scrollTop = 0;
      }
    });
  }
  
  const style = document.createElement('style');
  style.textContent = `
    #posts-container { transition: opacity 0.15s ease; }
    .empty-posts { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; border: 1px solid #dee2e6; }
    .empty-posts i { font-size: 48px; color: #6c757d; margin-bottom: 15px; }
    .empty-posts h4 { font-size: 18px; margin-bottom: 8px; color: #343a40; }
    .empty-posts p { font-size: 14px; color: #6c757d; }
    .like-btn.liked { color: #e83e8c; }
    .like-btn.liked i { font-weight: 900; }
    .pagination .page-numbers { display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; align-items: center; }
    .page-number { padding: 7px 12px; border: 1px solid #dee2e6; background: white; cursor: pointer; border-radius: 4px; font-size: 12px; transition: all 0.2s; }
    .page-number:hover { background: #e9ecef; }
    .page-number.active { background: #28a745; color: white; border-color: #28a745; }
    .page-dots { padding: 0 5px; color: #6c757d; }
    #prevPage:disabled, #nextPage:disabled { opacity: 0.5; cursor: not-allowed; }
    #pagination { display: flex; justify-content: center; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 20px; }
    .post-card { transition: transform 0.2s ease, box-shadow 0.2s ease; margin-bottom: 20px; }
    .post-card:hover { transform: translateY(-2px); }
    .posts-scroll-container { height: 700px; overflow-y: auto; padding-right: 10px; }
    .posts-scroll-container::-webkit-scrollbar { width: 8px; }
    .posts-scroll-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .posts-scroll-container::-webkit-scrollbar-thumb { background: #28a745; border-radius: 10px; }
    .posts-scroll-container::-webkit-scrollbar-thumb:hover { background: #146c43; }
    .posts-section { flex: 2; }
  `;
  document.head.appendChild(style);
});
</script>

@endsection