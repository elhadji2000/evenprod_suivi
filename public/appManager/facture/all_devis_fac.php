<?php
include '../../../config/fonction.php';

$serieId = $_GET['id'] ?? 0;
$serie = getSerieById($serieId);
$factures = getFacturesBySerieId($connexion, $serieId);

// Calcul des totaux
$totalDevis = 0;
$totalFactures = 0;
foreach ($factures as $row) {
    if ($row['type'] == 'devis') {
        $totalDevis += $row['total'];
    } else {
        $totalFactures += $row['total'];
    }
}
$totalGlobal = $totalDevis + $totalFactures;
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

.factures-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.factures-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.factures-header {
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

.factures-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.factures-header-avatar {
    width: 70px;
    height: 70px;
    flex: 0 0 70px;
    border-radius: 16px;
    overflow: hidden;
    border: 3px solid var(--accent);
    background: #f4f4f5;
}

.factures-header-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.factures-header-avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 30px;
}

.factures-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.factures-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.factures-header p {
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
   STATS OVERVIEW
========================================================= */

.stats-overview {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 25px;
}

.stat-overview-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    box-shadow: var(--shadow);
    transition: .3s;
}

.stat-overview-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, .08);
}

.stat-overview-card .label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--muted);
}

.stat-overview-card .value {
    font-size: 20px;
    font-weight: 900;
    margin: 4px 0 0;
}

.stat-overview-card .value.accent {
    color: var(--accent);
}

.stat-overview-card .value.success {
    color: var(--success);
}

.stat-overview-card .value.warning {
    color: var(--warning);
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

.factures-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.factures-table thead {
    background: #fafafa;
    border-bottom: 2px solid var(--border);
}

.factures-table thead th {
    padding: 14px 16px;
    text-align: left;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--muted);
}

.factures-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}

.factures-table tbody tr:hover {
    background: #fafafa;
}

.factures-table tbody tr:last-child {
    border-bottom: 0;
}

.factures-table tbody td {
    padding: 12px 16px;
    vertical-align: middle;
}

/* =========================================================
   BADGES
========================================================= */

