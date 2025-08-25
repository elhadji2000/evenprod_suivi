<?php
// ajouter_serie.php
session_start();
include '../config/fonction.php'; // fichier avec la connexion $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupérer et sécuriser les données
    $titre = mysqli_real_escape_string($connexion, $_POST['titre']);
    $type = mysqli_real_escape_string($connexion, $_POST['type']);
    $budget = floatval($_POST['budget']);
    $description = mysqli_real_escape_string($connexion, $_POST['description']);

    // Vérifier l'image
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $allowed = ['jpg','jpeg','png','gif'];
        $fileName = $_FILES['photo']['name'];
        $fileTmp = $_FILES['photo']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allowed)) {
            // Créer un nom unique pour l'image
            $newFileName = uniqid('serie_', true) . '.' . $fileExt;
            $uploadDir = '../uploads/series/';
            
            // Créer le dossier s'il n'existe pas
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $destination)) {
                // Insertion dans la base
                $sql = "INSERT INTO series (titre, type, budget, description, logo) 
                        VALUES ('$titre', '$type', $budget, '$description', '$newFileName')";

                if (mysqli_query($connexion, $sql)) {
                    $_SESSION['success'] = "La série a été enregistrée avec succès.";
                    header("Location: add_serie.php?reussi=1");
                    exit;
                } else {
                    $error = "Erreur SQL: " . mysqli_error($connexion);
                }
            } else {
                $error = "Erreur lors de l'upload de l'image.";
            }
        } else {
            $error = "Format de fichier non autorisé. Seuls jpg, jpeg, png, gif sont acceptés.";
        }
    } else {
        $error = "Veuillez choisir une image pour la série.";
    }

    if (isset($error)) {
        $_SESSION['error'] = $error;
        header("Location: ajouter_serie.php");
        exit;
    }
}
?>
