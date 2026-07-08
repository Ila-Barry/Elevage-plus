{{-- resources/views/stocks.blade.php --}}

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
            </div>

            <!-- ===== BARRE RECHERCHE + FILTRE ===== -->
            <div class="stocks-toolbar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Rechercher un produit...">
                </div>
                <button class="btn-search" id="searchButton">
                    <i class="fas fa-search"></i> Rechercher
                </button>
                <select class="filter-cat" id="filterCategory">
                    <option value="all">Toutes les catégories</option>
                    <option value="aliment">Aliments</option>
                    <option value="medicament">Médicaments</option>
                    <option value="equipement">Équipements</option>
                    <option value="autre">Autre</option>
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
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-success"></i>
                                <p class="mt-2 text-muted">Chargement des produits...</p>
                            </td>
                        </tr>
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
            <div id="addError" class="alert alert-danger" style="display: none;"></div>
            <form id="addProductForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- ... contenu existant inchangé ... -->
                    <div class="form-group">
                        <label>Nom du produit *</label>
                        <input type="text" class="form-control" id="addProductName" placeholder="Ex: Aliment vache premium" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Catégorie *</label>
                            <select class="form-control" id="addProductCategory" required>
                                <option value="">Choisir...</option>
                                <option value="aliment">Aliments</option>
                                <option value="medicament">Médicaments</option>
                                <option value="equipement">Équipements</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Unité de mesure</label>
                            <select class="form-control" id="addProductUnit">
                                <option value="kg">Kilogramme (kg)</option>
                                <option value="g">Gramme (g)</option>
                                <option value="l">Litre (l)</option>
                                <option value="ml">Millilitre (ml)</option>
                                <option value="piece">Pièce</option>
                                <option value="boite">Boîte</option>
                                <option value="sac">Sac</option>
                                <option value="bouteille">Bouteille</option>
                                <option value="unite">Unité</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Quantité initiale *</label>
                            <input type="number" class="form-control" id="addProductQuantity" placeholder="0" min="0" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Seuil d'alerte *</label>
                            <input type="number" class="form-control" id="addProductThreshold" placeholder="Ex: 50" min="0" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Élevage *</label>
                        <select class="form-control" id="addElevage" required>
                            <option value="">Sélectionnez un élevage...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Description (optionnel)</label>
                        <textarea class="form-control" id="addProductDescription" rows="2" placeholder="Informations supplémentaires..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel-modal" data-dismiss="modal" onclick="closeModalSafe('modalAjoutProduit')">
                        ❌ Annuler
                    </button>
                    <button type="submit" class="btn-add-modal" id="addSubmitBtn">
                        ✅ Ajouter
                    </button>
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
            <div id="movementError" class="alert alert-danger" style="display: none;"></div>
            <form id="movementForm">
                <div class="modal-body">
                    <!-- ... contenu existant inchangé ... -->
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
                            <input type="number" class="form-control" id="mvtQuantite" placeholder="100" min="0.01" step="0.01" required>
                        </div>
                    </div>

                    <div class="form-row mt-3">
                        <div class="col-md-6">
                            <label>Motif *</label>
                            <select class="form-control" id="mvtMotif" required>
                                <option value="">Choisir...</option>
                                <option value="achat">Achat fournisseur</option>
                                <option value="don">Don</option>
                                <option value="production">Production propre</option>
                                <option value="consommation">Consommation animale</option>
                                <option value="perte">Perte</option>
                                <option value="inventaire">Ajustement inventaire</option>
                                <option value="autre">Autre</option>
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
                    <button type="button" class="btn-cancel-modal" data-dismiss="modal" onclick="closeModalSafe('modalMouvement')">
                        ❌ Annuler
                    </button>
                    <button type="submit" class="btn-add-modal" id="mvtSubmitBtn">
                        ✅ Valider
                    </button>
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
                <button type="button" class="btn-cancel-modal" data-dismiss="modal" onclick="closeModalSafe('modalVoir')" style="border-color: #6c757d; color: #6c757d; min-width: 100px;">
                    Fermer
                </button>
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
            <div id="editError" class="alert alert-danger" style="display: none;"></div>
            <form id="editProductForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editProductId">
                <div class="modal-body">
                    <!-- ... contenu existant inchangé ... -->
                    <div class="form-group">
                        <label>Nom du produit *</label>
                        <input type="text" class="form-control" id="editProductName" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Catégorie *</label>
                            <select class="form-control" id="editProductCategory" required>
                                <option value="aliment">Aliments</option>
                                <option value="medicament">Médicaments</option>
                                <option value="equipement">Équipements</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Unité de mesure</label>
                            <select class="form-control" id="editProductUnit">
                                <option value="kg">Kilogramme (kg)</option>
                                <option value="g">Gramme (g)</option>
                                <option value="l">Litre (l)</option>
                                <option value="ml">Millilitre (ml)</option>
                                <option value="piece">Pièce</option>
                                <option value="boite">Boîte</option>
                                <option value="sac">Sac</option>
                                <option value="bouteille">Bouteille</option>
                                <option value="unite">Unité</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Quantité actuelle</label>
                            <input type="number" class="form-control" id="editProductQuantity" disabled>
                            <small class="text-muted">La quantité ne peut être modifiée que via un mouvement.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Seuil d'alerte *</label>
                            <input type="number" class="form-control" id="editProductThreshold" min="0" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" id="editProductDescription" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel-modal" data-dismiss="modal" onclick="closeModalSafe('modalModifier')">
                        ❌ Annuler
                    </button>
                    <button type="submit" class="btn-add-modal" id="editSubmitBtn">
                        ✅ Enregistrer
                    </button>
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

