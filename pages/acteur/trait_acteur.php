<?php
session_start();
include '../../config/fonction.php'; // connexion $connexion

// Dossier d’upload
$cvUploadDir = '../../uploads/cv/';
$photoUploadDir = '../../uploads/photos/';
if (!is_dir($cvUploadDir)) mkdir($cvUploadDir, 0777, true);
if (!is_dir($photoUploadDir)) mkdir($photoUploadDir, 0777, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ID pour savoir si on est en modification
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    // Récupération et sécurisation des données
    $prenom = mysqli_real_escape_string($connexion, $_POST['prenom']);
    $nom = mysqli_real_escape_string($connexion, $_POST['nom']);
    $date_naissance = mysqli_real_escape_string($connexion, $_POST['date_naissance']);
    $contact = mysqli_real_escape_string($connexion, $_POST['contact']);
    $adresse = mysqli_real_escape_string($connexion, $_POST['adresse']);

    // Récupération ancien acteur si update
    $old = null;
    if ($id > 0) {
        $res = mysqli_query($connexion, "SELECT * FROM acteurs WHERE id=$id");
        $old = mysqli_fetch_assoc($res);
    }

    // === Gestion du CV ===
    $cvNewName = $old['cv_file'] ?? null; // ancien CV si update
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === 0) {
        $cvTmp = $_FILES['cv']['tmp_name'];
        $cvName = $_FILES['cv']['name'];
        $cvExt = strtolower(pathinfo($cvName, PATHINFO_EXTENSION));
        if ($cvExt !== 'pdf') {
            $_SESSION['erroract'] = "Le CV doit être un fichier PDF.";
            header("Location: add_act.php");
            exit;
        }
        // supprimer ancien CV si existe
        if ($old && !empty($old['cv_file']) && is_file($cvUploadDir.$old['cv_file'])) {
            unlink($cvUploadDir.$old['cv_file']);
        }
        $cvNewName = uniqid('cv_', true).'.pdf';
        $cvDestination = $cvUploadDir.$cvNewName;
        if (!move_uploaded_file($cvTmp, $cvDestination)) {
            $_SESSION['erroract'] = "Erreur lors de l'upload du CV.";
            header("Location: add_act.php");
            exit;
        }
    } elseif ($id === 0) {
        $_SESSION['erroract'] = "Veuillez sélectionner un CV.";
        header("Location: add_act.php");
        exit;
    }

    // === Gestion de la photo ===
    $photoNewName = $old['photo'] ?? null; // ancienne photo si update
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $photoTmp = $_FILES['photo']['tmp_name'];
        $photoName = $_FILES['photo']['name'];
        $photoExt = strtolower(pathinfo($photoName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array($photoExt, $allowed)) {
            $_SESSION['erroract'] = "La photo doit être au format jpg, jpeg, png ou gif.";
            header("Location: add_act.php");
            exit;
        }
        // supprimer ancienne photo si existe
        if ($old && !empty($old['photo']) && is_file($photoUploadDir.$old['photo'])) {
            unlink($photoUploadDir.$old['photo']);
        }
        $photoNewName = uniqid('photo_', true).'.'.$photoExt;
        $photoDestination = $photoUploadDir.$photoNewName;
        if (!move_uploaded_file($photoTmp, $photoDestination)) {
            $_SESSION['erroract'] = "Erreur lors de l'upload de la photo.";
            header("Location: add_act.php");
            exit;
        }
    } elseif ($id === 0) {
        $_SESSION['erroract'] = "Veuillez sélectionner une photo.";
        header("Location: add_act.php");
        exit;
    }

    // === Insert ou Update ===
    if ($id > 0) {
        // Update
        $sql = "UPDATE acteurs SET prenom='$prenom', nom='$nom', date_naissance='$date_naissance',
                contact='$contact', adresse='$adresse', photo='$photoNewName', cv_file='$cvNewName'
                WHERE id=$id";
        if (mysqli_query($connexion, $sql)) {
            $_SESSION['successact'] = "L'acteur a été mis à jour avec succès.";
        } else {
            $_SESSION['erroract'] = "Erreur SQL : ".mysqli_error($connexion);
        }
    } else {
        // Insert
        $sql = "INSERT INTO acteurs (prenom, nom, date_naissance, contact, adresse, photo, cv_file)
                VALUES ('$prenom','$nom','$date_naissance','$contact','$adresse','$photoNewName','$cvNewName')";
        if (mysqli_query($connexion, $sql)) {
            $_SESSION['successact'] = "L'acteur a été ajouté avec succès.";
        } else {
            $_SESSION['erroract'] = "Erreur SQL : ".mysqli_error($connexion);
        }
    }

    header("Location: add_act.php");
    exit;
}
?>
