<?php
session_start();
include '../config/fonction.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serieId = $_POST['serie_id'] ?? null; // null si ajout
    $titre = $_POST['titre'];
    $type = $_POST['type'];
    $budget = $_POST['budget'];
    $description = $_POST['description'];

    $logo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $allowed = ['jpg','jpeg','png','gif'];
        $fileName = $_FILES['photo']['name'];
        $fileTmp = $_FILES['photo']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allowed)) {
            $newFileName = uniqid('serie_', true) . '.' . $fileExt;
            $uploadDir = '../uploads/series/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $destination = $uploadDir . $newFileName;
            if (move_uploaded_file($fileTmp, $destination)) {
                $logo = $newFileName;
            } else {
                $_SESSION['error'] = "Erreur lors de l'upload de l'image.";
                header("Location: add_serie");
                exit;
            }
        } else {
            $_SESSION['error'] = "Format de fichier non autorisé (jpg, jpeg, png, gif).";
            header("Location: add_serie");
            exit;
        }
    }

    if ($serieId) {
        // Modification
        $result = modifierSerie($serieId, $titre, $type, $budget, $description, $logo);
    } else {
        // Ajout
        if ($logo === null) {
            $_SESSION['error'] = "Veuillez choisir une image pour la série.";
            header("Location: add_serie");
            exit;
        }
        $result = ajouterSerie($titre, $type, $budget, $description, $logo);
    }

    if ($result['success']) {
        $_SESSION['success'] = $serieId ? "Série modifiée avec succès." : "Série ajoutée avec succès.";
        header("Location: add_serie?reussi=1&id=$serieId");
        exit;
    } else {
        $_SESSION['error'] = $result['message'] ?? "Une erreur est survenue.";
        header("Location: add_serie");
        exit;
    }
}
