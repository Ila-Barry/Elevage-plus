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
                <button class="btn btn-add" id="openAddProduct">
                    <i class="fas fa-plus"></i> Ajouter un produit
                </button>
                <button class="btn btn-report" id="generateReport">
                    <i class="fas fa-chart-bar"></i> Rapport
                </button>
                <button class="btn btn-export" id="exportData">
                    <i class="fas fa-file-export"></i> Exporter
                </button>
            </div>

            <!-- ===== BARRE RECHERCHE + FILTRE ===== -->
            <div class="stocks-toolbar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Rechercher un produit...">
                </div>
                <select class="filter-cat" id="filterCategory">
                    <option value="all">Toutes les catégories</option>
                    <option value="Aliments">Aliments</option>
                    <option value="Médicaments">Médicaments</option>
                    <option value="Equipements">Equipements</option>
                    <option value="Autre">Autre</option>
                </select>
                <select class="filter-cat" id="filterStatus">
                    <option value="all">Tous les statuts</option>
                    <option value="good">Stock bon</option>
                    <option value="medium">Stock moyen</option>
                    <option value="low">Stock faible</option>
                    <option value="critical">Stock critique</option>
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
                    <tbody id="stocksTableBody">
                        <!-- Les lignes seront générées par JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- ===== STATISTIQUES ===== -->
            <div class="stocks-stats" id="stocksStats">
                <div class="stat-item">
                    <span class="stat-label">Total produits</span>
                    <span class="stat-value" id="totalProducts">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Stock critique</span>
                    <span class="stat-value critical" id="criticalProducts">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Stock faible</span>
                    <span class="stat-value warning" id="lowProducts">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Stock bon</span>
                    <span class="stat-value good" id="goodProducts">0</span>
                </div>
            </div>

            <!-- ===== HISTORIQUE ===== -->
            <div class="historique">
                <h3><i class="fas fa-file-alt"></i> HISTORIQUE DES MOUVEMENTS (7 derniers jours)</h3>
                <div class="hist-list" id="historyList">
                    <!-- Généré par JavaScript -->
                </div>
            </div>

        </div>
    </main>
</div>

<!-- ============================================================ -->
<!-- ===================== MODALS ===================== -->
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
            <form id="addProductForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nom du produit *</label>
                        <input type="text" class="form-control" id="addProductName" placeholder="Ex: Aliment vache premium" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Catégorie *</label>
                            <select class="form-control" id="addProductCategory" required>
                                <option value="">Choisir...</option>
                                <option value="Aliments">Aliments</option>
                                <option value="Médicaments">Médicaments</option>
                                <option value="Equipements">Equipements</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Unité de mesure</label>
                            <input type="text" class="form-control" id="addProductUnit" placeholder="kg, boîtes, paires...">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Quantité initiale *</label>
                            <input type="number" class="form-control" id="addProductQuantity" placeholder="0" min="0" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Seuil d'alerte</label>
                            <input type="number" class="form-control" id="addProductThreshold" placeholder="Ex: 50" min="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description (optionnel)</label>
                        <textarea class="form-control" id="addProductDescription" rows="2" placeholder="Informations supplémentaires..."></textarea>
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

<!-- 2. MODAL AJOUTER UN MOUVEMENT DE STOCK -->
<div class="modal fade" id="modalMouvement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exchange-alt text-success mr-2"></i> Ajouter un mouvement de stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="movementForm">
                <div class="modal-body">
                    <div class="form-row mb-3">
                        <div class="col-md-6">
                            <label><strong>Produit</strong></label>
                            <p class="form-control-static" id="mvtProduit">-</p>
                        </div>
                        <div class="col-md-6">
                            <label><strong>Stock actuel</strong></label>
                            <p class="form-control-static" id="mvtStockActuel">-</p>
                        </div>
                    </div>
                    <input type="hidden" id="mvtProductId">

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
                            <input type="number" class="form-control" id="mvtQuantite" placeholder="100" min="1" required>
                        </div>
                    </div>

                    <div class="form-row mt-3">
                        <div class="col-md-6">
                            <label>Motif *</label>
                            <select class="form-control" id="mvtMotif" required>
                                <option value="">Choisir...</option>
                                <option value="Achat fournisseur">Achat fournisseur</option>
                                <option value="Don">Don</option>
                                <option value="Production propre">Production propre</option>
                                <option value="Consommation animale">Consommation animale</option>
                                <option value="Perte">Perte</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Date</label>
                            <input type="date" class="form-control" id="mvtDate">
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label><strong>Nouveau stock après mouvement :</strong></label>
                        <p class="form-control-static" id="mvtNouveauStock">-</p>
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

