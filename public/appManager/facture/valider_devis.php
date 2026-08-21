<?php
header('Content-Type: application/json');
include '../../../config/fonction.php';

// Récupérer l'ID depuis POST ou GET (pour plus de flexibilité)
$id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

// Log pour déboguer
error_log("Tentative de validation du devis ID: " . $id);

if ($id > 0) {
    // Vérifier d'abord si la facture existe
    $checkSql = "SELECT id, type FROM factures WHERE id = $id";
    $checkResult = $connexion->query($checkSql);
    
    if ($checkResult && $checkResult->num_rows > 0) {
        $facture = $checkResult->fetch_assoc();
        
        // Vérifier si c'est déjà une facture
        if ($facture['type'] == 'Facture') {
            echo json_encode([
                "success" => false, 
                "message" => "Ce document est déjà une facture."
            ]);
            exit;
        }
        
        // Mise à jour type + date_validation
        $dateValidation = date('Y-m-d');
        $sql = "UPDATE factures 
                SET type = 'Facture', date_validation = '$dateValidation' 
                WHERE id = $id AND type != 'Facture'";
        
        if ($connexion->query($sql)) {
            if ($connexion->affected_rows > 0) {
                echo json_encode([
                    "success" => true,
                    "message" => "Devis validé avec succès !"
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Aucune modification effectuée. Le devis a peut-être déjà été validé."
                ]);
            }
        } else {
            echo json_encode([
                "success" => false, 
                "message" => "Erreur SQL : " . $connexion->error
            ]);
        }
    } else {
        echo json_encode([
            "success" => false, 
            "message" => "Aucun devis trouvé avec l'ID : " . $id
        ]);
    }
} else {
    echo json_encode([
        "success" => false, 
        "message" => "ID invalide ou manquant. ID reçu : " . ($id ?: 'vide')
    ]);
}
?>