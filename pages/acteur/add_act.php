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
 * | MODE AJOUT / MODIFICATION
 * |--------------------------------------------------------------------------
 */

$acteur = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $acteur = getActeurById($_GET['id']);
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

.actor-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.actor-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.actor-header {
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

.actor-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.actor-header-icon {
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

.actor-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.actor-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.actor-header p {
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

.actor-layout {
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

input[type="date"] {
    color-scheme: light;
}

/* =========================================================
   PHOTO
========================================================= */

.photo-section {
    display: grid;
    grid-template-columns: 190px 1fr;
    gap: 22px;
    align-items: center;
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
    padding: 30px;
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
    width: 52px;
    height: 52px;
    margin: 0 auto 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: #171717;
    color: white;
    font-size: 18px;
}

.photo-upload strong {
    display: block;
    font-size: 13px;
    font-weight: 900;
    margin-bottom: 5px;
}

.photo-upload span {
    display: block;
    color: #999;
    font-size: 11px;
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
   ACTOR PREVIEW
========================================================= */

.actor-preview-body {
    padding: 20px;
    text-align: center;
}

.actor-preview-photo {
    width: 135px;
    height: 165px;
    margin: 0 auto 15px;
    border-radius: 14px;
    overflow: hidden;
    background: #f4f4f5;
}

.actor-preview-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.actor-preview-placeholder {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 35px;
}

.actor-preview-name {
    margin: 0;
    font-size: 18px;
    font-weight: 900;
}

.actor-preview-contact {
    margin-top: 5px;
    color: #999;
    font-size: 11px;
}

.actor-meta {
    margin-top: 18px;
    padding-top: 15px;
    border-top: 1px solid var(--border);
    text-align: left;
}

.actor-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 7px 0;
}

.actor-meta-row span:first-child {
    color: #999;
    font-size: 10px;
}

.actor-meta-row strong {
    font-size: 10px;
    text-align: right;
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
   ALERT
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
    .actor-layout {
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
    .actor-page {
        padding: 15px 0 35px;
    }

    .actor-container {
        padding: 0 12px;
    }

    .actor-header {
        padding: 18px;
        flex-direction: column;
        align-items: flex-start;
    }

    .actor-header h1 {
        font-size: 21px;
    }

    .actor-header-icon {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .card-header,
    .card-body {
        padding: 18px;
    }

    .photo-section {
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
    .actor-header-left {
        gap: 12px;
    }

    .actor-header-icon {
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

<section class="actor-page">
    <div class="actor-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="actor-header">
            <div class="actor-header-left">
                <div class="actor-header-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <div class="actor-breadcrumb">
                        EVENPROD / PRODUCTIONS / ACTEURS
                    </div>
                    <h1>
                        <?= $acteur ? "Modifier l'acteur" : 'Nouvel acteur' ?>
                    </h1>
                    <p>
                        <?= $acteur ? 'Modifiez les informations de cet acteur.' : 'Ajoutez un nouveau talent à votre maison de production.' ?>
                    </p>
                </div>
            </div>
            <div class="header-status <?= $acteur ? 'edit' : '' ?>">
                <i class="fas <?= $acteur ? 'fa-edit' : 'fa-user-plus' ?>"></i>
                <?= $acteur ? 'Mode modification' : 'Nouveau talent' ?>
            </div>
        </header>

        <!-- =========================================================
        MESSAGES
        ========================================================= -->

        <?php if (isset($_SESSION['successact'])): ?>
        <div class="modern-alert success">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <?= htmlspecialchars($_SESSION['successact']) ?>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php unset($_SESSION['successact']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['erroract'])): ?>
        <div class="modern-alert error">
            <div class="alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div>
                <?= htmlspecialchars($_SESSION['erroract']) ?>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php unset($_SESSION['erroract']); ?>
        <?php endif; ?>

        <!-- =========================================================
        LAYOUT
        ========================================================= -->

        <div class="actor-layout">

            <!-- =========================================================
            FORMULAIRE
            ========================================================= -->

            <main>
                <form action="trait_acteur" method="post" enctype="multipart/form-data" id="acteurForm">

                    <?php if ($acteur): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($acteur['id']) ?>">
                    <?php endif; ?>

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
                                <p>Identité et coordonnées de l'acteur</p>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="form-grid">

                                <!-- PRENOM -->
                                <div class="form-group">
                                    <label for="prenom">
                                        Prénom
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user"></i>
                                        <input type="text" id="prenom" name="prenom" class="modern-input"
                                            placeholder="Ex : Omar" required
                                            value="<?= htmlspecialchars($acteur['prenom'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- NOM -->
                                <div class="form-group">
                                    <label for="nom">
                                        Nom
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user-tag"></i>
                                        <input type="text" id="nom" name="nom" class="modern-input"
                                            placeholder="Ex : Sy" required
                                            value="<?= htmlspecialchars($acteur['nom'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- DATE NAISSANCE -->
                                <div class="form-group">
                                    <label for="date_naissance">
                                        Date de naissance
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-calendar-alt"></i>
                                        <input type="date" id="date_naissance" name="date_naissance"
                                            class="modern-input" required
                                            value="<?= htmlspecialchars($acteur['date_naissance'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- CONTACT -->
                                <div class="form-group">
                                    <label for="contact">
                                        Téléphone / Contact
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-phone"></i>
                                        <input type="text" id="contact" name="contact" class="modern-input"
                                            placeholder="Ex : 221784413400" required maxlength="12" minlength="12"
                                            value="<?= htmlspecialchars($acteur['contact'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- ADRESSE -->
                                <div class="form-group">
                                    <label for="adresse">
                                        Adresse
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <input type="text" id="adresse" name="adresse" class="modern-input"
                                            placeholder="Adresse de résidence" required
                                            value="<?= htmlspecialchars($acteur['adresse'] ?? '') ?>">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- =====================================================
                    PHOTO DE L'ACTEUR
                    ====================================================== -->

                    <section class="form-card">
                        <div class="card-header">
                            <div class="card-header-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div>
                                <h2>Photo de l'acteur</h2>
                                <p>Vérifiez l'image avant d'enregistrer le profil</p>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="photo-section">

                                <div class="photo-preview" id="photoPreview">
                                    <?php if (!empty($acteur['photo'])): ?>
                                    <img src="../../uploads/photos/<?= htmlspecialchars($acteur['photo']) ?>"
                                        alt="Photo de <?= htmlspecialchars(($acteur['prenom'] ?? '') . ' ' . ($acteur['nom'] ?? '')) ?>">
                                    <?php else: ?>
                                    <div class="photo-placeholder">
                                        <i class="fas fa-user"></i>
                                        <span>Aperçu</span>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <label for="photo" class="photo-upload">
                                    <div class="photo-upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <strong>Importer la photo de l'acteur</strong>
                                    <span>Cliquez ici pour sélectionner une image</span>
                                </label>

                                <input type="file" id="photo" name="photo" accept="image/*" hidden>

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
                            <i class="fas <?= $acteur ? 'fa-save' : 'fa-user-plus' ?>"></i>
                            <span>
                                <?= $acteur ? 'Enregistrer les modifications' : "Enregistrer l'acteur" ?>
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
                APERCU ACTEUR
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

                    <div class="actor-preview-body">
                        <div class="actor-preview-photo" id="actorPreviewPhoto">
                            <?php if (!empty($acteur['photo'])): ?>
                            <img src="../../uploads/photos/<?= htmlspecialchars($acteur['photo']) ?>" alt="Aperçu">
                            <?php else: ?>
                            <div class="actor-preview-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                            <?php endif; ?>
                        </div>

                        <h2 class="actor-preview-name" id="previewName">
                            <?= htmlspecialchars(trim(($acteur['prenom'] ?? '') . ' ' . ($acteur['nom'] ?? '')) ?: "Nom de l'acteur") ?>
                        </h2>

                        <div class="actor-preview-contact" id="previewContact">
                            <?= htmlspecialchars($acteur['contact'] ?? 'Contact non renseigné') ?>
                        </div>

                        <div class="actor-meta">
                            <div class="actor-meta-row">
                                <span><i class="fas fa-calendar"></i> Naissance</span>
                                <strong id="previewBirth">
                                    <?= !empty($acteur['date_naissance']) ? htmlspecialchars($acteur['date_naissance']) : 'Non renseignée' ?>
                                </strong>
                            </div>
                            <div class="actor-meta-row">
                                <span><i class="fas fa-map-marker-alt"></i> Adresse</span>
                                <strong id="previewAddress">
                                    <?= !empty($acteur['adresse']) ? htmlspecialchars($acteur['adresse']) : 'Non renseignée' ?>
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
                            <img src="../../uploads/series/<?php echo htmlspecialchars($lastSerie['logo']); ?>"
                                alt="<?= htmlspecialchars($lastSerie['titre']) ?>">
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
                                <?= number_format((float) ($lastSerie['budget'] ?? 0), 0, ',', ' ') ?>
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
                        <strong>Conseil de production</strong>
                    </div>
                    <p>
                        Assurez-vous que le nom, les coordonnées et la photo correspondent bien à l'acteur.
                        Ces informations permettront à l'équipe de production de l'identifier facilement.
                    </p>
                </div>

            </aside>

        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const prenom = document.getElementById('prenom');
    const nom = document.getElementById('nom');
    const contact = document.getElementById('contact');
    const dateNaissance = document.getElementById('date_naissance');
    const adresse = document.getElementById('adresse');
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const actorPreviewPhoto = document.getElementById('actorPreviewPhoto');

    /* =====================================================
       NOM
    ===================================================== */

    function updateName() {
        const first = prenom.value.trim();
        const last = nom.value.trim();
        const fullName = (first + ' ' + last).trim();
        document.getElementById('previewName').textContent = fullName || 'Nom de l\'acteur';
    }

    prenom.addEventListener('input', updateName);
    nom.addEventListener('input', updateName);

    /* =====================================================
       CONTACT
    ===================================================== */

    contact.addEventListener('input', function() {
        document.getElementById('previewContact').textContent = this.value.trim() ||
            'Contact non renseigné';
    });

    /* =====================================================
       DATE
    ===================================================== */

    dateNaissance.addEventListener('change', function() {
        document.getElementById('previewBirth').textContent = this.value || 'Non renseignée';
    });

    /* =====================================================
       ADRESSE
    ===================================================== */

    adresse.addEventListener('input', function() {
        document.getElementById('previewAddress').textContent = this.value.trim() || 'Non renseignée';
    });

    /* =====================================================
       PHOTO
    ===================================================== */

    if (photoInput) {
        photoInput.addEventListener('change', function() {
            if (!this.files || !this.files.length) return;

            const file = this.files[0];
            const reader = new FileReader();

            reader.onload = function(event) {
                const html = `<img src="${event.target.result}" alt="Photo de l'acteur">`;
                photoPreview.innerHTML = html;
                actorPreviewPhoto.innerHTML = html;
            };

            reader.readAsDataURL(file);
        });
    }

    /* =====================================================
       SUBMIT
    ===================================================== */

    const form = document.getElementById('acteurForm');

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

});
</script>

<?php include '../../includes/footer.php'; ?>