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
    <strong id="statPublications">48</strong>
    <span><i class="fas fa-file-alt"></i> Publications</span>
  </div>
  <div class="stat-item stat-likes">
    <strong id="statLikes">2.3k</strong>
    <span><i class="fas fa-heart"></i> Likes</span>
  </div>
  <div class="stat-item stat-comments">
    <strong id="statComments">127</strong>
    <span><i class="fas fa-comment"></i> Commentaires</span>
  </div>
  <div class="stat-item stat-followers">
    <strong id="statFollowers">156</strong>
    <span><i class="fas fa-users"></i> Abonnés</span>
  </div>
  <div class="stat-item stat-following">
    <strong id="statFollowing">89</strong>
    <span><i class="fas fa-user-plus"></i> Abonnements</span>
  </div>
</div>
          
          
          
        </div>

        <div class="profile-bio">
          <p><i class="fas fa-circle text-green"></i> <strong>Bio :</strong> <span id="profileBio">Éleveur passionné depuis 10 ans, je partage mon expérience pour aider la communauté agricole.</span></p>
          <p><i class="fas fa-circle text-green"></i> <strong>Site web :</strong> <a href="#" id="profileWebsite" target="_blank">www.jean-elevage.com</a></p>
          <p><i class="fas fa-circle text-green"></i> <strong>Email :</strong> <span id="profileEmail">jean.dupont@elevageplus.com</span></p>
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
        <h2><i class="fas fa-file-alt"></i> PUBLICATIONS DE <span id="userNameDisplay">JEAN DUPONT</span></h2>
        <select class="sort-select" id="sortSelect">
          <option value="recent">Trier par : plus récentes</option>
          <option value="oldest">Plus anciennes</option>
          <option value="mostLiked">Plus likées</option>
          <option value="mostViewed">Plus vues</option>
        </select>
      </div>

      <!-- Container des publications -->
      <div id="publicationsContainer"></div>

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
// ================= DONNÉES DU PROFIL =================
const profileData = {
  name: "Jean Dupont",
  location: "Thiès, Sénégal",
  type: "Élevage bovin - 45 animaux",
  memberSince: "mars 2025",
  bio: "Éleveur passionné depuis 10 ans, je partage mon expérience pour aider la communauté agricole. Spécialisé dans l'élevage bovin laitier et la production de viande de qualité.",
  website: "https://www.jean-elevage.com",
  email: "jean.dupont@elevageplus.com",
  avatar: "https://i.pravatar.cc/120?u=jean_dupont",
  cover: "https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=1600",
  isFollowing: false,
  stats: {
    publications: 12,
    likes: 2340,
    comments: 127,
    followers: 156,
    following: 89
  }
};

