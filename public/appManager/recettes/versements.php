<?php
include '../../../config/fonction.php';

$id_fact = (int)($_GET['id_fact'] ?? 0);

// === AJOUT ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $type = mysqli_real_escape_string($connexion, $_POST['type']);
    $montant = (int)$_POST['montant'];

    // Référence unique
    $result = mysqli_query($connexion, "SELECT MAX(id) AS max_id FROM paiements");
    $row = mysqli_fetch_assoc($result);
    $nextId = ($row['max_id'] ?? 0) + 1;
    $ref = "PAY-" . date("y") . "-" . str_pad($nextId, 3, "0", STR_PAD_LEFT);

    // Upload PDF
    $pdf = null;
    if (!empty($_FILES['pdf']['name'])) {
        $dir = "../../../uploads/paiements/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $pdf = time() . "_" . basename($_FILES['pdf']['name']);
        move_uploaded_file($_FILES['pdf']['tmp_name'], $dir . $pdf);
    }

    mysqli_query($connexion, "INSERT INTO paiements (facture_id, type, montant, reference, piece_jointe)
        VALUES ($id_fact, '$type', $montant, '$ref', " . ($pdf ? "'$pdf'" : "NULL") . ")");
    header("Location: versements.php?id_fact=$id_fact&success=1");
    exit;
}

// === MODIFICATION ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $pid = (int)$_POST['id'];
    $type = mysqli_real_escape_string($connexion, $_POST['type']);
    $montant = (int)$_POST['montant'];

    $old = mysqli_fetch_assoc(mysqli_query($connexion, "SELECT piece_jointe FROM paiements WHERE id=$pid"));
    $pdf = $old['piece_jointe'];

    if (!empty($_FILES['pdf']['name'])) {
        $dir = "../../../uploads/paiements/";
        if ($pdf && is_file($dir . $pdf)) unlink($dir . $pdf);
        $pdf = time() . "_" . basename($_FILES['pdf']['name']);
        move_uploaded_file($_FILES['pdf']['tmp_name'], $dir . $pdf);
    }

    mysqli_query($connexion, "UPDATE paiements SET type='$type', montant=$montant,
        piece_jointe=" . ($pdf ? "'$pdf'" : "NULL") . " WHERE id=$pid");
    header("Location: versements.php?id_fact=$id_fact&success=2");
    exit;
}

// === SUPPRESSION ===
if (isset($_GET['delete'])) {
    $pid = (int)$_GET['delete'];
    $old = mysqli_fetch_assoc(mysqli_query($connexion, "SELECT piece_jointe FROM paiements WHERE id=$pid"));
    if ($old['piece_jointe'] && is_file("../../../uploads/paiements/" . $old['piece_jointe'])) {
        unlink("../../../uploads/paiements/" . $old['piece_jointe']);
    }
    mysqli_query($connexion, "DELETE FROM paiements WHERE id=$pid");
    header("Location: versements.php?id_fact=$id_fact&success=3");
    exit;
}

// === RÉCUPÉRATION DONNÉES ===
$paiements = getPaiementsByFactureId($connexion, $id_fact);
$facture = getFactureWithPaiements($connexion, $id_fact);
?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
</head>

<?php include '../../../includes/header.php'; ?>

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

.versements-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.versements-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.versements-header {
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

.versements-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.versements-header-icon {
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

.versements-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.versements-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.versements-header p {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 14px;
}

.header-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 30px;
    background: #f4f4f5;
    font-size: 12px;
    font-weight: 800;
}

.header-status i {
    color: var(--accent);
}

/* =========================================================
   FACTURE INFO CARD
========================================================= */

.facture-info-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px 24px;
    margin-bottom: 25px;
    box-shadow: var(--shadow);
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
}

.facture-info-item {
    text-align: center;
}

.facture-info-item .label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--muted);
}

.facture-info-item .value {
    font-size: 16px;
    font-weight: 900;
    margin-top: 4px;
}

.facture-info-item .value.accent {
    color: var(--accent);
}

.facture-info-item .value.success {
    color: var(--success);
}

.facture-info-item .value.danger {
    color: var(--danger);
}

.facture-info-item .value.warning {
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
    flex-wrap: wrap;
    gap: 12px;
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

.table-card-header .actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-add {
    height: 40px;
    padding: 0 20px;
    border-radius: 10px;
    border: 0;
    background: var(--success);
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: .2s;
}

.btn-add:hover {
    background: #15803d;
    transform: translateY(-1px);
    color: white;
}

.btn-add:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.table-responsive {
    padding: 0 24px 24px;
    overflow-x: auto;
}

/* =========================================================
   DATA TABLE CUSTOM
========================================================= */

.versements-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.versements-table thead {
    background: #fafafa;
    border-bottom: 2px solid var(--border);
}

.versements-table thead th {
    padding: 14px 16px;
    text-align: left;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--muted);
}

.versements-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}

.versements-table tbody tr:hover {
    background: #fafafa;
}

.versements-table tbody tr:last-child {
    border-bottom: 0;
}

.versements-table tbody td {
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
}

.badge-type.virement {
    background: #dbeafe;
    color: #1e40af;
}

.badge-type.cheque {
    background: #fef3c7;
    color: #92400e;
}

.badge-type.especes {
    background: #d1fae5;
    color: #065f46;
}

.badge-pdf {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 12px;
    background: #fef2f2;
    color: var(--danger);
    font-size: 10px;
    font-weight: 700;
    text-decoration: none;
    transition: .2s;
}

.badge-pdf:hover {
    background: #fecaca;
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

/* =========================================================
   EMPTY STATE
========================================================= */

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--muted);
}

.empty-state i {
    font-size: 38px;
    color: #d4d4d8;
    margin-bottom: 12px;
}

.empty-state h3 {
    font-size: 16px;
    font-weight: 900;
    color: var(--text);
    margin-bottom: 6px;
}

.empty-state p {
    font-size: 12px;
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
    max-width: 500px;
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
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
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
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 0;
    background: #f4f4f5;
    color: var(--muted);
    font-size: 14px;
    cursor: pointer;
    transition: .2s;
}

.modal-close:hover {
    background: var(--danger);
    color: white;
}

.modal-body-custom {
    padding: 24px;
}

.modal-footer-custom {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* =========================================================
   FORM DANS MODAL
========================================================= */

.modern-label {
    display: block;
    font-size: 12px;
    font-weight: 800;
    margin-bottom: 6px;
    color: var(--text);
}

.modern-label i {
    margin-right: 6px;
    color: var(--muted);
}

.modern-select,
.modern-input-modal {
    width: 100%;
    height: 44px;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 0 14px;
    font-size: 13px;
    background: #fafafa;
    outline: none;
    transition: .2s;
}

.modern-select:focus,
.modern-input-modal:focus {
    background: white;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(229, 9, 20, .1);
}

.file-upload-wrapper-modal {
    position: relative;
}

.file-upload-wrapper-modal input[type="file"] {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.file-upload-label-modal {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 44px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: #fafafa;
    padding: 0 14px;
    font-size: 12px;
    transition: .2s;
}

.file-upload-label-modal .file-name-modal {
    font-weight: 600;
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* =========================================================
   BUTTONS MODAL
========================================================= */

.btn-modal {
    height: 40px;
    padding: 0 20px;
    border-radius: 10px;
    border: 0;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    transition: .2s;
}

.btn-modal.cancel {
    background: #f4f4f5;
    color: var(--text);
}

.btn-modal.cancel:hover {
    background: #e5e7eb;
}

.btn-modal.submit {
    background: var(--accent);
    color: white;
}

.btn-modal.submit:hover {
    background: var(--accent-hover);
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
    .facture-info-card {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 992px) {
    .versements-header {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 768px) {
    .versements-page {
        padding: 15px 0 35px;
    }
    .versements-container {
        padding: 0 12px;
    }
    .versements-header {
        padding: 18px;
    }
    .versements-header h1 {
        font-size: 21px;
    }
    .versements-header-icon {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
    }
    .facture-info-card {
        grid-template-columns: 1fr 1fr;
        padding: 16px;
        gap: 12px;
    }
    .facture-info-item .value {
        font-size: 14px;
    }
    .table-card-header {
        flex-direction: column;
        align-items: stretch;
    }
    .table-card-header .actions {
        width: 100%;
    }
    .btn-add {
        width: 100%;
        justify-content: center;
    }
    .table-responsive {
        padding: 0 12px 12px;
    }
    .versements-table thead {
        display: none;
    }
    .versements-table tbody tr {
        display: block;
        padding: 12px 0;
        border-bottom: 2px solid var(--border);
    }
    .versements-table tbody tr:last-child {
        border-bottom: 0;
    }
    .versements-table tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 0;
    }
    .versements-table tbody td::before {
        content: attr(data-label);
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--muted);
        letter-spacing: 0.3px;
    }
    .versements-table tbody td:first-child::before {
        display: none;
    }
    .versements-table tbody td:first-child {
        justify-content: flex-start;
        font-weight: 700;
    }
    .action-group {
        gap: 4px;
    }
    .modal-box {
        width: 98%;
    }
}

@media (max-width: 480px) {
    .versements-header-left {
        gap: 12px;
    }
    .versements-header-icon {
        display: none;
    }
    .facture-info-card {
        grid-template-columns: 1fr;
    }
}
</style>

<section class="versements-page">
    <div class="versements-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="versements-header">
            <div class="versements-header-left">
                <div class="versements-header-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div>
                    <div class="versements-breadcrumb">
                        EVENPROD / FACTURES / VERSEMENTS
                    </div>
                    <h1>Gestion des versements</h1>
                    <p>
                        <i class="fas fa-file-invoice" style="color:var(--accent);"></i>
                        Facture N° <strong><?= htmlspecialchars($facture['reference'] ?? 'N/A') ?></strong>
                        &nbsp;&bull;&nbsp;
                        Client : <strong><?= htmlspecialchars($facture['client_nom'] ?? 'Non défini') ?></strong>
                    </p>
                </div>
            </div>
            <div class="header-status">
                <i class="fas fa-list"></i>
                <?= count($paiements) ?> versement<?= count($paiements) > 1 ? 's' : '' ?>
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
            <div>Paiement ajouté avec succès !</div>
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
            <div>Paiement modifié avec succès !</div>
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
            <div>Paiement supprimé avec succès !</div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- =========================================================
        FACTURE INFO
        ========================================================= -->

        <?php if ($facture): ?>
        <div class="facture-info-card">
            <div class="facture-info-item">
                <div class="label">Total</div>
                <div class="value accent"><?= number_format($facture['total'], 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="facture-info-item">
                <div class="label">Déjà payé</div>
                <div class="value success"><?= number_format($facture['verse'], 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="facture-info-item">
                <div class="label">Reste à payer</div>
                <div class="value <?= $facture['reste'] > 0 ? 'danger' : 'success' ?>">
                    <?= number_format($facture['reste'], 0, ',', ' ') ?> FCFA
                </div>
            </div>
            <div class="facture-info-item">
                <div class="label">Taux de paiement</div>
                <div class="value warning">
                    <?php 
                        $taux = $facture['total'] > 0 ? ($facture['verse'] / $facture['total']) * 100 : 0;
                        echo number_format($taux, 1, ',', ' ') . '%';
                    ?>
                </div>
            </div>
            <div class="facture-info-item">
                <div class="label">Statut</div>
                <div class="value" style="color:<?= $facture['reste'] > 0 ? 'var(--danger)' : 'var(--success)' ?>;">
                    <?= $facture['reste'] > 0 ? '❌ Non soldée' : '✅ Soldée' ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- =========================================================
        TABLE DES VERSEMENTS
        ========================================================= -->

        <div class="table-card">
            <div class="table-card-header">
                <h2>
                    <i class="fas fa-list"></i>
                    Liste des versements
                </h2>
                <div class="actions">
                    <?php if ($facture && $facture['reste'] > 0): ?>
                    <button class="btn-add" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Ajouter un versement
                    </button>
                    <?php else: ?>
                    <button class="btn-add" disabled>
                        <i class="fas fa-check"></i> Facture soldée
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive">
                <?php if (!empty($paiements)): ?>
                <table class="versements-table" id="versementTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Référence</th>
                            <th>Pièce jointe</th>
                            <th style="text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($paiements as $v): ?>
                        <tr>
                            <td data-label="#">
                                <span style="font-weight:700; color:var(--muted);">#<?= $i ?></span>
                            </td>
                            <td data-label="Type">
                                <?php
                                    $typeClass = match(strtolower($v['type'])) {
                                        'virement bancaire' => 'virement',
                                        'chèque', 'cheque' => 'cheque',
                                        'espèces', 'especes' => 'especes',
                                        default => 'virement'
                                    };
                                    $label = $v['type'] ?? 'Non défini';
                                ?>
                                <span class="badge-type <?= $typeClass ?>">
                                    <?php if ($typeClass === 'virement'): ?><i class="fas fa-university"></i>
                                    <?php elseif ($typeClass === 'cheque'): ?><i class="fas fa-money-check"></i>
                                    <?php elseif ($typeClass === 'especes'): ?><i class="fas fa-hand-holding-usd"></i>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($label) ?>
                                </span>
                            </td>
                            <td data-label="Montant">
                                <span style="font-weight:700; color:var(--text);">
                                    <?= number_format($v['montant'], 0, ',', ' ') ?> FCFA
                                </span>
                            </td>
                            <td data-label="Référence">
                                <span style="font-weight:600; font-size:12px;">
                                    <i class="fas fa-hashtag" style="color:var(--muted);"></i>
                                    <?= htmlspecialchars($v['reference']) ?>
                                </span>
                            </td>
                            <td data-label="Pièce jointe">
                                <?php if (!empty($v['piece_jointe'])): ?>
                                <a href="../../../uploads/paiements/<?= htmlspecialchars($v['piece_jointe']) ?>" 
                                   class="badge-pdf" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Voir PDF
                                </a>
                                <?php else: ?>
                                <span style="color:var(--muted); font-size:11px;">Aucun</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Actions" style="text-align:center;">
                                <div class="action-group">
                                    <button type="button" class="btn-action edit" 
                                            onclick="openEditModal(<?= $v['id'] ?>, '<?= addslashes($v['type']) ?>', <?= $v['montant'] ?>, '<?= addslashes($v['piece_jointe'] ?? '') ?>')"
                                            title="Modifier">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <a href="?id_fact=<?= $id_fact ?>&delete=<?= $v['id'] ?>" 
                                       class="btn-action delete" 
                                       onclick="return confirm('Voulez-vous vraiment supprimer ce versement ?')"
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
                    <i class="fas fa-hand-holding-usd"></i>
                    <h3>Aucun versement</h3>
                    <p>Aucun versement n'a encore été enregistré pour cette facture.</p>
                    <?php if ($facture && $facture['reste'] > 0): ?>
                    <button class="btn-add" onclick="openAddModal()" style="display:inline-flex;">
                        <i class="fas fa-plus"></i> Ajouter le premier versement
                    </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<!-- =========================================================
MODAL AJOUT
========================================================= -->

<div class="modal-overlay" id="addModal">
    <div class="modal-box">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">

            <div class="modal-header-custom">
                <h5>
                    <i class="fas fa-plus-circle" style="color:var(--success);"></i>
                    Ajouter un versement
                </h5>
                <button type="button" class="modal-close" onclick="closeAddModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body-custom">
                <!-- Type -->
                <div class="form-group" style="margin-bottom:16px;">
                    <label for="addType" class="modern-label">
                        <i class="fas fa-tag"></i> Type de versement
                    </label>
                    <select name="type" id="addType" class="modern-select" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="Virement bancaire">Virement bancaire</option>
                        <option value="Chèque">Chèque</option>
                        <option value="Espèces">Espèces</option>
                    </select>
                </div>

                <!-- Montant -->
                <div class="form-group" style="margin-bottom:16px;">
                    <label for="addMontant" class="modern-label">
                        <i class="fas fa-coins"></i> Montant (FCFA)
                    </label>
                    <input type="number" name="montant" id="addMontant" class="modern-input-modal" 
                           min="0" step="100" placeholder="Ex: 50000" required>
                </div>

                <!-- Pièce jointe -->
                <div class="form-group">
                    <label for="addPdf" class="modern-label">
                        <i class="fas fa-file-pdf"></i> Pièce jointe (PDF)
                    </label>
                    <div class="file-upload-wrapper-modal">
                        <input type="file" name="pdf" id="addPdf" accept=".pdf">
                        <div class="file-upload-label-modal">
                            <span class="file-name-modal" id="addFileName">Aucun fichier choisi</span>
                            <span style="font-size:10px; font-weight:700; color:var(--accent);">Parcourir</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer-custom">
                <button type="button" class="btn-modal cancel" onclick="closeAddModal()">
                    Annuler
                </button>
                <button type="submit" class="btn-modal submit">
                    <i class="fas fa-save"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================
MODAL MODIFICATION
========================================================= -->

<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId" value="">

            <div class="modal-header-custom">
                <h5>
                    <i class="fas fa-pen" style="color:var(--warning);"></i>
                    Modifier un versement
                </h5>
                <button type="button" class="modal-close" onclick="closeEditModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body-custom">
                <!-- Type -->
                <div class="form-group" style="margin-bottom:16px;">
                    <label for="editType" class="modern-label">
                        <i class="fas fa-tag"></i> Type de versement
                    </label>
                    <select name="type" id="editType" class="modern-select" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="Virement bancaire">Virement bancaire</option>
                        <option value="Chèque">Chèque</option>
                        <option value="Espèces">Espèces</option>
                    </select>
                </div>

                <!-- Montant -->
                <div class="form-group" style="margin-bottom:16px;">
                    <label for="editMontant" class="modern-label">
                        <i class="fas fa-coins"></i> Montant (FCFA)
                    </label>
                    <input type="number" name="montant" id="editMontant" class="modern-input-modal" 
                           min="0" step="100" placeholder="Ex: 50000" required>
                </div>

                <!-- Pièce jointe -->
                <div class="form-group">
                    <label for="editPdf" class="modern-label">
                        <i class="fas fa-file-pdf"></i> Nouvelle pièce jointe (PDF)
                    </label>
                    <div class="file-upload-wrapper-modal">
                        <input type="file" name="pdf" id="editPdf" accept=".pdf">
                        <div class="file-upload-label-modal">
                            <span class="file-name-modal" id="editFileName">Conserver le fichier actuel</span>
                            <span style="font-size:10px; font-weight:700; color:var(--accent);">Parcourir</span>
                        </div>
                    </div>
                    <span id="editFileInfo" style="font-size:10px; color:var(--muted);"></span>
                </div>
            </div>

            <div class="modal-footer-custom">
                <button type="button" class="btn-modal cancel" onclick="closeEditModal()">
                    Annuler
                </button>
                <button type="submit" class="btn-modal submit">
                    <i class="fas fa-save"></i>
                    Modifier
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================
SCRIPTS
========================================================= -->

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
// ============================================================
// DATATABLE
// ============================================================

$(document).ready(function() {
    if ($('#versementTable tbody tr').length > 0) {
        $('#versementTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50],
            "responsive": true,
            "columnDefs": [
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 5 }
            ]
        });
    }
});

// ============================================================
// MODAL AJOUT
// ============================================================

function openAddModal() {
    document.getElementById('addModal').classList.add('active');
    document.body.style.overflow = 'hidden';
    // Réinitialiser le formulaire
    document.getElementById('addType').value = '';
    document.getElementById('addMontant').value = '';
    document.getElementById('addPdf').value = '';
    document.getElementById('addFileName').textContent = 'Aucun fichier choisi';
}

function closeAddModal() {
    document.getElementById('addModal').classList.remove('active');
    document.body.style.overflow = '';
}

// ============================================================
// MODAL MODIFICATION
// ============================================================

function openEditModal(id, type, montant, pieceJointe) {
    document.getElementById('editId').value = id;
    document.getElementById('editType').value = type || '';
    document.getElementById('editMontant').value = montant || '';
    document.getElementById('editPdf').value = '';
    document.getElementById('editFileName').textContent = 'Conserver le fichier actuel';
    
    if (pieceJointe) {
        document.getElementById('editFileInfo').textContent = 'Fichier actuel : ' + pieceJointe;
    } else {
        document.getElementById('editFileInfo').textContent = 'Aucun fichier actuel';
    }
    
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    document.body.style.overflow = '';
}

// ============================================================
// FERMETURE MODALS EN CLIQUANT À L'EXTÉRIEUR
// ============================================================

document.getElementById('addModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// ============================================================
// FERMETURE AVEC ECHAP
// ============================================================

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});

// ============================================================
// AFFICHAGE NOM FICHIER AJOUT
// ============================================================

document.getElementById('addPdf').addEventListener('change', function() {
    if (this.files && this.files.length > 0) {
        document.getElementById('addFileName').textContent = this.files[0].name;
    } else {
        document.getElementById('addFileName').textContent = 'Aucun fichier choisi';
    }
});

// ============================================================
// AFFICHAGE NOM FICHIER MODIFICATION
// ============================================================

document.getElementById('editPdf').addEventListener('change', function() {
    if (this.files && this.files.length > 0) {
        document.getElementById('editFileName').textContent = this.files[0].name + ' (remplacement)';
    } else {
        document.getElementById('editFileName').textContent = 'Conserver le fichier actuel';
    }
});
</script>

<?php include '../../../includes/footer.php'; ?>