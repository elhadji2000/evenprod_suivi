<?php

include '../config/fonction.php';

/*
|--------------------------------------------------------------------------
| MODE CREATION / MODIFICATION
|--------------------------------------------------------------------------
*/

$serieId = $_GET['id'] ?? null;
$serie   = $serieId ? getSerieById($serieId) : null;

$lastSerie = getLastSerie();

include '../includes/header.php';

?>

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


<style>

/* =========================================================
   VARIABLES
========================================================= */

:root {
    --primary: #171717;
    --primary-light: #292929;
    --accent: #e50914;
    --accent-dark: #b20710;

    --bg: #f5f6f8;
    --card: #ffffff;

    --text: #171717;
    --muted: #737373;
    --border: #e5e7eb;

    --success: #16a34a;
    --warning: #f59e0b;

    --radius: 18px;
    --shadow: 0 10px 30px rgba(0,0,0,.06);
}


/* =========================================================
   PAGE
========================================================= */

.serie-page {
    min-height: 100vh;
    background: var(--bg);
    padding: 25px 0 50px;
    color: var(--text);
}

.serie-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}


/* =========================================================
   HEADER
========================================================= */

.serie-header {
    background: #fff;
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

.serie-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.serie-header-icon {
    width: 58px;
    height: 58px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 16px;

    background: #171717;
    color: #fff;

    font-size: 23px;
}

.breadcrumb {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #999;

    margin-bottom: 5px;
}

.serie-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 800;
    letter-spacing: -.5px;
}

.serie-header p {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 14px;
}

.header-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 9px 14px;

    background: #f4f4f5;
    border-radius: 30px;

    font-size: 12px;
    font-weight: 700;
}

.header-status.new {
    color: #171717;
}

.header-status.edit {
    color: #b45309;
    background: #fff7ed;
}


/* =========================================================
   LAYOUT
========================================================= */

.serie-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 360px;
    gap: 25px;
    align-items: start;
}


/* =========================================================
   CARDS
========================================================= */

.form-card,
.preview-card {
    background: var(--card);
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

    border-radius: 12px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #f4f4f5;
    color: #171717;

    font-size: 16px;
}

.card-header h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
}

.card-header p {
    margin: 3px 0 0;
    color: var(--muted);
    font-size: 12px;
}

.card-body {
    padding: 24px;
}


/* =========================================================
   FORM
========================================================= */

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group.full {
    grid-column: 1 / -1;
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

.input-box {
    position: relative;
}

.input-box > i {
    position: absolute;

    left: 14px;
    top: 50%;

    transform: translateY(-50%);

    color: #a1a1aa;

    font-size: 14px;

    pointer-events: none;
}

.form-control-modern {
    width: 100%;
    height: 48px;

    border: 1px solid var(--border);
    border-radius: 12px;

    padding: 0 14px 0 42px;

    background: #fafafa;

    font-size: 13px;

    color: var(--text);

    outline: none;

    transition: .2s;
}

.form-control-modern:focus {
    background: #fff;
    border-color: #a1a1aa;

    box-shadow: 0 0 0 3px rgba(0,0,0,.04);
}

textarea.form-control-modern {
    height: auto;
    min-height: 135px;

    resize: vertical;

    padding: 14px;
}

textarea.description-field {
    padding-left: 14px;
}

.textarea-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-top: 6px;

    font-size: 11px;
    color: #999;
}


/* =========================================================
   IMAGE UPLOAD
========================================================= */

.upload-wrapper {
    display: grid;
    grid-template-columns: 190px 1fr;
    gap: 22px;
    align-items: center;
}

.poster-preview {
    width: 190px;
    height: 250px;

    border-radius: 14px;

    background: #f4f4f5;

    border: 1px solid var(--border);

    overflow: hidden;

    position: relative;
}

.poster-preview img {
    width: 100%;
    height: 100%;

    object-fit: cover;

    display: block;
}

.poster-placeholder {
    width: 100%;
    height: 100%;

    display: flex;
    flex-direction: column;

    align-items: center;
    justify-content: center;

    color: #a1a1aa;

    gap: 10px;

    text-align: center;
}

.poster-placeholder i {
    font-size: 35px;
}

.poster-placeholder span {
    font-size: 11px;
    font-weight: 600;
}

.upload-content {
    border: 1.5px dashed #d4d4d8;
    border-radius: 15px;

    padding: 30px;

    text-align: center;

    background: #fafafa;

    transition: .2s;

    cursor: pointer;
}

.upload-content:hover {
    border-color: #737373;
    background: #f7f7f7;
}

.upload-icon {
    width: 52px;
    height: 52px;

    margin: 0 auto 12px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #171717;
    color: #fff;

    border-radius: 14px;

    font-size: 18px;
}

.upload-content strong {
    display: block;

    font-size: 13px;
    font-weight: 800;

    margin-bottom: 5px;
}

.upload-content span {
    display: block;

    color: #999;

    font-size: 11px;

    margin-bottom: 10px;
}

.file-name {
    display: block;

    color: #171717;

    font-size: 11px;
    font-weight: 700;

    word-break: break-word;
}


/* =========================================================
   BUDGET
========================================================= */

.budget-intro {
    display: flex;
    align-items: center;
    gap: 13px;

    padding: 14px 16px;

    background: #fafafa;

    border: 1px solid var(--border);
    border-radius: 13px;

    margin-bottom: 20px;
}

.budget-intro-icon {
    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: #171717;
    color: #fff;
}

.budget-intro strong {
    display: block;

    font-size: 12px;
    margin-bottom: 2px;
}

.budget-intro p {
    margin: 0;

    font-size: 11px;
    color: var(--muted);
}

.budget-grid {
    display: grid;

    grid-template-columns: repeat(2, minmax(0, 1fr));

    gap: 12px;
}

.budget-item {
    border: 1px solid var(--border);

    border-radius: 13px;

    padding: 13px 14px;

    display: flex;
    align-items: center;
    gap: 12px;

    background: #fff;

    transition: .2s;
}

.budget-item:focus-within {
    border-color: #a1a1aa;
    box-shadow: 0 0 0 3px rgba(0,0,0,.03);
}

.budget-icon {
    width: 36px;
    height: 36px;

    flex: 0 0 36px;

    border-radius: 10px;

    background: #f4f4f5;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #52525b;

    font-size: 13px;
}

.budget-info {
    min-width: 0;
    flex: 1;
}

.budget-info label {
    display: block;

    font-size: 11px;
    font-weight: 800;

    margin-bottom: 5px;
}

.money-input {
    display: flex;
    align-items: center;

    border: 1px solid #e4e4e7;

    border-radius: 8px;

    background: #fafafa;

    overflow: hidden;
}

.money-input input {
    width: 100%;

    height: 32px;

    border: 0;
    outline: none;

    background: transparent;

    padding: 0 8px;

    font-size: 12px;
    font-weight: 700;
}

.money-input span {
    padding: 0 8px;

    color: #a1a1aa;

    font-size: 9px;
    font-weight: 800;
}

.budget-total {
    margin-top: 20px;

    border-radius: 15px;

    padding: 18px 20px;

    background: #171717;

    color: #fff;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;
}

.total-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.total-icon {
    width: 40px;
    height: 40px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: rgba(255,255,255,.1);

    border-radius: 10px;
}

.total-label {
    display: block;

    font-size: 12px;
    font-weight: 800;
}

.total-description {
    display: block;

    color: #a1a1aa;

    font-size: 10px;

    margin-top: 3px;
}

.total-value {
    font-size: 20px;
    font-weight: 900;

    white-space: nowrap;
}

.total-value small {
    font-size: 10px;
    font-weight: 700;

    color: #a1a1aa;
}


/* =========================================================
   ACTIONS
========================================================= */

.form-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 5px 0 20px;

    gap: 12px;
}

.btn {
    height: 48px;

    border-radius: 12px;

    padding: 0 20px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 9px;

    text-decoration: none;

    font-size: 12px;
    font-weight: 800;

    border: 0;

    cursor: pointer;

    transition: .2s;
}

.btn-cancel {
    background: #fff;

    border: 1px solid var(--border);

    color: #52525b;
}

.btn-cancel:hover {
    background: #f4f4f5;
    color: #171717;
}

.btn-submit {
    background: #171717;
    color: #fff;

    min-width: 210px;
}

.btn-submit:hover {
    background: #000;

    transform: translateY(-1px);

    box-shadow: 0 8px 20px rgba(0,0,0,.12);
}


/* =========================================================
   SIDEBAR
========================================================= */

.sidebar {
    position: sticky;
    top: 20px;
}

.preview-card {
    padding: 0;
}

.preview-header {
    padding: 18px 20px;

    border-bottom: 1px solid var(--border);
}

.preview-header h3 {
    margin: 0;

    font-size: 14px;
    font-weight: 800;
}

.preview-header p {
    margin: 4px 0 0;

    font-size: 11px;
    color: var(--muted);
}

.preview-poster {
    margin: 20px auto 15px;

    width: 150px;
    height: 200px;

    border-radius: 13px;

    background: #f4f4f5;

    overflow: hidden;
}

.preview-poster img {
    width: 100%;
    height: 100%;

    object-fit: cover;
}

.preview-poster-placeholder {
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #c4c4c7;

    font-size: 35px;
}

.preview-content {
    padding: 0 20px 20px;

    text-align: center;
}

.preview-title {
    font-size: 18px;
    font-weight: 900;

    margin: 0 0 5px;

    word-break: break-word;
}

.preview-type {
    display: inline-flex;

    padding: 5px 10px;

    background: #f4f4f5;

    border-radius: 30px;

    color: #52525b;

    font-size: 10px;
    font-weight: 800;
}

.preview-budget {
    margin-top: 18px;

    border-top: 1px solid var(--border);

    padding-top: 16px;

    text-align: left;
}

.preview-budget-label {
    color: #999;

    font-size: 10px;
    font-weight: 700;
}

.preview-budget-value {
    margin-top: 3px;

    font-size: 18px;
    font-weight: 900;
}

.preview-budget-value small {
    font-size: 10px;
    color: #999;
}


/* =========================================================
   SUMMARY
========================================================= */

.summary-card {
    padding: 20px;
}

.summary-title {
    display: flex;
    align-items: center;
    gap: 10px;

    font-size: 13px;
    font-weight: 800;

    margin-bottom: 15px;
}

.summary-title i {
    color: #71717a;
}

.summary-list {
    display: flex;
    flex-direction: column;

    gap: 9px;
}

.summary-row {
    display: flex;
    align-items: center;
    justify-content: space-between;

    font-size: 11px;
}

.summary-row span:first-child {
    color: #737373;
}

.summary-row strong {
    font-size: 11px;
}

.summary-total {
    border-top: 1px solid var(--border);

    margin-top: 14px;
    padding-top: 14px;

    display: flex;
    justify-content: space-between;

    font-size: 12px;
    font-weight: 900;
}


/* =========================================================
   TIPS
========================================================= */

.tip-card {
    background: #171717;

    color: #fff;

    padding: 20px;

    border-radius: var(--radius);

    box-shadow: var(--shadow);
}

.tip-card-header {
    display: flex;
    align-items: center;
    gap: 10px;

    margin-bottom: 10px;
}

.tip-card-header i {
    color: #fbbf24;
}

.tip-card-header strong {
    font-size: 12px;
}

.tip-card p {
    margin: 0;

    color: #bdbdbd;

    line-height: 1.6;

    font-size: 11px;
}


/* =========================================================
   TOAST
========================================================= */

.success-toast {
    position: fixed;

    top: 25px;
    right: 25px;

    z-index: 9999;

    min-width: 320px;

    background: #fff;

    border: 1px solid #e5e7eb;

    border-radius: 14px;

    padding: 15px;

    box-shadow: 0 15px 40px rgba(0,0,0,.15);

    display: flex;
    align-items: center;

    gap: 12px;

    transform: translateX(120%);

    transition: .4s;
}

.success-toast.show {
    transform: translateX(0);
}

.success-toast-icon {
    width: 38px;
    height: 38px;

    border-radius: 50%;

    background: #dcfce7;

    color: #16a34a;

    display: flex;
    align-items: center;
    justify-content: center;
}

.success-toast strong {
    display: block;
    font-size: 12px;
}

.success-toast span {
    display: block;

    margin-top: 3px;

    color: #737373;

    font-size: 11px;
}

.toast-close {
    margin-left: auto;

    border: 0;
    background: transparent;

    cursor: pointer;

    color: #999;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1100px) {

    .serie-layout {
        grid-template-columns: 1fr;
    }

    .sidebar {
        position: static;

        display: grid;

        grid-template-columns: repeat(2, 1fr);

        gap: 20px;
    }

    .sidebar > * {
        margin-bottom: 0;
    }

}


@media (max-width: 768px) {

    .serie-page {
        padding: 15px 0 35px;
    }

    .serie-container {
        padding: 0 12px;
    }

    .serie-header {
        padding: 18px;

        align-items: flex-start;

        flex-direction: column;
    }

    .serie-header h1 {
        font-size: 21px;
    }

    .serie-header-left {
        align-items: flex-start;
    }

    .serie-header-icon {
        width: 48px;
        height: 48px;
    }

    .form-grid,
    .budget-grid {
        grid-template-columns: 1fr;
    }

    .form-group.full {
        grid-column: auto;
    }

    .card-header,
    .card-body {
        padding: 18px;
    }

    .upload-wrapper {
        grid-template-columns: 1fr;
    }

    .poster-preview {
        margin: 0 auto;
    }

    .sidebar {
        grid-template-columns: 1fr;
    }

    .budget-total {
        align-items: flex-start;

        flex-direction: column;
    }

    .total-value {
        width: 100%;

        padding-left: 52px;
    }

    .form-actions {
        flex-direction: column-reverse;

        align-items: stretch;
    }

    .btn {
        width: 100%;
    }

    .success-toast {
        left: 12px;
        right: 12px;

        top: 12px;

        min-width: auto;
    }

}


@media (max-width: 480px) {

    .serie-header-left {
        gap: 12px;
    }

    .serie-header-icon {
        display: none;
    }

    .card-header {
        gap: 10px;
    }

    .card-header-icon {
        width: 36px;
        height: 36px;
    }

    .card-header h2 {
        font-size: 14px;
    }

    .preview-poster {
        width: 130px;
        height: 175px;
    }

}

</style>


<section class="serie-page">

<div class="serie-container">


<!-- =========================================================
     HEADER
========================================================= -->

<header class="serie-header">

    <div class="serie-header-left">

        <div class="serie-header-icon">
            <i class="fas fa-film"></i>
        </div>

        <div>

            <div class="breadcrumb">
                EVENPROD / PRODUCTIONS / SÉRIES
            </div>

            <h1>
                <?= $serieId ? 'Modifier la série' : 'Nouvelle série' ?>
            </h1>

            <p>
                <?= $serieId
                    ? 'Modifiez les informations de votre production.'
                    : 'Configurez votre nouvelle série et son budget prévisionnel.'
                ?>
            </p>

        </div>

    </div>


    <div class="header-status <?= $serieId ? 'edit' : 'new' ?>">

        <i class="fas <?= $serieId ? 'fa-edit' : 'fa-plus-circle' ?>"></i>

        <?= $serieId ? 'Mode modification' : 'Nouvelle production' ?>

    </div>

</header>



<!-- =========================================================
     FORM + SIDEBAR
========================================================= -->

<div class="serie-layout">


<!-- =========================================================
     COLONNE PRINCIPALE
========================================================= -->

<main>


<form action="trait_serie"
      method="post"
      enctype="multipart/form-data"
      id="serieForm">


<?php if ($serieId): ?>

    <input type="hidden"
           name="serie_id"
           value="<?= htmlspecialchars($serieId) ?>">

<?php endif; ?>


<!-- =========================================================
     INFORMATIONS
========================================================= -->

<section class="form-card">

    <div class="card-header">

        <div class="card-header-icon">
            <i class="fas fa-film"></i>
        </div>

        <div>

            <h2>Informations de la série</h2>

            <p>
                Les informations principales de votre production
            </p>

        </div>

    </div>


    <div class="card-body">

        <div class="form-grid">


            <!-- TITRE -->

            <div class="form-group">

                <label for="titre">
                    Titre de la série <span>*</span>
                </label>

                <div class="input-box">

                    <i class="fas fa-heading"></i>

                    <input
                        type="text"
                        id="titre"
                        name="titre"
                        class="form-control-modern"
                        placeholder="Ex : Les Héritiers"
                        value="<?= htmlspecialchars($serie['titre'] ?? '') ?>"
                        required
                    >

                </div>

            </div>



            <!-- TYPE -->

            <div class="form-group">

                <label for="type">
                    Type de production <span>*</span>
                </label>

                <div class="input-box">

                    <i class="fas fa-layer-group"></i>

                    <select
                        id="type"
                        name="type"
                        class="form-control-modern"
                        required
                    >

                        <option value="">
                            Sélectionnez un type
                        </option>

                        <?php

                        $types = [
                            'Film',
                            'Série TV',
                            'Documentaire'
                        ];

                        foreach ($types as $t):

                            $selected =
                                ($serie['type'] ?? '') === $t
                                    ? 'selected'
                                    : '';

                        ?>

                            <option
                                value="<?= htmlspecialchars($t) ?>"
                                <?= $selected ?>
                            >
                                <?= htmlspecialchars($t) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>



            <!-- DESCRIPTION -->

            <div class="form-group full">

                <label for="description">
                    Synopsis / présentation <span>*</span>
                </label>

                <textarea
                    id="description"
                    name="description"
                    class="form-control-modern description-field"
                    maxlength="2000"
                    placeholder="Présentez brièvement l'histoire, l'univers et le concept de la série..."
                    required
                ><?= htmlspecialchars($serie['description'] ?? '') ?></textarea>

                <div class="textarea-footer">

                    <span>
                        <i class="fas fa-align-left"></i>
                        Présentation de la production
                    </span>

                    <span id="descriptionCount">
                        0 caractère
                    </span>

                </div>

            </div>


        </div>

    </div>

</section>



<!-- =========================================================
     IDENTITE VISUELLE
========================================================= -->

<section class="form-card">

    <div class="card-header">

        <div class="card-header-icon">
            <i class="fas fa-image"></i>
        </div>

        <div>

            <h2>Identité visuelle</h2>

            <p>
                Ajoutez l'affiche officielle de votre série
            </p>

        </div>

    </div>


    <div class="card-body">


        <input
            type="file"
            id="photo"
            name="photo"
            accept="image/png,image/jpeg,image/jpg"
            <?= $serieId ? '' : 'required' ?>
            hidden
        >


        <div class="upload-wrapper">


            <!-- PREVIEW -->

            <div class="poster-preview"
                 id="posterPreview">

                <?php if (!empty($serie['logo'])): ?>

                    <img
                        src="../uploads/series/<?= htmlspecialchars($serie['logo']) ?>"
                        alt="<?= htmlspecialchars($serie['titre'] ?? 'Affiche') ?>"
                    >

                <?php else: ?>

                    <div class="poster-placeholder">

                        <i class="fas fa-film"></i>

                        <span>
                            Aperçu de l'affiche
                        </span>

                    </div>

                <?php endif; ?>

            </div>



            <!-- UPLOAD -->

            <label for="photo"
                   class="upload-content">

                <div class="upload-icon">

                    <i class="fas fa-cloud-upload-alt"></i>

                </div>

                <strong>
                    Ajouter l'affiche de la série
                </strong>

                <span>
                    Cliquez ici pour sélectionner une image
                </span>

                <small class="file-name"
                       id="file-name">

                    <?= !empty($serie['logo'])
                        ? htmlspecialchars($serie['logo'])
                        : 'Aucun fichier sélectionné'
                    ?>

                </small>

            </label>


        </div>

    </div>

</section>



<!-- =========================================================
     BUDGET
========================================================= -->

<section class="form-card">

    <div class="card-header">

        <div class="card-header-icon">

            <i class="fas fa-wallet"></i>

        </div>

        <div>

            <h2>Budget prévisionnel</h2>

            <p>
                Définissez les dépenses prévues pour la production
            </p>

        </div>

    </div>


    <div class="card-body">


        <div class="budget-intro">

            <div class="budget-intro-icon">

                <i class="fas fa-calculator"></i>

            </div>

            <div>

                <strong>
                    Budget automatique
                </strong>

                <p>
                    Le montant total est calculé automatiquement
                    à partir des différents postes.
                </p>

            </div>

        </div>



        <div class="budget-grid">


            <!-- TRANSPORT -->

            <div class="budget-item">

                <div class="budget-icon">
                    <i class="fas fa-car"></i>
                </div>

                <div class="budget-info">

                    <label>
                        Transport
                    </label>

                    <div class="money-input">

                        <input
                            type="number"
                            name="transport"
                            class="budget-input"
                            min="0"
                            step="1"
                            placeholder="0"
                            value="<?= htmlspecialchars($serie['transport'] ?? 0) ?>"
                        >

                        <span>FCFA</span>

                    </div>

                </div>

            </div>



            <!-- DECORS -->

            <div class="budget-item">

                <div class="budget-icon">
                    <i class="fas fa-paint-roller"></i>
                </div>

                <div class="budget-info">

                    <label>
                        Décors
                    </label>

                    <div class="money-input">

                        <input
                            type="number"
                            name="decors"
                            class="budget-input"
                            min="0"
                            step="1"
                            placeholder="0"
                            value="<?= htmlspecialchars($serie['decors'] ?? 0) ?>"
                        >

                        <span>FCFA</span>

                    </div>

                </div>

            </div>



            <!-- ACTEURS -->

            <div class="budget-item">

                <div class="budget-icon">
                    <i class="fas fa-users"></i>
                </div>

                <div class="budget-info">

                    <label>
                        Règlement acteurs
                    </label>

                    <div class="money-input">

                        <input
                            type="number"
                            name="reglement_acteurs"
                            class="budget-input"
                            min="0"
                            step="1"
                            placeholder="0"
                            value="<?= htmlspecialchars($serie['reglement_acteurs'] ?? 0) ?>"
                        >

                        <span>FCFA</span>

                    </div>

                </div>

            </div>



            <!-- ACCESSOIRES -->

            <div class="budget-item">

                <div class="budget-icon">
                    <i class="fas fa-toolbox"></i>
                </div>

                <div class="budget-info">

                    <label>
                        Accessoires
                    </label>

                    <div class="money-input">

                        <input
                            type="number"
                            name="accessoires"
                            class="budget-input"
                            min="0"
                            step="1"
                            placeholder="0"
                            value="<?= htmlspecialchars($serie['accessoires'] ?? 0) ?>"
                        >

                        <span>FCFA</span>

                    </div>

                </div>

            </div>



            <!-- HMC -->

            <div class="budget-item">

                <div class="budget-icon">
                    <i class="fas fa-magic"></i>
                </div>

                <div class="budget-info">

                    <label>
                        HMC
                    </label>

                    <div class="money-input">

                        <input
                            type="number"
                            name="hmc"
                            class="budget-input"
                            min="0"
                            step="1"
                            placeholder="0"
                            value="<?= htmlspecialchars($serie['hmc'] ?? 0) ?>"
                        >

                        <span>FCFA</span>

                    </div>

                </div>

            </div>



            <!-- CARBURANT -->

            <div class="budget-item">

                <div class="budget-icon">
                    <i class="fas fa-gas-pump"></i>
                </div>

                <div class="budget-info">

                    <label>
                        Carburant
                    </label>

                    <div class="money-input">

                        <input
                            type="number"
                            name="carburant"
                            class="budget-input"
                            min="0"
                            step="1"
                            placeholder="0"
                            value="<?= htmlspecialchars($serie['carburant'] ?? 0) ?>"
                        >

                        <span>FCFA</span>

                    </div>

                </div>

            </div>



            <!-- PHARMACIE -->

            <div class="budget-item">

                <div class="budget-icon">
                    <i class="fas fa-first-aid"></i>
                </div>

                <div class="budget-info">

                    <label>
                        Pharmacie
                    </label>

                    <div class="money-input">

                        <input
                            type="number"
                            name="pharmacie"
                            class="budget-input"
                            min="0"
                            step="1"
                            placeholder="0"
                            value="<?= htmlspecialchars($serie['pharmacie'] ?? 0) ?>"
                        >

                        <span>FCFA</span>

                    </div>

                </div>

            </div>



            <!-- RECEPTIONS -->

            <div class="budget-item">

                <div class="budget-icon">
                    <i class="fas fa-utensils"></i>
                </div>

                <div class="budget-info">

                    <label>
                        Réceptions
                    </label>

                    <div class="money-input">

                        <input
                            type="number"
                            name="receptions"
                            class="budget-input"
                            min="0"
                            step="1"
                            placeholder="0"
                            value="<?= htmlspecialchars($serie['receptions'] ?? 0) ?>"
                        >

                        <span>FCFA</span>

                    </div>

                </div>

            </div>



            <!-- AUTRES -->

            <div class="budget-item">

                <div class="budget-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>

                <div class="budget-info">

                    <label>
                        Autres achats
                    </label>

                    <div class="money-input">

                        <input
                            type="number"
                            name="autres_achats"
                            class="budget-input"
                            min="0"
                            step="1"
                            placeholder="0"
                            value="<?= htmlspecialchars($serie['autres_achats'] ?? 0) ?>"
                        >

                        <span>FCFA</span>

                    </div>

                </div>

            </div>


        </div>



        <!-- TOTAL -->

        <div class="budget-total">

            <div class="total-left">

                <div class="total-icon">

                    <i class="fas fa-calculator"></i>

                </div>

                <div>

                    <span class="total-label">
                        Budget prévisionnel total
                    </span>

                    <span class="total-description">
                        Somme de tous les postes
                    </span>

                </div>

            </div>


            <div class="total-value">

                <span id="budgetTotalDisplay">
                    0
                </span>

                <small>
                    FCFA
                </small>

            </div>

        </div>


        <input
            type="hidden"
            id="budget_total"
            name="budget"
            value="<?= htmlspecialchars($serie['budget'] ?? 0) ?>"
        >


    </div>

</section>



<!-- =========================================================
     ACTIONS
========================================================= -->

<div class="form-actions">

    <a href="javascript:history.back()"
       class="btn btn-cancel">

        <i class="fas fa-arrow-left"></i>

        Annuler

    </a>


    <button
        type="submit"
        class="btn btn-submit"
        id="submitBtn"
    >

        <i class="fas <?= $serieId ? 'fa-save' : 'fa-plus' ?>"></i>

        <span>
            <?= $serieId
                ? 'Enregistrer les modifications'
                : 'Créer la série'
            ?>
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
         APERÇU
    ====================================================== -->

    <div class="preview-card">

        <div class="preview-header">

            <h3>
                <i class="fas fa-eye"></i>
                Aperçu de la série
            </h3>

            <p>
                Mise à jour en temps réel
            </p>

        </div>


        <div class="preview-poster"
             id="previewPoster">

            <?php if (!empty($serie['logo'])): ?>

                <img
                    src="../uploads/series/<?= htmlspecialchars($serie['logo']) ?>"
                    alt="Affiche"
                >

            <?php else: ?>

                <div class="preview-poster-placeholder">

                    <i class="fas fa-film"></i>

                </div>

            <?php endif; ?>

        </div>


        <div class="preview-content">

            <h2 class="preview-title"
                id="previewTitle">

                <?= htmlspecialchars(
                    $serie['titre'] ?? 'Votre série'
                ) ?>

            </h2>


            <span class="preview-type"
                  id="previewType">

                <?= htmlspecialchars(
                    $serie['type'] ?? 'Série TV'
                ) ?>

            </span>


            <div class="preview-budget">

                <span class="preview-budget-label">
                    Budget prévisionnel
                </span>

                <div class="preview-budget-value">

                    <span id="previewBudget">
                        0
                    </span>

                    <small>
                        FCFA
                    </small>

                </div>

            </div>

        </div>

    </div>



    <!-- =====================================================
         RESUME BUDGET
    ====================================================== -->

    <div class="form-card summary-card">

        <div class="summary-title">

            <i class="fas fa-chart-pie"></i>

            Répartition du budget

        </div>


        <div class="summary-list">


            <div class="summary-row">

                <span>Transport</span>

                <strong data-summary="transport">
                    0
                </strong>

            </div>


            <div class="summary-row">

                <span>Décors</span>

                <strong data-summary="decors">
                    0
                </strong>

            </div>


            <div class="summary-row">

                <span>Acteurs</span>

                <strong data-summary="reglement_acteurs">
                    0
                </strong>

            </div>


            <div class="summary-row">

                <span>Accessoires</span>

                <strong data-summary="accessoires">
                    0
                </strong>

            </div>


            <div class="summary-row">

                <span>HMC</span>

                <strong data-summary="hmc">
                    0
                </strong>

            </div>


            <div class="summary-row">

                <span>Carburant</span>

                <strong data-summary="carburant">
                    0
                </strong>

            </div>


            <div class="summary-row">

                <span>Pharmacie</span>

                <strong data-summary="pharmacie">
                    0
                </strong>

            </div>


            <div class="summary-row">

                <span>Réceptions</span>

                <strong data-summary="receptions">
                    0
                </strong>

            </div>


            <div class="summary-row">

                <span>Autres achats</span>

                <strong data-summary="autres_achats">
                    0
                </strong>

            </div>


        </div>


        <div class="summary-total">

            <span>
                TOTAL
            </span>

            <strong>
                <span id="summaryTotal">
                    0
                </span>
                FCFA
            </strong>

        </div>

    </div>



    <!-- =====================================================
         CONSEIL
    ====================================================== -->

    <div class="tip-card">

        <div class="tip-card-header">

            <i class="fas fa-lightbulb"></i>

            <strong>
                Conseil de production
            </strong>

        </div>


        <p>

            Commencez par définir clairement le titre,
            le concept et la description de la série.
            Vous pourrez ensuite gérer les acteurs,
            épisodes, équipes et dépenses depuis
            la fiche de production.

        </p>

    </div>


</aside>


</div>

</div>

</section>



<script>

document.addEventListener('DOMContentLoaded', function () {


    /* =====================================================
       ELEMENTS
    ===================================================== */

    const form = document.getElementById('serieForm');

    const titleInput = document.getElementById('titre');

    const typeInput = document.getElementById('type');

    const descriptionInput =
        document.getElementById('description');

    const descriptionCount =
        document.getElementById('descriptionCount');

    const photoInput =
        document.getElementById('photo');

    const fileName =
        document.getElementById('file-name');

    const posterPreview =
        document.getElementById('posterPreview');

    const previewPoster =
        document.getElementById('previewPoster');

    const previewTitle =
        document.getElementById('previewTitle');

    const previewType =
        document.getElementById('previewType');

    const previewBudget =
        document.getElementById('previewBudget');

    const summaryTotal =
        document.getElementById('summaryTotal');

    const budgetTotalDisplay =
        document.getElementById('budgetTotalDisplay');

    const budgetTotal =
        document.getElementById('budget_total');

    const budgetInputs =
        document.querySelectorAll('.budget-input');



    /* =====================================================
       FORMATAGE
    ===================================================== */

    function formatNumber(value) {

        return new Intl.NumberFormat('fr-FR')
            .format(Math.round(value));

    }



    /* =====================================================
       TITRE
    ===================================================== */

    function updateTitle() {

        if (!titleInput) {
            return;
        }

        const title =
            titleInput.value.trim();

        previewTitle.textContent =
            title || 'Votre série';

    }

    titleInput.addEventListener(
        'input',
        updateTitle
    );



    /* =====================================================
       TYPE
    ===================================================== */

    function updateType() {

        if (!typeInput) {
            return;
        }

        previewType.textContent =
            typeInput.value || 'Série TV';

    }

    typeInput.addEventListener(
        'change',
        updateType
    );



    /* =====================================================
       DESCRIPTION
    ===================================================== */

    function updateDescription() {

        if (!descriptionInput) {
            return;
        }

        const length =
            descriptionInput.value.length;

        descriptionCount.textContent =
            length +
            ' caractère' +
            (length > 1 ? 's' : '');

    }

    descriptionInput.addEventListener(
        'input',
        updateDescription
    );

    updateDescription();



    /* =====================================================
       BUDGET
    ===================================================== */

    function calculerBudgetTotal() {

        let total = 0;


        budgetInputs.forEach(function (input) {

            let value =
                parseFloat(input.value) || 0;


            if (value < 0) {

                value = 0;

                input.value = 0;

            }


            total += value;


            const summary =
                document.querySelector(
                    '[data-summary="' +
                    input.name +
                    '"]'
                );


            if (summary) {

                summary.textContent =
                    formatNumber(value);

            }

        });


        budgetTotal.value =
            total;


        budgetTotalDisplay.textContent =
            formatNumber(total);


        summaryTotal.textContent =
            formatNumber(total);


        previewBudget.textContent =
            formatNumber(total);

    }


    budgetInputs.forEach(function (input) {

        input.addEventListener(
            'input',
            calculerBudgetTotal
        );

        input.addEventListener(
            'change',
            calculerBudgetTotal
        );

    });


    calculerBudgetTotal();



    /* =====================================================
       IMAGE
    ===================================================== */

    if (photoInput) {

        photoInput.addEventListener(
            'change',
            function () {

                if (
                    !this.files ||
                    !this.files.length
                ) {

                    return;

                }


                const file =
                    this.files[0];


                fileName.textContent =
                    file.name;


                /*
                 * Aperçu de l'image
                 */

                const reader =
                    new FileReader();


                reader.onload =
                    function (event) {

                        const html = `
                            <img
                                src="${event.target.result}"
                                alt="Aperçu de l'affiche"
                            >
                        `;

                        posterPreview.innerHTML =
                            html;

                        previewPoster.innerHTML =
                            html;

                    };


                reader.readAsDataURL(file);

            }
        );

    }



    /* =====================================================
       VALIDATION
    ===================================================== */

    if (form) {

        form.addEventListener(
            'submit',
            function (event) {

                /*
                 * Recalcul avant envoi
                 */

                calculerBudgetTotal();


                /*
                 * Vérification du titre
                 */

                if (
                    titleInput &&
                    titleInput.value.trim() === ''
                ) {

                    event.preventDefault();

                    titleInput.focus();

                    alert(
                        'Veuillez renseigner le titre de la série.'
                    );

                    return;

                }


                /*
                 * Vérification description
                 */

                if (
                    descriptionInput &&
                    descriptionInput.value.trim() === ''
                ) {

                    event.preventDefault();

                    descriptionInput.focus();

                    alert(
                        'Veuillez renseigner la présentation de la série.'
                    );

                    return;

                }


                /*
                 * Eviter double clic
                 */

                const submitBtn =
                    document.getElementById(
                        'submitBtn'
                    );


                if (submitBtn) {

                    submitBtn.disabled = true;

                    submitBtn.style.opacity =
                        '0.7';

                    submitBtn.style.cursor =
                        'not-allowed';


                    const buttonText =
                        submitBtn.querySelector('span');


                    if (buttonText) {

                        buttonText.textContent =
                            'Enregistrement...';

                    }

                }

            }
        );

    }



    /* =====================================================
       TOAST SUCCES
    ===================================================== */

    <?php if (
        isset($_GET['reussi']) &&
        $_GET['reussi'] == 1
    ): ?>

    const toast =
        document.createElement('div');

    toast.className =
        'success-toast';


    toast.innerHTML = `

        <div class="success-toast-icon">

            <i class="fas fa-check"></i>

        </div>


        <div>

            <strong>
                Opération réussie
            </strong>

            <span>
                Série
                <?= $serieId
                    ? 'modifiée'
                    : 'ajoutée'
                ?>
                avec succès.
            </span>

        </div>


        <button
            type="button"
            class="toast-close"
        >

            <i class="fas fa-times"></i>

        </button>

    `;


    document.body.appendChild(toast);


    setTimeout(function () {

        toast.classList.add('show');

    }, 100);


    const closeButton =
        toast.querySelector('.toast-close');


    if (closeButton) {

        closeButton.addEventListener(
            'click',
            function () {

                toast.classList.remove('show');

                setTimeout(
                    () => toast.remove(),
                    400
                );

            }
        );

    }


    setTimeout(function () {

        toast.classList.remove('show');

        setTimeout(
            () => toast.remove(),
            400
        );

    }, 5000);


    <?php endif; ?>


});

</script>

<?php include '../includes/footer.php'; ?>