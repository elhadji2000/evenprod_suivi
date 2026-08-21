<?php
include '../../../config/fonction.php';

$id = $_GET['id'] ?? 0;
$serie = getSerieById($id);
$factures = getFacturesWithPaiementsBySerie($connexion, $id);

// Calcul des totaux
$totalMontant = array_sum(array_column($factures, 'total'));
$totalVerser = array_sum(array_column($factures, 'verse'));
$totalReste = array_sum(array_column($factures, 'reste'));
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

.recettes-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.recettes-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.recettes-header {
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

.recettes-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.recettes-header-avatar {
    width: 70px;
    height: 70px;
    flex: 0 0 70px;
    border-radius: 16px;
    overflow: hidden;
    border: 3px solid var(--accent);
    background: #f4f4f5;
}

.recettes-header-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.recettes-header-avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 30px;
}

.recettes-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.recettes-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.recettes-header p {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 14px;
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

.stat-overview-card .value.info {
    color: var(--info);
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

.recettes-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.recettes-table thead {
    background: #fafafa;
    border-bottom: 2px solid var(--border);
}

.recettes-table thead th {
    padding: 14px 16px;
    text-align: left;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--muted);
}

.recettes-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}

.recettes-table tbody tr:hover {
    background: #fafafa;
}

.recettes-table tbody tr:last-child {
    border-bottom: 0;
}

.recettes-table tbody td {
    padding: 12px 16px;
    vertical-align: middle;
}

/* =========================================================
   MONTANTS COLORES
========================================================= */

.montant-total {
    font-weight: 700;
    color: var(--accent);
}

.montant-verse {
    font-weight: 700;
    color: var(--success);
    cursor: pointer;
    text-decoration: underline;
    transition: .2s;
}

.montant-verse:hover {
    color: var(--info);
}

.montant-reste {
    font-weight: 700;
    color: var(--warning);
}

.montant-reste.zero {
    color: var(--success);
}

/* =========================================================
   BADGES
========================================================= */

.badge-pdf {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 20px;
    background: #fef2f2;
    color: var(--danger);
    font-size: 10px;
    font-weight: 700;
    text-decoration: none;
    transition: .2s;
}

.badge-pdf:hover {
    background: #fecaca;
    color: var(--danger-hover);
}

.badge-pdf i {
    font-size: 14px;
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
   RESPONSIVE
========================================================= */

@media (max-width: 1200px) {
    .stats-overview {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 992px) {
    .recettes-header {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 768px) {
    .recettes-page {
        padding: 15px 0 35px;
    }
    .recettes-container {
        padding: 0 12px;
    }
    .recettes-header {
        padding: 18px;
    }
    .recettes-header h1 {
        font-size: 21px;
    }
    .recettes-header-avatar {
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
    .recettes-table thead {
        display: none;
    }
    .recettes-table tbody tr {
        display: block;
        padding: 12px 0;
        border-bottom: 2px solid var(--border);
    }
    .recettes-table tbody tr:last-child {
        border-bottom: 0;
    }
    .recettes-table tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 0;
        flex-wrap: wrap;
    }
    .recettes-table tbody td::before {
        content: attr(data-label);
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--muted);
        letter-spacing: 0.3px;
    }
    .recettes-table tbody td:first-child::before {
        display: none;
    }
    .recettes-table tbody td:first-child {
        justify-content: flex-start;
        font-weight: 700;
    }
}

@media (max-width: 480px) {
    .recettes-header-left {
        gap: 12px;
    }
    .recettes-header-avatar {
        display: none;
    }
    .stats-overview {
        grid-template-columns: 1fr;
    }
}
</style>

<section class="recettes-page">
    <div class="recettes-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="recettes-header">
            <div class="recettes-header-left">
                <div class="recettes-header-avatar">
                    <?php if (!empty($serie['logo']) && file_exists('../../../uploads/series/' . $serie['logo'])): ?>
                    <img src="../../../uploads/series/<?= htmlspecialchars($serie['logo']) ?>" alt="<?= htmlspecialchars($serie['titre']) ?>">
                    <?php else: ?>
                    <div class="recettes-header-avatar-placeholder">
                        <i class="fas fa-film"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="recettes-breadcrumb">
                        EVENPROD / SÉRIES / RECETTES
                    </div>
                    <h1>Détails des recettes</h1>
                    <p>
                        <i class="fas fa-film" style="color:var(--accent);"></i>
                        Série : <strong><?= htmlspecialchars($serie['titre'] ?? 'Série introuvable') ?></strong>
                        &nbsp;&bull;&nbsp;
                        <i class="fas fa-file-invoice"></i>
                        <?= count($factures) ?> facture<?= count($factures) > 1 ? 's' : '' ?>
                    </p>
                </div>
            </div>
            <div class="header-count">
                <i class="fas fa-arrow-up" style="color:var(--success);"></i>
                Total : <?= number_format($totalMontant, 0, ',', ' ') ?> FCFA
            </div>
        </header>

        <!-- =========================================================
        STATS OVERVIEW
        ========================================================= -->

        <div class="stats-overview">
            <div class="stat-overview-card">
                <div class="label"><i class="fas fa-file-invoice"></i> Total factures</div>
                <div class="value accent"><?= number_format($totalMontant, 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="stat-overview-card">
                <div class="label"><i class="fas fa-arrow-down" style="color:var(--success);"></i> Montant versé</div>
                <div class="value success"><?= number_format($totalVerser, 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="stat-overview-card">
                <div class="label"><i class="fas fa-arrow-up" style="color:var(--warning);"></i> Reste à payer</div>
                <div class="value warning"><?= number_format($totalReste, 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="stat-overview-card">
                <div class="label"><i class="fas fa-percent"></i> Taux de recouvrement</div>
                <div class="value info">
                    <?php 
                        $taux = $totalMontant > 0 ? ($totalVerser / $totalMontant) * 100 : 0;
                        echo number_format($taux, 1, ',', ' ') . '%';
                    ?>
                </div>
            </div>
        </div>

        <!-- =========================================================
        TABLE DES FACTURES
        ========================================================= -->

        <div class="table-card">
            <div class="table-card-header">
                <h2>
                    <i class="fas fa-list"></i>
                    Liste des factures
                </h2>
                <span><?= count($factures) ?> facture<?= count($factures) > 1 ? 's' : '' ?></span>
            </div>

            <div class="table-responsive">
                <?php if (!empty($factures)): ?>
                <table class="recettes-table" id="factureTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client</th>
                            <th>Contact</th>
                            <th>Référence</th>
                            <th>Montant total</th>
                            <th>Montant versé</th>
                            <th>Reste à payer</th>
                            <th style="text-align:center;">Facture</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($factures as $f): ?>
                        <tr>
                            <td data-label="#">
                                <span style="font-weight:700; color:var(--muted);">#<?= $i ?></span>
                            </td>
                            <td data-label="Client">
                                <i class="fas fa-building" style="color:var(--muted); font-size:11px; margin-right:4px;"></i>
                                <span style="font-weight:600;"><?= htmlspecialchars($f['nom']) ?></span>
                            </td>
                            <td data-label="Contact">
                                <i class="fas fa-phone" style="color:var(--muted); font-size:11px; margin-right:4px;"></i>
                                <?= htmlspecialchars($f['contact']) ?>
                            </td>
                            <td data-label="Référence">
                                <span style="font-weight:700; font-size:12px;">
                                    <i class="fas fa-hashtag" style="color:var(--accent); font-size:10px;"></i>
                                    <?= htmlspecialchars($f['reference']) ?>
                                </span>
                            </td>
                            <td data-label="Montant total">
                                <span class="montant-total">
                                    <?= number_format($f['total'], 0, ',', ' ') ?> FCFA
                                </span>
                            </td>
                            <td data-label="Montant versé">
                                <a href="versements.php?id_fact=<?= $f['id'] ?>" class="montant-verse">
                                    <?= number_format($f['verse'], 0, ',', ' ') ?> FCFA
                                </a>
                            </td>
                            <td data-label="Reste à payer">
                                <?php if ($f['reste'] <= 0): ?>
                                <span class="montant-reste zero">
                                    <i class="fas fa-check-circle" style="color:var(--success);"></i>
                                    Payé
                                </span>
                                <?php else: ?>
                                <span class="montant-reste">
                                    <?= number_format($f['reste'], 0, ',', ' ') ?> FCFA
                                </span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Facture" style="text-align:center;">
                                <a href="../facture/facture_pdf.php?id=<?= $f['id'] ?>" 
                                   class="badge-pdf" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                    PDF
                                </a>
                            </td>
                        </tr>
                        <?php $i++; endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-file-invoice"></i>
                    <h3>Aucune facture</h3>
                    <p>Aucune facture n'est encore enregistrée pour cette série.</p>
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
            "columnDefs": [
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 7 }
            ]
        });
    }
});
</script>

<?php include '../../../includes/footer.php'; ?>