// ================= DONNÉES DES PUBLICATIONS =================
let publications = [
  {
    id: 1,
    image: "https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=200",
    title: "COMMENT J'AI AUGMENTÉ MA PRODUCTION LAITIÈRE DE 30%",
    content: "Le mois dernier, j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches. Après avoir consulté un nutritionniste animalier, j'ai modifié la ration quotidienne...",
    fullContent: "Le mois dernier, j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches. Après avoir consulté un nutritionniste animalier, j'ai modifié la ration quotidienne en ajoutant des compléments protéinés naturels. Les résultats sont spectaculaires : augmentation de 30% de la production laitière, meilleure santé générale des animaux et réduction des coûts d'alimentation de 15%. Je recommande cette méthode à tous les éleveurs bovins qui souhaitent améliorer leur productivité.",
    likes: 89,
    comments: 23,
    views: 456,
    time: "2 jours",
    timestamp: new Date(2026, 5, 13),
    userLiked: false
  },
  {
    id: 2,
    image: "https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?q=80&w=200",
    title: "ALERTE : FIÈVRE APHTEUSE DANS LA RÉGION DE THIÈS",
    content: "⚠️ Une épidémie de fièvre aphteuse a été signalée dans la région de Thiès. Les symptômes à surveiller : fièvre, aphtes dans la bouche et sur les sabots...",
    fullContent: "Une épidémie de fièvre aphteuse a été signalée dans la région de Thiès. Les symptômes à surveiller : fièvre, aphtes dans la bouche et sur les sabots, baisse de production laitière, boiterie. Recommandations : isolez immédiatement les animaux suspects, contactez votre vétérinaire, évitez tout déplacement d'animaux. La situation est sous surveillance par les autorités vétérinaires.",
    likes: 156,
    comments: 45,
    views: 890,
    time: "2 semaines",
    timestamp: new Date(2026, 5, 1),
    userLiked: false
  },
  {
    id: 3,
    image: "https://images.unsplash.com/photo-1589923188651-268a9765e432?q=80&w=200",
    title: "5 ASTUCES POUR L'HIVERNAGE DES BOVINS",
    content: "L'hivernage approche et il est crucial de bien préparer vos animaux. Voici mes 5 astuces essentielles pour protéger votre troupeau...",
    fullContent: "L'hivernage approche et il est crucial de bien préparer vos animaux. Voici mes 5 astuces essentielles : 1) Stockez suffisamment de fourrage pour au moins 3 mois, 2) Vérifiez et réparez les abris avant la saison des pluies, 3) Programmez les vaccinations au moins 2 semaines avant l'hivernage, 4) Augmentez les compléments énergétiques dans la ration, 5) Surveillez l'état corporel de chaque animal chaque semaine. Avec ces conseils, votre troupeau passera l'hivernage en pleine forme !",
    likes: 234,
    comments: 67,
    views: 1200,
    time: "1 mois",
    timestamp: new Date(2026, 4, 20),
    userLiked: false
  },
  {
    id: 4,
    image: "https://images.unsplash.com/photo-1516467508483-72145faca6d0?q=80&w=200",
    title: "NOUVEAU : INSTALLATION D'UN SYSTÈME D'IRRIGATION",
    content: "Cette semaine, j'ai installé un nouveau système d'irrigation goutte-à-goutte pour mes pâturages. Une véritable révolution pour l'alimentation de mes bêtes...",
    fullContent: "Cette semaine, j'ai installé un nouveau système d'irrigation goutte-à-goutte pour mes pâturages. Une véritable révolution pour l'alimentation de mes bêtes ! L'investissement initial est conséquent (2.5 millions FCFA) mais les bénéfices sont immédiats : économie d'eau de 60%, production fourragère augmentée de 80%, et une meilleure santé des animaux grâce à une alimentation plus régulière.",
    likes: 178,
    comments: 34,
    views: 678,
    time: "2 mois",
    timestamp: new Date(2026, 3, 10),
    userLiked: false
  },
  {
    id: 5,
    image: "https://images.unsplash.com/photo-1559839734-2b71ea197ec2?q=80&w=200",
    title: "MON EXPÉRIENCE AVEC L'ÉLEVAGE BIOLOGIQUE",
    content: "Après 5 ans d'élevage conventionnel, j'ai décidé de passer au bio. Un défi passionnant avec de nombreux apprentissages...",
    fullContent: "Après 5 ans d'élevage conventionnel, j'ai décidé de passer au bio. Les défis étaient nombreux : trouver des aliments certifiés biologiques, adapter les traitements vétérinaires sans antibiotiques systématiques, former le personnel aux nouvelles méthodes. Mais les résultats en valent la peine : animaux plus résistants, qualité de lait supérieure, et un prix de vente augmenté de 40% sur le marché.",
    likes: 312,
    comments: 89,
    views: 2100,
    time: "3 mois",
    timestamp: new Date(2026, 2, 15),
    userLiked: false
  },
  {
    id: 6,
    image: "https://images.unsplash.com/photo-1546448396-6aef80193ceb?q=80&w=200",
    title: "FORMATION GRATUITE SUR L'ÉLEVAGE MODERNE",
    content: "📢 Je propose une formation gratuite le 15 juillet à l'INP de Thiès sur les techniques d'élevage moderne...",
    fullContent: "Je propose une formation gratuite le 15 juillet à l'INP de Thiès sur les techniques d'élevage moderne. Au programme : alimentation rationnée, gestion sanitaire, reproduction assistée, et marketing des produits d'élevage. Places limitées à 50 personnes. Inscriptions par message privé. Ne manquez pas cette opportunité !",
    likes: 267,
    comments: 78,
    views: 1567,
    time: "4 mois",
    timestamp: new Date(2026, 1, 20),
    userLiked: false
  },
  {
    id: 7,
    image: "https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?q=80&w=200",
    title: "NOUVELLE PORTÉE DE 12 CHEVREAUX",
    content: "Grande joie dans mon élevage cette semaine : 12 chevreaux sont nés ! La reproduction caprine est en plein essor...",
    fullContent: "Grande joie dans mon élevage cette semaine : 12 chevreaux sont nés ! La reproduction caprine est en plein essor. Les petits sont en parfaite santé et déjà très actifs. J'ai installé une maternité spéciale pour suivre leur développement. Si vous voulez des conseils sur la mise bas chez les chèvres, n'hésitez pas à me contacter.",
    likes: 145,
    comments: 56,
    views: 789,
    time: "5 mois",
    timestamp: new Date(2026, 0, 10),
    userLiked: false
  },
  {
    id: 8,
    image: "https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=200",
    title: "CONSEILS POUR CHOISIR UN REPRODUCTEUR",
    content: "Le choix d'un bon reproducteur est crucial pour la qualité de votre troupeau. Voici mes critères de sélection...",
    fullContent: "Le choix d'un bon reproducteur est crucial pour la qualité de votre troupeau. Voici mes critères de sélection : 1) Origine et pedigree du taureau, 2) Performance génétique (index), 3) Morphologie et santé, 4) Tempérament, 5) Historique de reproduction. N'hésitez pas à investir dans un bon reproducteur, cela améliorera toute votre production sur plusieurs générations.",
    likes: 198,
    comments: 45,
    views: 945,
    time: "6 mois",
    timestamp: new Date(2025, 11, 5),
    userLiked: false
  }
];

