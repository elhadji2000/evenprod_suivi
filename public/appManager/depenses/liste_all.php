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

// Récupérer la liste des tournages pour le filtre
$tournages = getTournagesBySerieId($serieId);

?>

<?php include '../../../includes/header.php'; ?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
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

* {
    font-size: 12px !important;
}

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
    font-size: 20px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.depenses-header p {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 12px;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-add {
    height: 40px;
    padding: 0 20px;
    border-radius: 12px;
    border: 0;
    background: var(--accent);
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 8px;
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
    padding: 8px 14px;
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
    padding: 16px 18px;
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
    font-size: 18px;
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
   FILTRES
========================================================= */

.filters-section {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px 20px;
    margin-bottom: 25px;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}

.filters-section .filter-group {
    display: flex;
    align-items: center;
    gap: 6px;
}

.filters-section .filter-group label {
    font-size: 10px;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
}

.filters-section .filter-group input,
.filters-section .filter-group select {
    height: 32px;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0 10px;
    font-size: 12px;
    outline: none;
    transition: .2s;
    background: #fafafa;
}

.filters-section .filter-group input:focus,
.filters-section .filter-group select:focus {
    border-color: var(--accent);
    background: white;
    box-shadow: 0 0 0 3px rgba(229, 9, 20, .1);
}

.filters-section .filter-group input[type="date"] {
    min-width: 130px;
}

.filters-section .filter-group select {
    min-width: 150px;
}

.filters-section .btn-filter-reset {
    height: 32px;
    padding: 0 14px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: white;
    color: var(--muted);
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
    transition: .2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.filters-section .btn-filter-reset:hover {
    background: #f4f4f5;
    color: var(--text);
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
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}

.table-card-header h2 {
    margin: 0;
    font-size: 14px;
    font-weight: 900;
    display: flex;
    align-items: center;
    gap: 8px;
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
    padding: 0 20px 20px;
    overflow-x: auto;
}

/* =========================================================
   DATA TABLE CUSTOM
========================================================= */

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    font-size: 12px !important;
    color: var(--text);
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 12px;
    outline: none;
    transition: .2s;
    height: 32px;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(229, 9, 20, .1);
}

.dataTables_wrapper .dataTables_length select {
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 12px;
    outline: none;
    height: 32px;
}

/* Boutons export DataTables */
.dt-buttons {
    display: flex !important;
    gap: 8px !important;
}

.dt-buttons .dt-button {
    background: var(--success) !important;
    color: white !important;
    border: 0 !important;
    border-radius: 8px !important;
    padding: 6px 14px !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    transition: .2s !important;
    font-family: inherit !important;
    height: 32px !important;
}

.dt-buttons .dt-button:hover {
    background: #15803d !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3) !important;
}

.dt-buttons .dt-button i {
    font-size: 13px !important;
}

/* =========================================================
   TABLEAU - TOUT EN 12px MAX
========================================================= */

.depenses-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px !important;
}

.depenses-table thead {
    background: #fafafa;
    border-bottom: 2px solid var(--border);
}

.depenses-table thead th {
    padding: 10px 12px;
    text-align: left;
    font-size: 11px !important;
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
    padding: 8px 12px;
    vertical-align: middle;
    font-size: 12px !important;
}

.depenses-table tbody td span,
.depenses-table tbody td a,
.depenses-table tbody td em,
.depenses-table tbody td strong {
    font-size: 12px !important;
}

/* =========================================================
   BADGES
========================================================= */

.badge-type {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 16px;
    font-size: 10px !important;
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
    font-size: 11px !important;
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
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
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
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}

.categories-header h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 900;
    display: flex;
    align-items: center;
    gap: 8px;
}

.categories-header h3 i {
    color: var(--accent);
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    padding: 16px 20px;
}

.category-item {
    background: #fafafa;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 10px 12px;
    text-align: center;
    transition: .2s;
}

.category-item:hover {
    border-color: var(--accent);
    transform: translateY(-2px);
}

.category-item .icon {
    font-size: 18px;
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
    font-size: 13px;
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
    padding: 40px 20px;
    color: var(--muted);
}

.empty-state i {
    font-size: 40px;
    color: #d4d4d8;
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 16px;
    font-weight: 900;
    color: var(--text);
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 12px;
    margin-bottom: 20px;
}

/* =========================================================
   ALERT / TOAST
========================================================= */

