<?php
include '../../config/fonction.php';

/*
 * |--------------------------------------------------------------------------
 * | CONFIGURATION DE LA PAGINATION
 * |--------------------------------------------------------------------------
 */

$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

/*
 * |--------------------------------------------------------------------------
 * | FILTRES ET RECHERCHE
 * |--------------------------------------------------------------------------
 */

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$statutFilter = isset($_GET['statut']) ? trim($_GET['statut']) : '';
$contratFilter = isset($_GET['contrat']) ? trim($_GET['contrat']) : '';
$fonctionFilter = isset($_GET['fonction']) ? trim($_GET['fonction']) : '';

/*
 * |--------------------------------------------------------------------------
 * | RÉCUPÉRATION DES DONNÉES
 * |--------------------------------------------------------------------------
 */

$resultats = getSalariesFiltres($search, $statutFilter, $contratFilter, $fonctionFilter, $limit, $offset);
$totalSalaries = $resultats['total'];
$salaries = $resultats['data'];
$totalPages = ceil($totalSalaries / $limit);

// Récupération des fonctions uniques pour le filtre
$fonctions = getSalariesFonctions();

// Dernière série pour la sidebar
$lastSerie = getLastSerie();
$stats = getSalariesStats();

?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <!-- Buttons CSS pour l'export -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
</head>

<?php include '../../includes/header.php'; ?>

<style>
/* =========================================================
   VARIABLES
========================================================= */

:root {
    --primary: #171717;
    --primary-hover: #000;
    --accent: #e50914;
    --background: #f5f6f8;
    --white: #ffffff;
    --text: #171717;
    --muted: #737373;
    --border: #e5e7eb;
    --success: #16a34a;
    --danger: #dc2626;
    --warning: #f59e0b;
    --radius: 14px;
    --shadow: 0 8px 25px rgba(0, 0, 0, .05);
    --transition: all .25s cubic-bezier(.4, 0, .2, 1);
}

/* =========================================================
   PAGE
========================================================= */

.salaries-page {
    min-height: 100vh;
    background: var(--background);
    padding: 20px 0 40px;
    color: var(--text);
    font-size: 12px;
}

.salaries-container {
    max-width: 100%;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.salaries-header {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 25px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    box-shadow: var(--shadow);
    flex-wrap: wrap;
}

.salaries-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

.salaries-header-icon {
    width: 44px;
    height: 44px;
    flex: 0 0 44px;
    border-radius: 12px;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.salaries-breadcrumb {
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 3px;
}

.salaries-header h1 {
    margin: 0;
    font-size: 20px;
    font-weight: 900;
    letter-spacing: -.3px;
}

.salaries-header p {
    margin: 3px 0 0;
    color: var(--muted);
    font-size: 12px;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.header-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    background: #f4f4f5;
    font-size: 10px;
    font-weight: 800;
}

/* =========================================================
   STATS
========================================================= */

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.stat-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 12px 16px;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 12px;
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, .08);
}