.badge-type {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.badge-type.facture {
    background: #fef3c7;
    color: #92400e;
}

.badge-type.devis {
    background: #dbeafe;
    color: #1e40af;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
}

.badge-status.valide {
    background: #d1fae5;
    color: #065f46;
}

.badge-status.valide i {
    color: var(--success);
}

.badge-status.en-attente {
    background: #fef3c7;
    color: #92400e;
}

.badge-status.en-attente i {
    color: var(--warning);
}

/* =========================================================
   ACTIONS
========================================================= */

.action-group {
    display: flex;
    align-items: center;
    gap: 6px;
    justify-content: center;
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

.btn-action.validate {
    color: var(--success);
    background: #f0fdf4;
}

.btn-action.validate:hover {
    background: #bbf7d0;
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

@media (max-width: 1200px) {
    .stats-overview {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 992px) {
    .factures-header {
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
    .factures-page {
        padding: 15px 0 35px;
    }

    .factures-container {
        padding: 0 12px;
    }

    .factures-header {
        padding: 18px;
    }

    .factures-header h1 {
        font-size: 21px;
    }

    .factures-header-avatar {
        width: 56px;
        height: 56px;
        flex-basis: 56px;
    }

    .stats-overview {
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .stat-overview-card {
        padding: 14px 16px;
    }

    .stat-overview-card .value {
        font-size: 17px;
    }

    .table-responsive {
        padding: 0 12px 12px;
    }

    .factures-table thead {
        display: none;
    }

    .factures-table tbody tr {
        display: block;
        padding: 12px 0;
        border-bottom: 2px solid var(--border);
    }

    .factures-table tbody tr:last-child {
        border-bottom: 0;
    }

    .factures-table tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 0;
        flex-wrap: wrap;
    }

    .factures-table tbody td::before {
        content: attr(data-label);
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--muted);
        letter-spacing: 0.3px;
    }

    .factures-table tbody td:first-child::before {
        display: none;
    }

    .factures-table tbody td:first-child {
        justify-content: flex-start;
        font-weight: 700;
    }

    .action-group {
        gap: 4px;
    }
}

@media (max-width: 480px) {
    .factures-header-left {
        gap: 12px;
    }

    .factures-header-avatar {
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

    .stats-overview {
        grid-template-columns: 1fr;
    }
}
</style>

<section class="factures-page">
    <div class="factures-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="factures-header">
            <div class="factures-header-left">
                <div class="factures-header-avatar">
                    <?php if (!empty($serie['logo']) && file_exists('../../../uploads/series/' . $serie['logo'])): ?>
                    <img src="../../../uploads/series/<?= htmlspecialchars($serie['logo']) ?>"
                        alt="<?= htmlspecialchars($serie['titre']) ?>">
                    <?php else: ?>
                    <div class="factures-header-avatar-placeholder">
                        <i class="fas fa-film"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="factures-breadcrumb">
                        EVENPROD / SÉRIES / DEVIS & FACTURES
                    </div>
                    <h1>Gestion des devis et factures</h1>
                    <p>
                        <i class="fas fa-film" style="color:var(--accent);"></i>
                        Série : <strong><?= htmlspecialchars($serie['titre'] ?? 'Série introuvable') ?></strong>
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <span class="header-count">
                    <i class="fas fa-file-invoice"></i>
                    <?= count($factures) ?> document<?= count($factures) > 1 ? 's' : '' ?>
                </span>
                <a href="add_devis?id=<?= htmlspecialchars($serie['id'] ?? 0) ?>" class="btn-add">
                    <i class="fas fa-plus"></i>
                    Nouveau devis
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
            <div>Devis supprimé avec succès !</div>
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
            <div>Document supprimé avec succès !</div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['validate']) && $_GET['validate'] == 1): ?>
        <div class="modern-alert success">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>Devis validé avec succès !</div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- =========================================================
        STATS OVERVIEW
        ========================================================= -->

        <div class="stats-overview">
            <div class="stat-overview-card">
                <div class="label"><i class="fas fa-file-invoice"></i> Total général</div>
                <div class="value accent"><?= number_format($totalGlobal, 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="stat-overview-card">
                <div class="label"><i class="fas fa-file-invoice" style="color:var(--warning);"></i> Total devis</div>
                <div class="value warning"><?= number_format($totalDevis, 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="stat-overview-card">
                <div class="label"><i class="fas fa-file-invoice" style="color:var(--success);"></i> Total factures
                </div>
                <div class="value success"><?= number_format($totalFactures, 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="stat-overview-card">
                <div class="label"><i class="fas fa-file-alt"></i> Documents</div>
                <div class="value"><?= count($factures) ?></div>
            </div>
        </div>

        <!-- =========================================================
        TABLE DES DEVIS & FACTURES
        ========================================================= -->

        <div class="table-card">
            <div class="table-card-header">
                <h2>
                    <i class="fas fa-list"></i>
                    Liste des documents
                </h2>
                <span><?= count($factures) ?> enregistré<?= count($factures) > 1 ? 's' : '' ?></span>
            </div>

            <div class="table-responsive">
                <?php if (!empty($factures)): ?>
                <table class="factures-table" id="factureTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Référence</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th style="text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($factures as $row): ?>
                        <tr>
                            <td data-label="#">
                                <span style="font-weight:700; color:var(--muted);">#<?= $i ?></span>
                            </td>
                            <td data-label="Référence">
                                <span style="font-weight:700; font-size:12px;">
                                    <i class="fas fa-hashtag" style="color:var(--accent); font-size:10px;"></i>
                                    <?= htmlspecialchars($row['reference'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td data-label="Client">
                                <i class="fas fa-building"
                                    style="color:var(--muted); font-size:11px; margin-right:4px;"></i>
                                <?= htmlspecialchars($row['client_nom']) ?>
                            </td>
                            <td data-label="Date">
                                <i class="fas fa-calendar-alt"
                                    style="color:var(--muted); font-size:11px; margin-right:4px;"></i>
                                <?= date('d/m/Y', strtotime($row['date_facture'])) ?>
                            </td>
                            <td data-label="Montant">
                                <span style="font-weight:700; color:var(--accent);">
                                    <?= number_format($row['total'], 0, ',', ' ') ?> FCFA
                                </span>
                            </td>
                            <td data-label="Type">
                                <?php if ($row['type'] == "Facture"): ?>
                                <span class="badge-type facture">
                                    <i class="fas fa-check-circle"></i> Facture
                                </span>
                                <?php else: ?>
                                <span class="badge-type devis">
                                    <i class="fas fa-file-alt"></i> Devis
                                </span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Statut">
                                <?php if ($row['type'] == "Facture"): ?>
                                <span class="badge-status valide">
                                    <i class="fas fa-check-circle"></i> Validé
                                </span>
                                <?php else: ?>
                                <span class="badge-status en-attente">
                                    <i class="fas fa-clock"></i> En attente
                                </span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Actions" style="text-align:center;">
                                <div class="action-group">
                                    <!-- PDF -->
                                    <a href="facture_pdf.php?id=<?= $row['id'] ?>" class="btn-action view"
                                        target="_blank" title="Voir PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>

                                    <!-- Valider (uniquement pour les devis) -->
                                    <?php if (strtolower($row['type']) === 'devis'): ?>

                                    <a href="valider_devis.php?id=<?= (int)$row['id'] ?>&serie_id=<?= (int)$serieId ?>"
                                        class="btn-action validate" title="Valider le devis"
                                        onclick="return confirm('Voulez-vous vraiment valider ce devis ? Il deviendra une facture.');">

                                        <i class="fas fa-check"></i>

                                    </a>

                                    <?php endif; ?>
                                    <!-- Supprimer (uniquement pour les devis) -->
                                    <?php if ($row['type'] == "devis"): ?>
                                    <a href="<?= $url_base ?>public/appManager/delete.php?table=factures&id=<?= htmlspecialchars($row['id']) ?>&serie_id=<?= $serieId ?>&redirect=<?= $url_base ?>public/appManager/facture/all_devis_fac.php?id=<?= $serieId ?>"
                                        class="btn-action delete"
                                        onclick="return confirm('Voulez-vous vraiment supprimer ce devis ?')"
                                        title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php else: ?>
                                    <span style="color:var(--muted); font-size:10px; font-weight:700;">Protégé</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php $i++; endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-file-invoice"></i>
                    <h3>Aucun document</h3>
                    <p>Aucun devis ou facture n'est encore enregistré pour cette série.</p>
                    <a href="add_devis?id=<?= htmlspecialchars($serie['id'] ?? 0) ?>" class="btn-add"
                        style="display:inline-flex;">
                        <i class="fas fa-plus"></i>
                        Créer le premier devis
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<!-- =========================================================
SCRIPTS
========================================================= -->

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function() {
    if ($('#factureTable tbody tr').length > 0) {
        $('#factureTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50],
            "responsive": true,
            "columnDefs": [{
                "orderable": false,
                "targets": 7
            }]
        });
    }
});

// Fonction pour valider un devis
function validerDevis(button) {

    const id = parseInt(button.dataset.id, 10);

    console.log("ID du devis envoyé :", id);

    if (!id || id <= 0) {
        alert("Impossible de récupérer l'identifiant du devis.");
        return;
    }

    if (!confirm("Voulez-vous vraiment valider ce devis ? Il deviendra une facture.")) {
        return;
    }

    const originalContent = button.innerHTML;

    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    // Création du formulaire
    const formData = new FormData();

    formData.append("id", id);

    console.log("FormData ID :", formData.get("id"));

    fetch("./valider_devis.php", {
            method: "POST",
            body: formData
        })
        .then(response => {

            console.log("HTTP :", response.status);

            return response.text();
        })
        .then(text => {

            console.log("Réponse PHP :", text);

            let data;

            try {
                data = JSON.parse(text);
            } catch (error) {

                throw new Error(
                    "Le serveur n'a pas retourné du JSON valide.\n\n" +
                    text
                );
            }

            return data;
        })
        .then(data => {

            console.log("Résultat :", data);

            if (data.success) {

                showToast(
                    data.message || "Devis validé avec succès !",
                    "var(--success)"
                );

                setTimeout(() => {
                    window.location.reload();
                }, 1000);

            } else {

                alert(
                    data.message ||
                    "Impossible de valider le devis."
                );

                button.disabled = false;
                button.innerHTML = originalContent;
            }
        })
        .catch(error => {

            console.error("Erreur validation :", error);

            alert(
                "Erreur lors de la validation :\n\n" +
                error.message
            );

            button.disabled = false;
            button.innerHTML = originalContent;
        });
}

// Fonction Toast
function showToast(message, bgColor) {
    const toast = document.createElement("div");
    toast.textContent = message;
    toast.style.position = "fixed";
    toast.style.top = "80px";
    toast.style.right = "30px";
    toast.style.padding = "15px 25px";
    toast.style.backgroundColor = bgColor;
    toast.style.color = "white";
    toast.style.borderRadius = "5px";
    toast.style.boxShadow = "0 2px 10px rgba(0,0,0,0.2)";
    toast.style.zIndex = 9999;
    toast.style.fontWeight = "bold";
    toast.style.opacity = 0;
    toast.style.transition = "opacity 0.5s";

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = 1;
    }, 100);

    setTimeout(() => {
        toast.style.opacity = 0;
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}
</script>

<?php include '../../../includes/footer.php'; ?>