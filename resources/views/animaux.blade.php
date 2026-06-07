
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

<!-- MODALE AJOUTER UN ANIMAL -->
<div id="addAnimalModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>
                <i class="fas fa-paw" style="color: #198754; margin-right: 10px;"></i>
                AJOUTER UN ANIMAL
            </h2>
            <span class="modal-close">&times;</span>
        </div>

        <div class="modal-body">
            <!-- Logo ÉLEVAGE+ -->
            <div class="modal-logo">
                <span class="logo-text">ÉLEVAGE<span style="color: #198754;">+</span></span>
            </div>

            <!-- Photo section -->
            <div class="photo-section">
                <label class="photo-label">Photo <span class="optional">(optionnelle)</span></label>
                <div class="photo-preview" id="photoPreview">
                    <i class="fas fa-camera-retro"></i>
                    <span>Aperçu photo</span>
                </div>
                <div class="photo-actions">
                    <button type="button" class="btn-choose-img" id="chooseImageBtn">
                        <i class="fas fa-folder-open"></i>
                        Choisir une image
                    </button>
                    <button type="button" class="btn-delete-img" id="deleteImageBtn">
                        <i class="fas fa-trash-alt"></i>
                        Supprimer
                    </button>
                </div>
                <input type="file" id="animalImageInput" accept="image/*" style="display: none;">
            </div>

            <!-- Formulaire -->
            <form id="addAnimalForm">
                <div class="form-group">
                    <label>NOM</label>
                    <input type="text" placeholder="nom animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>RACE</label>
                    <input type="text" placeholder="race animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>POIDS (kg)</label>
                    <input type="number" step="0.1" placeholder="poids animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>ELEVAGE</label>
                    <select class="form-control">
                        <option value="">élevage</option>
                        <option>Ferme des Monts</option>
                        <option>Vallée Verte</option>
                        <option>Prairie Fleurie</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>NOTE <span class="optional">(optionnelle)</span></label>
                    <textarea rows="3" placeholder="description de l'animal" class="form-control"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel">Annuler</button>
                    <button type="submit" class="btn-save">Enregistrer</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- MODALE MODIFIER L'ANIMAL -->
<div id="editAnimalModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>
                <i class="fas fa-edit" style="color: #198754; margin-right: 10px;"></i>
                MODIFIER L'ANIMAL
            </h2>
            <span class="modal-close">&times;</span>
        </div>

        <div class="modal-body">
            <!-- Logo ÉLEVAGE+ -->
            <div class="modal-logo">
                <span class="logo-text">ÉLEVAGE<span style="color: #198754;">+</span></span>
            </div>

            <!-- Photo section -->
            <div class="photo-section">
                <label class="photo-label">Photo <span class="optional">(optionnelle)</span></label>
                <div class="photo-preview" id="editPhotoPreview">
                    <img id="editPreviewImage" src="" alt="Aperçu" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                    <div id="editPreviewPlaceholder" style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                        <i class="fas fa-camera-retro"></i>
                        <span>Aperçu photo</span>
                    </div>
                </div>
                <div class="photo-actions">
                    <button type="button" class="btn-choose-img" id="editChooseImageBtn">
                        <i class="fas fa-folder-open"></i>
                        Choisir une image
                    </button>
                    <button type="button" class="btn-delete-img" id="editDeleteImageBtn">
                        <i class="fas fa-trash-alt"></i>
                        Supprimer
                    </button>
                </div>
                <input type="file" id="editAnimalImageInput" accept="image/*" style="display: none;">
            </div>

            <!-- Formulaire -->
            <form id="editAnimalForm">
                <input type="hidden" id="editAnimalId">
                <div class="form-group">
                    <label>NOM</label>
                    <input type="text" id="editNom" placeholder="nom animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>RACE</label>
                    <input type="text" id="editRace" placeholder="race animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>POIDS (kg)</label>
                    <input type="number" step="0.1" id="editPoids" placeholder="poids animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>ELEVAGE</label>
                    <select id="editElevage" class="form-control">
                        <option value="">élevage</option>
                        <option>Ferme des Monts</option>
                        <option>Vallée Verte</option>
                        <option>Prairie Fleurie</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>NOTE <span class="optional">(optionnelle)</span></label>
                    <textarea id="editNote" rows="3" placeholder="description de l'animal" class="form-control"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel">Annuler</button>
                    <button type="submit" class="btn-save">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

@endsection

