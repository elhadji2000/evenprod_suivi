<?php
include '../../../config/fonction.php';

$serieId = $_GET['id'] ?? 0;
$serie = getSerieById($serieId);
$depenses = getDepensesBySerie($serieId);
$totaux = getTotauxDepensesSerie($connexion, $serieId);

// Calcul du total
$total = 0;
foreach ($depenses as $d) {
    $total += $d['montant'];
}
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

.depenses-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.depenses-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.depenses-header {
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

.depenses-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.depenses-header-avatar {
    width: 70px;
    height: 70px;
    flex: 0 0 70px;
    border-radius: 16px;
    overflow: hidden;
    border: 3px solid var(--accent);
    background: #f4f4f5;
}

.depenses-header-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.depenses-header-avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 30px;
}

.depenses-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.depenses-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.depenses-header p {
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

.stat-overview-card .progress-bar-custom {
    margin-top: 8px;
    height: 4px;
    border-radius: 2px;
    background: #f4f4f5;
    overflow: hidden;
}

.stat-overview-card .progress-bar-custom .fill {
    height: 100%;
    border-radius: 2px;
    background: var(--accent);
    transition: width .6s ease;
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
    margin-bottom: 25px;
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

.depenses-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.depenses-table thead {
    background: #fafafa;
    border-bottom: 2px solid var(--border);
}

.depenses-table thead th {
    padding: 14px 16px;
    text-align: left;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--muted);
}

.depenses-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}

.depenses-table tbody tr:hover {
    background: #fafafa;
}

.depenses-table tbody tr:last-child {
    border-bottom: 0;
}

.depenses-table tbody td {
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

.badge-type.cachet {
    background: #fef3c7;
    color: #92400e;
}

.badge-type.decor {
    background: #dbeafe;
    color: #1e40af;
}

.badge-type.transport {
    background: #d1fae5;
    color: #065f46;
}

.badge-type.reception {
    background: #fce4ec;
    color: #9a1f3c;
}

.badge-type.accessoire {
    background: #ede9fe;
    color: #5b21b6;
}

.badge-type.reglement_acteur {
    background: #fce7f3;
    color: #9d174d;
}

.badge-type.hmc {
    background: #e0e7ff;
    color: #3730a3;
}

.badge-type.carburant {
    background: #fffbeb;
    color: #92400e;
}

.badge-type.pharmacie {
    background: #ecfdf5;
    color: #065f46;
}

.badge-type.autre {
    background: #f4f4f5;
    color: #52525b;
}

.badge-justif {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 12px;
    background: #eff6ff;
    color: var(--info);
    font-size: 10px;
    font-weight: 700;
    text-decoration: none;
    transition: .2s;
}

.badge-justif:hover {
    background: #dbeafe;
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

.btn-action.delete {
    color: var(--danger);
    background: #fef2f2;
}

.btn-action.delete:hover {
    background: #fecaca;
}

/* =========================================================
   CATEGORIES SECTION
========================================================= */

.categories-section {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.categories-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.categories-header h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 900;
    display: flex;
    align-items: center;
    gap: 10px;
}

.categories-header h3 i {
    color: var(--accent);
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    padding: 20px 24px;
}

.category-item {
    background: #fafafa;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 12px 14px;
    text-align: center;
    transition: .2s;
}

.category-item:hover {
    border-color: var(--accent);
    transform: translateY(-2px);
}

.category-item .icon {
    font-size: 20px;
    margin-bottom: 4px;
    display: block;
    color: var(--muted);
}

.category-item .label {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--muted);
    display: block;
    margin-bottom: 2px;
}

.category-item .value {
    font-size: 14px;
    font-weight: 900;
    color: var(--text);
}

.category-item .value small {
    font-size: 9px;
    font-weight: 400;
    color: var(--muted);
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

    .categories-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 992px) {
    .depenses-header {
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
    .depenses-page {
        padding: 15px 0 35px;
    }

    .depenses-container {
        padding: 0 12px;
    }

    .depenses-header {
        padding: 18px;
    }

    .depenses-header h1 {
        font-size: 21px;
    }

    .depenses-header-avatar {
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

    .depenses-table thead {
        display: none;
    }

    .depenses-table tbody tr {
        display: block;
        padding: 12px 0;
        border-bottom: 2px solid var(--border);
    }

    .depenses-table tbody tr:last-child {
        border-bottom: 0;
    }

    .depenses-table tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 0;
    }

    .depenses-table tbody td::before {
        content: attr(data-label);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--muted);
        letter-spacing: 0.3px;
    }

    .depenses-table tbody td:first-child::before {
        display: none;
    }

    .depenses-table tbody td:first-child {
        justify-content: flex-start;
        font-weight: 700;
    }

    .categories-grid {
        grid-template-columns: repeat(2, 1fr);
        padding: 12px 16px;
    }

    .action-group {
        gap: 4px;
    }
}

@media (max-width: 480px) {
    .depenses-header-left {
        gap: 12px;
    }

    .depenses-header-avatar {
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

    .categories-grid {
        grid-template-columns: 1fr 1fr;
    }
}
</style>

<section class="depenses-page">
    <div class="depenses-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="depenses-header">
            <div class="depenses-header-left">
                <div class="depenses-header-avatar">
                    <?php if (!empty($serie['logo']) && file_exists('../../../uploads/series/' . $serie['logo'])): ?>
                    <img src="../../../uploads/series/<?= htmlspecialchars($serie['logo']) ?>"
                        alt="<?= htmlspecialchars($serie['titre']) ?>">
                    <?php else: ?>
                    <div class="depenses-header-avatar-placeholder">
                        <i class="fas fa-film"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="depenses-breadcrumb">
                        EVENPROD / SÉRIES / DÉPENSES
                    </div>
                    <h1>Gestion des dépenses</h1>
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
                    <i class="fas fa-arrow-down" style="color:var(--danger);"></i>
                    <?= count($depenses) ?> dépense<?= count($depenses) > 1 ? 's' : '' ?>
                </span>
                <a href="add_depense.php?id=<?= htmlspecialchars($serie['id'] ?? 0) ?>" class="btn-add">
                    <i class="fas fa-plus"></i>
                    Nouvelle dépense
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
            <div>Dépense supprimée avec succès !</div>
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
            <div>Dépense supprimée avec succès !</div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- =========================================================
        STATS OVERVIEW
        ========================================================= -->

        <?php
        $budget = $serie['budget'] ?? 0;
        $taux = $budget > 0 ? ($total / $budget) * 100 : 0;
        $reste = $budget - $total;
        ?>

        <div class="stats-overview">
            <div class="stat-overview-card">
                <div class="label"><i class="fas fa-coins"></i> Budget total</div>
                <div class="value accent"><?= number_format($budget, 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="stat-overview-card">
                <div class="label"><i class="fas fa-arrow-down" style="color:var(--danger);"></i> Dépenses totales</div>
                <div class="value" style="color:var(--danger);"><?= number_format($total, 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="stat-overview-card">
                <div class="label"><i class="fas fa-arrow-up" style="color:var(--success);"></i> Budget restant</div>
                <div class="value success"><?= number_format($reste, 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="stat-overview-card">
                <div class="label"><i class="fas fa-chart-pie"></i> Taux d'utilisation</div>
                <div class="value warning"><?= number_format($taux, 1, ',', ' ') ?>%</div>
                <div class="progress-bar-custom">
                    <div class="fill" style="width: <?= min($taux, 100) ?>%;"></div>
                </div>
            </div>
        </div>

        <!-- =========================================================
        TABLE DES DÉPENSES
        ========================================================= -->

        <div class="table-card">
            <div class="table-card-header">
                <h2>
                    <i class="fas fa-list"></i>
                    Liste des dépenses
                </h2>
                <span><?= count($depenses) ?> enregistrée<?= count($depenses) > 1 ? 's' : '' ?></span>
            </div>

            <div class="table-responsive">
                <?php if (!empty($depenses)): ?>
                <table class="depenses-table" id="depenseTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Libellé</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Tournage</th>
                            <th>Justificatif</th>
                            <th style="text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($depenses as $row): ?>
                        <tr>
                            <td data-label="#">
                                <span style="font-weight:700; color:var(--muted);">#<?= $i ?></span>
                            </td>
                            <td data-label="Libellé">
                                <?= !empty($row['libelle']) ? htmlspecialchars($row['libelle']) : '<em style="color:var(--muted);">Aucun</em>' ?>
                            </td>
                            <td data-label="Type">
                                <?php
                                $type = strtolower($row['type_depense'] ?? '');

                                switch ($type) {
                                    case 'cachet':
                                        $typeClass = 'cachet';
                                        break;

                                    case 'decor':
                                    case 'decors':
                                        $typeClass = 'decor';
                                        break;

                                    case 'transport':
                                        $typeClass = 'transport';
                                        break;

                                    case 'reception':
                                        $typeClass = 'reception';
                                        break;

                                    case 'accessoire':
                                    case 'accessoires':
                                        $typeClass = 'accessoire';
                                        break;

                                    case 'reglement_acteur':
                                    case 'reglement acteur':
                                        $typeClass = 'reglement_acteur';
                                        break;

                                    case 'hmc':
                                        $typeClass = 'hmc';
                                        break;

                                    case 'carburant':
                                        $typeClass = 'carburant';
                                        break;

                                    case 'pharmacie':
                                        $typeClass = 'pharmacie';
                                        break;

                                    default:
                                        $typeClass = 'autre';
                                        break;
                                }

                                $label = ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $row['type_depense'] ?? 'Autre'
                                    )
                                );
                                $label = ucfirst(str_replace('_', ' ', $row['type_depense'] ?? 'Autre'));
                                ?>
                                <span class="badge-type <?= $typeClass ?>">
                                    <i class="fas fa-tag"></i>
                                    <?= htmlspecialchars($label) ?>
                                </span>
                            </td>
                            <td data-label="Date">
                                <i class="fas fa-calendar-alt"
                                    style="color:var(--muted); font-size:11px; margin-right:4px;"></i>
                                <?= date('d/m/Y', strtotime($row['date_depense'])) ?>
                            </td>
                            <td data-label="Montant">
                                <span style="font-weight:700; color:var(--danger);">
                                    <?= number_format($row['montant'], 0, ',', ' ') ?> FCFA
                                </span>
                            </td>
                            <td data-label="Tournage">
                                <?php if (!empty($row['tournage_reference'])): ?>
                                <span style="font-weight:600; font-size:11px;">
                                    <i class="fas fa-video" style="color:var(--muted);"></i>
                                    <?= htmlspecialchars($row['tournage_reference']) ?>
                                </span>
                                <?php else: ?>
                                <em style="color:var(--muted);">Aucun</em>
                                <?php endif; ?>
                            </td>
                            <td data-label="Justificatif">
                                <?php if (!empty($row['justificatif'])): ?>
                                <a class="badge-justif"
                                    href="<?= $url_base ?>uploads/justificatifs/<?= htmlspecialchars($row['justificatif']) ?>"
                                    target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                    Voir
                                </a>
                                <?php else: ?>
                                <span style="color:var(--muted);">-</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Action" style="text-align:center;">
                                <div class="action-group">
                                    <a href="<?= $url_base ?>public/appManager/delete?table=depenses&id=<?= htmlspecialchars($row['id']) ?>&redirect=<?= $url_base ?>public/appManager/depenses/liste_all?id=<?= $serieId ?>"
                                        class="btn-action delete"
                                        onclick="return confirm('Voulez-vous vraiment supprimer cette dépense ?')"
                                        title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php $i++;
                        endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-arrow-down" style="color:var(--danger);"></i>
                    <h3>Aucune dépense</h3>
                    <p>Aucune dépense n'est encore enregistrée pour cette série.</p>
                    <a href="add_depense.php?id=<?= htmlspecialchars($serie['id'] ?? 0) ?>" class="btn-add"
                        style="display:inline-flex;">
                        <i class="fas fa-plus"></i>
                        Ajouter la première dépense
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- =========================================================
        CATÉGORIES DE DÉPENSES
        ========================================================= -->

        <div class="categories-section">
            <div class="categories-header">
                <h3>
                    <i class="fas fa-chart-pie"></i>
                    Répartition par catégorie
                </h3>
                <span style="font-size:12px; color:var(--muted); font-weight:700;">
                    <i class="fas fa-coins"></i>
                    Total : <?= number_format($total, 0, ',', ' ') ?> FCFA
                </span>
            </div>
            <div class="categories-grid">
                <?php
                $categories = [
                    ['key' => 'cachet', 'icon' => 'fa-hand-holding-usd', 'label' => 'Cachets'],
                    ['key' => 'decor', 'icon' => 'fa-paint-roller', 'label' => 'Décors'],
                    ['key' => 'transport', 'icon' => 'fa-truck', 'label' => 'Transport'],
                    ['key' => 'reception', 'icon' => 'fa-utensils', 'label' => 'Réceptions'],
                    ['key' => 'accessoire', 'icon' => 'fa-tools', 'label' => 'Accessoires'],
                    ['key' => 'reglement_acteur', 'icon' => 'fa-user-check', 'label' => 'Règlement acteurs'],
                    ['key' => 'hmc', 'icon' => 'fa-building', 'label' => 'HMC'],
                    ['key' => 'carburant', 'icon' => 'fa-gas-pump', 'label' => 'Carburant'],
                    ['key' => 'pharmacie', 'icon' => 'fa-medkit', 'label' => 'Pharmacie'],
                    ['key' => 'autre', 'icon' => 'fa-ellipsis-h', 'label' => 'Autres'],
                ];
                ?>
                <?php foreach ($categories as $cat): ?>
                <div class="category-item">
                    <span class="icon"><i class="fas <?= $cat['icon'] ?>"></i></span>
                    <span class="label"><?= $cat['label'] ?></span>
                    <span class="value">
                        <?= number_format($totaux[$cat['key']] ?? 0, 0, ',', ' ') ?>
                        <small>FCFA</small>
                    </span>
                </div>
                <?php endforeach; ?>
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
    if ($('#depenseTable tbody tr').length > 0) {
        $('#depenseTable').DataTable({
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
</script>

<?php include '../../../includes/footer.php'; ?>