<?php
include '../../../config/fonction.php';

$id = $_GET['id'] ?? 0;
$serie = getSerieById($id);
$tournages = getTournagesBySerieId($id);
?>
<?php include '../../../includes/header.php'; ?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
</head>

<style>
/* =========================================================
   VARIABLES
========================================================= */

:root {
    --primary: #171717;
    --primary-hover: #000;
    --accent: #e50914;
    --accent-hover: #b20710;
    --background: #f5f6f8;
    --white: #ffffff;
    --text: #171717;
    --muted: #737373;
    --border: #e5e7eb;
    --success: #16a34a;
    --danger: #dc2626;
    --warning: #f59e0b;
    --info: #3b82f6;
    --radius: 18px;
    --shadow: 0 10px 30px rgba(0, 0, 0, .06);
}

/* =========================================================
   PAGE
========================================================= */

.tournages-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.tournages-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.tournages-header {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 25px 30px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    box-shadow: var(--shadow);
}

.tournages-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.tournages-header-avatar {
    width: 70px;
    height: 70px;
    flex: 0 0 70px;
    border-radius: 16px;
    overflow: hidden;
    border: 3px solid var(--accent);
    background: #f4f4f5;
}

.tournages-header-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.tournages-header-avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 30px;
}

.tournages-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.tournages-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.tournages-header p {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 14px;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-add {
    height: 48px;
    padding: 0 24px;
    border-radius: 12px;
    border: 0;
    background: var(--accent);
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: .2s;
}

.btn-add:hover {
    background: var(--accent-hover);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(229, 9, 20, .3);
    color: white;
}

.header-count {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 30px;
    background: #f4f4f5;
    font-size: 12px;
    font-weight: 800;
}

/* =========================================================
   TABLE CARD
========================================================= */

.table-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.table-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.table-card-header h2 {
    margin: 0;
    font-size: 15px;
    font-weight: 900;
    display: flex;
    align-items: center;
    gap: 10px;
}

.table-card-header h2 i {
    color: var(--accent);
}

.table-card-header span {
    font-size: 12px;
    color: var(--muted);
    font-weight: 700;
}

.table-responsive {
    padding: 0 24px 24px;
    overflow-x: auto;
}

/* =========================================================
   DATA TABLE CUSTOM
========================================================= */

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    font-size: 12px;
    color: var(--text);
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 6px 12px;
    font-size: 12px;
    outline: none;
    transition: .2s;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(229, 9, 20, .1);
}

.dataTables_wrapper .dataTables_length select {
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 6px 12px;
    font-size: 12px;
    outline: none;
}

.tournage-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.tournage-table thead {
    background: #fafafa;
    border-bottom: 2px solid var(--border);
}

.tournage-table thead th {
    padding: 14px 16px;
    text-align: left;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--muted);
}

.tournage-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}

.tournage-table tbody tr:hover {
    background: #fafafa;
}

.tournage-table tbody tr:last-child {
    border-bottom: 0;
}

.tournage-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
}

/* =========================================================
   BADGES
========================================================= */

.badge-team {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    background: #f4f4f5;
    font-size: 11px;
    font-weight: 700;
    color: var(--text);
    cursor: pointer;
    transition: .2s;
}

.badge-team:hover {
    background: var(--accent);
    color: white;
}

.badge-team i {
    font-size: 11px;
}

.badge-depense {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    background: #f0fdf4;
    font-size: 11px;
    font-weight: 700;
    color: var(--success);
}

.badge-depense i {
    font-size: 11px;
}

/* =========================================================
   ACTIONS
========================================================= */

.action-group {
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-action {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    text-decoration: none;
    transition: .2s;
    cursor: pointer;
    background: transparent;
}

.btn-action:hover {
    transform: translateY(-1px);
}

.btn-action.edit {
    color: var(--warning);
    background: #fffbeb;
}

.btn-action.edit:hover {
    background: #fef3c7;
}

.btn-action.delete {
    color: var(--danger);
    background: #fef2f2;
}

.btn-action.delete:hover {
    background: #fecaca;
}

.btn-action.view {
    color: var(--info);
    background: #eff6ff;
}

.btn-action.view:hover {
    background: #dbeafe;
}

/* =========================================================
   MODAL CUSTOM
========================================================= */

.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, .5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
}

