<?php
include '../../config/fonction.php'; // connexion BDD
$url_base = "http://localhost/projet_suivi/";

if (isset($_GET['id'], $_GET['table'])) {
    $id = (int) $_GET['id'];
    $table = $_GET['table'];

    $allowedTables = ['series', 'acteurs', 'clients','depenses', 'factures', 'tournages', 'serie_acteur'];

    if (in_array($table, $allowedTables)) {

        // 1️⃣ Récupérer l'image si applicable
        if ($table === 'acteurs') {
            $stmt = $connexion->prepare("SELECT photo, cv_file FROM $table WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res && !empty($res['photo'])) {
                $imagePath = '../../uploads/photos/'.$res['photo'];
                $cvPath = '../../uploads/cv/'.$res['cv_file'];
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Supprime seulement l'image
                    unlink($cvPath);
                }
            }
        }
        if ($table === 'series') {
            $stmt = $connexion->prepare("SELECT logo FROM $table WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res && !empty($res['logo'])) {
                $imagePath = '../../uploads/series/'.$res['logo'];
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Supprime seulement l'image
                }
            }
        }
        if ($table === 'clients') {
            $stmt = $connexion->prepare("SELECT logo FROM $table WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res && !empty($res['logo'])) {
                $imagePath = '../../uploads/logos/'.$res['logo'];
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Supprime seulement l'image
                }
            }
        }
        if ($table === 'depenses') {
            $stmt = $connexion->prepare("SELECT justificatif FROM $table WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res && !empty($res['photo'])) {
                $justPath = '../../uploads/justificatifs/'.$res['justificatif'];
                if (file_exists($justPath)) {
                    unlink($justPath); 
                }
            }
        }
        

        // 2️⃣ Supprimer l’élément principal
        $stmt = $connexion->prepare("DELETE FROM `$table` WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute() ? 1 : 0;

    } else {
        $success = 0; // table non autorisée
    }

    // Redirection
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
    $separator = (strpos($redirect, '?') !== false) ? '&' : '?';
    header("Location: {$redirect}{$separator}success={$success}");
exit;


} else {
    header("Location: index.php?success=0");
    exit;
}