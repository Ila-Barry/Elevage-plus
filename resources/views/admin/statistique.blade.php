{{-- resources/views/admin/statistique.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Statistiques globales')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/statistique.css') }}">
@endpush

@push('scripts')
<script>
    // Dropdown personnalisé pour "Ce mois"
    function toggleMonthDropdown() {
        const menu = document.getElementById('monthDropdownMenu');
        menu.classList.toggle('show');
    }
    // Fermer si clic en dehors
    document.addEventListener('click', function(event) {
        const dropdown = document.querySelector('.custom-dropdown');
        if (dropdown && !dropdown.contains(event.target)) {
            const menu = document.getElementById('monthDropdownMenu');
            if (menu) menu.classList.remove('show');
        }
    });
</script>
@endpush

@section('content')
    <div class="dashboard-wrapper">

        {{-- ==================== EN-TÊTE + ACTIONS ==================== --}}
        <div class="stats-header">
            <div class="header-left">
                <h1><i class="fas fa-chart-pie"></i> STATISTIQUES GLOBALES</h1>
                <p class="subtitle">Analyser complètement les performances de la plateforme</p>
            </div>
            <div class="header-actions">
                {{-- Dropdown personnalisé "Ce mois" avec les 12 mois --}}
                <div class="custom-dropdown">
                    <button class="btn btn-outline dropdown-toggle" id="monthDropdownBtn" onclick="toggleMonthDropdown()">
                        Ce mois <i class="fas fa-chevron-down"></i>
                    </button>
                    <ul class="custom-dropdown-menu" id="monthDropdownMenu">
                        <li><a href="#">Janvier</a></li>
                        <li><a href="#">Février</a></li>
                        <li><a href="#">Mars</a></li>
                        <li><a href="#">Avril</a></li>
                        <li><a href="#">Mai</a></li>
                        <li><a href="#">Juin</a></li>
                        <li><a href="#">Juillet</a></li>
                        <li><a href="#">Août</a></li>
                        <li><a href="#">Septembre</a></li>
                        <li><a href="#">Octobre</a></li>
                        <li><a href="#">Novembre</a></li>
                        <li><a href="#">Décembre</a></li>
                    </ul>
                </div>
                <button class="btn btn-outline"><i class="fas fa-file-pdf"></i> Exporter PDF</button>
                <button class="btn btn-outline"><i class="fas fa-envelope"></i> Envoyer rapport</button>
                <button class="btn btn-primary"><i class="fas fa-chart-simple"></i> Comparer</button>
            </div>
        </div>

        {{-- ==================== ÉVOLUTION DE LA PLATEFORME (COURBE SVG) ==================== --}}
        <section class="stats-section">
            <h2><i class="fas fa-chart-line"></i> Évolution de la plateforme</h2>
            <div class="evolution-chart-wrapper">
                <div class="chart-container">
                    <svg viewBox="0 0 700 300" preserveAspectRatio="xMidYMid meet">
                        <!-- Grille horizontale -->
                        <line x1="60" y1="250" x2="660" y2="250" stroke="#e9ecef" stroke-width="1" />
                        <line x1="60" y1="200" x2="660" y2="200" stroke="#e9ecef" stroke-width="1" stroke-dasharray="4" />
                        <line x1="60" y1="150" x2="660" y2="150" stroke="#e9ecef" stroke-width="1" stroke-dasharray="4" />
                        <line x1="60" y1="100" x2="660" y2="100" stroke="#e9ecef" stroke-width="1" stroke-dasharray="4" />
                        <line x1="60" y1="50" x2="660" y2="50" stroke="#e9ecef" stroke-width="1" stroke-dasharray="4" />

                        <!-- Axe X : j, f, m, a, m, j, j -->
                        <text x="100" y="275" fill="#6c757d" font-size="13" text-anchor="middle" font-weight="500">j</text>
                        <text x="190" y="275" fill="#6c757d" font-size="13" text-anchor="middle" font-weight="500">f</text>
                        <text x="280" y="275" fill="#6c757d" font-size="13" text-anchor="middle" font-weight="500">m</text>
                        <text x="370" y="275" fill="#6c757d" font-size="13" text-anchor="middle" font-weight="500">a</text>
                        <text x="460" y="275" fill="#6c757d" font-size="13" text-anchor="middle" font-weight="500">m</text>
                        <text x="550" y="275" fill="#6c757d" font-size="13" text-anchor="middle" font-weight="500">j</text>
                        <text x="640" y="275" fill="#6c757d" font-size="13" text-anchor="middle" font-weight="500">j</text>

                        <!-- Courbe Utilisateurs (bleue) -->
                        <polyline points="100,230 190,210 280,180 370,140 460,90 550,50 640,30" fill="none" stroke="#4e73df" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="100" cy="230" r="4.5" fill="#4e73df" />
                        <circle cx="190" cy="210" r="4.5" fill="#4e73df" />
                        <circle cx="280" cy="180" r="4.5" fill="#4e73df" />
                        <circle cx="370" cy="140" r="4.5" fill="#4e73df" />
                        <circle cx="460" cy="90" r="4.5" fill="#4e73df" />
                        <circle cx="550" cy="50" r="4.5" fill="#4e73df" />
                        <circle cx="640" cy="30" r="4.5" fill="#4e73df" />

                        <!-- Courbe Publications (verte) -->
                        <polyline points="100,240 190,225 280,200 370,170 460,130 550,90 640,60" fill="none" stroke="#1cc88a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="100" cy="240" r="4.5" fill="#1cc88a" />
                        <circle cx="190" cy="225" r="4.5" fill="#1cc88a" />
                        <circle cx="280" cy="200" r="4.5" fill="#1cc88a" />
                        <circle cx="370" cy="170" r="4.5" fill="#1cc88a" />
                        <circle cx="460" cy="130" r="4.5" fill="#1cc88a" />
                        <circle cx="550" cy="90" r="4.5" fill="#1cc88a" />
                        <circle cx="640" cy="60" r="4.5" fill="#1cc88a" />

                        <!-- Courbe Commentaires (jaune) -->
                        <polyline points="100,250 190,240 280,220 370,190 460,160 550,120 640,90" fill="none" stroke="#f6c23e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="100" cy="250" r="4.5" fill="#f6c23e" />
                        <circle cx="190" cy="240" r="4.5" fill="#f6c23e" />
                        <circle cx="280" cy="220" r="4.5" fill="#f6c23e" />
                        <circle cx="370" cy="190" r="4.5" fill="#f6c23e" />
                        <circle cx="460" cy="160" r="4.5" fill="#f6c23e" />
                        <circle cx="550" cy="120" r="4.5" fill="#f6c23e" />
                        <circle cx="640" cy="90" r="4.5" fill="#f6c23e" />

                        <!-- Courbe Likes (rouge) -->
                        <polyline points="100,260 190,250 280,235 370,210 460,180 550,150 640,120" fill="none" stroke="#e74a3b" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="100" cy="260" r="4.5" fill="#e74a3b" />
                        <circle cx="190" cy="250" r="4.5" fill="#e74a3b" />
                        <circle cx="280" cy="235" r="4.5" fill="#e74a3b" />
                        <circle cx="370" cy="210" r="4.5" fill="#e74a3b" />
                        <circle cx="460" cy="180" r="4.5" fill="#e74a3b" />
                        <circle cx="550" cy="150" r="4.5" fill="#e74a3b" />
                        <circle cx="640" cy="120" r="4.5" fill="#e74a3b" />

                        <rect x="60" y="10" width="600" height="250" fill="none" stroke="#dee2e6" stroke-width="1" />
                    </svg>
                </div>

                {{-- Légende de la courbe --}}
                <div class="chart-legend">
                    <span><span class="legend-dot" style="background:#4e73df;"></span> utilisateurs</span>
                    <span><span class="legend-dot" style="background:#1cc88a;"></span> publications</span>
                    <span><span class="legend-dot" style="background:#f6c23e;"></span> commentaires</span>
                    <span><span class="legend-dot" style="background:#e74a3b;"></span> likes</span>
                </div>
            </div>
        </section>

        {{-- ==================== STATISTIQUES UTILISATEURS ==================== --}}
        <section class="stats-section">
            <h2><i class="fas fa-users"></i> STATISTIQUES UTILISATEURS</h2>
            <div class="row g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card blue">
                        <div class="stat-number">127</div>
                        <div class="stat-label">Total utilisateurs</div>
                        <div class="stat-detail">Nouveaux ce mois : 12</div>
                        <div class="stat-detail">Utilisateurs actifs : 118</div>
                        <div class="stat-detail">Taux de rétention : 92%</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card green">
                        <div class="stat-number">12</div>
                        <div class="stat-label">Nouveaux ce mois</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card orange">
                        <div class="stat-number">118</div>
                        <div class="stat-label">Utilisateurs actifs</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card purple">
                        <div class="stat-number">92%</div>
                        <div class="stat-label">Taux de rétention</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================== ÉLEVEURS & ADMINS ==================== --}}
        <section class="stats-section">
            <h2><i class="fas fa-user-tie"></i> Éleveurs</h2>
            <div class="row">
                <div class="col-md-8">
                    <div class="role-distribution">
                        <div class="role-item">
                            <span class="role-label">Éleveurs</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 89%; background: #4e73df;">89%</div>
                            </div>
                            <span class="role-count">115</span>
                        </div>
                        <div class="role-item">
                            <span class="role-label">Admins</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 3%; background: #1cc88a;">3%</div>
                            </div>
                            <span class="role-count">3</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    {{-- Espace vide --}}
                </div>
            </div>
        </section>

        {{-- ==================== ENGAGEMENT ==================== --}}
        <section class="stats-section">
            <h2><i class="fas fa-chart-simple"></i> ENGAGEMENT</h2>
            <div class="row g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number">4.2%</div>
                        <div class="stat-label">Taux d’engagement moyen</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number">18</div>
                        <div class="stat-label"><i class="fas fa-heart" style="color:#e74a3b;"></i> Likes par publication</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number">6</div>
                        <div class="stat-label"><i class="fas fa-comment" style="color:#4e73df;"></i> Commentaires par post</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number">245</div>
                        <div class="stat-label"><i class="fas fa-eye" style="color:#f6c23e;"></i> Vues moyennes</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================== MEILLEUR POST ==================== --}}
        <section class="stats-section">
            <h2><i class="fas fa-star"></i> MEILLEUR POST</h2>
            <div class="best-post">
                <div class="post-content">
                    <span class="post-title">« Astuces pour élevage reussite »</span>
                    <div class="post-metrics">
                        <span><i class="fas fa-heart" style="color:#e74a3b;"></i> 89 likes</span>
                        <span><i class="fas fa-eye" style="color:#4e73df;"></i> 670 vues</span>
                    </div>
                </div>
                <div class="post-badge">
                    <i class="fas fa-crown"></i>
                </div>
            </div>
        </section>

        {{-- ==================== CATÉGORIES POPULAIRES (avec camembert) ==================== --}}
        <section class="stats-section">
            <h2><i class="fas fa-tags"></i> CATÉGORIES POPULAIRES</h2>
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="category-bars">
                        <div class="category-item">
                            <span class="category-label">Conseils</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 45%; background: #4e73df;">45%</div>
                            </div>
                        </div>
                        <div class="category-item">
                            <span class="category-label">Expériences</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 30%; background: #1cc88a;">30%</div>
                            </div>
                        </div>
                        <div class="category-item">
                            <span class="category-label">Alertes</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 15%; background: #f6c23e;">15%</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-center">
                    <div class="pie-chart-wrapper">
                        <svg viewBox="0 0 100 100" width="180" height="180">
                            {{-- Part Conseil : 45% (angle 162°) --}}
                            <path d="M 50 50 L 50 10 A 40 40 0 0 1 62.36 88.04 Z" fill="#4e73df" />
                            {{-- Part Expériences : 30% (angle 108°) --}}
                            <path d="M 50 50 L 62.36 88.04 A 40 40 0 0 1 10 50 Z" fill="#1cc88a" />
                            {{-- Part Alertes : 15% (angle 54°) --}}
                            <path d="M 50 50 L 10 50 A 40 40 0 0 1 26.5 17.64 Z" fill="#f6c23e" />
                            {{-- Le reste (10%) n'est pas dessiné, cercle ouvert --}}
                        </svg>
                    </div>
                    <div class="pie-legend">
                        <span><span class="color-dot" style="background:#4e73df;"></span> Conseils 45%</span>
                        <span><span class="color-dot" style="background:#1cc88a;"></span> Expériences 30%</span>
                        <span><span class="color-dot" style="background:#f6c23e;"></span> Alertes 15%</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================== ACTIVITÉ PAR JOUR ==================== --}}
        <section class="stats-section">
            <h2><i class="fas fa-calendar-day"></i> ACTIVITÉ PAR JOUR</h2>
            <div class="activity-chart">
                <div class="bar-container">
                    <div class="bar-wrapper">
                        <div class="bar" style="height: 45%;"><span>45</span></div>
                        <span class="day-label">Lun</span>
                    </div>
                    <div class="bar-wrapper">
                        <div class="bar" style="height: 52%;"><span>52</span></div>
                        <span class="day-label">Mar</span>
                    </div>
                    <div class="bar-wrapper">
                        <div class="bar" style="height: 48%;"><span>48</span></div>
                        <span class="day-label">Mer</span>
                    </div>
                    <div class="bar-wrapper">
                        <div class="bar" style="height: 67%;"><span>67</span></div>
                        <span class="day-label">Jeu</span>
                    </div>
                    <div class="bar-wrapper">
                        <div class="bar" style="height: 89%;"><span>89</span></div>
                        <span class="day-label">Ven</span>
                    </div>
                    <div class="bar-wrapper">
                        <div class="bar" style="height: 34%;"><span>34</span></div>
                        <span class="day-label">Sam</span>
                    </div>
                    <div class="bar-wrapper">
                        <div class="bar" style="height: 28%;"><span>28</span></div>
                        <span class="day-label">Dim</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================== TOP 10 DES ÉLEVEURS LES PLUS ACTIFS ==================== --}}
        <section class="stats-section">
            <h2><i class="fas fa-trophy"></i> TOP 10 DES ÉLEVEURS LES PLUS ACTIFS</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>RANG</th>
                            <th>NOM</th>
                            <th>POSTS</th>
                            <th>LIKES</th>
                            <th>COMMENTAIRES</th>
                            <th>CONVERSATION</th>
                            <th>CONTACT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $eleveurs = [
                                ['rang' => 1, 'nom' => 'Jean Dupont', 'posts' => 48, 'likes' => '2,345', 'commentaires' => 127, 'conversation' => 34],
                                ['rang' => 2, 'nom' => 'Marie Diop', 'posts' => 42, 'likes' => '1,890', 'commentaires' => 98, 'conversation' => 28],
                                ['rang' => 3, 'nom' => 'Amadou Sy', 'posts' => 38, 'likes' => '1,567', 'commentaires' => 87, 'conversation' => 25],
                                ['rang' => 4, 'nom' => 'Ibrahima Fall', 'posts' => 31, 'likes' => '1,234', 'commentaires' => 65, 'conversation' => 19],
                                ['rang' => 5, 'nom' => 'Fatou Sow', 'posts' => 28, 'likes' => '987', 'commentaires' => 54, 'conversation' => 15],
                                ['rang' => 6, 'nom' => 'Aliou Ndiaye', 'posts' => 27, 'likes' => '986', 'commentaires' => 50, 'conversation' => 14],
                                ['rang' => 7, 'nom' => 'Moussa Diallo', 'posts' => 24, 'likes' => '984', 'commentaires' => 46, 'conversation' => 12],
                                ['rang' => 8, 'nom' => 'Oumou Kâ', 'posts' => 23, 'likes' => '982', 'commentaires' => 42, 'conversation' => 11],
                                ['rang' => 9, 'nom' => 'Cheikh Diop', 'posts' => 20, 'likes' => '983', 'commentaires' => 40, 'conversation' => 9],
                                ['rang' => 10, 'nom' => 'Aminata Seck', 'posts' => 15, 'likes' => '977', 'commentaires' => 50, 'conversation' => 8],
                            ];
                        @endphp

                        @foreach ($eleveurs as $eleveur)
                            <tr>
                                <td><span class="badge bg-primary rounded-pill">{{ $eleveur['rang'] }}</span></td>
                                <td><strong>{{ $eleveur['nom'] }}</strong></td>
                                <td>{{ $eleveur['posts'] }}</td>
                                <td>{{ $eleveur['likes'] }}</td>
                                <td>{{ $eleveur['commentaires'] }}</td>
                                <td>
                                    <i class="fas fa-comment-dots text-primary me-1"></i>
                                    {{ $eleveur['conversation'] }}
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary me-1" title="Envoyer un message">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-success me-1" title="Appeler">
                                        <i class="fas fa-phone"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-info" title="Voir le profil">
                                        <i class="fas fa-user"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- ==================== RAPPORT MENSUEL (Mai 2026) ==================== --}}
        <section class="stats-section">
            <h2><i class="fas fa-calendar-alt"></i> RAPPORT MENSUEL (Mai 2026)</h2>
            <div class="report-grid">
                <div class="report-item">
                    <div class="report-icon success">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="report-content">
                        <span class="report-label">Croissance utilisateurs</span>
                        <span class="report-value">+12% <small>par rapport au mois dernier</small></span>
                    </div>
                </div>
                <div class="report-item">
                    <div class="report-icon success">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="report-content">
                        <span class="report-label">Publications</span>
                        <span class="report-value">+23 nouvelles <small>(+8% de croissance)</small></span>
                    </div>
                </div>
                <div class="report-item">
                    <div class="report-icon danger">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="report-content">
                        <span class="report-label">Signalements</span>
                        <span class="report-value">-15% <small>(bonne modération)</small></span>
                    </div>
                </div>
                <div class="report-item">
                    <div class="report-icon primary">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="report-content">
                        <span class="report-label">Meilleur jour d'activité</span>
                        <span class="report-value">Vendredi <small>(89 interactions)</small></span>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection