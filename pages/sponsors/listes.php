<?php
include '../../config/fonction.php';

$clients = getClients($connexion);
?>

<?php include '../../includes/header.php'; ?>

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

.partenaires-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.partenaires-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.partenaires-header {
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

.partenaires-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.partenaires-header-icon {
    width: 58px;
    height: 58px;
    flex: 0 0 58px;
    border-radius: 16px;
    background: var(--accent);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 23px;
}

.partenaires-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.partenaires-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.partenaires-header p {
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

.partenaires-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.partenaires-table thead {
    background: #fafafa;
    border-bottom: 2px solid var(--border);
}

.partenaires-table thead th {
    padding: 14px 16px;
    text-align: left;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--muted);
}

.partenaires-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}

.partenaires-table tbody tr:hover {
    background: #fafafa;
}

.partenaires-table tbody tr:last-child {
    border-bottom: 0;
}

.partenaires-table tbody td {
    padding: 12px 16px;
    vertical-align: middle;
}

/* =========================================================
   LOGO
========================================================= */

.logo-cell {
    text-align: center;
}

.logo-img {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    object-fit: cover;
    border: 2px solid var(--border);
    background: #fafafa;
    transition: .2s;
}

.logo-img:hover {
    border-color: var(--accent);
    transform: scale(1.05);
}

.logo-placeholder-cell {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: #f4f4f5;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 20px;
    border: 2px solid var(--border);
}

/* =========================================================
   NOM CLIENT
========================================================= */

.client-name {
    font-weight: 700;
    color: var(--text);
    text-decoration: none;
    transition: .2s;
}

.client-name:hover {
    color: var(--accent);
}

.client-name i {
    color: var(--muted);
    font-size: 11px;
    margin-right: 4px;
}

/* =========================================================
   BADGE DATE
========================================================= */

.badge-date {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 12px;
    background: #f4f4f5;
    font-size: 10px;
    font-weight: 700;
    color: var(--muted);
}

.badge-date i {
    font-size: 9px;
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
    .partenaires-header {
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
    .partenaires-page {
        padding: 15px 0 35px;
    }
    .partenaires-container {
        padding: 0 12px;
    }
    .partenaires-header {
        padding: 18px;
    }
    .partenaires-header h1 {
        font-size: 21px;
    }
    .partenaires-header-icon {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
    }
    .table-responsive {
        padding: 0 12px 12px;
    }
    .partenaires-table thead {
        display: none;
    }
    .partenaires-table tbody tr {
        display: block;
        padding: 12px 0;
        border-bottom: 2px solid var(--border);
    }
    .partenaires-table tbody tr:last-child {
        border-bottom: 0;
    }
    .partenaires-table tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 0;
        flex-wrap: wrap;
    }
    .partenaires-table tbody td::before {
        content: attr(data-label);
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--muted);
        letter-spacing: 0.3px;
    }
    .partenaires-table tbody td:first-child::before {
        display: none;
    }
    .partenaires-table tbody td:first-child {
        justify-content: center;
        padding-bottom: 8px;
    }
    .logo-cell {
        width: 100%;
    }
    .action-group {
        gap: 4px;
    }
}