// Variables d'état
let currentSort = 'recent';
let displayedCount = 5;
let toastTimeout = null;
let likedPosts = new Set();

// Initialiser les posts likés depuis le localStorage
const savedLikes = localStorage.getItem('profilLikedPosts');
if (savedLikes) {
  likedPosts = new Set(JSON.parse(savedLikes));
}

// Mettre à jour userLiked dans les publications
publications.forEach(post => {
  if (likedPosts.has(post.id)) {
    post.userLiked = true;
  }
});

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

// ================= FONCTIONS MODAL =================
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
      <div class="modal-body">${content}</div>
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

function openPostModal(post) {
  showModal(`
    <div class="modal-meta">
      <span><i class="fas fa-user"></i> ${profileData.name}</span>
      <span><i class="far fa-clock"></i> ${post.time}</span>
    </div>
    <div class="modal-image"><img src="${post.image}" alt="${post.title}"></div>
    <div class="modal-content-text">
      <h3>${post.title}</h3>
      <p>${post.fullContent || post.content}</p>
    </div>
    <div class="modal-stats">
      <span><i class="fas fa-heart ${post.userLiked ? 'text-red' : ''}" style="color: ${post.userLiked ? '#dc3545' : '#6c757d'}"></i> ${post.likes} likes</span>
      <span><i class="fas fa-comment"></i> ${post.comments} commentaires</span>
      <span><i class="fas fa-eye"></i> ${post.views} vues</span>
    </div>
    <div class="modal-actions">
      <button class="modal-like-btn" onclick="likePostFromModal(${post.id})"><i class="fas fa-heart"></i> ${post.userLiked ? 'Je n\'aime plus' : 'J\'aime'}</button>
      <button class="modal-comment-btn" onclick="openCommentModalFromModal(${post.id}, '${post.title.replace(/'/g, "\\'")}')"><i class="fas fa-comment"></i> Commenter</button>
      <button class="modal-share-btn" onclick="sharePost(${post.id}, '${post.title.replace(/'/g, "\\'")}')"><i class="fas fa-share-alt"></i> Partager</button>
    </div>
  `, post.title);
}

