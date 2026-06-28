@extends('layouts.menu')

@section('title', 'Stocks')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/stocks.css') }}">
@endpush

@section('content')



<div class="dashboard-wrapper">

    <main>
        <div class="container">

            <!-- ===== TITRE ===== -->
            <h1 class="page-title">GESTION DES STOCKS</h1>

            <!-- ===== BOUTONS BAS ===== -->
            <div class="stocks-bottom-actions">
                <!-- Bouton pour ouvrir le modal "Ajouter un produit" -->
                <button class="btn btn-add" data-toggle="modal" data-target="#modalAjoutProduit">
                    <i class="fas fa-plus"></i> Ajouter un produit
                </button>
                <button class="btn btn-report"><i class="fas fa-chart-bar"></i> Rapport</button>
            </div>

            <!-- ===== BARRE RECHERCHE + FILTRE ===== -->
            <div class="stocks-toolbar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="recher...">
                </div>
                <select class="filter-cat">
                    <option>Catégorie ▼</option>
                    <option>Aliments</option>
                    <option>Médicaments</option>
                    <option>Equipements</option>
                </select>
            </div>

            <!-- ===== TABLEAU ===== -->
            <div class="table-wrapper">
                <table class="stocks-table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Catégorie</th>
                            <th>Quantité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ligne 1 : Aliment vache premium -->
                        <tr>
                            <td>
                                <div class="prod-cell">
                                    <span class="prod-icon"><i class="fas fa-cow"></i></span>
                                    Aliment vache premium
                                </div>
                            </td>
                            <td>Aliments</td>
                            <td>
                                850kg<br>
                                <span class="stock-status status-medium"><i class="fas fa-exclamation-triangle"></i> Stock moyen</span>
                            </td>
                            <td>
                                <div class="actions">
                                    <!-- Ajouter mouvement -->
                                    <button class="btn-action btn-plus" data-toggle="modal" data-target="#modalMouvement" data-produit="Aliment vache premium" data-stock="850" data-categorie="Aliments">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <!-- Voir -->
                                    <button class="btn-action btn-eye" data-toggle="modal" data-target="#modalVoir" data-produit="Aliment vache premium" data-categorie="Aliments" data-stock="850" data-seuil="200">
                                        <i class="far fa-eye"></i>
                                    </button>
                                    <!-- Modifier -->
                                    <button class="btn-action btn-edit" data-toggle="modal" data-target="#modalModifier" data-produit="Aliment vache premium" data-categorie="Aliments" data-stock="850" data-seuil="200">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <!-- Supprimer -->
                                    <button class="btn-action btn-del" data-toggle="modal" data-target="#modalSupprimer" data-produit="Aliment vache premium">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Ligne 2 : Vitamine B12 -->
                        <tr>
                            <td>
                                <div class="prod-cell">
                                    <span class="prod-icon"><i class="fas fa-pills"></i></span>
                                    Vitamine B12
                                </div>
                            </td>
                            <td>Médicaments</td>
                            <td>
                                12 boîtes<br>
                                <span class="stock-status status-good"><i class="fas fa-check-circle"></i> Stock bon</span>
                            </td>
                            <td>
                                <div class="actions">
                                    <button class="btn-action btn-plus" data-toggle="modal" data-target="#modalMouvement" data-produit="Vitamine B12" data-stock="12" data-categorie="Médicaments"><i class="fas fa-plus"></i></button>
                                    <button class="btn-action btn-eye" data-toggle="modal" data-target="#modalVoir" data-produit="Vitamine B12" data-categorie="Médicaments" data-stock="12" data-seuil="5"><i class="far fa-eye"></i></button>
                                    <button class="btn-action btn-edit" data-toggle="modal" data-target="#modalModifier" data-produit="Vitamine B12" data-categorie="Médicaments" data-stock="12" data-seuil="5"><i class="fas fa-pen"></i></button>
                                    <button class="btn-action btn-del" data-toggle="modal" data-target="#modalSupprimer" data-produit="Vitamine B12"><i class="fas fa-times"></i></button>
                                </div>
                            </td>
                        </tr>
                        <!-- Ligne 3 : Paille -->
                        <tr>
                            <td>
                                <div class="prod-cell">
                                    <span class="prod-icon"><i class="fas fa-seedling"></i></span>
                                    Paille
                                </div>
                            </td>
                            <td>Aliments</td>
                            <td>
                                120kg<br>
                                <span class="stock-status status-critical"><i class="fas fa-circle"></i> Stock critique</span>
                            </td>
                            <td>
                                <div class="actions">
                                    <button class="btn-action btn-plus" data-toggle="modal" data-target="#modalMouvement" data-produit="Paille" data-stock="120" data-categorie="Aliments"><i class="fas fa-plus"></i></button>
                                    <button class="btn-action btn-eye" data-toggle="modal" data-target="#modalVoir" data-produit="Paille" data-categorie="Aliments" data-stock="120" data-seuil="150"><i class="far fa-eye"></i></button>
                                    <button class="btn-action btn-edit" data-toggle="modal" data-target="#modalModifier" data-produit="Paille" data-categorie="Aliments" data-stock="120" data-seuil="150"><i class="fas fa-pen"></i></button>
                                    <button class="btn-action btn-del" data-toggle="modal" data-target="#modalSupprimer" data-produit="Paille"><i class="fas fa-times"></i></button>
                                </div>
                            </td>
                        </tr>
                        <!-- Ligne 4 : Gants vétérina -->
                        <tr>
                            <td>
                                <div class="prod-cell">
                                    <span class="prod-icon"><i class="fas fa-mitten"></i></span>
                                    Gants vétérina
                                </div>
                            </td>
                            <td>Equipements</td>
                            <td>
                                45 paires<br>
                                <span class="stock-status status-good"><i class="fas fa-check-circle"></i> Stock bon</span>
                            </td>
                            <td>
                                <div class="actions">
                                    <button class="btn-action btn-plus" data-toggle="modal" data-target="#modalMouvement" data-produit="Gants vétérina" data-stock="45" data-categorie="Equipements"><i class="fas fa-plus"></i></button>
                                    <button class="btn-action btn-eye" data-toggle="modal" data-target="#modalVoir" data-produit="Gants vétérina" data-categorie="Equipements" data-stock="45" data-seuil="20"><i class="far fa-eye"></i></button>
                                    <button class="btn-action btn-edit" data-toggle="modal" data-target="#modalModifier" data-produit="Gants vétérina" data-categorie="Equipements" data-stock="45" data-seuil="20"><i class="fas fa-pen"></i></button>
                                    <button class="btn-action btn-del" data-toggle="modal" data-target="#modalSupprimer" data-produit="Gants vétérina"><i class="fas fa-times"></i></button>
                                </div>
                            </td>
                        </tr>
                        <!-- Ligne 5 : Antibiotique -->
                        <tr>
                            <td>
                                <div class="prod-cell">
                                    <span class="prod-icon"><i class="fas fa-syringe"></i></span>
                                    Antibiotique
                                </div>
                            </td>
                            <td>Médicaments</td>
                            <td>
                                850kg<br>
                                <span class="stock-status status-low"><i class="fas fa-exclamation-triangle"></i> Stock faible</span>
                            </td>
                            <td>
                                <div class="actions">
                                    <button class="btn-action btn-plus" data-toggle="modal" data-target="#modalMouvement" data-produit="Antibiotique" data-stock="850" data-categorie="Médicaments"><i class="fas fa-plus"></i></button>
                                    <button class="btn-action btn-eye" data-toggle="modal" data-target="#modalVoir" data-produit="Antibiotique" data-categorie="Médicaments" data-stock="850" data-seuil="1000"><i class="far fa-eye"></i></button>
                                    <button class="btn-action btn-edit" data-toggle="modal" data-target="#modalModifier" data-produit="Antibiotique" data-categorie="Médicaments" data-stock="850" data-seuil="1000"><i class="fas fa-pen"></i></button>
                                    <button class="btn-action btn-del" data-toggle="modal" data-target="#modalSupprimer" data-produit="Antibiotique"><i class="fas fa-times"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ===== HISTORIQUE ===== -->
            <div class="historique">
                <h3><i class="fas fa-file-alt"></i> HISTORIQUE DES MOUVEMENTS (7 derniers jours)</h3>
                <div class="hist-list">
                    <div class="hist-item">
                        <span class="dot dot-green"></span>
                        <span>12/05 - Entrée : +200 kg Aliment Vache Premium</span>
                    </div>
                    <div class="hist-item">
                        <span class="dot dot-red"></span>
                        <span>11/05 - Sortie : -50 kg Aliment Vache Premium</span>
                    </div>
                    <div class="hist-item">
                        <span class="dot dot-red"></span>
                        <span>10/05 - Sortie : -2 boites Vitamine B12</span>
                    </div>
                    <div class="hist-item">
                        <span class="dot dot-green"></span>
                        <span>09/05 - Entrée : +100 kg Paille</span>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- ============================================================ -->