<script>
// ================= CONFIGURATION =================
const API_URL = window.location.origin + '/api';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';
const rawToken = localStorage.getItem('access_token');
const token = rawToken ? rawToken.replace(/^"(.*)"$/, '$1').trim() : null;

console.log('🔍 Configuration Stocks:', { 
    API_URL, 
    token: token ? '✅ Présent' : '❌ Absent',
    token_preview: token ? token.substring(0, 20) + '...' : null
});

// ================= VARIABLES =================
let products = [];
let movements = [];
let elevages = [];
let toastTimeout = null;
let currentProductId = null;
let currentPage = 1;
const itemsPerPage = 10;

// ================= FONCTIONS TOAST =================
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

// ================= API CALLS AVEC GESTION D'ERREUR =================
async function fetchWithAuth(url, options = {}) {
    const headers = {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + token,
        'X-CSRF-TOKEN': CSRF_TOKEN,
        ...options.headers
    };
    
    if (!token) {
        showToast('Session expirée. Veuillez vous reconnecter.', 'danger');
        setTimeout(() => window.location.href = '/auth/login', 2000);
        throw new Error('Token manquant');
    }
    
    const response = await fetch(url, {
        ...options,
        headers: headers
    });

    if (response.status === 401) {
        console.error('❌ Token invalide ou expiré');
        localStorage.removeItem('access_token');
        localStorage.removeItem('token_expiry');
        localStorage.removeItem('user');
        showToast('Session expirée. Veuillez vous reconnecter.', 'danger');
        setTimeout(() => window.location.href = '/auth/login', 2000);
        throw new Error('Non authentifié');
    }

    const result = await response.json();
    if (!response.ok) {
        throw result;
    }
    return result;
}

// ================= BOUTON RAPPORT =================
document.getElementById('generateReport').addEventListener('click', function() {
    generateReport();
});

function generateReport() {
    if (!products || products.length === 0) {
        showToast('Aucun produit disponible', 'warning');
        return;
    }

    // Créer le HTML du rapport
    let html = `
    <!DOCTYPE html>
    <html>
    <head>
        <title>Rapport de Stock</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            h1 { color: #28a745; text-align: center; }
            .header { text-align: center; margin-bottom: 30px; }
            .stats { display: flex; justify-content: space-around; margin: 20px 0; }
            .stat { text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 8px; min-width: 100px; }
            .stat-value { font-size: 24px; font-weight: bold; }
            .stat-label { color: #666; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background: #28a745; color: white; padding: 10px; text-align: left; }
            td { padding: 10px; border-bottom: 1px solid #ddd; }
            .critical { color: #dc3545; }
            .low { color: #ffc107; }
            .good { color: #28a745; }
            .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
            @media print {
                .no-print { display: none; }
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>📊 RAPPORT DE STOCK</h1>
            <p>Généré le ${new Date().toLocaleDateString('fr-FR')} à ${new Date().toLocaleTimeString('fr-FR')}</p>
        </div>
    `;

    // Statistiques
    const total = products.length;
    const critical = products.filter(p => getStockStatus(p.quantite || 0, p.seuil_alerte || 50).status === 'critical').length;
    const low = products.filter(p => getStockStatus(p.quantite || 0, p.seuil_alerte || 50).status === 'low').length;
    const good = products.filter(p => getStockStatus(p.quantite || 0, p.seuil_alerte || 50).status === 'good' || getStockStatus(p.quantite || 0, p.seuil_alerte || 50).status === 'medium').length;

    html += `
        <div class="stats">
            <div class="stat">
                <div class="stat-value">${total}</div>
                <div class="stat-label">Total produits</div>
            </div>
            <div class="stat">
                <div class="stat-value" style="color: #dc3545;">${critical}</div>
                <div class="stat-label">Stock critique</div>
            </div>
            <div class="stat">
                <div class="stat-value" style="color: #ffc107;">${low}</div>
                <div class="stat-label">Stock faible</div>
            </div>
            <div class="stat">
                <div class="stat-value" style="color: #28a745;">${good}</div>
                <div class="stat-label">Stock bon</div>
            </div>
        </div>
    `;

    // Tableau des produits
    html += `
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Catégorie</th>
                    <th>Quantité</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
    `;

    products.forEach(p => {
        const status = getStockStatus(p.quantite || 0, p.seuil_alerte || 50);
        const statusClass = status.status;
        html += `
            <tr>
                <td>${p.nom || 'Sans nom'}</td>
                <td>${p.categorie_label || p.categorie || 'N/A'}</td>
                <td>${p.quantite || 0} ${p.unite || ''}</td>
                <td class="${statusClass}">${status.label}</td>
            </tr>
        `;
    });

    html += `
            </tbody>
        </table>
        <div class="footer">
            <p>Rapport généré par Elevage+ - ${new Date().toISOString().split('T')[0]}</p>
        </div>
        <div style="text-align: center; margin-top: 20px;" class="no-print">
            <button onclick="window.print()" style="padding: 10px 30px; background: #28a745; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px;">
                🖨️ Imprimer / PDF
            </button>
            <button onclick="window.close()" style="padding: 10px 30px; background: #6c757d; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; margin-left: 10px;">
                Fermer
            </button>
        </div>
    </body>
    </html>
    `;

    // Ouvrir dans une nouvelle fenêtre
    const win = window.open('', '_blank');
    win.document.write(html);
    win.document.close();
    
    showToast('Rapport généré avec succès !', 'success');
}



// ================= FONCTIONS API =================
async function fetchElevages() {
    try {
        console.log('📤 Récupération des élevages...');
        const url = `${API_URL}/elevages?per_page=50`;
        return await fetchWithAuth(url);
    } catch (error) {
        console.error('❌ Erreur fetch élevages:', error);
        throw error;
    }
}

async function fetchProducts(search = '', category = 'all', status = 'all') {
    try {
        console.log('📤 Récupération des produits...');
        let url = `${API_URL}/stock/produits?per_page=50`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (category !== 'all') url += `&categorie=${encodeURIComponent(category)}`;
        if (status === 'critical') url += `&statut=critique`;
        else if (status === 'low') url += `&statut=low`;
        else if (status === 'good') url += `&statut=good`;
        
        return await fetchWithAuth(url);
    } catch (error) {
        console.error('❌ Erreur fetch produits:', error);
        throw error;
    }
}

async function fetchMovements(productId = null) {
    try {
        console.log('📤 Récupération des mouvements...');
        let url = `${API_URL}/stock/mouvements?per_page=20`;
        if (productId) url += `&produit_id=${productId}`;
        
        const result = await fetchWithAuth(url);
        console.log('📥 Réponse mouvements brute:', result);
        return result;
    } catch (error) {
        console.error('❌ Erreur fetch mouvements:', error);
        throw error;
    }
}

