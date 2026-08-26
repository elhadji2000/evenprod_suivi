<?php 

include '../../config/fonction.php';
// Redirection si l'utilisateur n'a pas mis à jour son mot de passe

/*
|--------------------------------------------------------------------------
| RÉCUPÉRATION DES ACTEURS
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id,
        nom,
        prenom,
        date_naissance,
        cv_file,
        adresse,
        contact,
        photo
    FROM acteurs
    ORDER BY id DESC
";

$result = mysqli_query($connexion, $sql);

$acteurs = [];

if ($result && mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        $acteurs[] = $row;

    }

}

?>

<head>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
    >

</head>

<?php include '../../includes/header.php'; ?>


<style>

/* =========================================================
   VARIABLES
========================================================= */

:root {

    --primary: #171717;
    --primary-hover: #000000;

    --accent: #e50914;

    --background: #f5f6f8;

    --white: #ffffff;

    --text: #171717;

    --muted: #737373;

    --border: #e5e7eb;

    --success: #16a34a;

    --danger: #dc2626;

    --warning: #d97706;

    --radius: 18px;

    --shadow:
        0 10px 30px rgba(0, 0, 0, .06);

}


/* =========================================================
   PAGE
========================================================= */

.actors-page {

    min-height: 100vh;

    background: var(--background);

    padding: 25px 0 50px;

}


.actors-container {

    max-width: 1500px;

    margin: auto;

    padding: 0 25px;

}


/* =========================================================
   HEADER
========================================================= */

.actors-header {

    background: var(--white);

    border: 1px solid var(--border);

    border-radius: var(--radius);

    padding: 24px 28px;

    margin-bottom: 22px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    box-shadow: var(--shadow);

}


.actors-header-left {

    display: flex;

    align-items: center;

    gap: 16px;

}


.actors-header-icon {

    width: 58px;

    height: 58px;

    flex: 0 0 58px;

    border-radius: 16px;

    background: var(--primary);

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 22px;

}


.actors-breadcrumb {

    font-size: 10px;

    font-weight: 900;

    letter-spacing: 1px;

    color: #a1a1aa;

    margin-bottom: 5px;

}


.actors-header h1 {

    margin: 0;

    font-size: 25px;

    font-weight: 900;

    letter-spacing: -.5px;

}


.actors-header p {

    margin: 5px 0 0;

    font-size: 13px;

    color: var(--muted);

}


/* =========================================================
   HEADER ACTION
========================================================= */

.btn-add {

    height: 46px;

    padding: 0 18px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    border-radius: 11px;

    background: var(--primary);

    color: white;

    text-decoration: none;

    font-size: 12px;

    font-weight: 800;

    transition: .2s;

}


.btn-add:hover {

    background: #000;

    color: white;

    transform: translateY(-1px);

    box-shadow:
        0 8px 20px rgba(0, 0, 0, .12);

}


/* =========================================================
   STATS
========================================================= */

.actors-stats {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 15px;

    margin-bottom: 22px;

}


.stat-card {

    background: white;

    border: 1px solid var(--border);

    border-radius: 15px;

    padding: 17px 19px;

    display: flex;

    align-items: center;

    gap: 13px;

    box-shadow: var(--shadow);

}


.stat-icon {

    width: 42px;

    height: 42px;

    border-radius: 11px;

    background: #f4f4f5;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 16px;

}


.stat-info span {

    display: block;

    color: #999;

    font-size: 10px;

    font-weight: 700;

}


.stat-info strong {

    display: block;

    margin-top: 2px;

    font-size: 18px;

    font-weight: 900;

}


/* =========================================================
   MAIN GRID
========================================================= */

.actors-layout {

    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        360px;

    gap: 22px;

    align-items: start;

}


/* =========================================================
   LIST CARD
========================================================= */

.list-card {

    background: white;

    border: 1px solid var(--border);

    border-radius: var(--radius);

    box-shadow: var(--shadow);

    overflow: hidden;

}


