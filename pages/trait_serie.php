<?php

session_start();

include '../config/fonction.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add_serie');
    exit;
}


/*
|--------------------------------------------------------------------------
| IDENTIFICATION
|--------------------------------------------------------------------------
*/

$serieId = !empty($_POST['serie_id'])
    ? (int) $_POST['serie_id']
    : null;


/*
|--------------------------------------------------------------------------
| INFORMATIONS GENERALES
|--------------------------------------------------------------------------
*/

$titre = trim($_POST['titre'] ?? '');

$type = trim($_POST['type'] ?? '');

$description = trim($_POST['description'] ?? '');


/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if ($titre === '') {

    $_SESSION['error'] = 'Le titre de la série est obligatoire.';

    header('Location: add_serie');

    exit;
}


if ($type === '') {

    $_SESSION['error'] = 'Le type de production est obligatoire.';

    header('Location: add_serie');

    exit;
}


/*
|--------------------------------------------------------------------------
| BUDGETS
|--------------------------------------------------------------------------
*/

$transport = max(0, (float) ($_POST['transport'] ?? 0));

$decors = max(0, (float) ($_POST['decors'] ?? 0));

$reglement_acteurs = max(
    0,
    (float) ($_POST['reglement_acteurs'] ?? 0)
);

$accessoires = max(
    0,
    (float) ($_POST['accessoires'] ?? 0)
);

$hmc = max(
    0,
    (float) ($_POST['hmc'] ?? 0)
);

$carburant = max(
    0,
    (float) ($_POST['carburant'] ?? 0)
);

$pharmacie = max(
    0,
    (float) ($_POST['pharmacie'] ?? 0)
);

$receptions = max(
    0,
    (float) ($_POST['receptions'] ?? 0)
);

$autres_achats = max(
    0,
    (float) ($_POST['autres_achats'] ?? 0)
);


/*
|--------------------------------------------------------------------------
| CALCUL DU BUDGET TOTAL
|--------------------------------------------------------------------------
*/

$budget =
      $transport
    + $decors
    + $reglement_acteurs
    + $accessoires
    + $hmc
    + $carburant
    + $pharmacie
    + $receptions
    + $autres_achats;


/*
|--------------------------------------------------------------------------
| GESTION DE LA PHOTO
|--------------------------------------------------------------------------
*/

$logo = null;

if (
    isset($_FILES['photo'])
    && $_FILES['photo']['error'] === UPLOAD_ERR_OK
) {

    $allowed = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp'
    ];

    $fileName = $_FILES['photo']['name'];

    $fileTmp = $_FILES['photo']['tmp_name'];

    $fileExt = strtolower(
        pathinfo($fileName, PATHINFO_EXTENSION)
    );


    if (!in_array($fileExt, $allowed, true)) {

        $_SESSION['error'] =
            'Format de fichier non autorisé.';

        header('Location: add_serie');

        exit;
    }


    $newFileName =
        uniqid('serie_', true)
        . '.'
        . $fileExt;


    $uploadDir = '../uploads/series/';


    if (!is_dir($uploadDir)) {

        mkdir(
            $uploadDir,
            0777,
            true
        );
    }


    $destination =
        $uploadDir . $newFileName;


    if (
        !move_uploaded_file(
            $fileTmp,
            $destination
        )
    ) {

        $_SESSION['error'] =
            "Erreur lors de l'upload de l'image.";

        header('Location: add_serie');

        exit;
    }


    $logo = $newFileName;
}


/*
|--------------------------------------------------------------------------
| MODIFICATION
|--------------------------------------------------------------------------
*/

if ($serieId) {

    $result = modifierSerie(
        $serieId,
        $titre,
        $type,
        $budget,
        $description,
        $logo,

        $transport,
        $decors,
        $reglement_acteurs,
        $accessoires,
        $hmc,
        $carburant,
        $pharmacie,
        $receptions,
        $autres_achats
    );

}


/*
|--------------------------------------------------------------------------
| AJOUT
|--------------------------------------------------------------------------
*/

else {

    if ($logo === null) {

        $_SESSION['error'] =
            'Veuillez choisir une image pour la série.';

        header('Location: add_serie');

        exit;
    }


    $result = ajouterSerie(
        $titre,
        $type,
        $budget,
        $description,
        $logo,

        $transport,
        $decors,
        $reglement_acteurs,
        $accessoires,
        $hmc,
        $carburant,
        $pharmacie,
        $receptions,
        $autres_achats
    );
}


/*
|--------------------------------------------------------------------------
| RESULTAT
|--------------------------------------------------------------------------
*/

if (!empty($result['success'])) {

    $_SESSION['success'] =
        $serieId
            ? 'Série modifiée avec succès.'
            : 'Série ajoutée avec succès.';


    if ($serieId) {

        header(
            "Location: add_serie?reussi=1&id=" . $serieId
        );

    } else {

        header(
            "Location: add_serie?reussi=1"
        );
    }

    exit;
}


/*
|--------------------------------------------------------------------------
| ERREUR
|--------------------------------------------------------------------------
*/

$_SESSION['error'] =
    $result['message']
    ?? 'Une erreur est survenue.';


if ($serieId) {

    header(
        "Location: add_serie?id=" . $serieId
    );

} else {

    header('Location: add_serie');
}

exit;