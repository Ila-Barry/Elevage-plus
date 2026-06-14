@extends('layouts.app')

@section('title', 'Accueil - Élevage+')

@section('content')

<!-- style_css -->
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/home.css') }}">

<main>
  <!-- HERO CARROUSEL 5 IMAGES + CARTE CENTRÉE -->
  <section class="hero-carousel">
    <div class="carousel-wrapper">
      <div class="carousel-slide active" style="background-image: url('https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=1600');"></div>
      <div class="carousel-slide" style="background-image: url('{{ asset('images/imageacceuill3.jpg') }}')"></div>
      <div class="carousel-slide" style="background-image: url('{{ asset('images/imageacceuil1.jpg') }}')"></div>
      <div class="carousel-slide" style="background-image: url('https://images.unsplash.com/photo-1589923188651-268a9765e432?q=80&w=1600');"></div>
      <div class="carousel-slide" style="background-image: url('{{ asset('images/imageacceuil4.jpg') }}')"></div>

    </div>

    <!-- Dots -->
    <div class="carousel-dots">
      <span class="dot active" data-slide="0"></span>
      <span class="dot" data-slide="1"></span>
      <span class="dot" data-slide="2"></span>
      <span class="dot" data-slide="3"></span>
      <span class="dot" data-slide="4"></span>
    </div>

    <!-- Carte centrée -->
    <div class="hero-card">
      <h1>Gérez votre élevage<br><span class="text-green">facilement</span></h1>
      <p class="hero-subtitle"><i class="fas fa-users"></i> Rejoignez la communauté éleveurs</p>
      <p>La plateforme tout-en-un pour gérer vos animaux, vos tâches, vos stocks<br>et échanger avec d'autres éleveurs.</p>
      <a href="#" class="btn btn-success"><i class="fas fa-rocket"></i> Commencez gratuitement <i class="fas fa-arrow-right"></i></a>
    </div>
  </section>

  <!-- CONTENU -->
  <div class="container content-wrapper">
    <!-- Colonne gauche : Publications -->
    <section class="posts">
      <div class="section-header">
        <h2><i class="fas fa-newspaper"></i> DERNIÈRES PUBLICATIONS</h2>
      </div>

      <!-- TABS -->
      <div class="bottom-tabs">
        <h3><i class="fas fa-filter"></i> Filtrer les publications</h3>
        <div class="tabs">
          <button class="tab active"><i class="fas fa-lightbulb"></i> Conseils</button>
          <button class="tab"><i class="fas fa-user-edit"></i> Expériences</button>
          <button class="tab"><i class="fas fa-bell"></i> Alertes</button>
          <button class="tab"><i class="fas fa-fire"></i> Tendances</button>
        </div>
      </div>

      <!-- Post 1 -->
      <article class="post-card">
        <div class="post-top">
          <img src="https://i.pravatar.cc/40?u=jean1" class="avatar" alt="Jean Dupont">
          <div class="post-info">
           <a href="{{ url('/profilEleveur') }}"> <h4>Jean Dupont - Éleveur bovin <i class="fas fa-circle-check text-info"></i> • 2 jours</h4></a>
            <div class="post-meta">
              <span><i class="fas fa-star text-warning"></i> 4.0 (45 likes)</span>
              <span><i class="far fa-comment-dots"></i> 12 commentaires</span>
              <span><i class="far fa-eye"></i> 230 vues</span>
            </div>
          </div>
        </div>
        <div class="post-content">
          <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=250" alt="Vaches">
          <div class="post-text">
            <h3>COMMENT J'AI AUGMENTÉ MA PRODUCTION LAITIÈRE DE 30%</h3>
            <p>Le mois dernier, j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...</p>
            <div class="post-actions">
              <button><i class="far fa-thumbs-up"></i> Liker 45</button>
              <button><i class="far fa-comment-dots"></i> Commenter 12</button>
              <button><i class="fas fa-share-alt"></i> Partager</button>
              <a href="#" class="read-more">Lire la suite <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </article>

      <!-- Post 2 -->
      <article class="post-card">
        <div class="post-top">
          <img src="https://i.pravatar.cc/40?u=jean2" class="avatar" alt="Jean Dupont">
          <div class="post-info">
              <a href="{{ url('/profilEleveur') }}"> <h4>Jean Dupont - Éleveur bovin <i class="fas fa-circle-check text-info"></i> • 2 jours</h4></a>
            <div class="post-meta">
              <span><i class="fas fa-star text-warning"></i> 4.0 (45 likes)</span>
              <span><i class="far fa-comment-dots"></i> 12 commentaires</span>
              <span><i class="far fa-eye"></i> 230 vues</span>
            </div>
          </div>
        </div>
        <div class="post-content">
          <img src="https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?q=80&w=250" alt="Moutons">
          <div class="post-text">
            <h3>COMMENT J'AI AUGMENTÉ MA PRODUCTION LAITIÈRE DE 30%</h3>
            <p>Le mois dernier, j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...</p>
            <div class="post-actions">
              <button><i class="far fa-thumbs-up"></i> Liker 45</button>
              <button><i class="far fa-comment-dots"></i> Commenter 12</button>
              <button><i class="fas fa-share-alt"></i> Partager</button>
              <a href="#" class="read-more">Lire la suite <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </article>

      <!-- Post 3 -->
      <article class="post-card">
        <div class="post-top">
          <img src="https://i.pravatar.cc/40?u=jean1" class="avatar" alt="Jean Dupont">
          <div class="post-info">
           <a href="{{ url('/profilEleveur') }}"> <h4>Jean Dupont - Éleveur bovin <i class="fas fa-circle-check text-info"></i> • 2 jours</h4></a>
            <div class="post-meta">
              <span><i class="fas fa-star text-warning"></i> 4.0 (45 likes)</span>
              <span><i class="far fa-comment-dots"></i> 12 commentaires</span>
              <span><i class="far fa-eye"></i> 230 vues</span>
            </div>
          </div>
        </div>
        <div class="post-content">
          <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=250" alt="Vaches">
          <div class="post-text">
            <h3>COMMENT J'AI AUGMENTÉ MA PRODUCTION LAITIÈRE DE 30%</h3>
            <p>Le mois dernier, j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...</p>
            <div class="post-actions">
              <button><i class="far fa-thumbs-up"></i> Liker 45</button>
              <button><i class="far fa-comment-dots"></i> Commenter 12</button>
              <button><i class="fas fa-share-alt"></i> Partager</button>
              <a href="#" class="read-more">Lire la suite <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </article>

      <!-- Post 4 -->
      <article class="post-card">
        <div class="post-top">
          <img src="https://i.pravatar.cc/40?u=jean2" class="avatar" alt="Jean Dupont">
          <div class="post-info">
                      <a href="{{ url('/profilEleveur') }}"> <h4>Jean Dupont - Éleveur bovin <i class="fas fa-circle-check text-info"></i> • 2 jours</h4></a>
            <div class="post-meta">
              <span><i class="fas fa-star text-warning"></i> 4.0 (45 likes)</span>
              <span><i class="far fa-comment-dots"></i> 12 commentaires</span>
              <span><i class="far fa-eye"></i> 230 vues</span>
            </div>
          </div>
        </div>
        <div class="post-content">
          <img src="https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?q=80&w=250" alt="Moutons">
          <div class="post-text">
            <h3>COMMENT J'AI AUGMENTÉ MA PRODUCTION LAITIÈRE DE 30%</h3>
            <p>Le mois dernier, j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...</p>
            <div class="post-actions">
              <button><i class="far fa-thumbs-up"></i> Liker 45</button>
              <button><i class="far fa-comment-dots"></i> Commenter 12</button>
              <button><i class="fas fa-share-alt"></i> Partager</button>
              <a href="#" class="read-more">Lire la suite <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </article>

      <!-- Pagination -->
      <div class="pagination">
        <button disabled><i class="fas fa-chevron-left"></i> précédente</button>
        <button class="active">1</button>
        <button>2</button>
        <button>3</button>
        <button>suivante <i class="fas fa-chevron-right"></i></button>
      </div>
    </section>

    <!-- Sidebar droite -->
    <aside class="sidebar">
      <div class="bottom-stats">
        <h3><i class="fas fa-chart-pie"></i> STATISTIQUES DE LA COMMUNAUTÉ</h3>
        <div class="stats-grid">
          <div class="stat-box stat-green">
            <i class="fas fa-user-friends stat-icon"></i>
            <div class="stat-num">127</div>
            <div>éleveurs</div>
          </div>
          <div class="stat-box stat-blue">
            <i class="fas fa-file-alt stat-icon"></i>
            <div class="stat-num">345</div>
            <div>articles</div>
          </div>
          <div class="stat-box stat-pink">
            <i class="fas fa-heart stat-icon"></i>
            <div class="stat-num">2.5k</div>
            <div>likes</div>
          </div>
          <div class="stat-box stat-mint">
            <i class="fas fa-comments stat-icon"></i>
            <div class="stat-num">890</div>
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
        <a href="{{ url('../auth/register') }}" class="btn btn-success w-100"><i class="fas fa-user-plus"></i> Créez votre compte gratuitement</a>
      </div>

      <div class="sidebar-imgs">
        <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=400" alt="Vaches">
        <img src="https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?q=80&w=400" alt="Moutons">
      </div>
    </aside>
  </div>
</main>

<script>
// Carrousel auto 5s
document.addEventListener('DOMContentLoaded', function() {
  const slides = document.querySelectorAll('.carousel-slide');
  const dots = document.querySelectorAll('.dot');
  let currentSlide = 0;
  
  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
      dots[i].classList.toggle('active', i === index);
    });
    currentSlide = index;
  }
  
  function nextSlide() {
    let next = (currentSlide + 1) % slides.length;
    showSlide(next);
  }
  
  // Auto play
  setInterval(nextSlide, 5000);
  
  // Click dots
  dots.forEach(dot => {
    dot.addEventListener('click', () => {
      showSlide(parseInt(dot.dataset.slide));
    });
  });
});
</script>

@endsection