// ================= FONCTIONS API PRODUITS =================
async function createProduct(data) {
    try {
        return await fetchWithAuth(`${API_URL}/stock/produits`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
    } catch (error) {
        console.error('❌ Erreur création produit:', error);
        throw error;
    }
}

async function updateProduct(id, data) {
    try {
        return await fetchWithAuth(`${API_URL}/stock/produits/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
    } catch (error) {
        console.error('❌ Erreur mise à jour produit:', error);
        throw error;
    }
}

async function deleteProduct(id) {
    try {
        return await fetchWithAuth(`${API_URL}/stock/produits/${id}`, {
            method: 'DELETE'
        });
    } catch (error) {
        console.error('❌ Erreur suppression produit:', error);
        throw error;
    }
}

async function addStock(productId, data) {
    try {
        return await fetchWithAuth(`${API_URL}/stock/mouvements/${productId}/entree`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
    } catch (error) {
        console.error('❌ Erreur ajout stock:', error);
        throw error;
    }
}

async function removeStock(productId, data) {
    try {
        return await fetchWithAuth(`${API_URL}/stock/mouvements/${productId}/sortie`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
    } catch (error) {
        console.error('❌ Erreur retrait stock:', error);
        throw error;
    }
}

// ================= GESTIONNAIRE UNIVERSEL DES MODALS =================
function openModalSafe(modalId) {
    const modalElement = document.getElementById(modalId);
    if (!modalElement) {
        console.error(`❌ Elément #${modalId} introuvable dans le DOM.`);
        return;
    }

    if (window.bootstrap && bootstrap.Modal) {
        const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        modalInstance.show();
    } else if (window.$ && typeof $.fn.modal === 'function') {
        $(modalElement).modal('show');
    } else {
        modalElement.style.setProperty('display', 'block', 'important');
        modalElement.classList.add('show');
        document.body.classList.add('modal-open');
        if (!document.querySelector('.modal-backdrop')) {
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        }
    }
}

// ================= FERMETURE ROBUSTE DES MODALES =================
function closeModalSafe(modalId) {
    const modalElement = document.getElementById(modalId);
    if (!modalElement) {
        console.warn(`⚠️ Modale #${modalId} introuvable.`);
        return;
    }

    // Essayer avec Bootstrap
    if (window.bootstrap && bootstrap.Modal) {
        try {
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
                return;
            }
            // Si pas d'instance, en créer une et la fermer
            const newInstance = new bootstrap.Modal(modalElement);
            newInstance.hide();
            return;
        } catch (e) {
            console.warn('⚠️ Erreur Bootstrap:', e);
        }
    }

    // Essayer avec jQuery
    if (window.$ && typeof $.fn.modal === 'function') {
        try {
            $(modalElement).modal('hide');
            return;
        } catch (e) {
            console.warn('⚠️ Erreur jQuery:', e);
        }
    }

    // Fallback manuel
    modalElement.classList.remove('show');
    modalElement.style.display = 'none';
    document.body.classList.remove('modal-open');
    
    // Supprimer les backdrops
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.remove();
    });
    
    // Remettre le scroll
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

// ================= FONCTIONS D'AFFICHAGE =================
function getStockStatus(quantite, seuil) {
    if (quantite <= 0) return { status: 'critical', label: 'Rupture de stock', icon: 'fa-times-circle' };
    if (quantite < seuil * 0.3) return { status: 'critical', label: 'Stock critique', icon: 'fa-exclamation-circle' };
    if (quantite < seuil * 0.6) return { status: 'low', label: 'Stock faible', icon: 'fa-exclamation-triangle' };
    if (quantite < seuil) return { status: 'medium', label: 'Stock moyen', icon: 'fa-info-circle' };
    return { status: 'good', label: 'Stock bon', icon: 'fa-check-circle' };
}