function likePostFromModal(postId) {
  likePost(postId);
  const modal = document.querySelector('.custom-modal');
  if (modal) modal.remove();
}

function openCommentModalFromModal(postId, postTitle) {
  openCommentModal(postId, postTitle);
  const modal = document.querySelector('.custom-modal');
  if (modal) modal.remove();
}

// ================= FONCTIONS D'INTERACTION =================
function likePost(postId) {
  const post = publications.find(p => p.id === postId);
  if (!post) return;
  
  const likeBtn = document.querySelector(`.like-btn-${postId}`);
  const likeCountSpan = document.querySelector(`.like-count-${postId}`);
  
  if (likedPosts.has(postId)) {
    likedPosts.delete(postId);
    post.likes--;
    post.userLiked = false;
    if (likeBtn) likeBtn.classList.remove('liked');
    showToast('Vous n\'aimez plus cette publication', 'info');
  } else {
    likedPosts.add(postId);
    post.likes++;
    post.userLiked = true;
    if (likeBtn) likeBtn.classList.add('liked');
    showToast('Publication aimée !', 'success');
  }
  
  localStorage.setItem('profilLikedPosts', JSON.stringify([...likedPosts]));
  
  if (likeCountSpan) likeCountSpan.textContent = post.likes;
  updateStats();
}

function sharePost(postId, postTitle) {
  const shareUrl = `${window.location.origin}/post/${postId}`;
  navigator.clipboard.writeText(shareUrl).then(() => {
    showToast(`"${postTitle.substring(0, 50)}" - Lien copié !`, 'success');
  }).catch(() => {
    showToast(`Partagez "${postTitle}" avec vos amis !`, 'success');
  });
}

function openCommentModal(postId, postTitle) {
  const comment = prompt(`Laissez un commentaire sur "${postTitle}" :`);
  if (comment && comment.trim()) {
    const post = publications.find(p => p.id === postId);
    if (post) {
      post.comments++;
      const commentSpan = document.querySelector(`.comment-count-${postId}`);
      if (commentSpan) commentSpan.textContent = post.comments;
      updateStats();
      showToast(`Commentaire ajouté : "${comment.substring(0, 50)}${comment.length > 50 ? '...' : ''}"`, 'success');
    }
  } else if (comment !== null) {
    showToast('Le commentaire ne peut pas être vide', 'warning');
  }
}

// ================= FONCTIONS DE TRI =================
function sortPublications() {
  const sorted = [...publications];
  switch(currentSort) {
    case 'oldest':
      sorted.sort((a, b) => a.timestamp - b.timestamp);
      break;
    case 'mostLiked':
      sorted.sort((a, b) => b.likes - a.likes);
      break;
    case 'mostViewed':
      sorted.sort((a, b) => b.views - a.views);
      break;
    default: // recent
      sorted.sort((a, b) => b.timestamp - a.timestamp);
  }
  return sorted;
}