.list-header {

    padding: 20px 22px;

    border-bottom: 1px solid var(--border);

}


.list-header-top {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    margin-bottom: 16px;

}


.list-title {

    display: flex;

    align-items: center;

    gap: 10px;

}


.list-title-icon {

    width: 38px;

    height: 38px;

    border-radius: 10px;

    background: #f4f4f5;

    display: flex;

    align-items: center;

    justify-content: center;

}


.list-title h2 {

    margin: 0;

    font-size: 15px;

    font-weight: 900;

}


.list-title p {

    margin: 3px 0 0;

    font-size: 10px;

    color: #999;

}


/* =========================================================
   SEARCH
========================================================= */

.search-box {

    position: relative;

}


.search-box i {

    position: absolute;

    left: 14px;

    top: 50%;

    transform: translateY(-50%);

    color: #a1a1aa;

    font-size: 13px;

}


.search-box input {

    width: 100%;

    height: 43px;

    border: 1px solid var(--border);

    border-radius: 11px;

    background: #fafafa;

    padding: 0 40px 0 38px;

    outline: none;

    font-size: 12px;

    transition: .2s;

}


.search-box input:focus {

    background: white;

    border-color: #a1a1aa;

    box-shadow:
        0 0 0 3px rgba(0,0,0,.04);

}


.search-clear {

    position: absolute;

    right: 10px;

    top: 50%;

    transform: translateY(-50%);

    width: 25px;

    height: 25px;

    border: 0;

    background: transparent;

    color: #a1a1aa;

    display: none;

    align-items: center;

    justify-content: center;

    cursor: pointer;

}


/* =========================================================
   ACTORS GRID
========================================================= */

.actors-grid {

    padding: 20px;

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 14px;

}


/* =========================================================
   ACTOR CARD
========================================================= */

.actor-card {

    position: relative;

    border: 1px solid var(--border);

    border-radius: 14px;

    background: white;

    padding: 13px;

    display: flex;

    align-items: center;

    gap: 13px;

    cursor: pointer;

    transition: .2s;

}


.actor-card:hover {

    border-color: #c4c4c7;

    transform: translateY(-2px);

    box-shadow:
        0 8px 20px rgba(0,0,0,.06);

}


.actor-card.active {

    border-color: #171717;

    background: #fafafa;

    box-shadow:
        0 0 0 2px rgba(23,23,23,.06);

}


/* =========================================================
   ACTOR PHOTO
========================================================= */

.actor-photo {

    width: 65px;

    height: 76px;

    flex: 0 0 65px;

    border-radius: 11px;

    overflow: hidden;

    background: #f4f4f5;

}


.actor-photo img {

    width: 100%;

    height: 100%;

    object-fit: cover;

}


.actor-photo-placeholder {

    width: 100%;

    height: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #c4c4c7;

    font-size: 24px;

}


/* =========================================================
   ACTOR INFO
========================================================= */

.actor-info {

    min-width: 0;

}


.actor-info h3 {

    margin: 0 0 5px;

    font-size: 13px;

    font-weight: 900;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

}


.actor-info p {

    margin: 0;

    color: #999;

    font-size: 10px;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

}


.actor-phone {

    display: flex;

    align-items: center;

    gap: 5px;

    margin-top: 7px;

    color: #737373 !important;

}


.actor-phone i {

    font-size: 9px;

}


/* =========================================================
   SELECT INDICATOR
========================================================= */

.actor-selected {

    position: absolute;

    right: 10px;

    top: 10px;

    width: 20px;

    height: 20px;

    border-radius: 50%;

    background: #171717;

    color: white;

    display: none;

    align-items: center;

    justify-content: center;

    font-size: 8px;

}


.actor-card.active .actor-selected {

    display: flex;

}


/* =========================================================
   NO RESULT
========================================================= */

.no-result {

    grid-column: 1 / -1;

    text-align: center;

    padding: 50px 20px;

    color: #999;

}