function renderTable() {
    const tbody = document.getElementById('stocksTableBody');
    if (!tbody) return;
    
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const categoryFilter = document.getElementById('filterCategory').value;
    const statusFilter = document.getElementById('filterStatus').value;
    
    let filtered = products.filter(p => {
        const matchSearch = p.nom?.toLowerCase().includes(searchTerm) || false;
        const matchCategory = categoryFilter === 'all' || p.categorie === categoryFilter;
        const status = getStockStatus(p.quantite || 0, p.seuil_alerte || 50);
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
        updateStats(filtered);
        return;
    }
    
    tbody.innerHTML = filtered.map(p => {
        const status = getStockStatus(p.quantite || 0, p.seuil_alerte || 50);
        return `
            <tr data-id="${p.id}">
                <td>
                    <div class="prod-cell">
                        <span class="prod-icon"><i class="fas fa-box"></i></span>
                        ${p.nom || 'Sans nom'}
                    </div>
                </td>
                <td>
                    <span class="categorie-badge categorie-${p.categorie}">
                        ${p.categorie_label || p.categorie || 'Non catégorisé'}
                    </span>
                </td>
                <td>
                    <div class="quantite-cell">
                        <span class="quantite-valeur">${p.quantite || 0} ${p.unite || ''}</span>
                        <span class="stock-status status-${status.status}">
                            <i class="fas ${status.icon}"></i> ${status.label}
                        </span>
                    </div>
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

function updateStats(filtered) {
    const total = filtered.length;
    const critical = filtered.filter(p => getStockStatus(p.quantite || 0, p.seuil_alerte || 50).status === 'critical').length;
    const low = filtered.filter(p => getStockStatus(p.quantite || 0, p.seuil_alerte || 50).status === 'low').length;
    const good = filtered.filter(p => getStockStatus(p.quantite || 0, p.seuil_alerte || 50).status === 'good' || getStockStatus(p.quantite || 0, p.seuil_alerte || 50).status === 'medium').length;
    
    document.getElementById('totalProducts').textContent = total;
    document.getElementById('criticalProducts').textContent = critical;
    document.getElementById('lowProducts').textContent = low;
    document.getElementById('goodProducts').textContent = good;
}

function renderHistory(movementsData) {
    const container = document.getElementById('historyList');
    if (!container) return;
    
    const data = movementsData || movements;
    if (!data || data.length === 0) {
        container.innerHTML = `<div class="hist-item"><span class="text-muted">Aucun mouvement récent</span></div>`;
        return;
    }
    
    const productMap = {};
    products.forEach(p => { productMap[p.id] = p; });
    
    const sorted = [...data].sort((a, b) => new Date(b.date_mouvement || b.created_at || 0) - new Date(a.date_mouvement || a.created_at || 0));
    
    container.innerHTML = sorted.slice(0, 10).map(h => {
        const dotClass = h.type === 'entree' ? 'dot-green' : 'dot-red';
        const sign = h.type === 'entree' ? '+' : '-';
        let produitNom = h.produit?.nom || h.produit_nom || productMap[h.produit_id]?.nom || 'Produit inconnu';
        let unite = h.produit?.unite || h.produit_unite || productMap[h.produit_id]?.unite || '';
        const date = h.date_mouvement ? new Date(h.date_mouvement).toLocaleDateString('fr-FR') : 'N/A';
        
        return `
            <div class="hist-item">
                <span class="dot ${dotClass}"></span>
                <span>${date} - ${h.type === 'entree' ? 'Entrée' : 'Sortie'} : ${sign}${h.quantite || 0} ${unite} ${produitNom}</span>
            </div>
        `;
    }).join('');
}

function populateElevageSelect() {
    const select = document.getElementById('addElevage');
    if (!select) return;
    select.innerHTML = '<option value="">Sélectionnez un élevage...</option>';
    if (!elevages || elevages.length === 0) {
        select.innerHTML += '<option value="" disabled>❌ Aucun élevage disponible</option>';
        return;
    }
    elevages.forEach(elevage => {
        const option = document.createElement('option');
        option.value = elevage.id;
        option.textContent = elevage.nom || `Élevage #${elevage.id}`;
        select.appendChild(option);
    });
}

// ================= CHARGEMENT DES DONNÉES =================
async function loadData() {
    try {
        if (!token) return;
        
        const [elevagesResult, productsResult, movementsResult] = await Promise.allSettled([
            fetchElevages(),
            fetchProducts(),
            fetchMovements()
        ]);
        
        if (elevagesResult.status === 'fulfilled') {
            const res = elevagesResult.value;
            elevages = res.data?.data || res.data || [];
            populateElevageSelect();
        }
        
        if (productsResult.status === 'fulfilled') {
            const res = productsResult.value;
            products = res.data?.data || res.data || [];
        }
        
        if (movementsResult.status === 'fulfilled') {
            const res = movementsResult.value;
            movements = res.data?.data || res.data || [];
        }
        
        renderTable();
        renderHistory(movements);
    } catch (error) {
        console.error('❌ Erreur globale chargement:', error);
    }
}

// ================= AJOUTER PRODUIT =================
document.getElementById('openAddProduct').addEventListener('click', function() {
    document.getElementById('addProductForm').reset();
    document.getElementById('addError').style.display = 'none';
    openModalSafe('modalAjoutProduit');
});

document.getElementById('addProductForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const submitBtn = document.getElementById('addSubmitBtn');
    const errorDiv = document.getElementById('addError');
    errorDiv.style.display = 'none';
    
    const data = {
        nom: document.getElementById('addProductName').value.trim(),
        categorie: document.getElementById('addProductCategory').value,
        unite: document.getElementById('addProductUnit').value,
        elevage_id: parseInt(document.getElementById('addElevage').value),
        quantite_initiale: parseFloat(document.getElementById('addProductQuantity').value) || 0,
        seuil_alerte: parseFloat(document.getElementById('addProductThreshold').value) || 50,
        description: document.getElementById('addProductDescription').value.trim() || null
    };
    
    submitBtn.disabled = true;
    try {
        const result = await createProduct(data);
        if (result.success || result.status === 'success') {
            closeModalSafe('modalAjoutProduit');
            showToast('Produit ajouté avec succès !', 'success');
            await loadData();
        }
    } catch (error) {
        errorDiv.textContent = error.message || 'Erreur lors de la création';
        errorDiv.style.display = 'block';
    } finally {
        submitBtn.disabled = false;
    }
});