<!-- ===================== MODALS (BOOTSTRAP) ===================== -->
<!-- ============================================================ -->

<!-- 1. MODAL AJOUTER UN PRODUIT -->
<div class="modal fade" id="modalAjoutProduit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle text-success mr-2"></i> Ajouter un produit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nom du produit *</label>
                        <input type="text" class="form-control" placeholder="Ex: Aliment vache premium" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Catégorie *</label>
                            <select class="form-control" required>
                                <option value="">Choisir...</option>
                                <option>Aliments</option>
                                <option>Médicaments</option>
                                <option>Equipements</option>
                                <option>Autre</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Unité de mesure</label>
                            <input type="text" class="form-control" placeholder="kg, boîtes, paires...">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Quantité initiale *</label>
                            <input type="number" class="form-control" placeholder="0" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Seuil d'alerte</label>
                            <input type="number" class="form-control" placeholder="Ex: 50">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description (optionnel)</label>
                        <textarea class="form-control" rows="2" placeholder="Informations supplémentaires..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. MODAL AJOUTER UN MOUVEMENT DE STOCK (conforme à l'image) -->
<div class="modal fade" id="modalMouvement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exchange-alt text-success mr-2"></i> Ajouter un mouvement de stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                <div class="modal-body">
                    <!-- Produit et stock actuel (affichage) -->
                    <div class="form-row mb-3">
                        <div class="col-md-6">
                            <label><strong>Produit</strong></label>
                            <p class="form-control-static" id="mvtProduit">Aliment Vache Premium</p>
                        </div>
                        <div class="col-md-6">
                            <label><strong>Stock actuel</strong></label>
                            <p class="form-control-static" id="mvtStockActuel">850 kg</p>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6">
                            <label>Type de mouvement *</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mvtType" id="mvtEntree" value="entree" checked>
                                <label class="form-check-label" for="mvtEntree">Entrée</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mvtType" id="mvtSortie" value="sortie">
                                <label class="form-check-label" for="mvtSortie">Sortie</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Quantité *</label>
                            <input type="number" class="form-control" id="mvtQuantite" placeholder="100" required>
                        </div>
                    </div>

                    <div class="form-row mt-3">
                        <div class="col-md-6">
                            <label>Motif *</label>
                            <select class="form-control" id="mvtMotif" required>
                                <option value="">Choisir...</option>
                                <option>Achat fournisseur</option>
                                <option>Don</option>
                                <option>Production propre</option>
                                <option>Consommation animale</option>
                                <option>Perte</option>
                                <option>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Date</label>
                            <input type="date" class="form-control" id="mvtDate" value="2026-06-13">
                        </div>
                    </div>

                    <!-- Nouveau stock calculé automatiquement -->
                    <div class="form-group mt-3">
                        <label><strong>Nouveau stock après mouvement :</strong></label>
                        <p class="form-control-static" id="mvtNouveauStock">950 kg</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. MODAL VOIR (détails du produit) -->