.stat-icon {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.stat-icon.total {
    background: #e0e7ff;
    color: #3730a3;
}

.stat-icon.actif {
    background: #d1fae5;
    color: #065f46;
}

.stat-icon.inactif {
    background: #fef2f2;
    color: #991b1b;
}

.stat-icon.conge {
    background: #fef3c7;
    color: #92400e;
}

.stat-icon.salaire {
    background: #dbeafe;
    color: #1e40af;
}

.stat-content {
    flex: 1;
    min-width: 0;
}

.stat-content .value {
    font-size: 18px;
    font-weight: 900;
    letter-spacing: -.3px;
    line-height: 1.2;
}

.stat-content .label {
    font-size: 9px;
    color: var(--muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .4px;
}

/* =========================================================
   FILTRES
========================================================= */

.filters-bar {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 12px 18px;
    margin-bottom: 18px;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.filters-bar .search-wrapper {
    flex: 1;
    min-width: 180px;
    position: relative;
}

.filters-bar .search-wrapper i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #a1a1aa;
    font-size: 12px;
}

.filters-bar .search-wrapper input {
    width: 100%;
    height: 36px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: #fafafa;
    padding: 0 12px 0 36px;
    font-size: 12px;
    outline: none;
    transition: var(--transition);
}

.filters-bar .search-wrapper input:focus {
    background: white;
    border-color: #a1a1aa;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, .04);
}

.filters-bar select {
    height: 36px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: #fafafa;
    padding: 0 30px 0 12px;
    font-size: 11px;
    outline: none;
    appearance: none;
    cursor: pointer;
    transition: var(--transition);
    min-width: 110px;
}

.filters-bar select:focus {
    background: white;
    border-color: #a1a1aa;
}

.filter-select-wrapper {
    position: relative;
}

.filter-select-wrapper::after {
    content: '\f078';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #a1a1aa;
    font-size: 10px;
    pointer-events: none;
}

.filters-bar .btn-reset {
    height: 36px;
    padding: 0 14px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: white;
    color: var(--muted);
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.filters-bar .btn-reset:hover {
    background: #f4f4f5;
    color: var(--text);
}

/* =========================================================
   BOUTONS
========================================================= */

.btn {
    height: 36px;
    padding: 0 16px;
    border-radius: 10px;
    border: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition);
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
}

.btn-success {
    background: var(--success);
    color: white;
}

.btn-success:hover {
    background: #15803d;
}

.btn-danger {
    background: var(--danger);
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
}

.btn-outline {
    background: transparent;
    color: var(--text);
    border: 1px solid var(--border);
}

.btn-outline:hover {
    background: #f4f4f5;
}

.btn-sm {
    height: 32px;
    padding: 0 14px;
    font-size: 11px;
}

/* =========================================================
   TABLE
========================================================= */

.table-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.table-toolbar {
    padding: 12px 18px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

.table-toolbar .info {
    font-size: 11px;
    color: var(--muted);
}

.table-toolbar .info strong {
    color: var(--text);
}

.table-toolbar .export-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.table-wrapper {
    overflow-x: auto;
    padding: 0;
}

/* =========================================================
   DATATABLES OVERRIDE
========================================================= */

.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    display: none !important;
}

.dataTables_wrapper .dataTable {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 11px !important;
}

.dataTables_wrapper .dataTable thead th {
    padding: 10px 12px !important;
    text-align: left !important;
    font-size: 9px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: .6px !important;
    color: var(--muted) !important;
    white-space: nowrap !important;
    background: #fafafa !important;
    border-bottom: 1px solid var(--border) !important;
}

.dataTables_wrapper .dataTable thead th:first-child {
    padding-left: 18px !important;
}

.dataTables_wrapper .dataTable thead th:last-child {
    padding-right: 18px !important;
}

.dataTables_wrapper .dataTable tbody tr {
    border-bottom: 1px solid var(--border) !important;
    transition: var(--transition) !important;
}

.dataTables_wrapper .dataTable tbody tr:hover {
    background: #fafafa !important;
}

.dataTables_wrapper .dataTable tbody tr:last-child {
    border-bottom: 0 !important;
}

.dataTables_wrapper .dataTable tbody td {
    padding: 10px 12px !important;
    vertical-align: middle !important;
    font-size: 11px !important;
}

.dataTables_wrapper .dataTable tbody td:first-child {
    padding-left: 18px !important;
}

.dataTables_wrapper .dataTable tbody td:last-child {
    padding-right: 18px !important;
}

.dataTables_wrapper .dataTable .col-photo {
    width: 36px !important;
}

.dataTables_wrapper .dataTable .col-photo img {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid var(--border);
    background: #f4f4f5;
}

.dataTables_wrapper .dataTable .col-photo .no-photo {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #f4f4f5;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 12px;
}

/* =========================================================
   BADGES
========================================================= */

.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 16px;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .2px;
}

.badge-statut.actif {
    background: #d1fae5;
    color: #065f46;
}

.badge-statut.inactif {
    background: #fef2f2;
    color: #991b1b;
}

.badge-statut.en_conge {
    background: #fef3c7;
    color: #92400e;
}

.badge-contrat.cdi {
    background: #dbeafe;
    color: #1e40af;
}

.badge-contrat.cdd {
    background: #fce4ec;
    color: #9a1f3c;
}

.badge-contrat.stage {
    background: #e0e7ff;
    color: #3730a3;
}

/* =========================================================
   ACTIONS TABLE - BOUTONS VISIBLES
========================================================= */

.col-actions {
    text-align: center !important;
    white-space: nowrap;
    min-width: 140px;
}

.col-actions .action-group {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.col-actions .action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 5px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
    transition: var(--transition);
    border: 1px solid transparent;
    min-width: 60px;
}

.col-actions .action-btn i {
    font-size: 12px;
}

.col-actions .action-btn.edit {
    background: #e0e7ff;
    color: #3730a3;
    border-color: #c7d2fe;
}

.col-actions .action-btn.edit:hover {
    background: #c7d2fe;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(55, 48, 163, 0.15);
}

.col-actions .action-btn.contrat {
    background: #fce4ec;
    color: #9a1f3c;
    border-color: #f8c4d0;
}

.col-actions .action-btn.contrat:hover {
    background: #f8c4d0;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(154, 31, 60, 0.15);
}

.col-actions .action-btn.delete {
    background: #fef2f2;
    color: #991b1b;
    border-color: #fecaca;
}

.col-actions .action-btn.delete:hover {
    background: #fecaca;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(153, 27, 27, 0.15);
}

.col-actions .action-btn .btn-label {
    display: inline;
}

@media (max-width: 768px) {
    .col-actions .action-btn .btn-label {
        display: none;
    }
    .col-actions .action-btn {
        min-width: 32px;
        padding: 5px 8px;
    }
    .col-actions {
        min-width: 80px;
    }
}

/* =========================================================
   SIDEBAR
========================================================= */

.sidebar {
    position: sticky;
    top: 20px;
}

.side-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 16px;
    overflow: hidden;
}