.no-result i {

    font-size: 35px;

    margin-bottom: 12px;

    color: #c4c4c7;

}


.no-result strong {

    display: block;

    color: #52525b;

    font-size: 13px;

}


.no-result span {

    display: block;

    margin-top: 5px;

    font-size: 11px;

}


/* =========================================================
   DETAILS CARD
========================================================= */

.details-card {

    background: white;

    border: 1px solid var(--border);

    border-radius: var(--radius);

    box-shadow: var(--shadow);

    overflow: hidden;

    position: sticky;

    top: 20px;

}


.details-header {

    padding: 18px 20px;

    border-bottom: 1px solid var(--border);

    display: flex;

    align-items: center;

    gap: 11px;

}


.details-header-icon {

    width: 38px;

    height: 38px;

    border-radius: 10px;

    background: #f4f4f5;

    display: flex;

    align-items: center;

    justify-content: center;

}


.details-header h2 {

    margin: 0;

    font-size: 14px;

    font-weight: 900;

}


.details-header p {

    margin: 3px 0 0;

    font-size: 10px;

    color: #999;

}


/* =========================================================
   DETAILS CONTENT
========================================================= */

.details-content {

    padding: 20px;

}


/* =========================================================
   DETAIL PROFILE
========================================================= */

.details-profile {

    text-align: center;

    padding-bottom: 18px;

    border-bottom: 1px solid var(--border);

}


.details-photo {

    width: 110px;

    height: 130px;

    border-radius: 13px;

    overflow: hidden;

    background: #f4f4f5;

    margin: 0 auto 13px;

}


.details-photo img {

    width: 100%;

    height: 100%;

    object-fit: cover;

}


.details-photo-placeholder {

    height: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #c4c4c7;

    font-size: 34px;

}


.details-name {

    margin: 0;

    font-size: 17px;

    font-weight: 900;

}


.details-subtitle {

    margin: 5px 0 0;

    color: #999;

    font-size: 10px;

}


/* =========================================================
   DETAILS LIST
========================================================= */

.details-list {

    margin-top: 16px;

}


.detail-row {

    display: flex;

    align-items: flex-start;

    gap: 11px;

    padding: 10px 0;

    border-bottom: 1px solid #f1f1f2;

}


.detail-row:last-child {

    border-bottom: 0;

}


.detail-icon {

    width: 30px;

    height: 30px;

    flex: 0 0 30px;

    border-radius: 8px;

    background: #f4f4f5;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #52525b;

    font-size: 11px;

}


.detail-label {

    display: block;

    color: #a1a1aa;

    font-size: 9px;

    margin-bottom: 3px;

}


.detail-value {

    display: block;

    color: #27272a;

    font-size: 11px;

    font-weight: 700;

    word-break: break-word;

}


/* =========================================================
   ACTIONS
========================================================= */

.detail-actions {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 9px;

    margin-top: 18px;

}


.detail-btn {

    height: 42px;

    border-radius: 10px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    font-size: 10px;

    font-weight: 800;

    text-decoration: none;

    transition: .2s;

}


.btn-edit {

    background: #f4f4f5;

    color: #27272a;

}


.btn-edit:hover {

    background: #e4e4e7;

    color: #171717;

}


.btn-delete {

    background: #fef2f2;

    color: #dc2626;

}


.btn-delete:hover {

    background: #fee2e2;

    color: #b91c1c;

}


/* =========================================================
   CV BUTTON
========================================================= */

.cv-button {

    width: 100%;

    height: 40px;

    margin-top: 9px;

    border-radius: 10px;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    background: #171717;

    color: white;

    text-decoration: none;

    font-size: 10px;

    font-weight: 800;

    transition: .2s;

}


.cv-button:hover {

    background: #000;

    color: white;

}


/* =========================================================
   EMPTY DETAILS
========================================================= */

.empty-details {

    padding: 55px 20px;

    text-align: center;

    color: #999;

}


.empty-details-icon {

    width: 60px;

    height: 60px;

    border-radius: 15px;

    margin: 0 auto 14px;

    background: #f4f4f5;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 22px;

    color: #b4b4b8;

}


.empty-details strong {

    display: block;

    color: #52525b;

    font-size: 12px;

}


.empty-details p {

    margin: 6px auto 0;

    max-width: 230px;

    line-height: 1.6;

    font-size: 10px;

}


/* =========================================================
   FOOTER LIST
========================================================= */

.list-footer {

    padding: 13px 20px;

    border-top: 1px solid var(--border);

    background: #fafafa;

    display: flex;

    align-items: center;

    justify-content: space-between;

}


.list-footer span {

    font-size: 10px;

    color: #999;

}


.list-footer strong {

    color: #52525b;

}


/* =========================================================
   TOAST
========================================================= */

.toast-success {

    position: fixed;

    right: 25px;

    top: 80px;

    z-index: 9999;

    min-width: 280px;

    padding: 14px 17px;

    border-radius: 12px;

    background: #171717;

    color: white;

    box-shadow:
        0 15px 40px rgba(0,0,0,.2);

    display: flex;

    align-items: center;

    gap: 10px;

    font-size: 11px;

    font-weight: 700;

    opacity: 0;

    transform: translateY(-10px);

    transition: .3s;

}


.toast-success.show {

    opacity: 1;

    transform: translateY(0);

}


.toast-success i {

    color: #4ade80;

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1100px) {

    .actors-layout {

        grid-template-columns: 1fr;

    }


    .details-card {

        position: static;

    }

}


@media (max-width: 850px) {

    .actors-grid {

        grid-template-columns: 1fr;

    }


    .actors-stats {

        grid-template-columns:
            repeat(3, 1fr);

    }

}


@media (max-width: 700px) {

    .actors-page {

        padding: 15px 0 35px;

    }


    .actors-container {

        padding: 0 12px;

    }


    .actors-header {

        padding: 18px;

        flex-direction: column;

        align-items: stretch;

    }


    .actors-header-left {

        align-items: flex-start;

    }


    .actors-header-icon {

        width: 48px;

        height: 48px;

        flex-basis: 48px;

    }


    .actors-header h1 {

        font-size: 21px;

    }


    .btn-add {

        width: 100%;

    }


    .actors-stats {

        grid-template-columns: 1fr;

    }


    .list-header-top {

        align-items: flex-start;

    }


}


@media (max-width: 480px) {

    .actors-header-icon {

        display: none;

    }


    .actors-grid {

        padding: 12px;

    }


    .actor-card {

        padding: 11px;

    }


    .actor-photo {

        width: 58px;

        height: 68px;

        flex-basis: 58px;

    }


    .details-content {

        padding: 15px;

    }


    .toast-success {

        left: 12px;

        right: 12px;

        min-width: auto;

    }

}

</style>


<section class="actors-page">

<div class="actors-container">


<!-- =========================================================
     HEADER
========================================================= -->

<header class="actors-header">


    <div class="actors-header-left">


        <div class="actors-header-icon">

            <i class="fas fa-users"></i>

        </div>


        <div>

            <div class="actors-breadcrumb">

                EVENPROD / PRODUCTIONS / ACTEURS

            </div>


            <h1>

                Gestion des acteurs

            </h1>


            <p>

                Gérez les talents et les profils de votre
                maison de production.

            </p>

        </div>


    </div>



    <a
        href="add_act"
        class="btn-add"
    >

        <i class="fas fa-user-plus"></i>

        Ajouter un acteur

    </a>


</header>



<!-- =========================================================
     STATISTIQUES
========================================================= -->