<!-- 3. MODAL VOIR -->
<div class="modal fade" id="modalVoir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye text-info mr-2"></i> Détails du produit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewProductBody">
                <!-- Rempli par JavaScript -->
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
            <form id="editProductForm">
                <div class="modal-body">
                    <input type="hidden" id="editProductId">
                    <div class="form-group">
                        <label>Nom du produit *</label>
                        <input type="text" class="form-control" id="editProductName" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Catégorie *</label>
                            <select class="form-control" id="editProductCategory" required>
                                <option value="Aliments">Aliments</option>
                                <option value="Médicaments">Médicaments</option>
                                <option value="Equipements">Equipements</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Unité de mesure</label>
                            <input type="text" class="form-control" id="editProductUnit">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Quantité actuelle</label>
                            <input type="number" class="form-control" id="editProductQuantity" disabled>
                            <small class="text-muted">La quantité ne peut être modifiée que via un mouvement.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Seuil d'alerte</label>
                            <input type="number" class="form-control" id="editProductThreshold" min="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" id="editProductDescription" rows="2"></textarea>
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

<!-- 5. MODAL SUPPRIMER -->
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
                <p class="font-weight-bold text-danger" id="supprNom">-</p>
                <p class="text-muted small">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="btnSupprimerConfirmer">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ================= DONNÉES =================
let products = [
    {
        id: 1,
        nom: "Aliment vache premium",
        categorie: "Aliments",
        quantite: 850,
        unite: "kg",
        seuil: 200,
        description: "Aliment premium pour vaches laitières.",
        icon: "fa-cow",
        historique: [
            { date: "12/05", type: "entree", quantite: 200, motif: "Achat fournisseur" },
            { date: "11/05", type: "sortie", quantite: 50, motif: "Consommation animale" }
        ]
    },
    {
        id: 2,
        nom: "Vitamine B12",
        categorie: "Médicaments",
        quantite: 12,
        unite: "boîtes",
        seuil: 5,
        description: "Complément vitaminique pour bovins.",
        icon: "fa-pills",
        historique: [
            { date: "10/05", type: "sortie", quantite: 2, motif: "Consommation animale" }
        ]
    },
    {
        id: 3,
        nom: "Paille",
        categorie: "Aliments",
        quantite: 120,
        unite: "kg",
        seuil: 150,
        description: "Paille de qualité pour litière.",
        icon: "fa-seedling",
        historique: [
            { date: "09/05", type: "entree", quantite: 100, motif: "Production propre" }
        ]
    },
    {
        id: 4,
        nom: "Gants vétérina",
        categorie: "Equipements",
        quantite: 45,
        unite: "paires",
        seuil: 20,
        description: "Gants jetables pour examens vétérinaires.",
        icon: "fa-mitten",
        historique: []
    },
    {
        id: 5,
        nom: "Antibiotique",
        categorie: "Médicaments",
        quantite: 850,
        unite: "g",
        seuil: 1000,
        description: "Antibiotique à large spectre.",
        icon: "fa-syringe",
        historique: []
    }
];

let nextId = 6;
let historyEntries = [
    { date: "12/05", type: "entree", produit: "Aliment vache premium", quantite: 200 },
    { date: "11/05", type: "sortie", produit: "Aliment vache premium", quantite: 50 },
    { date: "10/05", type: "sortie", produit: "Vitamine B12", quantite: 2 },
    { date: "09/05", type: "entree", produit: "Paille", quantite: 100 }
];