.modern-alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    margin-bottom: 20px;
    border-radius: 12px;
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
    width: 28px;
    height: 28px;
    flex: 0 0 28px;
    border-radius: 8px;
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
        font-size: 18px;
    }

    .depenses-header-avatar {
        width: 50px;
        height: 50px;
        flex-basis: 50px;
    }

    .stats-overview {
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .stat-overview-card {
        padding: 12px 14px;
    }

    .stat-overview-card .value {
        font-size: 16px;
    }

    .filters-section {
        flex-direction: column;
        align-items: stretch;
        padding: 14px;
    }

    .filters-section .filter-group {
        width: 100%;
        flex-wrap: wrap;
    }

    .filters-section .filter-group input,
    .filters-section .filter-group select {
        flex: 1;
        min-width: unset;
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
        font-size: 12px !important;
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
        FILTRES
        ========================================================= -->

        <div class="filters-section">
            <div class="filter-group">
                <label><i class="fas fa-calendar-start"></i> Date début</label>
                <input type="date" id="dateDebut" value="">
            </div>
            <div class="filter-group">
                <label><i class="fas fa-calendar-end"></i> Date fin</label>
                <input type="date" id="dateFin" value="">
            </div>
            <div class="filter-group">
                <label><i class="fas fa-video"></i> Tournage</label>
                <select id="tournageFilter">
                    <option value="">Tous les tournages</option>
                    <?php foreach ($tournages as $tournage): ?>
                    <option value="<?= htmlspecialchars($tournage['reference']) ?>">
                        <?= htmlspecialchars($tournage['reference']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn-filter-reset" id="resetFilters">
                <i class="fas fa-times"></i> Réinitialiser
            </button>
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
                <span id="compteurDepenses"><?= count($depenses) ?>
                    enregistrée<?= count($depenses) > 1 ? 's' : '' ?></span>
            </div>

            <div class="table-responsive">
                <?php if (!empty($depenses)): ?>
                <table class="depenses-table" id="depenseTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bénéficiaire</th>
                            <th>Téléphone</th>
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
                            <td data-label="Bénéficiaire">
                                <?= !empty($row['beneficiaire']) ? htmlspecialchars($row['beneficiaire']) : '<em style="color:var(--muted);">NULL</em>' ?>
                            </td>
                            <td data-label="Téléphone">
                                <?= !empty($row['telephone_beneficiaire']) ? htmlspecialchars($row['telephone_beneficiaire']) : '<em style="color:var(--muted);">NULL</em>' ?>
                            </td>
                            <td data-label="Libellé">
                                <?= !empty($row['libelle']) ? htmlspecialchars($row['libelle']) : '<em style="color:var(--muted);">NULL</em>' ?>
                            </td>
                            <td data-label="Type">
                                <?php
                                $type = strtolower($row['type_depense'] ?? '');
                                $typeClass = 'autre';
                                $typeClasses = [
                                    'cachet' => 'cachet',
                                    'decor' => 'decor',
                                    'decors' => 'decor',
                                    'transport' => 'transport',
                                    'reception' => 'reception',
                                    'accessoire' => 'accessoire',
                                    'accessoires' => 'accessoire',
                                    'reglement_acteur' => 'reglement_acteur',
                                    'reglement acteur' => 'reglement_acteur',
                                    'hmc' => 'hmc',
                                    'carburant' => 'carburant',
                                    'pharmacie' => 'pharmacie'
                                ];
                                if (isset($typeClasses[$type])) {
                                    $typeClass = $typeClasses[$type];
                                }
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
                                <span style="font-weight:700; color:var(--danger); font-size:12px;">
                                    <?= number_format($row['montant'], 0, ',', ' ') ?> FCFA
                                </span>
                            </td>
                            <td data-label="Tournage">
                                <?php if (!empty($row['tournage_reference'])): ?>
                                <span style="font-weight:600; font-size:12px;">
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
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    if ($('#depenseTable tbody tr').length > 0) {
        var table = $('#depenseTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "responsive": true,
            "columnDefs": [{
                "orderable": false,
                "targets": [0, 9]
            }],
            "dom": 'Bfrtip',
            "buttons": [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exporter Excel',
                className: 'btn-success',

                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7],

                    format: {
                        body: function(data, row, column, node) {

                            var clean = $('<div>').html(data).text().trim();

                            // Supprimer FCFA
                            clean = clean.replace(/FCFA/gi, '').trim();

                            // Nettoyer les espaces insécables
                            clean = clean.replace(/\u00A0/g, ' ');

                            // Montant
                            if (column === 6) {
                                clean = clean.replace(/\s/g, '');
                                clean = clean.replace(/[^\d.,-]/g, '');
                            }

                            return clean;
                        }
                    }
                },

                customizeData: function(data) {

                    // =====================================================
                    // EN-TÊTES EXACTEMENT COMME DEMANDÉS PAR LA BANQUE
                    // =====================================================

                    data.header = [
                        'Customer Name',
                        'Telephone Number',
                        'Amount',
                        'Reason for Payment',
                        'National ID',
                        'Reference'
                    ];

                    // =====================================================
                    // RECONSTRUIRE LES LIGNES
                    // =====================================================

                    data.body = data.body.map(function(row) {

                        return [
                            row[0], // Customer Name
                            row[1], // Telephone Number
                            row[5], // Amount
                            row[3], // Reason for Payment
                            '', // National ID
                            '' // Reference
                        ];

                    });
                },

                title: null,

                filename: 'depenses_paie_<?= htmlspecialchars($serie['id'] ?? '') ?>_<?= date('Y-m-d') ?>'
            }]
        });

        // Déplacer les boutons d'export dans l'en-tête
        $('.dt-buttons').appendTo('.table-card-header');

        // =========================================================
        // FILTRES PERSONNALISÉS
        // =========================================================

        // Filtre par date
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var dateDebut = $('#dateDebut').val();
            var dateFin = $('#dateFin').val();
            var dateCell = data[5];

            var dateMatch = dateCell.match(/(\d{2})\/(\d{2})\/(\d{4})/);
            if (!dateMatch) return true;

            var day = dateMatch[1];
            var month = dateMatch[2];
            var year = dateMatch[3];
            var dateObj = year + '-' + month + '-' + day;

            if (dateDebut && dateObj < dateDebut) return false;
            if (dateFin && dateObj > dateFin) return false;
            return true;
        });

        // Filtre par tournage
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var tournage = $('#tournageFilter').val();
            if (!tournage) return true;

            var tournageCell = data[7];
            return tournageCell.includes(tournage);
        });

        // Appliquer les filtres
        function applyFilters() {
            table.draw();
            var count = table.rows({
                filter: 'applied'
            }).count();
            $('#compteurDepenses').text(count + ' enregistrée' + (count > 1 ? 's' : ''));
        }

        $('#dateDebut, #dateFin, #tournageFilter').on('change', applyFilters);

        // Reset des filtres
        $('#resetFilters').on('click', function() {
            $('#dateDebut').val('');
            $('#dateFin').val('');
            $('#tournageFilter').val('');
            applyFilters();
        });

    }
});
</script>

<?php include '../../../includes/footer.php'; ?>