// ================= AFFICHAGE DES PUBLICATIONS =================
function renderPublications() {
  const container = document.getElementById('publicationsContainer');
  const emptyDiv = document.getElementById('emptyPublications');
  const loadMoreBtn = document.getElementById('loadMoreBtn');
  
  if (!container) return;
  
  const sorted = sortPublications();
  const displayed = sorted.slice(0, displayedCount);
  
  if (displayed.length === 0) {
    container.innerHTML = '';
    emptyDiv.style.display = 'flex';
    loadMoreBtn.style.display = 'none';
    return;
  }
  
  emptyDiv.style.display = 'none';
  
  container.innerHTML = displayed.map(post => `
    <article class="pub-card" data-post-id="${post.id}">
      <img src="${post.image}" alt="${post.title}" onclick="openPostModalById(${post.id})" style="cursor: pointer;">
      <div class="pub-content">
        <h3 onclick="openPostModalById(${post.id})" style="cursor: pointer;">${post.title}</h3>
        <p>${post.content.substring(0, 100)}${post.content.length > 100 ? '...' : ''}</p>
        <div class="pub-meta">
          <span><i class="fas fa-heart ${post.userLiked ? 'text-red' : ''}" style="color: ${post.userLiked ? '#dc3545' : '#6c757d'}"></i> <span class="like-count-${post.id}">${post.likes}</span> likes</span>
          <span><i class="far fa-comment-dots"></i> <span class="comment-count-${post.id}">${post.comments}</span> commentaires</span>
          <span><i class="far fa-eye"></i> ${post.views} vues</span>
          <span><i class="far fa-clock"></i> ${post.time}</span>
        </div>
        <div class="post-actions-mini">
          <button class="like-btn like-btn-${post.id} ${post.userLiked ? 'liked' : ''}" onclick="likePost(${post.id})">
            <i class="fas fa-heart"></i> ${post.userLiked ? 'Aimé' : 'Aimer'}
          </button>
          <button onclick="openCommentModal(${post.id}, '${post.title.replace(/'/g, "\\'")}')">
            <i class="fas fa-comment"></i> Commenter
          </button>
          <button onclick="sharePost(${post.id}, '${post.title.replace(/'/g, "\\'")}')">
            <i class="fas fa-share-alt"></i> Partager
          </button>
        </div>
      </div>
      <div class="pub-badge">${post.time}</div>
    </article>
  `).join('');
  
  // Afficher ou cacher le bouton "Afficher plus"
  if (displayedCount >= sorted.length) {
    loadMoreBtn.style.display = 'none';
  } else {
    loadMoreBtn.style.display = 'block';
  }
}

function openPostModalById(postId) {
  const post = publications.find(p => p.id === postId);
  if (post) openPostModal(post);
}

// ================= MISE À JOUR DU PROFIL =================
function updateProfileDisplay() {
  document.getElementById('profileName').textContent = profileData.name;
  document.getElementById('profileLocation').textContent = profileData.location;
  document.getElementById('profileType').textContent = profileData.type;
  document.getElementById('profileMemberSince').textContent = profileData.memberSince;
  document.getElementById('profileBio').textContent = profileData.bio;
  document.getElementById('profileWebsite').textContent = profileData.website;
  document.getElementById('profileWebsite').href = profileData.website;
  document.getElementById('profileEmail').textContent = profileData.email;
  document.getElementById('profileAvatar').src = profileData.avatar;
  document.getElementById('userNameDisplay').textContent = profileData.name.toUpperCase();
  
  // Mettre à jour la cover
  const cover = document.querySelector('.profile-cover');
  if (cover) cover.style.backgroundImage = `url('${profileData.cover}')`;
  
  updateStats();
}

function updateStats() {
  // Calculer les stats réelles depuis les publications
  let totalLikes = 0;
  let totalComments = 0;
  publications.forEach(post => {
    totalLikes += post.likes;
    totalComments += post.comments;
  });
  
  document.getElementById('statPublications').textContent = publications.length;
  document.getElementById('statLikes').textContent = totalLikes > 1000 ? `${(totalLikes / 1000).toFixed(1)}k` : totalLikes;
  document.getElementById('statComments').textContent = totalComments;
  document.getElementById('statFollowers').textContent = profileData.stats.followers;
  document.getElementById('statFollowing').textContent = profileData.stats.following;
}

