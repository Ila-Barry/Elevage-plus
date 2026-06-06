@extends('layouts.menu')

@section('title', 'Élevages')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/elevage.css') }}">
@endpush

@section('content')
<div class="dashboard-wrapper container-fluid py-4">

    <!-- En-tête avec bouton de création -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
        <button type="button" class="btn btn-create d-flex align-items-center" data-toggle="modal" data-target="#createElevageModal">
            <i class="fas fa-plus mr-2"></i> Créer un nouvel élevage
        </button>
    </div>

    <!-- Liste des élevages -->
    <div class="elevages-list d-flex flex-column gap-3 mb-4">
        @forelse($elevages ?? [
            ['id' => 1, 'titre' => 'ÉLEVAGE BOVIN - THIÈS', 'type' => 'bovins', 'local' => 'Thiès, Sénégal', 'surface' => '5 hectares', 'animaux' => '45 bovins', 'date' => '15/03/2025'],
            ['id' => 2, 'titre' => 'ÉLEVAGE CAPRIN - DAKAR', 'type' => 'caprins', 'local' => 'Thiès, Sénégal', 'surface' => '5 hectares', 'animaux' => '45 bovins', 'date' => '10/03/2024'],
            ['id' => 3, 'titre' => 'ÉLEVAGE BOVIN - THIÈS', 'type' => 'bovins', 'local' => 'Thiès, Sénégal', 'surface' => '5 hectares', 'animaux' => '45 bovins', 'date' => '15/03/2025']
        ] as $elevage)

            <div class="elevage-card bg-white p-3 rounded shadow-sm border">
                <div class="row align-items-center">

                    <!-- Image de l'élevage -->
                    <div class="col-12 col-md-3 col-lg-2 text-center text-md-left mb-3 mb-md-0">
                        <div class="img-container">
                            <img src="{{ asset('images/img-elevage.jpeg') }}" alt="Ferme Intégrée" class="img-fluid rounded border">
                            <div class="img-overlay-text text-uppercase text-center">Ferme Intégrée<br><small>"Union" - Dakar / Sénégal</small></div>
                        </div>
                    </div>

                    <!-- Informations textuelles de l'élevage -->
                    <div class="col-12 col-sm-8 col-md-6 col-lg-7">
                        <h2 class="elevage-card-title text-uppercase mb-3">{{ $elevage['titre'] }}</h2>

                        <div class="elevage-info-grid">
                            <div class="info-item d-flex align-items-center mb-2">
                                <i class="fas fa-map-marker-alt text-danger mr-3 info-icon"></i>
                                <span><strong>Localisation :</strong> {{ $elevage['local'] }}</span>
                            </div>
                            <div class="info-item d-flex align-items-center mb-2">
                                <i class="fas fa-layer-group text-secondary mr-3 info-icon"></i>
                                <span><strong>Superficie :</strong> {{ $elevage['surface'] }}</span>
                            </div>
                            <div class="info-item d-flex align-items-center mb-2">
                                <i class="fas fa-cow text-secondary mr-3 info-icon"></i>
                                <span><strong>Animaux :</strong> {{ $elevage['animaux'] }}</span>
                            </div>
                            <div class="info-item d-flex align-items-center mb-0">
                                <i class="far fa-calendar-alt text-danger mr-3 info-icon"></i>
                                <span><strong>Créé le :</strong> {{ $elevage['date'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne des Boutons d'action -->
                    <div class="col-12 col-sm-4 col-md-3 col-lg-3 text-center text-sm-right mt-3 mt-sm-0">
                        <div class="action-buttons-group d-flex flex-column align-items-stretch align-items-sm-end gap-2">
                            
                            <!-- Bouton Voir -->
                            <button type="button" class="btn d-flex align-items-center justify-content-center gap-2"
        style="background-color: rgba(25, 135, 84, 0.15); color: #198754; border: 1px solid rgba(25, 135, 84, 0.2); font-weight: 500; padding: 4px 15px; border-radius: 6px; min-width: 95px;"
        data-toggle="modal" data-target="#voirElevageModal">
    <i class="far fa-eye"></i>
    <span>voir</span>
</button>
                            
                            <!-- Bouton Modifier -->
                            <button type="button" class="btn btn-action btn-edit d-flex align-items-center justify-content-center" data-toggle="modal" data-target="#createElevageModal">
                                <i class="fas fa-pencil-alt mr-2"></i> modifier
                            </button>
                            
                            <!-- Bouton Supprimer -->
                            <button type="button" class="btn btn-action btn-delete d-flex align-items-center justify-content-center">
                                <i class="far fa-trash-alt mr-2"></i> Supprimer
                            </button>
                            
                        </div>
                    </div>

                </div>
            </div>

        @empty
            <div class="alert alert-info text-center">Aucun élevage enregistré pour le moment.</div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <a href="#" class="btn btn-outline-dark btn-pagination d-flex align-items-center">
            <i class="fas fa-caret-left mr-2"></i> précédente
        </a>
        <a href="#" class="btn btn-outline-dark btn-pagination d-flex align-items-center">
            suivante <i class="fas fa-caret-right ml-2"></i>
        </a>
    </div>

</div>

<!-- MODAL : CRÉER UN ÉLEVAGE -->
<div class="modal fade" id="createElevageModal" tabindex="-1" aria-labelledby="createElevageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content create-elevage-modal">

            <!-- En-tête du formulaire -->
            <div class="modal-header border-0 align-items-center pt-4 px-4 pb-2">
                <h5 class="modal-title font-weight-bold d-flex align-items-center" id="createElevageModalLabel">
                    <i class="fas fa-users-cog mr-2 text-dark"></i> CRÉER UN ÉLEVAGE
                </h5>
                <button type="button" class="close-modal-btn" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <!-- Corps du formulaire -->
            <div class="modal-body px-4 pb-4">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Section Photo -->
                    <div class="form-section-box p-3 mb-3 rounded">
                        <label class="form-label font-weight-bold mb-2">
                            <i class="far fa-image text-success mr-1"></i> Photo <span class="font-weight-normal text-muted text-lowercase">(optionnelle)</span>
                        </label>
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <div class="image-preview-placeholder d-flex align-items-center justify-content-center rounded border border-dashed">
                                <i class="far fa-image fa-2x text-muted"></i>
                            </div>
                            <div class="d-flex gap-2 flex-grow-1">
                                <label class="btn btn-outline-success btn-photo-action mb-0 d-flex align-items-center justify-content-center cursor-pointer">
                                    <i class="far fa-image mr-2"></i> Choisir une image
                                    <input type="file" name="photo" class="d-none" accept="image/*">
                                </label>
                                <button type="button" class="btn btn-outline-danger btn-photo-action d-flex align-items-center justify-content-center">
                                    <i class="fas fa-times mr-2"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Champ : Nom de l'élevage -->
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">
                            <i class="far fa-sticky-note text-warning mr-1"></i> Nom de l'élevage <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nom_elevage" class="form-control custom-input" placeholder="Élevage bovin de Thiès" required>
                    </div>

                    <!-- Champ : Type d'élevage -->
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-cow text-secondary mr-1"></i> Type d'élevage <span class="text-danger">*</span>
                        </label>
                        <div class="select-wrapper">
                            <select name="type_elevage" class="form-control custom-input appearance-none" required>
                                <option value="bovins" selected>Bovins</option>
                                <option value="caprins">Caprins</option>
                                <option value="ovins">Ovins</option>
                                <option value="volailles">Volailles</option>
                            </select>
                            <i class="fas fa-caret-down select-arrow"></i>
                        </div>
                    </div>

                    <!-- Champ : Localisation -->
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-map-marker-alt text-danger mr-1"></i> Localisation
                        </label>
                        <input type="text" name="localisation" class="form-control custom-input" placeholder="Thiès, Sénégal">
                    </div>

                    <!-- Champ : Superficie -->
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">Superficie (hectares)</label>
                        <input type="number" name="superficie" class="form-control custom-input" placeholder="5" min="0" step="any">
                    </div>

                    <!-- Champ : Description -->
                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-feather-alt text-secondary mr-1"></i> Description <span class="font-weight-normal text-muted text-lowercase">(optionnelle)</span>
                        </label>
                        <textarea name="description" class="form-control custom-textarea" rows="3" placeholder="Élevage spécialisé dans la production laitière"></textarea>
                    </div>

                    <!-- Boutons de fermeture inférieurs -->
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <button type="button" class="btn btn-modal-cancel d-flex align-items-center" data-dismiss="modal">
                            <i class="fas fa-times-circle mr-2 text-danger"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-modal-submit d-flex align-items-center">
                            <i class="fas fa-check-square mr-2 text-white"></i> Valider
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="voirElevageModal" tabindex="-1" role="dialog" aria-labelledby="voirElevageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            
            <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-uppercase" id="voirElevageModalLabel" style="font-size: 16px; color: #1a1a1a;">
                    🐄 CRÉER UN ÉLEVAGE
                </h5>
                <button type="button" class="close text-danger border-0 bg-transparent" data-dismiss="modal" aria-label="Close" style="font-size: 24px; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form>
                    <div class="p-3 mb-3 border rounded" style="background-color: #fafafa;">
                        <label class="form-label fw-bold small text-muted">🖼️ Photo <span class="text-muted font-weight-normal">(optionnelle)</span></label>
                        <div class="d-flex align-items-center gap-3 mt-1">
                            <div class="border rounded d-flex align-items-center justify-content-center bg-white" style="width: 70px; height: 70px; border-style: dashed !important;">
                                🖼️
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1" style="border-radius: 6px;">
                                🖼️ Choisir une image
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" style="border-radius: 6px;">
                                ❌ Supprimer
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">🏷️ Nom de l'élevage <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="Élevage bovin de Thiès" style="border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">🐄 Type d'élevage <span class="text-danger">*</span></label>
                        <select class="form-control form-select" style="border-radius: 6px; font-size: 14px;">
                            <option selected>Bovins</option>
                            <option>Caprins</option>
                            <option>Ovins</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">📍 Localisation</label>
                        <input type="text" class="form-control" value="Thiès, Sénégal" style="border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Superficie (hectares)</label>
                        <input type="number" class="form-control" value="5" style="border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">📝 Description <span class="text-muted font-weight-normal">(optionnelle)</span></label>
                        <textarea class="form-control" rows="3" style="border-radius: 6px; font-size: 14px;">Élevage spécialisé dans la production laitière</textarea>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-1" data-dismiss="modal" style="border-radius: 6px; min-width: 100px; justify-content: center;">
                            ❎ Annuler
                        </button>
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-1" style="background-color: #198754; border-radius: 6px; min-width: 100px; justify-content: center;">
                            ✅ Valider
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection