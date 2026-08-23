<?php
include '../../config/fonction.php';

/*
 * |--------------------------------------------------------------------------
 * | DERNIÈRE SÉRIE
 * |--------------------------------------------------------------------------
 */

$lastSerie = getLastSerie();

/*
 * |--------------------------------------------------------------------------
 * | RÉCUPÉRATION DU SALARIÉ (MODIFICATION)
 * |--------------------------------------------------------------------------
 */

$salarie = null;
$id = $_GET['id'] ?? 0;

if ($id > 0) {
    $salarie = getSalarieById($connexion, $id);
}

/*
 * |--------------------------------------------------------------------------
 * | TRAITEMENT DU FORMULAIRE
 * |--------------------------------------------------------------------------
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_salarie'] ?? null;
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $date_naissance = trim($_POST['date_naissance'] ?? '');
    $fonction = trim($_POST['fonction'] ?? '');
    $date_embauche = trim($_POST['date_embauche'] ?? '');
    $type_contrat = $_POST['type_contrat'] ?? '';
    $salaire = trim($_POST['salaire'] ?? '');
    $statut = $_POST['statut'] ?? '';
    $photoFile = $_FILES['photo'] ?? null;
    $contratFile = $_FILES['contrat'] ?? null;

    if ($id) {
        $result = modifierSalarie($id, $nom, $prenom, $telephone, $email, $adresse, $date_naissance, $fonction, $date_embauche, $type_contrat, $salaire, $statut, $photoFile, $contratFile);
    } else {
        $result = ajouterSalarie($nom, $prenom, $telephone, $email, $adresse, $date_naissance, $fonction, $date_embauche, $type_contrat, $salaire, $statut, $photoFile, $contratFile);
    }

    if ($result === "success") {
        header("Location: add_salarie?success=1");
        exit;
    } elseif ($result === "exists") {
        header("Location: add_salarie?error=exists");
        exit;
    } else {
        header("Location: add_salarie?error=1");
        exit;
    }
}

?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
    --radius: 18px;
    --shadow: 0 10px 30px rgba(0, 0, 0, .06);
}

/* =========================================================
   PAGE
========================================================= */

.salarie-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.salarie-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.salarie-header {
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

.salarie-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.salarie-header-icon {
    width: 58px;
    height: 58px;
    flex: 0 0 58px;
    border-radius: 16px;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 23px;
}

.salarie-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.salarie-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.salarie-header p {
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

.header-status.edit {
    color: #b45309;
    background: #fff7ed;
}

/* =========================================================
   LAYOUT
========================================================= */

.salarie-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 360px;
    gap: 25px;
    align-items: start;
}

/* =========================================================
   CARDS
========================================================= */

.form-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 22px;
    overflow: hidden;
}

.card-header {
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 14px;
    border-bottom: 1px solid var(--border);
}

.card-header-icon {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    border-radius: 12px;
    background: #f4f4f5;
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-header h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 900;
}

.card-header p {
    margin: 3px 0 0;
    font-size: 12px;
    color: var(--muted);
}

.card-body {
    padding: 24px;
}

/* =========================================================
   FORM GRID
========================================================= */

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.form-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
}

.form-group {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    font-size: 12px;
    font-weight: 800;
    margin-bottom: 8px;
}

.form-group label span {
    color: var(--accent);
}

.input-wrapper {
    position: relative;
}

.input-wrapper>i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #a1a1aa;
    font-size: 14px;
    pointer-events: none;
}

.modern-input {
    width: 100%;
    height: 48px;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: #fafafa;
    padding: 0 14px 0 42px;
    color: var(--text);
    font-size: 13px;
    outline: none;
    transition: .2s;
}

.modern-input:focus {
    background: white;
    border-color: #a1a1aa;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, .04);
}

.modern-input.no-icon {
    padding: 0 14px;
}

.modern-select {
    width: 100%;
    height: 48px;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: #fafafa;
    padding: 0 14px 0 42px;
    color: var(--text);
    font-size: 13px;
    outline: none;
    transition: .2s;
    appearance: none;
    cursor: pointer;
}

.modern-select:focus {
    background: white;
    border-color: #a1a1aa;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, .04);
}

.select-wrapper {
    position: relative;
}

.select-wrapper>i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #a1a1aa;
    font-size: 14px;
    pointer-events: none;
    z-index: 1;
}

.select-wrapper::after {
    content: '\f078';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #a1a1aa;
    font-size: 12px;
    pointer-events: none;
}

/* =========================================================
   PHOTO / CONTRAT
========================================================= */

.upload-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px;
}

.photo-preview {
    width: 190px;
    height: 230px;
    border-radius: 15px;
    overflow: hidden;
    background: #f4f4f5;
    border: 1px solid var(--border);
}

.photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #b5b5b8;
}

.photo-placeholder i {
    font-size: 38px;
}

.photo-placeholder span {
    font-size: 10px;
    font-weight: 700;
}

.photo-upload {
    border: 1.5px dashed #d4d4d8;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    background: #fafafa;
    cursor: pointer;
    transition: .2s;
}

.photo-upload:hover {
    border-color: #737373;
    background: #f7f7f7;
}

.photo-upload-icon {
    width: 44px;
    height: 44px;
    margin: 0 auto 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: #171717;
    color: white;
    font-size: 16px;
}

.photo-upload strong {
    display: block;
    font-size: 12px;
    font-weight: 900;
    margin-bottom: 4px;
}

.photo-upload span {
    display: block;
    color: #999;
    font-size: 10px;
}

.photo-upload .file-name-display {
    display: block;
    margin-top: 6px;
    font-size: 10px;
    color: var(--accent);
    font-weight: 700;
}

.upload-section {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* =========================================================
   ACTIONS
========================================================= */

.form-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-bottom: 20px;
}

.btn {
    height: 48px;
    padding: 0 20px;
    border-radius: 12px;
    border: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: .2s;
}

.btn-cancel {
    background: white;
    color: #52525b;
    border: 1px solid var(--border);
}

.btn-cancel:hover {
    background: #f4f4f5;
    color: #171717;
}

.btn-submit {
    min-width: 210px;
    background: #171717;
    color: white;
}

.btn-submit:hover {
    background: #000;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
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
    margin-bottom: 20px;
    overflow: hidden;
}

.side-header {
    padding: 18px 20px;
    border-bottom: 1px solid var(--border);
}

.side-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.side-header-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #f4f4f5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.side-header h3 {
    margin: 0;
    font-size: 13px;
    font-weight: 900;
}

.side-header p {
    margin: 3px 0 0;
    font-size: 10px;
    color: #999;
}

/* =========================================================
   SALARIE PREVIEW
========================================================= */

.salarie-preview-body {
    padding: 20px;
    text-align: center;
}

.salarie-preview-photo {
    width: 135px;
    height: 165px;
    margin: 0 auto 15px;
    border-radius: 14px;
    overflow: hidden;
    background: #f4f4f5;
}

.salarie-preview-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.salarie-preview-placeholder {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 35px;
}

.salarie-preview-name {
    margin: 0;
    font-size: 18px;
    font-weight: 900;
}

.salarie-preview-fonction {
    margin-top: 3px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 600;
}

.salarie-meta {
    margin-top: 18px;
    padding-top: 15px;
    border-top: 1px solid var(--border);
    text-align: left;
}

.salarie-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 5px 0;
}

.salarie-meta-row span:first-child {
    color: #999;
    font-size: 10px;
}

.salarie-meta-row strong {
    font-size: 10px;
    text-align: right;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
}

.status-badge.actif {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.inactif {
    background: #fef2f2;
    color: #991b1b;
}

.status-badge.en_conge {
    background: #fef3c7;
    color: #92400e;
}

.contrat-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
}

.contrat-badge.cdi {
    background: #dbeafe;
    color: #1e40af;
}

.contrat-badge.cdd {
    background: #fce4ec;
    color: #9a1f3c;
}

.contrat-badge.stage {
    background: #e0e7ff;
    color: #3730a3;
}

/* =========================================================
   DERNIERE SERIE
========================================================= */

.series-body {
    padding: 20px;
}

.series-poster {
    width: 100%;
    height: 150px;
    border-radius: 12px;
    background: #f4f4f5;
    overflow: hidden;
    margin-bottom: 15px;
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
    font-size: 35px;
}

.series-title {
    margin: 0 0 5px;
    font-size: 17px;
    font-weight: 900;
}