.side-header {
    padding: 14px 16px;
    border-bottom: 1px solid var(--border);
}

.side-header-title {
    display: flex;
    align-items: center;
    gap: 8px;
}

.side-header-icon {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: #f4f4f5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.side-header h3 {
    margin: 0;
    font-size: 12px;
    font-weight: 900;
}

.side-header p {
    margin: 2px 0 0;
    font-size: 9px;
    color: #999;
}

.series-body {
    padding: 16px;
}

.series-poster {
    width: 100%;
    height: 120px;
    border-radius: 10px;
    background: #f4f4f5;
    overflow: hidden;
    margin-bottom: 12px;
}

.series-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.series-poster-placeholder {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 28px;
}

.series-title {
    margin: 0 0 4px;
    font-size: 14px;
    font-weight: 900;
}

.series-type {
    display: inline-flex;
    padding: 3px 8px;
    border-radius: 16px;
    background: #f4f4f5;
    font-size: 8px;
    font-weight: 800;
}

.series-budget {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid var(--border);
}

.series-budget-label {
    display: block;
    color: #999;
    font-size: 9px;
}

.series-budget-value {
    display: block;
    margin-top: 2px;
    font-size: 16px;
    font-weight: 900;
}

.series-budget-value small {
    color: #999;
    font-size: 8px;
}

