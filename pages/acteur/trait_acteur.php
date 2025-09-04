<?php
session_start();
include '../../config/fonction.php'; // connexion $connexion

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération et sécurisation des données
    $prenom = mysqli_real_escape_string($connexion, $_POST['prenom']);
    $nom = mysqli_real_escape_string($connexion, $_POST['nom']);
    $date_naissance = mysqli_real_escape_string($connexion, $_POST['date_naissance']);
    $contact = mysqli_real_escape_string($connexion, $_POST['contact']);
    $adresse = mysqli_real_escape_string($connexion, $_POST['adresse']);

    // Gestion du CV PDF
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === 0) {
        $cvTmp = $_FILES['cv']['tmp_name'];
        $cvName = $_FILES['cv']['name'];
        $cvExt = strtolower(pathinfo($cvName, PATHINFO_EXTENSION));

        if ($cvExt !== 'pdf') {
            $_SESSION['error'] = "Le CV doit être un fichier PDF.";
            header("Location: add_act.php");
            exit;
        }

        $cvNewName = uniqid('cv_', true) . '.pdf';
        $cvUploadDir = '../../uploads/cv/';
        if (!is_dir($cvUploadDir)) mkdir($cvUploadDir, 0777, true);
        $cvDestination = $cvUploadDir . $cvNewName;

        if (!move_uploaded_file($cvTmp, $cvDestination)) {
            $_SESSION['error'] = "Erreur lors de l'upload du CV.";
            header("Location: add_act.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Veuillez sélectionner un CV.";
        header("Location: add_act.php");
        exit;
    }

    // Gestion de la photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $photoTmp = $_FILES['photo']['tmp_name'];
        $photoName = $_FILES['photo']['name'];
        $photoExt = strtolower(pathinfo($photoName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];

        if (!in_array($photoExt, $allowed)) {
            $_SESSION['error'] = "La photo doit être au format jpg, jpeg, png ou gif.";
            header("Location: add_act.php");
            exit;
        }

        $photoNewName = uniqid('photo_', true) . '.' . $photoExt;
        $photoUploadDir = '../../uploads/photos/';
        if (!is_dir($photoUploadDir)) mkdir($photoUploadDir, 0777, true);
        $photoDestination = $photoUploadDir . $photoNewName;

        if (!move_uploaded_file($photoTmp, $photoDestination)) {
            $_SESSION['error'] = "Erreur lors de l'upload de la photo.";
            header("Location: add_act.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Veuillez sélectionner une photo.";
        header("Location: trait_acteur.php");
        exit;
    }

    // Insertion dans la table acteurs
    $sql = "INSERT INTO acteurs (prenom, nom, date_naissance, contact, adresse, photo, cv_file) 
            VALUES ('$prenom', '$nom', '$date_naissance', '$contact', '$adresse', '$photoNewName', '$cvNewName')";

    if (mysqli_query($connexion, $sql)) {
        $_SESSION['success'] = "L'acteur a été ajouté avec succès.";
        header("Location: add_act.php");
        exit;
    } else {
        $_SESSION['error'] = "Erreur SQL : " . mysqli_error($connexion);
        header("Location: add_act.php");
        exit;
    }

   header("Location: add_act.php");
    exit;
}
?>