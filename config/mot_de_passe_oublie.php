<?php

session_start();

require_once __DIR__ . '/fonction.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    header('Location: ../index.php?reset_error=email');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../index.php?reset_error=email');
    exit;
}


// =========================================================
// Vérifier si l'utilisateur existe
// =========================================================

$stmt = mysqli_prepare(
    $connexion,
    "SELECT id, nom, prenom, email 
     FROM users 
     WHERE email = ?"
);

if (!$stmt) {
    header('Location: ../index.php?reset_error=server');
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $email);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


// =========================================================
// Vérifier l'existence de l'utilisateur
// =========================================================

if (!$user) {

    header('Location: ../index.php?reset_error=not_found');
    exit;
}


// =========================================================
// Générer un nouveau mot de passe
// =========================================================

$nouveauMotDePasse = genererMotDePasse(12);


// =========================================================
// SHA1 comme votre système actuel
// =========================================================

$passwordHash = sha1($nouveauMotDePasse);


// =========================================================
// Mettre à jour le mot de passe
// =========================================================

$stmt = mysqli_prepare(
    $connexion,
    "UPDATE users
     SET mot_de_passe = ?
     WHERE id = ?"
);

if (!$stmt) {
    header('Location: ../index.php?reset_error=server');
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    'si',
    $passwordHash,
    $user['id']
);

$success = mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);


if (!$success) {
    header('Location: ../index.php?reset_error=server');
    exit;
}


// =========================================================
// Envoyer le nouveau mot de passe par email
// =========================================================

$mailEnvoye = envoyerMailMotDePasseReset(
    $user['email'],
    $user['nom'],
    $user['prenom'],
    $nouveauMotDePasse
);


if ($mailEnvoye) {

    header('Location: ../index.php?reset_success=1');
    exit;

} else {

    header('Location: ../index.php?reset_error=mail');
    exit;
}