let toastTimeout = null;

// ================= TOAST =================
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

// ================= STATUT DU STOCK =================
function getStockStatus(quantite, seuil) {
    if (quantite <= 0) return { status: 'critical', label: 'Rupture de stock', icon: 'fa-circle' };
    if (quantite < seuil * 0.3) return { status: 'critical', label: 'Stock critique', icon: 'fa-circle' };
    if (quantite < seuil * 0.6) return { status: 'low', label: 'Stock faible', icon: 'fa-exclamation-triangle' };
    if (quantite < seuil) return { status: 'medium', label: 'Stock moyen', icon: 'fa-exclamation-triangle' };
    return { status: 'good', label: 'Stock bon', icon: 'fa-check-circle' };
}

// ================= RENDU DU TABLEAU =================
function renderTable() {
    const tbody = document.getElementById('stocksTableBody');
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const categoryFilter = document.getElementById('filterCategory').value;
    const statusFilter = document.getElementById('filterStatus').value;
    
    let filtered = products.filter(p => {
        const matchSearch = p.nom.toLowerCase().includes(searchTerm);
        const matchCategory = categoryFilter === 'all' || p.categorie === categoryFilter;
        const status = getStockStatus(p.quantite, p.seuil);
        const matchStatus = statusFilter === 'all' || status.status === statusFilter;
        return matchSearch && matchCategory && matchStatus;
    });
    
    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <i class="fas fa-box-open" style="font-size: 24px; color: #6c757d;"></i>
                    <p class="mt-2" style="color: #6c757d;">Aucun produit trouvé</p>
                </td>
            </tr>
        `;
        updateStats([]);
        return;
    }
    
    tbody.innerHTML = filtered.map(p => {
        const status = getStockStatus(p.quantite, p.seuil);
        const statusClass = status.status;
        const statusLabel = status.label;
        const statusIcon = status.icon;
        
        return `
            <tr data-id="${p.id}">
                <td>
                    <div class="prod-cell">
                        <span class="prod-icon"><i class="fas ${p.icon || 'fa-box'}"></i></span>
                        ${p.nom}
                    </div>
                </td>
                <td>${p.categorie}</td>
                <td>
                    ${p.quantite}${p.unite ? ' ' + p.unite : ''}<br>
                    <span class="stock-status status-${statusClass}">
                        <i class="fas ${statusIcon}"></i> ${statusLabel}
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <button class="btn-action btn-plus" onclick="openMovementModal(${p.id})" title="Ajouter un mouvement">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="btn-action btn-eye" onclick="viewProduct(${p.id})" title="Voir les détails">
                            <i class="far fa-eye"></i>
                        </button>
                        <button class="btn-action btn-edit" onclick="openEditModal(${p.id})" title="Modifier">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn-action btn-del" onclick="openDeleteModal(${p.id})" title="Supprimer">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    updateStats(filtered);
}

// ================= STATISTIQUES =================
function updateStats(filtered) {
    const total = filtered.length;
    const critical = filtered.filter(p => getStockStatus(p.quantite, p.seuil).status === 'critical').length;
    const low = filtered.filter(p => getStockStatus(p.quantite, p.seuil).status === 'low').length;
    const good = filtered.filter(p => getStockStatus(p.quantite, p.seuil).status === 'good').length;
    
    document.getElementById('totalProducts').textContent = total;
    document.getElementById('criticalProducts').textContent = critical;
    document.getElementById('lowProducts').textContent = low;
    document.getElementById('goodProducts').textContent = good;
}

// ================= HISTORIQUE =================
function renderHistory() {
    const container = document.getElementById('historyList');
    
    if (historyEntries.length === 0) {
        container.innerHTML = `
            <div class="hist-item">
                <span class="text-muted">Aucun mouvement récent</span>
            </div>
        `;
        return;
    }
    
    container.innerHTML = historyEntries.map(h => {
        const dotClass = h.type === 'entree' ? 'dot-green' : 'dot-red';
        const sign = h.type === 'entree' ? '+' : '-';
        return `
            <div class="hist-item">
                <span class="dot ${dotClass}"></span>
                <span>${h.date} - ${h.type === 'entree' ? 'Entrée' : 'Sortie'} : ${sign}${h.quantite} ${h.produit}</span>
            </div>
        `;
    }).join('');
}