<div class="actors-stats">


    <div class="stat-card">


        <div class="stat-icon">

            <i class="fas fa-users"></i>

        </div>


        <div class="stat-info">

            <span>

                Total acteurs

            </span>


            <strong>

                <?= count($acteurs) ?>

            </strong>

        </div>


    </div>



    <div class="stat-card">


        <div class="stat-icon">

            <i class="fas fa-file-pdf"></i>

        </div>


        <div class="stat-info">

            <span>

                CV disponibles

            </span>


            <strong>

                <?php

                $totalCV = 0;

                foreach ($acteurs as $a) {

                    if (!empty($a['cv_file'])) {

                        $totalCV++;

                    }

                }

                echo $totalCV;

                ?>

            </strong>

        </div>


    </div>



    <div class="stat-card">


        <div class="stat-icon">

            <i class="fas fa-camera"></i>

        </div>


        <div class="stat-info">

            <span>

                Profils avec photo

            </span>


            <strong>

                <?php

                $totalPhotos = 0;

                foreach ($acteurs as $a) {

                    if (!empty($a['photo'])) {

                        $totalPhotos++;

                    }

                }

                echo $totalPhotos;

                ?>

            </strong>

        </div>


    </div>


</div>



<!-- =========================================================
     LAYOUT
========================================================= -->

<div class="actors-layout">


<!-- =========================================================
     LISTE
========================================================= -->

<section class="list-card">


    <div class="list-header">


        <div class="list-header-top">


            <div class="list-title">


                <div class="list-title-icon">

                    <i class="fas fa-list"></i>

                </div>


                <div>

                    <h2>

                        Liste des acteurs

                    </h2>


                    <p>

                        Sélectionnez un acteur pour
                        consulter son profil

                    </p>

                </div>


            </div>


        </div>



        <div class="search-box">


            <i class="fas fa-search"></i>


            <input
                type="text"
                id="searchActor"
                placeholder="Rechercher par nom, prénom ou téléphone..."
                autocomplete="off"
            >


            <button
                type="button"
                class="search-clear"
                id="clearSearch"
            >

                <i class="fas fa-times"></i>

            </button>


        </div>


    </div>



    <div class="actors-grid" id="actorsGrid">


        <?php if (!empty($acteurs)): ?>


            <?php foreach ($acteurs as $index => $acteur): ?>


                <?php

                $fullName = trim(
                    $acteur['prenom'] . ' ' .
                    $acteur['nom']
                );


                $photo = !empty($acteur['photo'])
                    ? "../../uploads/photos/" .
                      $acteur['photo']
                    : null;

                ?>


                <div
                    class="actor-card"
                    data-id="<?= htmlspecialchars($acteur['id']) ?>"
                    data-search="<?= htmlspecialchars(
                        strtolower(
                            $fullName . ' ' .
                            $acteur['contact']
                        )
                    ) ?>"
                    data-nom="<?= htmlspecialchars($acteur['nom']) ?>"
                    data-prenom="<?= htmlspecialchars($acteur['prenom']) ?>"
                    data-date="<?= htmlspecialchars($acteur['date_naissance']) ?>"
                    data-adresse="<?= htmlspecialchars($acteur['adresse']) ?>"
                    data-contact="<?= htmlspecialchars($acteur['contact']) ?>"
                    data-cv="<?= htmlspecialchars($acteur['cv_file'] ?? '') ?>"
                    data-photo="<?= htmlspecialchars($acteur['photo'] ?? '') ?>"
                >


                    <div class="actor-photo">


                        <?php if ($photo): ?>


                            <img
                                src="<?= htmlspecialchars($photo) ?>"
                                alt="<?= htmlspecialchars($fullName) ?>"
                                loading="lazy"
                            >


                        <?php else: ?>


                            <div class="actor-photo-placeholder">

                                <i class="fas fa-user"></i>

                            </div>


                        <?php endif; ?>


                    </div>



                    <div class="actor-info">


                        <h3>

                            <?= htmlspecialchars(
                                $acteur['prenom']
                            ) ?>

                            <?= htmlspecialchars(
                                $acteur['nom']
                            ) ?>

                        </h3>


                        <p>

                            <?= !empty($acteur['date_naissance'])
                                ? htmlspecialchars(
                                    $acteur['date_naissance']
                                )
                                : 'Date de naissance non renseignée'
                            ?>

                        </p>


                        <p class="actor-phone">

                            <i class="fas fa-phone"></i>

                            <?= htmlspecialchars(
                                $acteur['contact']
                            ) ?>

                        </p>


                    </div>



                    <div class="actor-selected">

                        <i class="fas fa-check"></i>

                    </div>


                </div>


            <?php endforeach; ?>


        <?php else: ?>


            <div class="no-result">

                <i class="fas fa-users"></i>


                <strong>

                    Aucun acteur enregistré

                </strong>


                <span>

                    Commencez par ajouter votre premier acteur.

                </span>

            </div>


        <?php endif; ?>


        <!-- RECHERCHE VIDE -->

        <div
            class="no-result"
            id="noSearchResult"
            style="display:none;"
        >

            <i class="fas fa-search"></i>


            <strong>

                Aucun acteur trouvé

            </strong>


            <span>

                Essayez avec un autre nom ou numéro.

            </span>

        </div>


    </div>



    <div class="list-footer">


        <span>

            <strong id="visibleCount">

                <?= count($acteurs) ?>

            </strong>

            acteur(s) affiché(s)

        </span>


        <span>

            Total :
            <strong>

                <?= count($acteurs) ?>

            </strong>

        </span>


    </div>


