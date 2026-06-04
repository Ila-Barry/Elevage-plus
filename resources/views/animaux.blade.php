
@extends('layouts.menu')

@section('title', 'Gestion des animaux')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/animaux.css') }}">
@endpush

@section('content')

<div class="animals-page">

    <!-- HEADER -->
    <div class="page-header">
        <div>
            <h2>GESTION DES ANIMAUX</h2>
        </div>

        <div class="header-actions">
            <button class="btn-add-animal">
                <i class="fas fa-plus"></i>
                Ajouter un animal
            </button>

            <div class="search-box">
                <input type="text" placeholder="rechercher...">
                <button>
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- FILTRES -->
    <div class="filters-section">

        <span class="filter-label">Filtres :</span>

        <select>
            <option>Tous</option>
            <option>Bovin</option>
            <option>Ovin</option>
            <option>Caprin</option>
        </select>

        <select>
            <option>Espèce</option>
        </select>

        <select>
            <option>Âge</option>
        </select>

        <select>
            <option>Santé</option>
        </select>

    </div>

    <!-- LISTE DES ANIMAUX -->
    <div class="animals-list">

        {{-- Animal 1 --}}
        <div class="animal-card">

            <div class="animal-image">
                <img src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=600"
                     alt="Animal">
            </div>

            <div class="animal-info">

                <h5>NOM : Marguerite</h5>

                <div class="animal-details">

                    <div>
                        <i class="fas fa-tag"></i>
                        Race : Brune
                    </div>

                    <div>
                        <i class="fas fa-birthday-cake"></i>
                        Âge : 4 ans
                    </div>

                    <div>
                        <i class="fas fa-weight-hanging"></i>
                        Poids : 450 kg
                    </div>

                    <div>
                        <i class="fas fa-heartbeat"></i>
                        Santé :
                        <span class="badge-sante">Bonne</span>
                    </div>

                </div>

            </div>

            <div class="animal-actions">

                <button class="btn-detail">
                    <i class="fas fa-eye"></i>
                    Détail
                </button>

                <button class="btn-edit">
                    <i class="fas fa-pen"></i>
                    Modifier
                </button>

                <button class="btn-delete">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>

            </div>

        </div>

        {{-- Animal 2 --}}
        <div class="animal-card">

            <div class="animal-image">
                <img src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=600"
                     alt="Animal">
            </div>

            <div class="animal-info">

                <h5>NOM : Blanchette</h5>

                <div class="animal-details">

                    <div>
                        <i class="fas fa-tag"></i>
                        Race : Brune
                    </div>

                    <div>
                        <i class="fas fa-birthday-cake"></i>
                        Âge : 1 ans 6 mois
                    </div>

                    <div>
                        <i class="fas fa-weight-hanging"></i>
                        Poids : 250 kg
                    </div>

                    <div>
                        <i class="fas fa-heartbeat"></i>
                        Santé :
                        <span class="badge-sante">Bonne</span>
                    </div>

                </div>

            </div>

            <div class="animal-actions">

                <button class="btn-detail">
                    <i class="fas fa-eye"></i>
                    Détail
                </button>

                <button class="btn-edit">
                    <i class="fas fa-pen"></i>
                    Modifier
                </button>

                <button class="btn-delete">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>

            </div>

        </div>

        {{-- Animal 3 --}}
        <div class="animal-card">

            <div class="animal-image">
                <img src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=600"
                     alt="Animal">
            </div>

            <div class="animal-info">

                <h5>NOM : Roussette</h5>

                <div class="animal-details">

                    <div>
                        <i class="fas fa-tag"></i>
                        Race : Brune
                    </div>

                    <div>
                        <i class="fas fa-birthday-cake"></i>
                        Âge :  32 mois
                    </div>

                    <div>
                        <i class="fas fa-weight-hanging"></i>
                        Poids : 400 kg
                    </div>

                    <div>
                        <i class="fas fa-heartbeat"></i>
                        Santé :
                        <span class="badge-sante">Bonne</span>
                    </div>

                </div>

            </div>

            <div class="animal-actions">

                <button class="btn-detail">
                    <i class="fas fa-eye"></i>
                    Détail
                </button>

                <button class="btn-edit">
                    <i class="fas fa-pen"></i>
                    Modifier
                </button>

                <button class="btn-delete">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>

            </div>

        </div>

    </div>

    <!-- PAGINATION -->
    <div class="pagination-section">

        <div class="pagination-custom">

            <button>
                <i class="fas fa-angle-left"></i>
            </button>

            <button class="active">1</button>
            <button>2</button>
            <button>3</button>

            <button>
                <i class="fas fa-angle-right"></i>
            </button>

        </div>

        <div class="pagination-info">
            Affichage : 1/45
        </div>

    </div>

</div>

@endsection