// ================= FONCTIONS D'ACTIONS PROFIL =================
function initProfileActions() {
  const followBtn = document.getElementById('followBtn');
  const followBtnText = document.getElementById('followBtnText');
  const messageBtn = document.getElementById('messageBtn');
  const shareProfileBtn = document.getElementById('shareProfileBtn');
  const reportBtn = document.getElementById('reportBtn');
  
  if (followBtn) {
    followBtn.addEventListener('click', () => {
      profileData.isFollowing = !profileData.isFollowing;
      if (profileData.isFollowing) {
        followBtnText.textContent = 'Abonné';
        followBtn.innerHTML = '<i class="fas fa-user-check"></i> <span id="followBtnText">Abonné</span>';
        followBtn.style.background = '#6c757d';
        profileData.stats.followers++;
        showToast(`Vous suivez maintenant ${profileData.name}`, 'success');
      } else {
        followBtnText.textContent = 'Suivre';
        followBtn.innerHTML = '<i class="fas fa-user-plus"></i> <span id="followBtnText">Suivre</span>';
        followBtn.style.background = '#28a745';
        profileData.stats.followers--;
        showToast(`Vous ne suivez plus ${profileData.name}`, 'info');
      }
      updateStats();
    });
  }
  
  if (messageBtn) {
    messageBtn.addEventListener('click', () => {
      showToast(`Ouverture de la messagerie avec ${profileData.name}`, 'info');
    });
  }
  
  if (shareProfileBtn) {
    shareProfileBtn.addEventListener('click', () => {
      const url = window.location.href;
      navigator.clipboard.writeText(url).then(() => {
        showToast(`Profil de ${profileData.name} - Lien copié !`, 'success');
      });
    });
  }
  
  if (reportBtn) {
    reportBtn.addEventListener('click', () => {
      const reason = prompt('Motif du signalement :', 'Contenu inapproprié');
      if (reason && reason.trim()) {
        showToast(`Signalement envoyé. Merci pour votre vigilance.`, 'success');
      }
    });
  }
}

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
  updateProfileDisplay();
  renderPublications();
  initProfileActions();
  
  // Événement de tri
  const sortSelect = document.getElementById('sortSelect');
  if (sortSelect) {
    sortSelect.addEventListener('change', function() {
      currentSort = this.value;
      displayedCount = 5;
      renderPublications();
      showToast(`Tri par : ${sortSelect.options[sortSelect.selectedIndex].text}`, 'info');
    });
  }
  
  // Événement "Afficher plus"
  const loadMoreBtn = document.getElementById('loadMoreBtn');
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', () => {
      displayedCount += 3;
      renderPublications();
    });
  }
  
  // Ajouter les styles manquants
  const style = document.createElement('style');
  style.textContent = `
    .post-actions-mini {
      display: flex;
      gap: 15px;
      margin-top: 10px;
    }
    .post-actions-mini button {
      background: none;
      border: none;
      font-size: 12px;
      color: #6c757d;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 4px 8px;
      border-radius: 4px;
      transition: all 0.2s;
    }
    .post-actions-mini button:hover {
      background: #e9ecef;
      color: #28a745;
    }
    .like-btn.liked {
      color: #dc3545;
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
    .custom-toast.success .toast-content { background: #28a745; }
    .custom-toast.danger .toast-content { background: #dc3545; }
    .custom-toast.warning .toast-content { background: #ffc107; color: #343a40; }
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
    .custom-modal.show { visibility: visible; opacity: 1; }
    .modal-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
    }
    .modal-container {
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
    }
    .custom-modal.show .modal-container { transform: scale(1); }
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      border-bottom: 1px solid #dee2e6;
    }
    .modal-header h3 { margin: 0; font-size: 18px; }
    .modal-close {
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #6c757d;
    }
    .modal-body { padding: 20px; }
    .modal-meta {
      display: flex;
      gap: 15px;
      font-size: 13px;
      color: #6c757d;
      margin-bottom: 15px;
    }
    .modal-image img {
      width: 100%;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .modal-content-text h3 {
      font-size: 18px;
      margin-bottom: 10px;
    }
    .modal-content-text p {
      line-height: 1.6;
      margin-bottom: 15px;
    }
    .modal-stats {
      display: flex;
      gap: 20px;
      padding-top: 15px;
      border-top: 1px solid #dee2e6;
      color: #6c757d;
      font-size: 13px;
      margin-bottom: 15px;
    }
    .modal-actions {
      display: flex;
      gap: 10px;
      padding-top: 15px;
      border-top: 1px solid #dee2e6;
    }
    .modal-actions button {
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
    }
    .modal-actions button:hover {
      background: #f8f9fa;
      border-color: #28a745;
      color: #28a745;
    }
    @media (max-width: 768px) {
      .custom-toast {
        left: 15px;
        right: 15px;
        bottom: 15px;
        transform: translateY(100px);
      }
      .custom-toast.show { transform: translateY(0); }
      .post-actions-mini { flex-wrap: wrap; }
      .profile-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
      }
    }
  `;
  document.head.appendChild(style);
});
</script>

@endsection