</section>



<!-- =========================================================
     DETAILS
========================================================= -->

<aside class="details-card">


    <div class="details-header">


        <div class="details-header-icon">

            <i class="fas fa-id-card"></i>

        </div>


        <div>

            <h2>

                Profil de l'acteur

            </h2>


            <p>

                Informations détaillées

            </p>

        </div>


    </div>



    <!-- ÉTAT VIDE -->

    <div
        class="empty-details"
        id="emptyDetails"
    >


        <div class="empty-details-icon">

            <i class="fas fa-user"></i>

        </div>


        <strong>

            Aucun acteur sélectionné

        </strong>


        <p>

            Cliquez sur un acteur dans la liste
            pour afficher son profil complet.

        </p>


    </div>



    <!-- PROFIL -->

    <div
        class="details-content"
        id="actorDetails"
        style="display:none;"
    >


        <div class="details-profile">


            <div
                class="details-photo"
                id="detailsPhoto"
            >

                <div class="details-photo-placeholder">

                    <i class="fas fa-user"></i>

                </div>

            </div>


            <h2
                class="details-name"
                id="info-fullname"
            >

                ---

            </h2>


            <p
                class="details-subtitle"
                id="info-subtitle"
            >

                Acteur

            </p>


        </div>



        <div class="details-list">


            <!-- PRENOM -->

            <div class="detail-row">


                <div class="detail-icon">

                    <i class="fas fa-user"></i>

                </div>


                <div>

                    <span class="detail-label">

                        Prénom

                    </span>


                    <span
                        class="detail-value"
                        id="info-prenom"
                    >

                        ---

                    </span>

                </div>


            </div>



            <!-- NOM -->

            <div class="detail-row">


                <div class="detail-icon">

                    <i class="fas fa-user-tag"></i>

                </div>


                <div>

                    <span class="detail-label">

                        Nom

                    </span>


                    <span
                        class="detail-value"
                        id="info-nom"
                    >

                        ---

                    </span>

                </div>


            </div>



            <!-- DATE -->

            <div class="detail-row">


                <div class="detail-icon">

                    <i class="fas fa-calendar-alt"></i>

                </div>


                <div>

                    <span class="detail-label">

                        Date de naissance

                    </span>


                    <span
                        class="detail-value"
                        id="info-date"
                    >

                        ---

                    </span>

                </div>


            </div>



            <!-- ADRESSE -->

            <div class="detail-row">


                <div class="detail-icon">

                    <i class="fas fa-map-marker-alt"></i>

                </div>


                <div>

                    <span class="detail-label">

                        Adresse

                    </span>


                    <span
                        class="detail-value"
                        id="info-adresse"
                    >

                        ---

                    </span>

                </div>


            </div>



            <!-- CONTACT -->

            <div class="detail-row">


                <div class="detail-icon">

                    <i class="fas fa-phone"></i>

                </div>


                <div>

                    <span class="detail-label">

                        Téléphone

                    </span>


                    <span
                        class="detail-value"
                        id="info-contact"
                    >

                        ---

                    </span>

                </div>


            </div>


        </div>



        <!-- CV -->

        <a
            href="#"
            target="_blank"
            id="info-cv"
            class="cv-button"
            style="display:none;"
        >

            <i class="fas fa-file-pdf"></i>

            Consulter le CV

        </a>



        <!-- ACTIONS -->

        <div class="detail-actions">


            <a
                href="#"
                id="edit-link"
                class="detail-btn btn-edit"
            >

                <i class="fas fa-edit"></i>

                Modifier

            </a>


           <!--  <a
                href="#"
                id="delete-link"
                class="detail-btn btn-delete"
                onclick="
                    return confirm(
                        'Êtes-vous sûr de vouloir supprimer cet acteur ? Cette action est irréversible.'
                    );
                "
            >

                <i class="fas fa-trash-alt"></i>

                Supprimer

            </a> -->


        </div>


    </div>