@media (max-width: 480px) {
    .partenaires-header-left {
        gap: 12px;
    }
    .partenaires-header-icon {
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
}
</style>

<section class="partenaires-page">
    <div class="partenaires-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="partenaires-header">
            <div class="partenaires-header-left">
                <div class="partenaires-header-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <div class="partenaires-breadcrumb">
                        EVENPROD / PARTENARIATS
                    </div>
                    <h1>Gestion des partenaires</h1>
                    <p>Liste des clients, sponsors et partenaires enregistrés</p>
                </div>
            </div>
            <div class="header-actions">
                <span class="header-count">
                    <i class="fas fa-building"></i>
                    <?= count($clients) ?> partenaire<?= count($clients) > 1 ? 's' : '' ?>
                </span>
                <a href="add_spon" class="btn-add">
                    <i class="fas fa-plus"></i>
                    Nouveau partenaire
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
            <div>Partenaire supprimé avec succès !</div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] == 2): ?>
        <div class="modern-alert success">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>Partenaire ajouté avec succès !</div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] == 3): ?>
        <div class="modern-alert success">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>Partenaire modifié avec succès !</div>
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
            <div>Partenaire supprimé avec succès !</div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- =========================================================
        TABLE DES PARTENAIRES
        ========================================================= -->

        <div class="table-card">
            <div class="table-card-header">
                <h2>
                    <i class="fas fa-list"></i>
                    Liste des partenaires
                </h2>
                <span><?= count($clients) ?> enregistré<?= count($clients) > 1 ? 's' : '' ?></span>
            </div>

            <div class="table-responsive">
                <?php if (!empty($clients)): ?>
                <table class="partenaires-table" id="partenairesTable">
                    <thead>
                        <tr>
                            <th style="text-align:center; width:70px;">Logo</th>
                            <th>Nom</th>
                            <th>NINEA</th>
                            <th>Contact</th>
                            <th>Adresse</th>
                            <th>Date</th>
                            <th style="text-align:center; width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $cl): ?>
                        <tr>
                            <td data-label="Logo" class="logo-cell">
                                <?php if (!empty($cl['logo']) && file_exists('../../uploads/logos/' . $cl['logo'])): ?>
                                <img src="../../uploads/logos/<?= htmlspecialchars($cl['logo']) ?>" 
                                     alt="<?= htmlspecialchars($cl['nom']) ?>" 
                                     class="logo-img">
                                <?php else: ?>
                                <span class="logo-placeholder-cell">
                                    <i class="fas fa-building"></i>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Nom">
                                <a href="#" class="client-name">
                                    <i class="fas fa-building"></i>
                                    <?= htmlspecialchars($cl['nom']) ?>
                                </a>
                            </td>
                            <td data-label="NINEA">
                                <span style="font-weight:600; font-size:12px;">
                                    <?= htmlspecialchars($cl['ninea']) ?>
                                </span>
                            </td>
                            <td data-label="Contact">
                                <i class="fas fa-phone" style="color:var(--muted); font-size:11px; margin-right:4px;"></i>
                                <?= htmlspecialchars($cl['contact']) ?>
                            </td>
                            <td data-label="Adresse">
                                <i class="fas fa-map-marker-alt" style="color:var(--muted); font-size:11px; margin-right:4px;"></i>
                                <?= htmlspecialchars($cl['adresse'] ?? 'Non définie') ?>
                            </td>
                            <td data-label="Date">
                                <span class="badge-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?= date('d/m/Y', strtotime($cl['created_at'])) ?>
                                </span>
                            </td>
                            <td data-label="Actions" style="text-align:center;">
                                <div class="action-group">
                                    <a href="add_spon?id=<?= htmlspecialchars($cl['id']) ?>" 
                                       class="btn-action edit" title="Modifier">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="<?= $url_base ?>public/appManager/delete.php?table=clients&id=<?= htmlspecialchars($cl['id']) ?>&redirect=<?= $url_base ?>pages/sponsors/listes" 
                                       class="btn-action delete" 
                                       onclick="return confirm('Voulez-vous vraiment supprimer ce partenaire ?')"
                                       title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-handshake"></i>
                    <h3>Aucun partenaire</h3>
                    <p>Aucun partenaire n'est encore enregistré dans la plateforme.</p>
                    <a href="add_spon" class="btn-add" style="display:inline-flex;">
                        <i class="fas fa-plus"></i>
                        Ajouter le premier partenaire
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
    if ($('#partenairesTable tbody tr').length > 0) {
        $('#partenairesTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50],
            "responsive": true,
            "columnDefs": [
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 6 }
            ]
        });
    }
});
</script>

<?php include '../../includes/footer.php'; ?>