.modal-overlay.active {
    display: flex;
}

.modal-box {
    background: var(--white);
    border-radius: var(--radius);
    max-width: 900px;
    width: 95%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 30px 60px rgba(0, 0, 0, .3);
    animation: modalFadeIn .3s ease;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: translateY(-30px) scale(.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header-custom {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    background: var(--white);
    z-index: 10;
    border-radius: var(--radius) var(--radius) 0 0;
}

.modal-header-custom h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 900;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-header-custom h5 i {
    color: var(--accent);
}

.modal-close {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 0;
    background: #f4f4f5;
    color: var(--muted);
    font-size: 16px;
    cursor: pointer;
    transition: .2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: var(--danger);
    color: white;
}

.modal-body-custom {
    padding: 24px;
}

/* =========================================================
   EMPTY STATE
========================================================= */

.empty-state {
    text-align: center;
    padding: 50px 20px;
    color: var(--muted);
}

.empty-state i {
    font-size: 48px;
    color: #d4d4d8;
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 18px;
    font-weight: 900;
    color: var(--text);
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 13px;
    margin-bottom: 20px;
}

/* =========================================================
   ALERT / TOAST
========================================================= */

.modern-alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    margin-bottom: 20px;
    border-radius: 13px;
    font-size: 12px;
    font-weight: 600;
}

.modern-alert.success {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.modern-alert.error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert-icon {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, .7);
}

.alert-close {
    margin-left: auto;
    border: 0;
    background: transparent;
    color: inherit;
    cursor: pointer;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 992px) {
    .tournages-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .header-actions {
        width: 100%;
        flex-wrap: wrap;
    }
    .btn-add {
        flex: 1;
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .tournages-page {
        padding: 15px 0 35px;
    }
    .tournages-container {
        padding: 0 12px;
    }
    .tournages-header {
        padding: 18px;
    }
    .tournages-header h1 {
        font-size: 21px;
    }
    .tournages-header-avatar {
        width: 56px;
        height: 56px;
        flex-basis: 56px;
    }
    .table-responsive {
        padding: 0 12px 12px;
    }
    .tournage-table thead {
        display: none;
    }
    .tournage-table tbody tr {
        display: block;
        padding: 12px 0;
        border-bottom: 2px solid var(--border);
    }
    .tournage-table tbody tr:last-child {
        border-bottom: 0;
    }
    .tournage-table tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 0;
    }
    .tournage-table tbody td::before {
        content: attr(data-label);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--muted);
        letter-spacing: 0.3px;
    }
    .tournage-table tbody td:first-child::before {
        display: none;
    }
    .tournage-table tbody td:first-child {
        justify-content: flex-start;
        font-weight: 700;
        padding-bottom: 8px;
    }
    .action-group {
        gap: 4px;
    }
    .modal-box {
        width: 98%;
        max-height: 95vh;
    }
    .modal-header-custom h5 {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .tournages-header-left {
        gap: 12px;
    }
    .tournages-header-avatar {
        display: none;
    }
    .header-actions {
        flex-direction: column;
    }
    .btn-add {
        width: 100%;
    }
    .header-count {
        width: 100%;
        justify-content: center;
    }
    .badge-team {
        font-size: 10px;
        padding: 3px 10px;
    }
    .badge-depense {
        font-size: 10px;
        padding: 3px 10px;
    }
}
</style>

<section class="tournages-page">
    <div class="tournages-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="tournages-header">
            <div class="tournages-header-left">
                <div class="tournages-header-avatar">
                    <?php if (!empty($serie['logo']) && file_exists('../../../uploads/series/' . $serie['logo'])): ?>
                    <img src="../../../uploads/series/<?= htmlspecialchars($serie['logo']) ?>" alt="<?= htmlspecialchars($serie['titre']) ?>">
                    <?php else: ?>
                    <div class="tournages-header-avatar-placeholder">
                        <i class="fas fa-film"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="tournages-breadcrumb">
                        EVENPROD / SÉRIES / TOURNAGES
                    </div>
                    <h1>Gestion des tournages</h1>
                    <p>
                        <i class="fas fa-film" style="color:var(--accent);"></i>
                        Série : <strong><?= htmlspecialchars($serie['titre'] ?? 'Série introuvable') ?></strong>
                        &nbsp;&bull;&nbsp;
                        <i class="fas fa-coins"></i>
                        Budget : <?= number_format($serie['budget'] ?? 0, 0, ',', ' ') ?> FCFA
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <span class="header-count">
                    <i class="fas fa-video"></i>
                    <?= count($tournages) ?> tournage<?= count($tournages) > 1 ? 's' : '' ?>
                </span>
                <a href="add_tourn.php?id_serie=<?= htmlspecialchars($serie['id'] ?? 0) ?>" class="btn-add">
                    <i class="fas fa-plus"></i>
                    Nouveau tournage
                </a>
            </div>
        </header>

        <!-- =========================================================
        ALERTES
        ========================================================= -->

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="modern-alert success">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>Tournage supprimé avec succès !</div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
        <div class="modern-alert success">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>Tournage supprimé avec succès !</div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- =========================================================
        TABLE DES TOURNAGES
        ========================================================= -->

        <div class="table-card">
            <div class="table-card-header">
                <h2>
                    <i class="fas fa-clipboard-list"></i>
                    Liste des tournages
                </h2>
                <span><?= count($tournages) ?> enregistré<?= count($tournages) > 1 ? 's' : '' ?></span>
            </div>

            <div class="table-responsive">
                <?php if (!empty($tournages)): ?>
                <table class="tournage-table" id="tournageTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Équipe</th>
                            <th>Dépenses</th>
                            <th style="text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($tournages as $t): 
                            $depense = getDepenseByTournage($id, $t['id']);
                            $equipeCount = getEquipeCountByTournage($t['id']);
                            $acteurs = getActeursByTournage($t['id']);
                        ?>
                        <tr>
                            <td data-label="#">
                                <span style="font-weight:700; color:var(--muted);">#<?= $i ?></span>
                            </td>
                            <td data-label="Référence">
                                <span style="font-weight:700;">
                                    <i class="fas fa-hashtag" style="color:var(--accent); font-size:11px;"></i>
                                    <?= htmlspecialchars($t['reference']) ?>
                                </span>
                            </td>
                            <td data-label="Date">
                                <i class="fas fa-calendar-alt" style="color:var(--muted); font-size:11px; margin-right:4px;"></i>
                                <?= date('d/m/Y', strtotime($t['date_tournage'])) ?>
                            </td>
                            <td data-label="Équipe">
                                <span class="badge-team voir-acteurs" 
                                      data-acteurs='<?= htmlspecialchars(json_encode($acteurs)) ?>'
                                      style="cursor:pointer;">
                                    <i class="fas fa-users"></i>
                                    +<?= $equipeCount ?> acteur<?= $equipeCount > 1 ? 's' : '' ?>
                                </span>
                            </td>
                            <td data-label="Dépenses">
                                <span class="badge-depense">
                                    <i class="fas fa-coins"></i>
                                    <?= number_format($depense, 0, ',', ' ') ?> FCFA
                                </span>
                            </td>
                            <td data-label="Actions" style="text-align:center;">
                                <div class="action-group" style="justify-content:center;">
                                    <a href="add_tourn.php?id_serie=<?= $id ?>&id_tournage=<?= $t['id'] ?>" 
                                       class="btn-action edit" title="Modifier">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="<?= $url_base ?>public/appManager/delete.php?table=tournages&id=<?= htmlspecialchars($t['id']) ?>&redirect=<?= $url_base ?>public/appManager/series/tournages.php?id=<?= htmlspecialchars($id) ?>" 
                                       class="btn-action delete" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce tournage ? Cette action est irréversible.');"
                                       title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php $i++; endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-video"></i>
                    <h3>Aucun tournage</h3>
                    <p>Aucun tournage n'est encore enregistré pour cette série.</p>
                    <a href="add_tourn.php?id_serie=<?= htmlspecialchars($serie['id'] ?? 0) ?>" class="btn-add" style="display:inline-flex;">
                        <i class="fas fa-plus"></i>
                        Ajouter le premier tournage
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<!-- =========================================================
MODAL ACTEURS
========================================================= -->

<div class="modal-overlay" id="modalActeurs">
    <div class="modal-box">
        <div class="modal-header-custom">
            <h5>
                <i class="fas fa-users"></i>
                Acteurs du tournage
            </h5>
            <button type="button" class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body-custom" id="acteursContent">
            <!-- Contenu injecté par JS -->
        </div>
    </div>
</div>

<!-- =========================================================
SCRIPTS
========================================================= -->

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
// Fonction pour ouvrir le modal
function openModal(content) {
    document.getElementById('acteursContent').innerHTML = content;
    document.getElementById('modalActeurs').classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Fonction pour fermer le modal
function closeModal() {
    document.getElementById('modalActeurs').classList.remove('active');
    document.body.style.overflow = '';
}

// Fermer le modal en cliquant en dehors
document.getElementById('modalActeurs').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Fermer le modal avec Echap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// DataTable
$(document).ready(function() {
    if ($('#tournageTable tbody tr').length > 0) {
        $('#tournageTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50],
            "responsive": true,
            "columnDefs": [
                { "orderable": false, "targets": 5 }
            ]
        });
    }
});

// Affichage des acteurs dans le modal
$(document).on('click', '.voir-acteurs', function(e) {
    e.preventDefault();
    let acteurs = $(this).data('acteurs');
    
    if (typeof acteurs === "string") {
        acteurs = JSON.parse(acteurs);
    }

    if (!acteurs || acteurs.length === 0) {
        openModal(`
            <div style="text-align:center; padding:30px; color:var(--muted);">
                <i class="fas fa-users" style="font-size:38px; color:#d4d4d8; margin-bottom:12px; display:block;"></i>
                <p>Aucun acteur n'est associé à ce tournage.</p>
            </div>
        `);
        return;
    }

    let html = `
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead style="background:#fafafa; border-bottom:2px solid var(--border);">
                    <tr>
                        <th style="padding:10px 12px; text-align:left; font-size:10px; font-weight:800; text-transform:uppercase; color:var(--muted);">Acteur</th>
                        <th style="padding:10px 12px; text-align:left; font-size:10px; font-weight:800; text-transform:uppercase; color:var(--muted);">Date naissance</th>
                        <th style="padding:10px 12px; text-align:left; font-size:10px; font-weight:800; text-transform:uppercase; color:var(--muted);">Contact</th>
                        <th style="padding:10px 12px; text-align:right; font-size:10px; font-weight:800; text-transform:uppercase; color:var(--muted);">Cachet</th>
                    </tr>
                </thead>
                <tbody>
    `;

    acteurs.forEach(function(a) {
        html += `
            <tr style="border-bottom:1px solid var(--border);">
                <td style="padding:10px 12px; font-weight:700;">
                    <i class="fas fa-user" style="color:var(--muted); font-size:11px; margin-right:6px;"></i>
                    ${a.prenom} ${a.nom}
                </td>
                <td style="padding:10px 12px; color:var(--muted);">
                    <i class="fas fa-calendar-alt" style="font-size:10px; margin-right:4px;"></i>
                    ${a.date_naissance || 'Non renseignée'}
                </td>
                <td style="padding:10px 12px; color:var(--muted);">
                    <i class="fas fa-phone" style="font-size:10px; margin-right:4px;"></i>
                    ${a.contact || 'Non renseigné'}
                </td>
                <td style="padding:10px 12px; text-align:right; font-weight:700; color:var(--accent);">
                    ${Number(a.cachet).toLocaleString('fr-FR')} FCFA
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    openModal(html);
});
</script>

<?php include '../../../includes/footer.php'; ?>