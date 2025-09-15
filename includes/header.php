<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$url_base = "https://evenapp.fr/";  
//$url_base = "http://localhost/projet_suivi/"; 
$role = $_SESSION['role']; // ✅ Rôle de l’utilisateur connecté
// Redirection si pas connecté
if (!isset($_SESSION['id'])) {
    header("Location: $url_base/index.php?error=2");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>evenprod</title>
    <link rel="shortcut icon" href="<?php echo $url_base; ?>monde.svg" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="<?php echo $url_base; ?>assets/bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link id="pagestyle" href="<?php echo $url_base; ?>assets/css/material-kit.css?v=3.1.0" rel="stylesheet" />
    
    
    <script>
    // Durée d'inactivité en millisecondes (3 minutes)
    const INACTIVITY_LIMIT = 3 * 60 * 1000; // 180000 ms

    let inactivityTimer;

    function resetTimer() {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(() => {
            // Redirection vers logout.php après 3 minutes d'inactivité
            window.location.href = "https://evenapp.fr/";
        }, INACTIVITY_LIMIT);
    }

    // Événements qui réinitialisent le compteur
    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;
    document.onscroll = resetTimer;
    document.onclick = resetTimer;
    </script>
</head>

<body>
    <nav class="navbar navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="<?php echo $url_base; ?>assets/images/logo2.png" alt="" width="50" height="30">
            </a>
            <ul class="nav justify-content-end">

                <!-- Accueil visible à tout le monde -->
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page"
                        href="<?php echo $url_base; ?>public/appManager/series/home">Home</a>
                </li>

                <!-- Series (visible admin & manager) -->
                <?php if ($role === 'admin' || $role === 'tournage' || $role === 'comptable' || $role === 'caisse'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                        aria-expanded="false">Series</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo $url_base; ?>pages/add_serie">Ajouter</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?php echo $url_base; ?>pages/about-us">Lister</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Acteurs (visible admin uniquement) -->
                <?php if ($role === 'admin' || $role === 'tournage'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                        aria-expanded="false">Acteurs</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo $url_base; ?>pages/acteur/add_act">Ajouter</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?php echo $url_base; ?>pages/acteur/liste">Lister</a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Partenariat (visible manager & admin) -->
                <?php if ($role === 'admin' || $role === 'comptable'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                        aria-expanded="false">Partenariat</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item"
                                href="<?php echo $url_base; ?>pages/sponsors/add_spon">Ajouter</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?php echo $url_base; ?>pages/sponsors/listes">Lister</a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Utilisateurs (admin uniquement) -->
                <?php if ($role === 'admin'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                        aria-expanded="false">Utilisateurs</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item"
                                href="<?php echo $url_base; ?>public/admin/add_user">Ajouter</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?php echo $url_base; ?>public/admin/users">Lister</a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Compte (toujours visible) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                        aria-expanded="false">Compte</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo $url_base; ?>public/admin/profile">Profile</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <a class="dropdown-item" href="<?php echo $url_base; ?>index?logout=1">Deconnexion</a>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <script src="<?php echo $url_base; ?>assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>