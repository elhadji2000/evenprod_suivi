<?php

include '../../../config/fonction.php';


// =====================================================
// PARAMÈTRES
// =====================================================

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$serieId = isset($_GET['serie_id'])
    ? (int) $_GET['serie_id']
    : 0;


// =====================================================
// URL DE RETOUR
// =====================================================

$redirect = "all_devis_fac.php?id=" . $serieId;


// =====================================================
// ID INVALIDE
// =====================================================

if ($id <= 0) {

    header("Location: $redirect&error=invalid_id");
    exit;
}


// =====================================================
// RECHERCHER LE DEVIS
// =====================================================

$sql = "
    SELECT id, type
    FROM factures
    WHERE id = $id
    LIMIT 1
";

$result = mysqli_query($connexion, $sql);

if (!$result || mysqli_num_rows($result) === 0) {

    header("Location: $redirect&error=not_found");
    exit;
}

$facture = mysqli_fetch_assoc($result);


// =====================================================
// VÉRIFIER LE TYPE
// =====================================================

if (strtolower(trim($facture['type'])) !== 'devis') {

    header("Location: $redirect&error=already_validated");
    exit;
}


// =====================================================
// VALIDATION
// =====================================================

$dateValidation = date('Y-m-d H:i:s');

$sqlUpdate = "
    UPDATE factures
    SET
        type = 'Facture',
        date_validation = '$dateValidation'
    WHERE id = $id
      AND LOWER(TRIM(type)) = 'devis'
";

if (mysqli_query($connexion, $sqlUpdate)) {

    header("Location: $redirect&success=validated");
    exit;
}


// =====================================================
// ERREUR
// =====================================================

header("Location: $redirect&error=sql");
exit;