// ================= MODAL MOUVEMENT =================
function openMovementModal(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;
    
    currentProductId = productId;
    document.getElementById('mvtProductId').value = productId;
    document.getElementById('mvtProduit').textContent = product.nom || 'Sans nom';
    document.getElementById('mvtStockActuel').textContent = (product.quantite || 0) + ' ' + (product.unite || '');
    document.getElementById('mvtNouveauStock').textContent = (product.quantite || 0) + ' ' + (product.unite || '');
    document.getElementById('mvtQuantite').value = '';
    document.getElementById('mvtMotif').value = '';
    document.getElementById('mvtDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('movementError').style.display = 'none';
    document.getElementById('mvtEntree').checked = true;
    
    openModalSafe('modalMouvement');
}

function updateDynamicStock() {
    const stockText = document.getElementById('mvtStockActuel').textContent;
    const stockActuel = parseFloat(stockText.replace(/[^0-9.]/g, '')) || 0;
    const quantite = parseFloat(document.getElementById('mvtQuantite').value) || 0;
    const type = document.querySelector('input[name="mvtType"]:checked')?.value || 'entree';
    const unite = stockText.replace(/[0-9.]/g, '').trim();
    
    const nouveau = type === 'entree' ? stockActuel + quantite : stockActuel - quantite;
    document.getElementById('mvtNouveauStock').textContent = (quantite > 0 ? nouveau : stockActuel) + ' ' + unite;
}

document.getElementById('mvtQuantite').addEventListener('input', updateDynamicStock);
document.querySelectorAll('input[name="mvtType"]').forEach(radio => radio.addEventListener('change', updateDynamicStock));

document.getElementById('movementForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const submitBtn = document.getElementById('mvtSubmitBtn');
    const errorDiv = document.getElementById('movementError');
    
    const productId = parseInt(document.getElementById('mvtProductId').value);
    const type = document.querySelector('input[name="mvtType"]:checked')?.value;
    const quantite = parseFloat(document.getElementById('mvtQuantite').value);
    const motif = document.getElementById('mvtMotif').value;
    
    const data = {
        type: type,
        quantite: quantite,
        motif: motif,
        description: document.getElementById('mvtMotif').selectedOptions[0]?.text || '',
        date_mouvement: document.getElementById('mvtDate').value || null
    };
    
    submitBtn.disabled = true;
    try {
        let result = type === 'entree' ? await addStock(productId, data) : await removeStock(productId, data);
        if (result.success || result.status === 'success') {
            closeModalSafe('modalMouvement');
            showToast('Mouvement enregistré avec succès !', 'success');
            await loadData();
        }
    } catch (error) {
        errorDiv.textContent = error.message || 'Erreur lors du mouvement';
        errorDiv.style.display = 'block';
    } finally {
        submitBtn.disabled = false;
    }
});