// ================= AJOUTER PRODUIT =================
document.getElementById('openAddProduct').addEventListener('click', function() {
    document.getElementById('addProductForm').reset();
    $('#modalAjoutProduit').modal('show');
});

document.getElementById('addProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const nom = document.getElementById('addProductName').value.trim();
    const categorie = document.getElementById('addProductCategory').value;
    const unite = document.getElementById('addProductUnit').value.trim();
    const quantite = parseFloat(document.getElementById('addProductQuantity').value) || 0;
    const seuil = parseFloat(document.getElementById('addProductThreshold').value) || 50;
    const description = document.getElementById('addProductDescription').value.trim();
    
    if (!nom) {
        showToast('Veuillez saisir un nom', 'warning');
        return;
    }
    if (!categorie) {
        showToast('Veuillez sélectionner une catégorie', 'warning');
        return;
    }
    
    const newProduct = {
        id: nextId++,
        nom: nom,
        categorie: categorie,
        quantite: quantite,
        unite: unite || 'unité',
        seuil: seuil,
        description: description || 'Aucune description',
        icon: 'fa-box',
        historique: []
    };
    
    products.push(newProduct);
    renderTable();
    renderHistory();
    $('#modalAjoutProduit').modal('hide');
    showToast(`Produit "${nom}" ajouté avec succès !`, 'success');
});

// ================= MODAL MOUVEMENT =================
function openMovementModal(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;
    
    document.getElementById('mvtProductId').value = productId;
    document.getElementById('mvtProduit').textContent = product.nom;
    document.getElementById('mvtStockActuel').textContent = product.quantite + (product.unite ? ' ' + product.unite : '');
    document.getElementById('mvtNouveauStock').textContent = product.quantite + (product.unite ? ' ' + product.unite : '');
    document.getElementById('mvtQuantite').value = '';
    document.getElementById('mvtMotif').value = '';
    document.getElementById('mvtDate').value = new Date().toISOString().split('T')[0];
    
    // Réinitialiser le type à entrée
    document.getElementById('mvtEntree').checked = true;
    
    $('#modalMouvement').modal('show');
}

document.getElementById('movementForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const productId = parseInt(document.getElementById('mvtProductId').value);
    const product = products.find(p => p.id === productId);
    if (!product) {
        showToast('Produit non trouvé', 'danger');
        return;
    }
    
    const type = document.querySelector('input[name="mvtType"]:checked').value;
    const quantite = parseFloat(document.getElementById('mvtQuantite').value);
    const motif = document.getElementById('mvtMotif').value;
    const date = document.getElementById('mvtDate').value;
    
    if (!quantite || quantite <= 0) {
        showToast('Veuillez saisir une quantité valide', 'warning');
        return;
    }
    
    if (!motif) {
        showToast('Veuillez sélectionner un motif', 'warning');
        return;
    }
    
    if (type === 'sortie' && quantite > product.quantite) {
        showToast('Quantité insuffisante en stock', 'danger');
        return;
    }
    
    // Mettre à jour le stock
    const oldQuantite = product.quantite;
    product.quantite = type === 'entree' ? product.quantite + quantite : product.quantite - quantite;
    
    // Ajouter à l'historique
    historyEntries.unshift({
        date: date || new Date().toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' }),
        type: type,
        produit: product.nom,
        quantite: quantite
    });
    
    // Ajouter à l'historique du produit
    product.historique.unshift({
        date: date || new Date().toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' }),
        type: type,
        quantite: quantite,
        motif: motif
    });
    
    renderTable();
    renderHistory();
    $('#modalMouvement').modal('hide');
    showToast(`Mouvement enregistré : ${type === 'entree' ? '+' : '-'}${quantite} ${product.unite}`, 'success');
});

