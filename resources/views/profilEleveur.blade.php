@extends('layouts.app')

@section('title', 'Profil - Élevage+')

@section('content')

    <!-- style_css -->
    <link rel="stylesheet" href="{{ asset('css/eleveurCSS/profilEleveur.css') }}">

    <main>
  <!-- COVER avec background image -->
  <section class="profile-cover">
    <div class="profile-card">
      <div class="profile-left">
        <div class="profile-avatar">
          <img src="https://i.pravatar.cc/120?u=jean_dupont" alt="Jean Dupont">
          <span class="badge-online"></span>
        </div>
        <div class="profile-info">
          <h1>Jean Dupont</h1>
          <p class="location"><i class="fas fa-map-marker-alt"></i> Thiès, Sénégal</p>
          <p class="type"><i class="fas fa-cow"></i> Élevage bovin - 45 animaux</p>
          <p class="member"><i class="far fa-calendar-alt"></i> Membre depuis mars 2025</p>
        </div>
      </div>
      
      <div class="profile-stats">
        <div class="stat-item">
          <strong>48</strong>
          <span>Publications</span>
        </div>
        <div class="stat-item stat-heart">
          <strong>2.3k</strong>
          <span>Likes</span>
        </div>
        <div class="stat-item">
          <strong>127</strong>
          <span>Commentaires</span>
        </div>
      </div>

      <div class="profile-bio">
        <p><i class="fas fa-circle text-green"></i> <strong>Bio :</strong> Éleveur passionné depuis 10 ans, je partage mon expérience pour aider la communauté agricole.</p>
        <p><i class="fas fa-circle text-green"></i> <strong>Site web :</strong> <a href="http://www.jean-elevage.com" target="_blank">www.jean-elevage.com</a></p>
      </div>

      <div class="profile-actions">
        <button class="btn btn-suivre">Suivre</button>
        <button class="btn btn-outline"><i class="far fa-comment-dots"></i> Commenter</button>
        <button class="btn btn-outline"><i class="fas fa-share-alt"></i> partager</button>
      </div>
    </div>
  </section>

  <!-- PUBLICATIONS -->
  <div class="container">
    <section class="publications">
      <div class="pub-header">
        <h2><i class="fas fa-file-alt"></i> PUBLICATIONS DE JEAN DUPONT</h2>
        <select class="sort-select">
          <option>trier par : plus récentes</option>
          <option>plus anciennes</option>
          <option>plus likées</option>
        </select>
      </div>

      <!-- Post 1 -->
      <article class="pub-card">
        <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=200" alt="Vaches">
        <div class="pub-content">
          <h3>COMMENT J'AI AUGMENTÉ MA PRODUCTION LAITIÈRE</h3>
          <p>Le mois dernier j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...</p>
          <div class="pub-meta">
            <span><i class="fas fa-heart text-red"></i> 45 likes</span>
            <span><i class="far fa-comment-dots"></i> 12 commentaires</span>
            <span><i class="far fa-eye"></i> 230 vues</span>
          </div>
        </div>
        <div class="pub-badge">2j</div>
      </article>

      <!-- Post 2 -->
      <article class="pub-card">
        <img src="https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?q=80&w=200" alt="Moutons">
        <div class="pub-content">
          <h3>ALERTE : FIÈVRE APHTEUSE DANS LA RÉGION</h3>
          <p>Le mois dernier j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...</p>
          <div class="pub-meta">
            <span><i class="fas fa-heart text-red"></i> 45 likes</span>
            <span><i class="far fa-comment-dots"></i> 12 commentaires</span>
            <span><i class="far fa-eye"></i> 230 vues</span>
          </div>
        </div>
        <div class="pub-badge">2m</div>
      </article>

      <!-- Post 3 -->
      <article class="pub-card">
        <img src="https://images.unsplash.com/photo-1589923188651-268a9765e432?q=80&w=200" alt="Bovins">
        <div class="pub-content">
          <h3>5 ASTUCES POUR L'HIVERNAGE DES BOVINS</h3>
          <p>Le mois dernier j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...</p>
          <div class="pub-meta">
            <span><i class="fas fa-heart text-red"></i> 45 likes</span>
            <span><i class="far fa-comment-dots"></i> 12 commentaires</span>
            <span><i class="far fa-eye"></i> 230 vues</span>
          </div>
        </div>
        <div class="pub-badge">27j</div>
      </article>

       <!-- Post 3 -->
      <article class="pub-card">
        <img src="https://images.unsplash.com/photo-1589923188651-268a9765e432?q=80&w=200" alt="Bovins">
        <div class="pub-content">
          <h3>5 ASTUCES POUR L'HIVERNAGE DES BOVINS</h3>
          <p>Le mois dernier j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...</p>
          <div class="pub-meta">
            <span><i class="fas fa-heart text-red"></i> 45 likes</span>
            <span><i class="far fa-comment-dots"></i> 12 commentaires</span>
            <span><i class="far fa-eye"></i> 230 vues</span>
          </div>
        </div>
        <div class="pub-badge">27j</div>
      </article>

       <!-- Post 3 -->
      <article class="pub-card">
        <img src="https://images.unsplash.com/photo-1589923188651-268a9765e432?q=80&w=200" alt="Bovins">
        <div class="pub-content">
          <h3>5 ASTUCES POUR L'HIVERNAGE DES BOVINS</h3>
          <p>Le mois dernier j'ai appliqué une nouvelle méthode d'alimentation à mon troupeau de 45 vaches...</p>
          <div class="pub-meta">
            <span><i class="fas fa-heart text-red"></i> 45 likes</span>
            <span><i class="far fa-comment-dots"></i> 12 commentaires</span>
            <span><i class="far fa-eye"></i> 230 vues</span>
          </div>
        </div>
        <div class="pub-badge">27j</div>
      </article>

      <button class="btn-more">Afficher plus de publications <i class="fas fa-plus"></i></button>
    </section>
  </div>
</main>
    
@endsection