// ================= VOIR PRODUIT =================
window.viewProduct = async function(productId) {
    console.log('📤 viewProduct appelé avec ID:', productId);
    try {
        const result = await fetchWithAuth(`${API_URL}/stock/produits/${productId}`);
        console.log('📥 Résultat viewProduct:', result);
        
        if ((result.success || result.status === 'success') && result.data) {
            const p = result.data;
            const status = getStockStatus(p.quantite || 0, p.seuil_alerte || 50);
            const body = document.getElementById('viewProductBody');
            if (!body) return;
            
            body.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nom :</strong> ${p.nom || 'Sans nom'}</p>
                        <p><strong>Catégorie :</strong> ${p.categorie_label || p.categorie || 'Non catégorisé'}</p>
                        <p><strong>Unité :</strong> ${p.unite || 'Non défini'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Quantité :</strong> ${p.quantite || 0} ${p.unite || ''}</p>
                        <p><strong>Seuil d'alerte :</strong> ${p.seuil_alerte || 0} ${p.unite || ''}</p>
                        <p><strong>Statut :</strong> <span class="badge badge-${status.status}">${status.label}</span></p>
                    </div>
                </div>
                <hr>
                <p><strong>Description :</strong> ${p.description || 'Aucune description'}</p>
                <p><strong>Derniers mouvements :</strong></p>
                ${p.mouvements && p.mouvements.length > 0 ? `
                    <ul class="list-unstyled">
                        ${p.mouvements.slice(0, 5).map(h => `
                            <li class="hist-item" style="border: none; padding: 4px 0;">
                                <span class="dot ${h.type === 'entree' ? 'dot-green' : 'dot-red'}"></span>
                                ${new Date(h.date_mouvement).toLocaleDateString('fr-FR')} - ${h.type === 'entree' ? 'Entrée' : 'Sortie'} : ${h.type === 'entree' ? '+' : '-'}${h.quantite} ${p.unite || ''} (${h.motif_label || h.motif})
                            </li>
                        `).join('')}
                    </ul>
                ` : '<p class="text-muted">Aucun mouvement enregistré</p>'}
            `;
            
            openModalSafe('modalVoir');
        }
    } catch (error) {
        console.error('❌ Erreur viewProduct:', error);
    }
};

// ================= MODIFIER PRODUIT =================
window.openEditModal = async function(productId) {
    console.log('📤 openEditModal appelé avec ID:', productId);
    try {
        const result = await fetchWithAuth(`${API_URL}/stock/produits/${productId}`);
        console.log('📥 Résultat openEditModal:', result);
        
        if ((result.success || result.status === 'success') && result.data) {
            const p = result.data;
            
            document.getElementById('editProductId').value = p.id;
            document.getElementById('editProductName').value = p.nom || '';
            document.getElementById('editProductCategory').value = p.categorie || 'aliment';
            document.getElementById('editProductUnit').value = p.unite || 'unite';
            document.getElementById('editProductQuantity').value = p.quantite || 0;
            document.getElementById('editProductThreshold').value = p.seuil_alerte || 50;
            document.getElementById('editProductDescription').value = p.description || '';
            document.getElementById('editError').style.display = 'none';
            
            openModalSafe('modalModifier');
        }
    } catch (error) {
        console.error('❌ Erreur openEditModal:', error);
    }
};

document.getElementById('editProductForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const submitBtn = document.getElementById('editSubmitBtn');
    const errorDiv = document.getElementById('editError');
    
    const productId = parseInt(document.getElementById('editProductId').value);
    const data = {
        nom: document.getElementById('editProductName').value.trim(),
        categorie: document.getElementById('editProductCategory').value,
        unite: document.getElementById('editProductUnit').value,
        seuil_alerte: parseFloat(document.getElementById('editProductThreshold').value) || 50,
        description: document.getElementById('editProductDescription').value.trim() || null
    };
    
    submitBtn.disabled = true;
    try {
        const result = await updateProduct(productId, data);
        if (result.success || result.status === 'success') {
            closeModalSafe('modalModifier');
            showToast('Produit modifié avec succès !', 'success');
            await loadData();
        }
    } catch (error) {
        errorDiv.textContent = error.message || 'Erreur lors de la modification';
        errorDiv.style.display = 'block';
    } finally {
        submitBtn.disabled = false;
    }
});

// ================= SUPPRIMER PRODUIT =================
function openDeleteModal(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;
    
    document.getElementById('supprNom').textContent = product.nom || 'Sans nom';
    document.getElementById('btnSupprimerConfirmer').dataset.id = productId;
    openModalSafe('modalSupprimer');
}

document.getElementById('btnSupprimerConfirmer').addEventListener('click', async function() {
    const productId = parseInt(this.dataset.id);
    this.disabled = true;
    try {
        const result = await deleteProduct(productId);
        if (result.success || result.status === 'success') {
            closeModalSafe('modalSupprimer');
            showToast('Produit supprimé avec succès', 'success');
            await loadData();
        }
    } catch (error) {
        showToast(error.message || 'Erreur lors de la suppression', 'danger');
    } finally {
        this.disabled = false;
    }
});

// ================= RECHERCHE ET FILTRES =================

document.getElementById('filterCategory').addEventListener('change', renderTable);
document.getElementById('filterStatus').addEventListener('change', renderTable);

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
    if (!token) {
        window.location.href = '/auth/login';
        return;
    }
    loadData();
});

// ======================================================
// GESTION DE LA RECHERCHE - VERSION CORRECTE
// ======================================================

// Recherche avec le bouton
document.getElementById('searchButton').addEventListener('click', function() {
    performSearch();
});

// Recherche avec la touche Entrée
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        performSearch();
    }
});

// Quand on vide le champ, on recharge tout
document.getElementById('searchInput').addEventListener('input', function() {
    if (this.value.trim().length === 0) {
        renderTable();
    }
});

// Fonction principale de recherche
function performSearch() {
    const searchTerm = document.getElementById('searchInput').value.trim();
    
    if (searchTerm.length === 0) {
        renderTable();
        showToast('Veuillez saisir un terme de recherche', 'warning');
        return;
    }
    
    // Filtrer les produits
    const filtered = products.filter(p => {
        const nom = (p.nom || '').toLowerCase();
        const categorie = (p.categorie_label || p.categorie || '').toLowerCase();
        const description = (p.description || '').toLowerCase();
        const search = searchTerm.toLowerCase();
        
        return nom.includes(search) || 
               categorie.includes(search) || 
               description.includes(search);
    });
    
    // Mettre à jour l'affichage
    if (filtered.length === 0) {
        showToast('Aucun produit trouvé pour "' + searchTerm + '"', 'info');
    } else {
        showToast(filtered.length + ' produit(s) trouvé(s)', 'success');
    }
    
    renderFilteredTable(filtered);
}

// Fonction pour afficher les résultats filtrés
function renderFilteredTable(filtered) {
    const tbody = document.getElementById('stocksTableBody');
    if (!tbody) return;
    
    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <i class="fas fa-search" style="font-size: 24px; color: #6c757d;"></i>
                    <p class="mt-2" style="color: #6c757d;">Aucun produit correspondant</p>
                </td>
            </tr>
        `;
        updateStats(filtered);
        return;
    }
    
    tbody.innerHTML = filtered.map(p => {
        const status = getStockStatus(p.quantite || 0, p.seuil_alerte || 50);
        return `
            <tr data-id="${p.id}">
                <td>
                    <div class="prod-cell">
                        <span class="prod-icon"><i class="fas fa-box"></i></span>
                        ${p.nom || 'Sans nom'}
                    </div>
                </td>
                <td>
                    <span class="categorie-badge categorie-${p.categorie}">
                        ${p.categorie_label || p.categorie || 'Non catégorisé'}
                    </span>
                </td>
                <td>
                    <div class="quantite-cell">
                        <span class="quantite-valeur">${p.quantite || 0} ${p.unite || ''}</span>
                        <span class="stock-status status-${status.status}">
                            <i class="fas ${status.icon}"></i> ${status.label}
                        </span>
                    </div>
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

// ================= FERMETURE DES MODALES =================
// Fermeture manuelle des modales pour garantir le fonctionnement

// 1. Pour le bouton "Annuler" et la croix de chaque modale
document.querySelectorAll('.modal .close, .modal .btn-secondary').forEach(element => {
    element.addEventListener('click', function(e) {
        e.preventDefault();
        const modal = this.closest('.modal');
        if (modal) {
            closeModalSafe(modal.id);
        }
    });
});

// 2. Fermeture en cliquant en dehors de la modale
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModalSafe(this.id);
        }
    });
});

// 3. Fermeture avec la touche Echap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal.show');
        if (openModals.length > 0) {
            const lastModal = openModals[openModals.length - 1];
            closeModalSafe(lastModal.id);
        }
    }
});
</script>

@endsection