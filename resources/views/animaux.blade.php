
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

        @extends('layouts.menu')

@section('title', 'Ajouter un animal')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/ajouter.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

@section('content')

<div class="animal-form-container">

    <h2 class="page-title">
        AJOUTER UN ANIMAL
    </h2>

    <div class="animal-card">

        <form action="#" method="POST" enctype="multipart/form-data">

            @csrf

            <!-- PHOTO -->

            <div class="photo-section">

                <label class="section-label">
                    Photo <span>(optionnelle)</span>
                </label>

                <div class="photo-content">

                    <div class="preview-box">

                        <img id="preview"
                             src=""
                             alt="">

                    </div>

                    <input type="file"
                           id="photo"
                           name="photo"
                           hidden>

                    <button type="button"
                            class="btn-upload"
                            onclick="document.getElementById('photo').click()">
                        Choisir une image
                    </button>

                    <button type="button"
                            class="btn-remove"
                            onclick="removeImage()">
                        Supprimer
                    </button>

                </div>

            </div>

            <!-- CHAMPS -->

            <div class="form-grid">

                <div class="form-group">
                    <label>
                        <i class="fas fa-tag"></i>
                        Nom *
                    </label>

                    <input type="text"
                           placeholder="Nom animal">
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-paw"></i>
                        Espèce *
                    </label>

                    <input type="text"
                           placeholder="Animal espèce">
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-dna"></i>
                        Race *
                    </label>

                    <input type="text"
                           placeholder="Race">
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-calendar"></i>
                        Date naissance *
                    </label>

                    <input type="date">
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-weight"></i>
                        Poids (kg) *
                    </label>

                    <input type="number"
                           placeholder="Poids">
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-heartbeat"></i>
                        Statut sanitaire *
                    </label>

                    <input type="text"
                           placeholder="Santé animal">
                </div>

            </div>

            <!-- ELEVAGE -->

            <div class="form-group full-width">

                <label>
                    <i class="fas fa-warehouse"></i>
                    Élevage *
                </label>

                <select>
                    <option>Choisir un élevage</option>
                    <option>Élevage Thiès</option>
                    <option>Élevage Dakar</option>
                </select>

            </div>

            <!-- NOTE -->

            <div class="form-group full-width">

                <label>
                    <i class="fas fa-sticky-note"></i>
                    Note (optionnelle)
                </label>

                <textarea
                    rows="4"
                    placeholder="Description de l'animal"></textarea>

            </div>

            <!-- BOUTONS -->

            <div class="form-actions">

                <button type="reset"
                        class="btn-cancel">
                    Annuler
                </button>

                <button type="submit"
                        class="btn-save">
                    Enregistrer
                </button>

            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
<script>

document.getElementById('photo').addEventListener('change', function(e){

    const file = e.target.files[0];

    if(file){

        document.getElementById('preview').src =
        URL.createObjectURL(file);

    }

});

function removeImage(){

    document.getElementById('preview').src = '';
    document.getElementById('photo').value = '';

}

</script>
@endpush

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

