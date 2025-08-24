<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirection si pas connecté
if (!isset($_SESSION['id'])) {
    header("Location: index.php?error=2");
    exit;
}
/* $url_base ="https://gescoud.com/sygep/"; */
$url_base ="http://localhost/projet_suivi/";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>evenprod</title>
    <link href="<?php echo $url_base; ?>assets/bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link id="pagestyle" href="<?php echo $url_base; ?>assets/css/material-kit.css?v=3.1.0" rel="stylesheet" />
</head>
<nav class="navbar navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="<?php echo $url_base; ?>assets/images/logo2.png" alt="" width="30" height="24">
        </a>
        <ul class="nav justify-content-end">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="<?php echo $url_base; ?>public/appManager/series/home.php">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                    aria-expanded="false">Series</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo $url_base; ?>pages/add_serie.php">Ajouter</a></li>
                    <li style="color:black;">
                        <hr class="dropdown-divider" style="color:black;">
                    </li>
                    <li><a class="dropdown-item" href="<?php echo $url_base; ?>pages/about-us.php">Lister</a></li>
                </ul>
            </li>
             <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                    aria-expanded="false">Acteurs</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo $url_base; ?>pages/acteur/add_act.php">Ajouter</a></li>
                    <li style="color:black;">
                        <hr class="dropdown-divider" style="color:black;">
                    </li>
                    <li><a class="dropdown-item" href="<?php echo $url_base; ?>pages/acteur/liste.php">Lister</a></li>
                </ul>
            </li>
             <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                    aria-expanded="false">Partenariat</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo $url_base; ?>pages/sponsors/add_spon.php">Ajouter</a></li>
                    <li style="color:black;">
                        <hr class="dropdown-divider" style="color:black;">
                    </li>
                    <li><a class="dropdown-item" href="<?php echo $url_base; ?>pages/sponsors/listes.php">Lister</a></li>
                </ul>
            </li>
             <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                    aria-expanded="false">Utilisateurs</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo $url_base; ?>public/admin/add_user.php">Ajouter</a></li>
                    <li style="color:black;">
                        <hr class="dropdown-divider" style="color:black;">
                    </li>
                    <li><a class="dropdown-item" href="<?php echo $url_base; ?>public/admin/users.php">Lister</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                    aria-expanded="false">Compte</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li style="color:black;">
                        <hr class="dropdown-divider" style="color:black;">
                    </li>
                    <li><a class="dropdown-item" href="#">Deconnexion</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<!-- Bootstrap Bundle (inclut Popper.js) -->
<script src="<?php echo $url_base; ?>assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>