<div class="modal fade" id="modalVoir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye text-info mr-2"></i> Détails du produit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nom :</strong> <span id="voirNom">Aliment vache premium</span></p>
                        <p><strong>Catégorie :</strong> <span id="voirCategorie">Aliments</span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Quantité actuelle :</strong> <span id="voirStock">850 kg</span></p>
                        <p><strong>Seuil d'alerte :</strong> <span id="voirSeuil">200 kg</span></p>
                    </div>
                </div>
                <hr>
                <p><strong>Description :</strong> <span id="voirDescription">Aliment premium pour vaches laitières.</span></p>
                <p><strong>Dernier mouvement :</strong> 12/05 - Entrée +200 kg</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- 4. MODAL MODIFIER -->
<div class="modal fade" id="modalModifier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit text-warning mr-2"></i> Modifier le produit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nom du produit *</label>
                        <input type="text" class="form-control" id="editNom" value="Aliment vache premium" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Catégorie *</label>
                            <select class="form-control" id="editCategorie">
                                <option>Aliments</option>
                                <option>Médicaments</option>
                                <option>Equipements</option>
                                <option>Autre</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Unité de mesure</label>
                            <input type="text" class="form-control" id="editUnite" value="kg">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Quantité actuelle</label>
                            <input type="number" class="form-control" id="editStock" value="850" disabled>
                            <small class="text-muted">La quantité ne peut être modifiée que via un mouvement.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Seuil d'alerte</label>
                            <input type="number" class="form-control" id="editSeuil" value="200">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" rows="2" id="editDescription">Aliment premium pour vaches laitières.</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 5. MODAL SUPPRIMER (confirmation) -->
