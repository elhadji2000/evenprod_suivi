<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirection si pas connecté
if (!isset($_SESSION['id'])) {
    header("Location: index.php?error=2");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>evenprod</title>
    <link href="http://localhost/projet_suivi/assets/bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="http://localhost/projet_suivi/includes/header.css">
    <link id="pagestyle" href="http://localhost/projet_suivi/assets/css/material-kit.css?v=3.1.0" rel="stylesheet" />
</head>
<!-- HEADER -->
<header>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3 fixed-top" 
       style="z-index: 1050;"> <!-- Bootstrap modals utilisent z-index 1050 -->
    <div class="container">

      <!-- Logo -->
      <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
        <img src="http://localhost/projet_suivi/assets/images/logo2.png" 
             alt="Logo" class="me-2" style="height: 40px;">
        <span class="fw-bold text-dark">Evenprod</span>
      </a>

      <!-- Hamburger -->
      <button class="navbar-toggler shadow-none border-0" type="button" 
              data-bs-toggle="collapse" data-bs-target="#mainNav" 
              aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menu -->
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item">
            <a class="nav-link text-dark fw-semibold" 
               href="http://localhost/projet_suivi/public/appManager/series/home.php">
              <i class="fa-solid fa-chart-line me-1"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-dark fw-semibold" 
               href="http://localhost/projet_suivi/pages/about-us.php">
              <i class="fa-solid fa-clapperboard me-1"></i> Séries
            </a>
          </li>
           <li class="nav-item">
            <a class="nav-link text-dark fw-semibold" 
               href="http://localhost/projet_suivi/pages/add_serie.php">
              <i class="fa-solid fa-clapperboard me-1"></i> Add_serie
            </a>
          </li>
           <li class="nav-item">
            <a class="nav-link text-dark fw-semibold" 
               href="http://localhost/projet_suivi/pages/acteur/add_act.php"></i> Add_act
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-dark fw-semibold" 
               href="http://localhost/projet_suivi/pages/sponsors/listes.php"></i> sponsors
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-dark fw-semibold" href="infos.php">
              <i class="fa-solid fa-circle-info me-1"></i> Infos
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-dark fw-semibold" 
               href="http://localhost/projet_suivi/public/admin/users.php">
              <i class="fa-solid fa-users me-1"></i> Utilisateurs
            </a>
          </li>
        </ul>

        <!-- Profil (dropdown) -->
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center fw-bold text-dark" 
               href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fa-solid fa-user me-1"></i> 
              <?= $_SESSION['prenom'] . " " . $_SESSION['nom'] ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" aria-labelledby="userMenu">
              <li>
                <a class="dropdown-item d-flex align-items-center" href="update_mdp.php">
                  <i class="fa-solid fa-lock me-2 text-secondary"></i> Changer mot de passe
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item d-flex align-items-center text-danger" 
                   href="logout.php" onclick="return confirm('Se déconnecter ?')">
                  <i class="fa-solid fa-right-from-bracket me-2"></i> Déconnexion
                </a>
              </li>
            </ul>
          </li>
        </ul>

      </div>
    </div>
  </nav>
</header>

<!-- espace pour compenser la navbar fixe -->
<div style="height: 90px;"></div>

<!-- Bootstrap Bundle (inclut Popper.js) -->
<script src="http://localhost/projet_suivi/assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