.series-type {
    display: inline-flex;
    padding: 5px 9px;
    border-radius: 20px;
    background: #f4f4f5;
    font-size: 9px;
    font-weight: 800;
}

.series-budget {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid var(--border);
}

.series-budget-label {
    display: block;
    color: #999;
    font-size: 10px;
}

.series-budget-value {
    display: block;
    margin-top: 3px;
    font-size: 17px;
    font-weight: 900;
}

.series-budget-value small {
    color: #999;
    font-size: 9px;
}

/* =========================================================
   CONSEIL
========================================================= */

.tip-card {
    background: #171717;
    color: white;
    padding: 20px;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.tip-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.tip-header i {
    color: #fbbf24;
}

.tip-header strong {
    font-size: 12px;
}

.tip-card p {
    margin: 0;
    color: #bdbdbd;
    font-size: 11px;
    line-height: 1.7;
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

@media (max-width: 1100px) {
    .salarie-layout {
        grid-template-columns: 1fr;
    }
    .sidebar {
        position: static;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    .sidebar>* {
        margin-bottom: 0;
    }
}

@media (max-width: 768px) {
    .salarie-page {
        padding: 15px 0 35px;
    }
    .salarie-container {
        padding: 0 12px;
    }
    .salarie-header {
        padding: 18px;
        flex-direction: column;
        align-items: flex-start;
    }
    .salarie-header h1 {
        font-size: 21px;
    }
    .salarie-header-icon {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
    }
    .form-grid,
    .form-grid-3 {
        grid-template-columns: 1fr;
    }
    .card-header,
    .card-body {
        padding: 18px;
    }
    .upload-grid {
        grid-template-columns: 1fr;
    }
    .photo-preview {
        margin: auto;
    }
    .sidebar {
        grid-template-columns: 1fr;
    }
    .form-actions {
        flex-direction: column-reverse;
        align-items: stretch;
    }
    .btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .salarie-header-left {
        gap: 12px;
    }
    .salarie-header-icon {
        display: none;
    }
    .card-header {
        gap: 10px;
    }
    .card-header-icon {
        width: 36px;
        height: 36px;
        flex-basis: 36px;
    }
}
</style>

<section class="salarie-page">
    <div class="salarie-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="salarie-header">
            <div class="salarie-header-left">
                <div class="salarie-header-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <div class="salarie-breadcrumb">
                        EVENPROD / RH / SALARIÉS
                    </div>
                    <h1>
                        <?= $salarie ? 'Modifier le salarié' : 'Nouveau salarié' ?>
                    </h1>
                    <p>
                        <?= $salarie ? 'Modifiez les informations de ce salarié.' : 'Ajoutez un nouveau salarié à la plateforme.' ?>
                    </p>
                </div>
            </div>
            <div class="header-status <?= $salarie ? 'edit' : '' ?>">
                <i class="fas <?= $salarie ? 'fa-edit' : 'fa-user-plus' ?>"></i>
                <?= $salarie ? 'Mode modification' : 'Nouveau salarié' ?>
            </div>
        </header>

        <!-- =========================================================
        MESSAGES (via GET)
        ========================================================= -->

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="modern-alert success">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <?= $salarie ? 'Salarié modifié avec succès !' : 'Salarié ajouté avec succès !' ?>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'exists'): ?>
        <div class="modern-alert error">
            <div class="alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div>
                ⚠️ Ce salarié existe déjà !
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
        <div class="modern-alert error">
            <div class="alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div>
                ❌ Erreur lors de l'enregistrement.
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- =========================================================
        LAYOUT
        ========================================================= -->

        <div class="salarie-layout">

            <!-- =========================================================
            FORMULAIRE
            ========================================================= -->

            <main>
                <form action="add_salarie" method="post" enctype="multipart/form-data" id="salarieForm">

                    <input type="hidden" name="id_salarie" value="<?= htmlspecialchars($salarie['id'] ?? '') ?>">

                    <!-- =====================================================
                    INFORMATIONS PERSONNELLES
                    ====================================================== -->

                    <section class="form-card">
                        <div class="card-header">
                            <div class="card-header-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h2>Informations personnelles</h2>
                                <p>Identité et coordonnées du salarié</p>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="form-grid">

                                <!-- NOM -->
                                <div class="form-group">
                                    <label for="nom">
                                        Nom
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user-tag"></i>
                                        <input type="text" id="nom" name="nom" class="modern-input"
                                            placeholder="Ex : Diallo" required
                                            value="<?= htmlspecialchars($salarie['nom'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- PRENOM -->
                                <div class="form-group">
                                    <label for="prenom">
                                        Prénom
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user"></i>
                                        <input type="text" id="prenom" name="prenom" class="modern-input"
                                            placeholder="Ex : Fatou" required
                                            value="<?= htmlspecialchars($salarie['prenom'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- TELEPHONE -->
                                <div class="form-group">
                                    <label for="telephone">
                                        Téléphone
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-phone"></i>
                                        <input type="tel" id="telephone" name="telephone" class="modern-input"
                                            placeholder="Ex : 77 000 00 00" required
                                            value="<?= htmlspecialchars($salarie['telephone'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- EMAIL -->
                                <div class="form-group">
                                    <label for="email">
                                        Email
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-envelope"></i>
                                        <input type="email" id="email" name="email" class="modern-input"
                                            placeholder="Ex : fatou@evenprod.com" required
                                            value="<?= htmlspecialchars($salarie['email'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- ADRESSE -->
                                <div class="form-group" style="grid-column: span 2;">
                                    <label for="adresse">
                                        Adresse
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-home"></i>
                                        <input type="text" id="adresse" name="adresse" class="modern-input"
                                            placeholder="Ex : Dakar, Sénégal"
                                            value="<?= htmlspecialchars($salarie['adresse'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- DATE DE NAISSANCE -->
                                <div class="form-group">
                                    <label for="date_naissance">
                                        Date de naissance
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-calendar-alt"></i>
                                        <input type="date" id="date_naissance" name="date_naissance" class="modern-input"
                                            required
                                            value="<?= htmlspecialchars($salarie['date_naissance'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- FONCTION -->
                                <div class="form-group">
                                    <label for="fonction">
                                        Fonction
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-briefcase"></i>
                                        <input type="text" id="fonction" name="fonction" class="modern-input"
                                            placeholder="Ex : Réalisateur" required
                                            value="<?= htmlspecialchars($salarie['fonction'] ?? '') ?>">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- =====================================================
                    INFORMATIONS PROFESSIONNELLES
                    ====================================================== -->

                    <section class="form-card">
                        <div class="card-header">
                            <div class="card-header-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <h2>Informations professionnelles</h2>
                                <p>Contrat, salaire et statut</p>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="form-grid">

                                <!-- DATE D'EMBAUCHE -->
                                <div class="form-group">
                                    <label for="date_embauche">
                                        Date d'embauche
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-calendar-check"></i>
                                        <input type="date" id="date_embauche" name="date_embauche" class="modern-input"
                                            required
                                            value="<?= htmlspecialchars($salarie['date_embauche'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- TYPE DE CONTRAT -->
                                <div class="form-group">
                                    <label for="type_contrat">
                                        Type de contrat
                                        <span>*</span>
                                    </label>
                                    <div class="select-wrapper">
                                        <i class="fas fa-file-signature"></i>
                                        <select id="type_contrat" name="type_contrat" class="modern-select" required>
                                            <option value="">-- Sélectionnez --</option>
                                            <option value="cdi" <?= (isset($salarie['type_contrat']) && $salarie['type_contrat'] == 'cdi') ? 'selected' : '' ?>>CDI</option>
                                            <option value="cdd" <?= (isset($salarie['type_contrat']) && $salarie['type_contrat'] == 'cdd') ? 'selected' : '' ?>>CDD</option>
                                            <option value="stage" <?= (isset($salarie['type_contrat']) && $salarie['type_contrat'] == 'stage') ? 'selected' : '' ?>>Stage</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- SALAIRE -->
                                <div class="form-group">
                                    <label for="salaire">
                                        Salaire (FCFA)
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <input type="number" id="salaire" name="salaire" class="modern-input"
                                            placeholder="Ex : 500000" required min="0" step="1000"
                                            value="<?= htmlspecialchars($salarie['salaire'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- STATUT -->
                                <div class="form-group">
                                    <label for="statut">
                                        Statut
                                        <span>*</span>
                                    </label>
                                    <div class="select-wrapper">
                                        <i class="fas fa-user-check"></i>
                                        <select id="statut" name="statut" class="modern-select" required>
                                            <option value="">-- Sélectionnez --</option>
                                            <option value="actif" <?= (isset($salarie['statut']) && $salarie['statut'] == 'actif') ? 'selected' : '' ?>>Actif</option>
                                            <option value="inactif" <?= (isset($salarie['statut']) && $salarie['statut'] == 'inactif') ? 'selected' : '' ?>>Inactif</option>
                                            <option value="en_conge" <?= (isset($salarie['statut']) && $salarie['statut'] == 'en_conge') ? 'selected' : '' ?>>En congé</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- =====================================================
                    PHOTO & CONTRAT
                    ====================================================== -->

                    <section class="form-card">
                        <div class="card-header">
                            <div class="card-header-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div>
                                <h2>Documents</h2>
                                <p>Photo de profil et contrat de travail</p>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="upload-grid">

                                <!-- PHOTO -->
                                <div>
                                    <div style="display:flex; gap:20px; align-items:center; flex-wrap:wrap;">
                                        <div class="photo-preview" id="photoPreview">
                                            <?php if (!empty($salarie['photo'])): ?>
                                            <img src="uploads/salaries/<?= htmlspecialchars($salarie['photo']) ?>" alt="Photo de <?= htmlspecialchars(($salarie['prenom'] ?? '') . ' ' . ($salarie['nom'] ?? '')) ?>">
                                            <?php else: ?>
                                            <div class="photo-placeholder">
                                                <i class="fas fa-user"></i>
                                                <span>Aperçu</span>
                                            </div>
                                            <?php endif; ?>
                                        </div>

                                        <div style="flex:1;">
                                            <input type="file" id="photo" name="photo" accept="image/*" hidden>
                                            <label for="photo" class="photo-upload">
                                                <div class="photo-upload-icon">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                </div>
                                                <strong>Photo de profil</strong>
                                                <span>Cliquez pour sélectionner une image</span>
                                                <span id="photo-file-name" class="file-name-display">
                                                    <?= !empty($salarie['photo']) ? htmlspecialchars($salarie['photo']) : 'Aucun fichier choisi' ?>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- CONTRAT -->
                                <div>
                                    <div style="display:flex; gap:20px; align-items:center; flex-wrap:wrap;">
                                        <div class="photo-preview" id="contratPreview" style="height:150px; background:#f8f8f8; display:flex; align-items:center; justify-content:center;">
                                            <?php if (!empty($salarie['contrat'])): ?>
                                            <div style="text-align:center; padding:15px;">
                                                <i class="fas fa-file-pdf" style="font-size:40px; color:#dc2626;"></i>
                                                <span style="display:block; font-size:10px; color:#999; margin-top:5px;"><?= htmlspecialchars($salarie['contrat']) ?></span>
                                            </div>
                                            <?php else: ?>
                                            <div class="photo-placeholder" style="height:auto; padding:20px;">
                                                <i class="fas fa-file-contract" style="font-size:38px;"></i>
                                                <span>Aucun contrat</span>
                                            </div>
                                            <?php endif; ?>
                                        </div>

                                        <div style="flex:1;">
                                            <input type="file" id="contrat" name="contrat" accept=".pdf,.doc,.docx" hidden>
                                            <label for="contrat" class="photo-upload">
                                                <div class="photo-upload-icon">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                </div>
                                                <strong>Contrat de travail</strong>
                                                <span>PDF, DOC, DOCX</span>
                                                <span id="contrat-file-name" class="file-name-display">
                                                    <?= !empty($salarie['contrat']) ? htmlspecialchars($salarie['contrat']) : 'Aucun fichier choisi' ?>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- =====================================================
                    ACTIONS
                    ====================================================== -->

                    <div class="form-actions">
                        <a href="javascript:history.back()" class="btn btn-cancel">
                            <i class="fas fa-arrow-left"></i>
                            Annuler
                        </a>

                        <button type="submit" class="btn btn-submit" id="submitBtn">
                            <i class="fas <?= $salarie ? 'fa-save' : 'fa-user-plus' ?>"></i>
                            <span>
                                <?= $salarie ? 'Enregistrer les modifications' : 'Enregistrer le salarié' ?>
                            </span>
                        </button>
                    </div>

                </form>
            </main>

            <!-- =========================================================
            SIDEBAR
            ========================================================= -->

            <aside class="sidebar">

                <!-- =====================================================
                APERCU SALARIE
                ====================================================== -->

                <div class="side-card">
                    <div class="side-header">
                        <div class="side-header-title">
                            <div class="side-header-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div>
                                <h3>Aperçu du profil</h3>
                                <p>Mise à jour en temps réel</p>
                            </div>
                        </div>
                    </div>

                    <div class="salarie-preview-body">
                        <div class="salarie-preview-photo" id="salariePreviewPhoto">
                            <?php if (!empty($salarie['photo'])): ?>
                            <img src="uploads/salaries/<?= htmlspecialchars($salarie['photo']) ?>" alt="Aperçu">
                            <?php else: ?>
                            <div class="salarie-preview-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                            <?php endif; ?>
                        </div>

                        <h2 class="salarie-preview-name" id="previewName">
                            <?= htmlspecialchars(trim(($salarie['prenom'] ?? '') . ' ' . ($salarie['nom'] ?? '')) ?: 'Nom du salarié') ?>
                        </h2>

                        <div class="salarie-preview-fonction" id="previewFonction">
                            <?= htmlspecialchars($salarie['fonction'] ?? 'Fonction non renseignée') ?>
                        </div>

                        <div class="salarie-meta">
                            <div class="salarie-meta-row">
                                <span><i class="fas fa-phone"></i> Téléphone</span>
                                <strong id="previewPhone">
                                    <?= !empty($salarie['telephone']) ? htmlspecialchars($salarie['telephone']) : 'Non renseigné' ?>
                                </strong>
                            </div>
                            <div class="salarie-meta-row">
                                <span><i class="fas fa-envelope"></i> Email</span>
                                <strong id="previewEmail">
                                    <?= !empty($salarie['email']) ? htmlspecialchars($salarie['email']) : 'Non renseigné' ?>
                                </strong>
                            </div>
                            <div class="salarie-meta-row">
                                <span><i class="fas fa-calendar-alt"></i> Naissance</span>
                                <strong id="previewDateNaissance">
                                    <?= !empty($salarie['date_naissance']) ? date('d/m/Y', strtotime($salarie['date_naissance'])) : 'Non renseignée' ?>
                                </strong>
                            </div>
                            <div class="salarie-meta-row">
                                <span><i class="fas fa-calendar-check"></i> Embauche</span>
                                <strong id="previewDateEmbauche">
                                    <?= !empty($salarie['date_embauche']) ? date('d/m/Y', strtotime($salarie['date_embauche'])) : 'Non renseignée' ?>
                                </strong>
                            </div>
                            <div class="salarie-meta-row">
                                <span><i class="fas fa-file-signature"></i> Contrat</span>
                                <strong id="previewContrat">
                                    <?php if (!empty($salarie['type_contrat'])): ?>
                                    <span class="contrat-badge <?= htmlspecialchars($salarie['type_contrat']) ?>">
                                        <?= strtoupper(htmlspecialchars($salarie['type_contrat'])) ?>
                                    </span>
                                    <?php else: ?>
                                    Non défini
                                    <?php endif; ?>
                                </strong>
                            </div>
                            <div class="salarie-meta-row">
                                <span><i class="fas fa-money-bill-wave"></i> Salaire</span>
                                <strong id="previewSalaire">
                                    <?= !empty($salarie['salaire']) ? number_format((float)$salarie['salaire'], 0, ',', ' ') . ' FCFA' : 'Non renseigné' ?>
                                </strong>
                            </div>
                            <div class="salarie-meta-row">
                                <span><i class="fas fa-user-check"></i> Statut</span>
                                <strong id="previewStatut">
                                    <?php if (!empty($salarie['statut'])): ?>
                                    <span class="status-badge <?= htmlspecialchars($salarie['statut']) ?>">
                                        <?= str_replace('_', ' ', htmlspecialchars($salarie['statut'])) ?>
                                    </span>
                                    <?php else: ?>
                                    Non défini
                                    <?php endif; ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- =====================================================
                DERNIERE SERIE
                ====================================================== -->

                <div class="side-card">
                    <div class="side-header">
                        <div class="side-header-title">
                            <div class="side-header-icon">
                                <i class="fas fa-film"></i>
                            </div>
                            <div>
                                <h3>Dernière série</h3>
                                <p>Production récemment ajoutée</p>
                            </div>
                        </div>
                    </div>

                    <div class="series-body">
                        <?php if ($lastSerie): ?>
                        <div class="series-poster">
                            <?php if (!empty($lastSerie['logo'])): ?>
                            <img src="../../uploads/series/<?= htmlspecialchars($lastSerie['logo']) ?>" alt="<?= htmlspecialchars($lastSerie['titre']) ?>">
                            <?php else: ?>
                            <div class="series-poster-placeholder">
                                <i class="fas fa-film"></i>
                            </div>
                            <?php endif; ?>
                        </div>

                        <h2 class="series-title">
                            <?= htmlspecialchars($lastSerie['titre']) ?>
                        </h2>
                        <span class="series-type">
                            <i class="fas fa-tag"></i>
                            <?= htmlspecialchars($lastSerie['type']) ?>
                        </span>

                        <div class="series-budget">
                            <span class="series-budget-label">Budget prévisionnel</span>
                            <span class="series-budget-value">
                                <?= number_format((float)($lastSerie['budget'] ?? 0), 0, ',', ' ') ?>
                                <small>FCFA</small>
                            </span>
                        </div>
                        <?php else: ?>
                        <div style="text-align:center; padding:20px 5px; color:#999;">
                            <i class="fas fa-film" style="font-size:32px; margin-bottom:10px;"></i>
                            <p style="margin:0; font-size:11px; line-height:1.6;">
                                Aucune série n'est encore enregistrée dans la maison de production.
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- =====================================================
                CONSEIL
                ====================================================== -->

                <div class="tip-card">
                    <div class="tip-header">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Conseil RH</strong>
                    </div>
                    <p>
                        Assurez-vous que toutes les informations saisies sont correctes.
                        Le type de contrat et le statut détermineront les droits et
                        avantages du salarié au sein de l'entreprise.
                    </p>
                </div>

            </aside>

        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const nom = document.getElementById('nom');
    const prenom = document.getElementById('prenom');
    const telephone = document.getElementById('telephone');
    const email = document.getElementById('email');
    const dateNaissance = document.getElementById('date_naissance');
    const fonction = document.getElementById('fonction');
    const dateEmbauche = document.getElementById('date_embauche');
    const typeContrat = document.getElementById('type_contrat');
    const salaire = document.getElementById('salaire');
    const statut = document.getElementById('statut');
    const photoInput = document.getElementById('photo');
    const contratInput = document.getElementById('contrat');
    const photoPreview = document.getElementById('photoPreview');
    const contratPreview = document.getElementById('contratPreview');
    const salariePreviewPhoto = document.getElementById('salariePreviewPhoto');
    const photoFileName = document.getElementById('photo-file-name');
    const contratFileName = document.getElementById('contrat-file-name');

    /* =====================================================
       NOM & PRENOM
    ===================================================== */

    function updateName() {
        const first = prenom.value.trim();
        const last = nom.value.trim();
        const fullName = (first + ' ' + last).trim();
        document.getElementById('previewName').textContent = fullName || 'Nom du salarié';
    }

    prenom.addEventListener('input', updateName);
    nom.addEventListener('input', updateName);

    /* =====================================================
       TELEPHONE
    ===================================================== */

    telephone.addEventListener('input', function() {
        document.getElementById('previewPhone').textContent = this.value.trim() || 'Non renseigné';
    });

    /* =====================================================
       EMAIL
    ===================================================== */

    email.addEventListener('input', function() {
        document.getElementById('previewEmail').textContent = this.value.trim() || 'Non renseigné';
    });

    /* =====================================================
       DATE DE NAISSANCE
    ===================================================== */

    dateNaissance.addEventListener('change', function() {
        const val = this.value;
        if (val) {
            const d = new Date(val);
            document.getElementById('previewDateNaissance').textContent =
                d.toLocaleDateString('fr-FR');
        } else {
            document.getElementById('previewDateNaissance').textContent = 'Non renseignée';
        }
    });

    /* =====================================================
       FONCTION
    ===================================================== */

    fonction.addEventListener('input', function() {
        document.getElementById('previewFonction').textContent = this.value.trim() || 'Fonction non renseignée';
    });

    /* =====================================================
       DATE D'EMBAUCHE
    ===================================================== */

    dateEmbauche.addEventListener('change', function() {
        const val = this.value;
        if (val) {
            const d = new Date(val);
            document.getElementById('previewDateEmbauche').textContent =
                d.toLocaleDateString('fr-FR');
        } else {
            document.getElementById('previewDateEmbauche').textContent = 'Non renseignée';
        }
    });

    /* =====================================================
       TYPE DE CONTRAT
    ===================================================== */

    typeContrat.addEventListener('change', function() {
        const selected = this.value;
        const previewContrat = document.getElementById('previewContrat');
        if (selected) {
            const labels = { 'cdi': 'CDI', 'cdd': 'CDD', 'stage': 'STAGE' };
            previewContrat.innerHTML =
                `<span class="contrat-badge ${selected}">${labels[selected] || selected.toUpperCase()}</span>`;
        } else {
            previewContrat.textContent = 'Non défini';
        }
    });

    /* =====================================================
       SALAIRE
    ===================================================== */

    salaire.addEventListener('input', function() {
        const val = this.value.trim();
        if (val) {
            const num = parseFloat(val);
            document.getElementById('previewSalaire').textContent =
                !isNaN(num) ? num.toLocaleString('fr-FR') + ' FCFA' : 'Non renseigné';
        } else {
            document.getElementById('previewSalaire').textContent = 'Non renseigné';
        }
    });

    /* =====================================================
       STATUT
    ===================================================== */

    statut.addEventListener('change', function() {
        const selected = this.value;
        const previewStatut = document.getElementById('previewStatut');
        if (selected) {
            const labels = { 'actif': 'Actif', 'inactif': 'Inactif', 'en_conge': 'En congé' };
            previewStatut.innerHTML =
                `<span class="status-badge ${selected}">${labels[selected] || selected.replace('_', ' ')}</span>`;
        } else {
            previewStatut.textContent = 'Non défini';
        }
    });

    /* =====================================================
       PHOTO
    ===================================================== */

    if (photoInput) {
        photoInput.addEventListener('change', function() {
            if (!this.files || !this.files.length) return;

            const file = this.files[0];

            if (photoFileName) {
                photoFileName.textContent = file.name;
            }

            const reader = new FileReader();

            reader.onload = function(event) {
                const html = `<img src="${event.target.result}" alt="Photo du salarié">`;
                photoPreview.innerHTML = html;
                salariePreviewPhoto.innerHTML = html;
            };

            reader.readAsDataURL(file);
        });
    }

    /* =====================================================
       CONTRAT
    ===================================================== */

    if (contratInput) {
        contratInput.addEventListener('change', function() {
            if (!this.files || !this.files.length) return;

            const file = this.files[0];

            if (contratFileName) {
                contratFileName.textContent = file.name;
            }

            const icon = file.type.includes('pdf') ? 'fa-file-pdf' :
                         file.type.includes('word') || file.name.endsWith('.docx') || file.name.endsWith('.doc') ?
                         'fa-file-word' : 'fa-file';

            const color = file.type.includes('pdf') ? '#dc2626' :
                          file.type.includes('word') || file.name.endsWith('.docx') || file.name.endsWith('.doc') ?
                          '#2563eb' : '#737373';

            contratPreview.innerHTML = `
                <div style="text-align:center; padding:15px;">
                    <i class="fas ${icon}" style="font-size:40px; color:${color};"></i>
                    <span style="display:block; font-size:10px; color:#999; margin-top:5px;">${file.name}</span>
                </div>
            `;
        });
    }

    /* =====================================================
       SUBMIT
    ===================================================== */

    const form = document.getElementById('salarieForm');

    if (form) {
        form.addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '.7';
                submitBtn.style.cursor = 'not-allowed';
                const span = submitBtn.querySelector('span');
                if (span) {
                    span.textContent = 'Enregistrement...';
                }
            }
        });
    }

    /* =====================================================
       INIT - mettre à jour les previews au chargement
    ===================================================== */

    // Force update des previews avec les valeurs initiales
    if (salaire.value) {
        const event = new Event('input');
        salaire.dispatchEvent(event);
    }

});
</script>