<div class="modal fade" id="modalSupprimer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash-alt mr-2"></i> Confirmer la suppression</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le produit :</p>
                <p class="font-weight-bold text-danger" id="supprNom">Aliment vache premium</p>
                <p class="text-muted small">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="btnSupprimerConfirmer">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts Bootstrap (jQuery + Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script pour peupler les modals dynamiquement (simulation) -->
<script>
    $(document).ready(function() {
        // Pour le modal mouvement : on récupère les data-* des boutons
        $('#modalMouvement').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var produit = button.data('produit');
            var stock = button.data('stock');
            var categorie = button.data('categorie');
            var modal = $(this);
            modal.find('#mvtProduit').text(produit);
            modal.find('#mvtStockActuel').text(stock + ' kg');
            // Réinitialiser le nouveau stock
            modal.find('#mvtNouveauStock').text(stock + ' kg');
            // Réinitialiser les champs
            modal.find('#mvtQuantite').val('');
            modal.find('#mvtMotif').val('');
            // On pourrait aussi stocker l'ID du produit pour l'envoi
        });

        // Calcul automatique du nouveau stock
        $('#modalMouvement input[name="mvtType"], #modalMouvement #mvtQuantite').on('change keyup', function() {
            var modal = $('#modalMouvement');
            var stockActuel = parseFloat(modal.find('#mvtStockActuel').text().replace(' kg', ''));
            var quantite = parseFloat(modal.find('#mvtQuantite').val());
            var type = modal.find('input[name="mvtType"]:checked').val();
            if (!isNaN(quantite) && quantite > 0) {
                var nouveau = (type === 'entree') ? stockActuel + quantite : stockActuel - quantite;
                modal.find('#mvtNouveauStock').text(nouveau + ' kg');
            } else {
                modal.find('#mvtNouveauStock').text(stockActuel + ' kg');
            }
        });

        // Pour le modal Voir
        $('#modalVoir').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('#voirNom').text(button.data('produit'));
            modal.find('#voirCategorie').text(button.data('categorie'));
            modal.find('#voirStock').text(button.data('stock') + ' kg');
            modal.find('#voirSeuil').text(button.data('seuil') + ' kg');
        });

        // Pour le modal Modifier
        $('#modalModifier').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('#editNom').val(button.data('produit'));
            modal.find('#editCategorie').val(button.data('categorie'));
            modal.find('#editStock').val(button.data('stock'));
            modal.find('#editSeuil').val(button.data('seuil'));
            // On pourrait aussi pré-remplir la description etc.
        });

        // Pour le modal Supprimer
        $('#modalSupprimer').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('#supprNom').text(button.data('produit'));
        });

        // Confirmation suppression (simulation)
        $('#btnSupprimerConfirmer').on('click', function() {
            var nom = $('#supprNom').text();
            alert('Produit "' + nom + '" supprimé (simulation).');
            $('#modalSupprimer').modal('hide');
        });
    });
</script>



</div>




@endsection