.tip-card {
    background: #171717;
    color: white;
    padding: 16px;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.tip-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.tip-header i {
    color: #fbbf24;
}

.tip-header strong {
    font-size: 11px;
}

.tip-card p {
    margin: 0;
    color: #bdbdbd;
    font-size: 10px;
    line-height: 1.6;
}

/* =========================================================
   LAYOUT
========================================================= */

.salaries-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 280px;
    gap: 20px;
    align-items: start;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1200px) {
    .salaries-layout {
        grid-template-columns: 1fr;
    }

    .sidebar {
        position: static;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .sidebar>* {
        margin-bottom: 0;
    }
}

@media (max-width: 768px) {
    .salaries-page {
        padding: 12px 0 30px;
    }

    .salaries-container {
        padding: 0 12px;
    }

    .salaries-header {
        padding: 14px;
        flex-direction: column;
        align-items: flex-start;
    }

    .salaries-header h1 {
        font-size: 18px;
    }

    .salaries-header-icon {
        width: 38px;
        height: 38px;
        flex-basis: 38px;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .filters-bar {
        flex-direction: column;
        align-items: stretch;
        padding: 12px;
    }

    .filters-bar .search-wrapper {
        min-width: unset;
    }

    .filters-bar select {
        width: 100%;
    }

    .table-toolbar {
        flex-direction: column;
        align-items: stretch;
        padding: 10px 14px;
    }

    .sidebar {
        grid-template-columns: 1fr 1fr;
    }

    .dataTables_wrapper .dataTable tbody td {
        padding: 8px 10px !important;
        font-size: 10px !important;
    }

    .dataTables_wrapper .dataTable thead th {
        padding: 8px 10px !important;
        font-size: 8px !important;
    }
}

@media (max-width: 480px) {
    .salaries-header-left {
        gap: 10px;
    }

    .salaries-header-icon {
        display: none;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .sidebar {
        grid-template-columns: 1fr;
    }
}

/* =========================================================
   STYLES BOUTONS DATATABLE
========================================================= */

.dt-buttons {
    display: flex !important;
    gap: 8px !important;
}

.dt-buttons .dt-button {
    background: var(--success) !important;
    color: white !important;
    border: 0 !important;
    border-radius: 10px !important;
    padding: 6px 16px !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    transition: var(--transition) !important;
    font-family: inherit !important;
}

.dt-buttons .dt-button:hover {
    background: #15803d !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 8px 20px rgba(22, 163, 74, 0.3) !important;
}

.dt-buttons .dt-button i {
    font-size: 13px !important;
}
</style>

<section class="salaries-page">
    <div class="salaries-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="salaries-header">
            <div class="salaries-header-left">
                <div class="salaries-header-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="salaries-breadcrumb">
                        EVENPROD / RH / SALARIÉS
                    </div>
                    <h1>Gestion des salariés</h1>
                    <p>
                        <?= number_format($totalSalaries) ?> salarié<?= $totalSalaries > 1 ? 's' : '' ?>
                        enregistré<?= $totalSalaries > 1 ? 's' : '' ?>
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <div class="header-status">
                    <i class="fas fa-calendar-alt"></i>
                    <?= date('d/m/Y') ?>
                </div>
                <a href="add_salarie" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Nouveau
                </a>
            </div>
        </header>

        <!-- =========================================================
        STATISTIQUES
        ========================================================= -->

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="value"><?= number_format($stats['total'] ?? 0) ?></div>
                    <div class="label">Total salariés</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon actif">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <div class="value"><?= number_format($stats['actif'] ?? 0) ?></div>
                    <div class="label">Actifs</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon conge">
                    <i class="fas fa-umbrella-beach"></i>
                </div>
                <div class="stat-content">
                    <div class="value"><?= number_format($stats['en_conge'] ?? 0) ?></div>
                    <div class="label">En congé</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon inactif">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div class="stat-content">
                    <div class="value"><?= number_format($stats['inactif'] ?? 0) ?></div>
                    <div class="label">Inactifs</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon salaire">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-content">
                    <div class="value" style="font-size:15px;">
                        <?= number_format($stats['masse_salariale'] ?? 0, 0, ',', ' ') ?></div>
                    <div style="font-size:8px; color:var(--muted);">Masse salariale (FCFA)</div>
                </div>
            </div>
        </div>

        <!-- =========================================================
            TABLEAU
            ========================================================= -->

        <div style="width:100%;">
            <div class="table-card">

                <!-- TOOLBAR -->
                <div class="table-toolbar">
                    <div class="info">
                        <strong><?= number_format($totalSalaries) ?></strong>
                        salarié<?= $totalSalaries > 1 ? 's' : '' ?>
                        <?php if ($search || $statutFilter || $contratFilter || $fonctionFilter): ?>
                        <span style="color:var(--muted);">· Filtrés</span>
                        <?php endif; ?>
                    </div>
                    <div class="export-actions">
                        <!-- Les boutons d'export DataTables seront ici -->
                    </div>
                </div>

                <!-- FILTRES -->
                <div class="filters-bar">
                    <div class="search-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Rechercher un salarié..."
                            value="<?= htmlspecialchars($search) ?>">
                    </div>

                    <div class="filter-select-wrapper">
                        <select id="statutFilter">
                            <option value="">Tous statuts</option>
                            <option value="actif" <?= $statutFilter === 'actif' ? 'selected' : '' ?>>Actif</option>
                            <option value="inactif" <?= $statutFilter === 'inactif' ? 'selected' : '' ?>>Inactif
                            </option>
                            <option value="en_conge" <?= $statutFilter === 'en_conge' ? 'selected' : '' ?>>En congé
                            </option>
                        </select>
                    </div>

                    <div class="filter-select-wrapper">
                        <select id="contratFilter">
                            <option value="">Tous contrats</option>
                            <option value="cdi" <?= $contratFilter === 'cdi' ? 'selected' : '' ?>>CDI</option>
                            <option value="cdd" <?= $contratFilter === 'cdd' ? 'selected' : '' ?>>CDD</option>
                            <option value="stage" <?= $contratFilter === 'stage' ? 'selected' : '' ?>>Stage</option>
                        </select>
                    </div>

                    <div class="filter-select-wrapper">
                        <select id="fonctionFilter">
                            <option value="">Toutes fonctions</option>
                            <?php foreach ($fonctions as $f): ?>
                            <option value="<?= htmlspecialchars($f['fonction']) ?>"
                                <?= $fonctionFilter === $f['fonction'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['fonction']) ?> (<?= $f['count'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="btn-reset" id="resetFilters">
                        <i class="fas fa-times"></i> Reset
                    </button>
                </div>

                <!-- TABLEAU -->
                <div class="table-wrapper">
                    <table id="salariesTable" class="display responsive" style="width:100%;">
                        <thead>
                            <tr>
                                <th class="col-photo">Photo</th>
                                <th>Nom & Prénom</th>
                                <th>Fonction</th>
                                <th>Email</th>
                                <th>Date naissance</th>
                                <th>Téléphone</th>
                                <th>Contrat</th>
                                <th>Statut</th>
                                <th>Salaire</th>
                                <th class="col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salaries as $salarie): ?>
                            <tr>
                                <td class="col-photo">
                                    <?php if (!empty($salarie['photo'])): ?>
                                    <img src="uploads/salaries/<?= htmlspecialchars($salarie['photo']) ?>"
                                        alt="<?= htmlspecialchars($salarie['prenom']) ?>">
                                    <?php else: ?>
                                    <div class="no-photo">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="font-size:11px;"><?= htmlspecialchars($salarie['prenom'] . ' ' . $salarie['nom']) ?></strong>
                                    <div style="font-size:9px; color:var(--muted);">
                                        Embauche : <?= date('d/m/Y', strtotime($salarie['date_embauche'])) ?>
                                    </div>
                                </td>
                                <td style="font-size:11px;"><?= htmlspecialchars($salarie['fonction']) ?></td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($salarie['email']) ?>"
                                        style="color:var(--text); text-decoration:none; font-size:11px;">
                                        <?= htmlspecialchars($salarie['email']) ?>
                                    </a>
                                </td>
                                <td><?= date('d/m/Y', strtotime($salarie['date_naissance'] ?? '2000-01-01')) ?></td>
                                <td style="font-size:11px;"><?= htmlspecialchars($salarie['telephone']) ?></td>
                                <td>
                                    <span class="badge badge-contrat <?= htmlspecialchars($salarie['type_contrat']) ?>">
                                        <?= strtoupper(htmlspecialchars($salarie['type_contrat'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-statut <?= htmlspecialchars($salarie['statut']) ?>">
                                        <?php
                                            $statutLabels = [
                                                'actif' => 'Actif',
                                                'inactif' => 'Inactif',
                                                'en_conge' => 'En congé'
                                            ];
                                            echo $statutLabels[$salarie['statut']] ?? $salarie['statut'];
                                            ?>
                                    </span>
                                </td>
                                <td>
                                    <strong style="font-size:11px;"><?= number_format((float)$salarie['salaire'], 0, ',', ' ') ?></strong> <i style="font-size:8px; color:var(--muted);">FCFA</i>
                                </td>
                                <td class="col-actions">
                                    <div class="action-group">
                                        <a href="add_salarie?id=<?= $salarie['id'] ?>" class="action-btn edit" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                            <span class="btn-label">Éditer</span>
                                        </a>
                                        <?php if (!empty($salarie['contrat'])): ?>
                                        <a href="uploads/contrats/<?= htmlspecialchars($salarie['contrat']) ?>"
                                            target="_blank" class="action-btn contrat" title="Voir contrat">
                                            <i class="fas fa-file-pdf"></i>
                                            <span class="btn-label">Contrat</span>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- =========================================================
JQUERY + DATATABLES + BUTTONS
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

    // =========================================================
    // INIT DATATABLE AVEC EXPORT EXCEL
    // =========================================================

    const table = $('#salariesTable').DataTable({
        responsive: true,
        pageLength: 20,
        lengthMenu: [
            [10, 20, 50, 100, -1],
            [10, 20, 50, 100, "Tous"]
        ],
        order: [
            [1, 'asc']
        ],
        columnDefs: [
            { orderable: false, targets: [0, 9] },
            { className: 'col-photo', targets: [0] },
            { className: 'col-actions', targets: [9] }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exporter Excel',
                className: 'btn-success btn-sm',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7, 8],
                    format: {
                        body: function (data, row, column, node) {
                            // Nettoyer les données pour l'export
                            if (data.includes('FCFA')) {
                                return data.replace(/<[^>]*>/g, '').trim();
                            }
                            return $('<div>').html(data).text().trim();
                        }
                    }
                },
                title: 'Liste des salariés - ' + new Date().toLocaleDateString('fr-FR'),
                filename: 'salaries_' + new Date().toISOString().split('T')[0]
            }
        ]
    });

    // Déplacer les boutons d'export dans la toolbar
    $('.dt-buttons').appendTo('.export-actions');

    // =========================================================
    // FILTRES AVEC DATATABLE
    // =========================================================

    let searchTimeout;

    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        const value = $(this).val();
        searchTimeout = setTimeout(function() {
            table.search(value).draw();
        }, 300);
    });

    $('#statutFilter, #contratFilter, #fonctionFilter').on('change', function() {
        const statut = $('#statutFilter').val();
        const contrat = $('#contratFilter').val();
        const fonction = $('#fonctionFilter').val();

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const rowStatut = data[7] || '';
            const rowContrat = data[6] || '';
            const rowFonction = data[2] || '';

            let match = true;
            if (statut && !rowStatut.includes(getStatutLabel(statut))) match = false;
            if (contrat && !rowContrat.includes(contrat.toUpperCase())) match = false;
            if (fonction && !rowFonction.includes(fonction)) match = false;
            return match;
        });

        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    function getStatutLabel(value) {
        const labels = {
            'actif': 'Actif',
            'inactif': 'Inactif',
            'en_conge': 'En congé'
        };
        return labels[value] || value;
    }

    // =========================================================
    // RESET FILTRES
    // =========================================================

    $('#resetFilters').on('click', function() {
        $('#searchInput').val('');
        $('#statutFilter').val('');
        $('#contratFilter').val('');
        $('#fonctionFilter').val('');
        table.search('').columns().search('').draw();
    });

});
</script>

<!-- =========================================================
ALERT STYLES (complément)
========================================================= -->

<style>
.modern-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    margin-bottom: 16px;
    border-radius: 10px;
    font-size: 11px;
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
</style>

<?php include '../../includes/footer.php'; ?>