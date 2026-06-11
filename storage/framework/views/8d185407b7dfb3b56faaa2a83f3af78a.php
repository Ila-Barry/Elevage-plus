<?php $__env->startSection('title', 'Accueil - Élevage+'); ?>

<?php $__env->startSection('content'); ?>

    <!-- style_css -->
    <link rel="stylesheet" href="<?php echo e(asset('css/eleveurCSS/home.css')); ?>">

<main>
  <!-- HERO : 4 images en grille + carte centrée -->
  <section class="hero">
    <div class="hero-bg">
      <div class="hero-img hero-img-1"></div>
      <div class="hero-img hero-img-2"></div>
      <div class="hero-img hero-img-3"></div>
      <div class="hero-img hero-img-4"></div>
    </div>
    <div class="hero-card">
      <h1>Gérez votre élevage<br><span class="text-green">facilement</span></h1>
      <p class="hero-subtitle">Rejoignez la communauté éleveurs</p>
      <p>La plateforme tout-en-un pour gérer vos animaux, vos tâches, vos stocks<br>et échanger avec d'autres éleveurs.</p>
      <a href="#" class="btn btn-success">Commencez gratuitement <i class="fas fa-arrow-right"></i></a>
    </div>
  </section>

  <!-- CONTENU -->
  <div class="container content-wrapper">
    <!-- Publications -->
    <section class="posts">
      <div class="section-header">
        <h2>DERNIÈRES PUBLICATIONS</h2>
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
           <a href="<?php echo e(url('/profilEleveur')); ?>"> <h4>Jean Dupont - Éleveur bovin <i class="fas fa-circle-check text-info"></i> • 2 jours ago</h4></a>
            <div class="post-meta">
              <span><i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="far fa-star"></i> (45 likes)</span>
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
            <h4>Jean Dupont - Éleveur bovin <i class="fas fa-circle-check text-info"></i> • 2 jours ago</h4>
            <div class="post-meta">
              <span><i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="far fa-star"></i> (45 likes)</span>
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
          <img src="https://i.pravatar.cc/40?u=jean3" class="avatar" alt="Jean Dupont">
          <div class="post-info">
            <h4>Jean Dupont - Éleveur bovin <i class="fas fa-circle-check text-info"></i> • 2 jours ago</h4>
            <div class="post-meta">
              <span><i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="fas fa-star text-warning"></i> <i class="far fa-star"></i> (45 likes)</span>
              <span><i class="far fa-comment-dots"></i> 12 commentaires</span>
              <span><i class="far fa-eye"></i> 230 vues</span>
            </div>
          </div>
        </div>
        <div class="post-content">
          <img src="https://images.unsplash.com/photo-1589923188651-268a9765e432?q=80&w=250" alt="Éleveuse">
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
        <button>4</button>
        <button>suivante <i class="fas fa-chevron-right"></i></button>
      </div>
    </section>

    <!-- Sidebar -->
    <aside class="sidebar">
      <h3>STATISTIQUES DE LA COMMUNAUTÉ</h3>
      <div class="stats-grid">
        <div class="stat-box stat-green">
          <div class="stat-num">127</div>
          <div>éleveurs</div>
        </div>
        <div class="stat-box stat-blue">
          <div class="stat-num">345</div>
          <div>articles</div>
        </div>
        <div class="stat-box stat-pink">
          <div class="stat-num">2.5k</div>
          <div>likes</div>
        </div>
        <div class="stat-box stat-mint">
          <div class="stat-num">890</div>
          <div>coms</div>
        </div>
      </div>

      <h3>POURQUOI REJOINDRE ÉLEVAGE+ ?</h3>
      <div class="why-list">
        <div class="why-item">
          <i class="fas fa-chart-bar text-green"></i>
          <div>
            <strong>Suivi professionnel</strong>
            <p>Gérez vos animaux, vos tâches, vos stocks et toutes vos activités d'élevage facilement.</p>
          </div>
        </div>
        <div class="why-item">
          <i class="fas fa-users text-blue"></i>
          <div>
            <strong>Communauté d'entraide</strong>
            <p>Échangez avec d'autres éleveurs, partagez vos expériences et apprenez ensemble.</p>
          </div>
        </div>
        <div class="why-item">
          <i class="fas fa-bell text-orange"></i>
          <div>
            <strong>Alertes intelligentes</strong>
            <p>Recevez des rappels et des alertes pour ne rien oublier et prendre les bonnes décisions.</p>
          </div>
        </div>
      </div>

      <div class="cta-box">
        <h4>Prête améliorer votre élevage ?</h4>
        <p>rejoignez les centaines d'éleveurs qui nous font déjà confiance !</p>
        <a href="#" class="btn btn-success w-100">Créez votre compte gratuitement</a>
      </div>

      <div class="sidebar-imgs">
        <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=400" alt="Vaches">
        <img src="https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?q=80&w=400" alt="Moutons">
      </div>
    </aside>
  </div>
</main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Projets\Elevage-plus\resources\views/home.blade.php ENDPATH**/ ?>