// ================= VOIR PRODUIT =================
function viewProduct(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;
    
    const status = getStockStatus(product.quantite, product.seuil);
    const statusClass = status.status;
    const statusLabel = status.label;
    
    const body = document.getElementById('viewProductBody');
    body.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <p><strong>Nom :</strong> ${product.nom}</p>
                <p><strong>Catégorie :</strong> ${product.categorie}</p>
                <p><strong>Unité :</strong> ${product.unite}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Quantité :</strong> ${product.quantite} ${product.unite}</p>
                <p><strong>Seuil d'alerte :</strong> ${product.seuil} ${product.unite}</p>
                <p><strong>Statut :</strong> <span class="badge badge-${statusClass}">${statusLabel}</span></p>
            </div>
        </div>
        <hr>
        <p><strong>Description :</strong> ${product.description}</p>
        <p><strong>Derniers mouvements :</strong></p>
        ${product.historique && product.historique.length > 0 ? `
            <ul class="list-unstyled">
                ${product.historique.slice(0, 5).map(h => `
                    <li class="hist-item" style="border: none; padding: 4px 0;">
                        <span class="dot ${h.type === 'entree' ? 'dot-green' : 'dot-red'}"></span>
                        ${h.date} - ${h.type === 'entree' ? 'Entrée' : 'Sortie'} : ${h.type === 'entree' ? '+' : '-'}${h.quantite} ${product.unite} (${h.motif})
                    </li>
                `).join('')}
            </ul>
        ` : '<p class="text-muted">Aucun mouvement enregistré</p>'}
    `;
    
    $('#modalVoir').modal('show');
}

// ================= MODIFIER PRODUIT =================
function openEditModal(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;
    
    document.getElementById('editProductId').value = productId;
    document.getElementById('editProductName').value = product.nom;
    document.getElementById('editProductCategory').value = product.categorie;
    document.getElementById('editProductUnit').value = product.unite;
    document.getElementById('editProductQuantity').value = product.quantite;
    document.getElementById('editProductThreshold').value = product.seuil;
    document.getElementById('editProductDescription').value = product.description;
    
    $('#modalModifier').modal('show');
}

document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const productId = parseInt(document.getElementById('editProductId').value);
    const product = products.find(p => p.id === productId);
    if (!product) return;
    
    const nom = document.getElementById('editProductName').value.trim();
    const categorie = document.getElementById('editProductCategory').value;
    const unite = document.getElementById('editProductUnit').value.trim();
    const seuil = parseFloat(document.getElementById('editProductThreshold').value) || 50;
    const description = document.getElementById('editProductDescription').value.trim();
    
    if (!nom) {
        showToast('Veuillez saisir un nom', 'warning');
        return;
    }
    
    product.nom = nom;
    product.categorie = categorie;
    product.unite = unite || 'unité';
    product.seuil = seuil;
    product.description = description || 'Aucune description';
    
    renderTable();
    $('#modalModifier').modal('hide');
    showToast(`Produit "${nom}" modifié avec succès !`, 'success');
});

// ================= SUPPRIMER PRODUIT =================
function openDeleteModal(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;
    
    document.getElementById('supprNom').textContent = product.nom;
    document.getElementById('btnSupprimerConfirmer').dataset.id = productId;
    $('#modalSupprimer').modal('show');
}

document.getElementById('btnSupprimerConfirmer').addEventListener('click', function() {
    const productId = parseInt(this.dataset.id);
    const product = products.find(p => p.id === productId);
    if (!product) return;
    
    const nom = product.nom;
    products = products.filter(p => p.id !== productId);
    
    renderTable();
    $('#modalSupprimer').modal('hide');
    showToast(`Produit "${nom}" supprimé avec succès`, 'success');
});

// ================= RECHERCHE ET FILTRES =================
document.getElementById('searchInput').addEventListener('input', renderTable);
document.getElementById('filterCategory').addEventListener('change', renderTable);
document.getElementById('filterStatus').addEventListener('change', renderTable);

// ================= RAPPORT =================
document.getElementById('generateReport').addEventListener('click', function() {
    const total = products.length;
    const critical = products.filter(p => getStockStatus(p.quantite, p.seuil).status === 'critical').length;
    const low = products.filter(p => getStockStatus(p.quantite, p.seuil).status === 'low').length;
    const good = products.filter(p => getStockStatus(p.quantite, p.seuil).status === 'good').length;
    
    alert(
        '📊 RAPPORT DE STOCK\n' +
        '====================\n' +
        `Total produits : ${total}\n` +
        `Stock critique : ${critical}\n` +
        `Stock faible : ${low}\n` +
        `Stock bon : ${good}\n\n` +
        'Voir les détails dans le tableau ci-dessus.'
    );
});

// ================= EXPORTER =================
document.getElementById('exportData').addEventListener('click', function() {
    let csv = 'Produit, Catégorie, Quantité, Unité, Seuil, Statut\n';
    products.forEach(p => {
        const status = getStockStatus(p.quantite, p.seuil);
        csv += `${p.nom}, ${p.categorie}, ${p.quantite}, ${p.unite}, ${p.seuil}, ${status.label}\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `stock_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
    showToast('Export CSV effectué avec succès !', 'success');
});

// ================= CALCUL DU NOUVEAU STOCK =================
document.querySelectorAll('#modalMouvement input[name="mvtType"], #modalMouvement #mvtQuantite').forEach(el => {
    el.addEventListener('change keyup', function() {
        const modal = document.getElementById('modalMouvement');
        const stockText = modal.querySelector('#mvtStockActuel').textContent;
        const stockActuel = parseFloat(stockText.replace(/[^0-9.]/g, ''));
        const quantite = parseFloat(modal.querySelector('#mvtQuantite').value);
        const type = modal.querySelector('input[name="mvtType"]:checked').value;
        const unite = stockText.replace(/[0-9.]/g, '').trim();
        
        if (!isNaN(quantite) && quantite > 0) {
            const nouveau = type === 'entree' ? stockActuel + quantite : stockActuel - quantite;
            modal.querySelector('#mvtNouveauStock').textContent = nouveau + ' ' + unite;
        } else {
            modal.querySelector('#mvtNouveauStock').textContent = stockActuel + ' ' + unite;
        }
    });
});

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
    renderTable();
    renderHistory();
    
    // Date par défaut pour le mouvement
    document.getElementById('mvtDate').value = new Date().toISOString().split('T')[0];
});

// ================= STYLES ADDITIONNELS =================
const style = document.createElement('style');
style.textContent = `
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
    .custom-toast.info .toast-content { background: #17a2b8; color: white; }
    
    @media (max-width: 768px) {
        .custom-toast {
            left: 15px;
            right: 15px;
            bottom: 15px;
            transform: translateY(100px);
        }
        .custom-toast.show { transform: translateY(0); }
    }
    
    .badge-critical { background: #dc3545; color: white; }
    .badge-low { background: #ffc107; color: #343a40; }
    .badge-medium { background: #fd7e14; color: white; }
    .badge-good { background: #28a745; color: white; }
    
    .stocks-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }
    .stat-item {
        background: white;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        text-align: center;
    }
    .stat-label {
        display: block;
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
    }
    .stat-value {
        display: block;
        font-size: 1.8rem;
        font-weight: 700;
        margin-top: 4px;
    }
    .stat-value.critical { color: #dc3545; }
    .stat-value.warning { color: #ffc107; }
    .stat-value.good { color: #28a745; }
    
    .btn-export {
        background: #17a2b8;
        color: white;
        border-color: #17a2b8;
    }
    .btn-export:hover {
        background: #138496;
        border-color: #138496;
        color: white;
    }
    
    @media (max-width: 768px) {
        .stocks-stats {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .stat-value {
            font-size: 1.4rem;
        }
    }
    @media (max-width: 480px) {
        .stocks-stats {
            grid-template-columns: 1fr;
        }
    }
`;
document.head.appendChild(style);
</script>

@endsection