</aside>


</div>


</div>

</section>



<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /* =====================================================
           VARIABLES
        ===================================================== */

        const actorCards =
            document.querySelectorAll('.actor-card');


        const searchInput =
            document.getElementById('searchActor');


        const clearSearch =
            document.getElementById('clearSearch');


        const noSearchResult =
            document.getElementById('noSearchResult');


        const visibleCount =
            document.getElementById('visibleCount');


        const emptyDetails =
            document.getElementById('emptyDetails');


        const actorDetails =
            document.getElementById('actorDetails');


        const infoPrenom =
            document.getElementById('info-prenom');


        const infoNom =
            document.getElementById('info-nom');


        const infoFullname =
            document.getElementById('info-fullname');


        const infoDate =
            document.getElementById('info-date');


        const infoAdresse =
            document.getElementById('info-adresse');


        const infoContact =
            document.getElementById('info-contact');


        const infoCV =
            document.getElementById('info-cv');


        const infoPhoto =
            document.getElementById('detailsPhoto');


        const editLink =
            document.getElementById('edit-link');


       /* const deleteLink = document.getElementById('delete-link');*/


        const urlBase =
            <?= json_encode($url_base) ?>;


        const redirectUrl =
            urlBase +
            'pages/acteur/liste.php';



        /* =====================================================
           SELECTION ACTEUR
        ===================================================== */

        actorCards.forEach(
            function (card) {


                card.addEventListener(
                    'click',
                    function () {


                        /*
                         * Retirer la sélection
                         */

                        actorCards.forEach(
                            function (item) {

                                item.classList.remove(
                                    'active'
                                );

                            }
                        );


                        /*
                         * Activer la carte
                         */

                        this.classList.add(
                            'active'
                        );


                        /*
                         * Récupération données
                         */

                        const id =
                            this.dataset.id;


                        const prenom =
                            this.dataset.prenom ||
                            '---';


                        const nom =
                            this.dataset.nom ||
                            '---';


                        const date =
                            this.dataset.date ||
                            'Non renseignée';


                        const adresse =
                            this.dataset.adresse ||
                            'Non renseignée';


                        const contact =
                            this.dataset.contact ||
                            'Non renseigné';


                        const cv =
                            this.dataset.cv || '';


                        const photo =
                            this.dataset.photo || '';



                        /* =====================================
                           INFOS
                        ===================================== */

                        infoPrenom.textContent =
                            prenom;


                        infoNom.textContent =
                            nom;


                        infoFullname.textContent =
                            (
                                prenom +
                                ' ' +
                                nom
                            ).trim();


                        infoDate.textContent =
                            date;


                        infoAdresse.textContent =
                            adresse;


                        infoContact.textContent =
                            contact;



                        /* =====================================
                           PHOTO
                        ===================================== */

                        if (photo) {


                            infoPhoto.innerHTML = `

                                <img
                                    src="../../uploads/photos/${encodeURIComponent(photo)}"
                                    alt="${prenom} ${nom}"
                                >

                            `;


                        } else {


                            infoPhoto.innerHTML = `

                                <div class="details-photo-placeholder">

                                    <i class="fas fa-user"></i>

                                </div>

                            `;

                        }



                        /* =====================================
                           CV
                        ===================================== */

                        if (cv) {


                            infoCV.style.display =
                                'flex';


                            infoCV.href =
                                '../../uploads/cv/' +
                                encodeURIComponent(cv);


                        } else {


                            infoCV.style.display =
                                'none';


                            infoCV.removeAttribute(
                                'href'
                            );

                        }



                        /* =====================================
                           MODIFICATION
                        ===================================== */

                        editLink.href =
                            urlBase +
                            'pages/acteur/add_act.php?id=' +
                            encodeURIComponent(id);



                        /* =====================================
                           SUPPRESSION
                        ===================================== 

                        deleteLink.href =
                            urlBase +
                            'public/appManager/delete.php?' +
                            'table=acteurs' +
                            '&id=' +
                            encodeURIComponent(id) +
                            '&redirect=' +
                            encodeURIComponent(
                                redirectUrl
                            );*/



                        /* =====================================
                           AFFICHAGE DETAILS
                        ===================================== */

                        emptyDetails.style.display =
                            'none';


                        actorDetails.style.display =
                            'block';

                    }
                );

            }
        );



        /* =====================================================
           RECHERCHE
        ===================================================== */

        searchInput.addEventListener(
            'input',
            function () {


                const search =
                    this.value
                        .toLowerCase()
                        .trim();


                let count = 0;



                actorCards.forEach(
                    function (card) {


                        const text =
                            card.dataset.search ||
                            '';


                        const match =
                            text.includes(search);


                        if (match) {


                            card.style.display =
                                'flex';


                            count++;


                        } else {


                            card.style.display =
                                'none';

                        }

                    }
                );



                /*
                 * Compteur
                 */

                visibleCount.textContent =
                    count;



                /*
                 * Bouton clear
                 */

                clearSearch.style.display =
                    search
                        ? 'flex'
                        : 'none';



                /*
                 * Aucun résultat
                 */

                noSearchResult.style.display =
                    (
                        count === 0 &&
                        search !== ''
                    )
                        ? 'block'
                        : 'none';

            }
        );



        /* =====================================================
           CLEAR SEARCH
        ===================================================== */

        clearSearch.addEventListener(
            'click',
            function () {


                searchInput.value = '';


                searchInput.dispatchEvent(
                    new Event('input')
                );


                searchInput.focus();

            }
        );


    }
);

</script>



<?php if (
    isset($_GET['success']) &&
    $_GET['success'] == 1
): ?>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        const toast =
            document.createElement('div');


        toast.className =
            'toast-success';


        toast.innerHTML = `

            <i class="fas fa-check-circle"></i>

            <span>

                Acteur supprimé avec succès.

            </span>

        `;


        document.body.appendChild(
            toast
        );


        setTimeout(
            function () {

                toast.classList.add(
                    'show'
                );

            },
            100
        );


        setTimeout(
            function () {

                toast.classList.remove(
                    'show'
                );


                setTimeout(
                    function () {

                        toast.remove();

                    },
                    300
                );

            },
            4000
        );


    }
);

</script>


<?php endif; ?>

<?php include '../../includes/footer.php'; ?>