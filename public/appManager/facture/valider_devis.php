<?php
include '../../../config/fonction.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? 0;

if ($id) {
    $dateValidation = date('Y-m-d'); // date du jour

    // Mise à jour type + date_validation
    $sql = "UPDATE factures 
            SET type = 'Facture', date_validation = '$dateValidation' 
            WHERE id = $id";

    if ($connexion->query($sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Impossible de mettre à jour : " . $connexion->error]);
    }

} else {
    echo json_encode(["success" => false, "message" => "ID invalide"]);
}
