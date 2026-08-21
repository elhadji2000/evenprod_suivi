<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| URL de base
|--------------------------------------------------------------------------
*/

// Production
// $url_base = "https://evenapp.fr/";

// Développement local
$url_base = "http://localhost/projet_suivi/";


/*
|--------------------------------------------------------------------------
| Vérification de connexion
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['id'])) {
    header("Location: " . $url_base . "index.php?error=2");
    exit;
}

$role = $_SESSION['role'] ?? '';

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="EvenProd - Gestion de production audiovisuelle">

    <title>EvenProd</title>

    <!-- Favicon -->
    <link rel="shortcut icon"
          href="<?= $url_base ?>monde.svg">

    <!-- Bootstrap 5 -->
    <link
        href="<?= $url_base ?>assets/bootstrap-5.3.7-dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Police Poppins -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">


    <style>

        /*
        |--------------------------------------------------------------------------
        | GLOBAL
        |--------------------------------------------------------------------------
        */

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;

            font-family: 'Poppins', sans-serif;

            background-color: #f6f8fb;

            color: #343a40;
        }


        /*
        |--------------------------------------------------------------------------
        | NAVBAR
        |--------------------------------------------------------------------------
        */

        .evenprod-navbar {

            background: #ffffff;

            border-bottom: 1px solid #e9ecef;

            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);

            min-height: 72px;

            z-index: 1030;
        }


        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        .evenprod-logo {

            display: flex;

            align-items: center;

            text-decoration: none;

            gap: 10px;
        }

        .evenprod-logo img {

            width: 48px;

            height: 42px;

            object-fit: contain;
        }

        .evenprod-logo-text {

            display: flex;

            flex-direction: column;

            line-height: 1.1;
        }

        .evenprod-logo-title {

            font-size: 20px;

            font-weight: 700;

            color: #212529;

            letter-spacing: 0.3px;
        }

        .evenprod-logo-subtitle {

            font-size: 10px;

            color: #8a94a6;

            text-transform: uppercase;

            letter-spacing: 1.5px;
        }


        /*
        |--------------------------------------------------------------------------
        | NAVIGATION
        |--------------------------------------------------------------------------
        */

        .evenprod-navbar .navbar-nav {

            gap: 4px;
        }

        .evenprod-navbar .nav-link {

            color: #495057;

            font-size: 14px;

            font-weight: 500;

            padding: 10px 13px !important;

            border-radius: 8px;

            transition: all 0.2s ease;

            display: flex;

            align-items: center;

            gap: 7px;
        }

        .evenprod-navbar .nav-link:hover {

            background-color: #f1f5f9;

            color: #0d6efd;
        }

        .evenprod-navbar .nav-link.active {

            background-color: #eef5ff;

            color: #0d6efd;
        }


        /*
        |--------------------------------------------------------------------------
        | ICONES NAV
        |--------------------------------------------------------------------------
        */

        .evenprod-navbar .nav-link i {

            font-size: 14px;

            width: 17px;

            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | DROPDOWN
        |--------------------------------------------------------------------------
        */

        .evenprod-navbar .dropdown-menu {

            border: 1px solid #e9ecef;

            border-radius: 10px;

            padding: 8px;

            min-width: 200px;

            margin-top: 8px;

            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.10);
        }

        .evenprod-navbar .dropdown-item {

            font-size: 13px;

            font-weight: 500;

            color: #495057;

            padding: 10px 12px;

            border-radius: 7px;

            transition: all 0.2s ease;
        }

        .evenprod-navbar .dropdown-item:hover {

            background-color: #f1f5f9;

            color: #0d6efd;
        }

        .evenprod-navbar .dropdown-item i {

            width: 20px;

            margin-right: 5px;

            color: #6c757d;
        }

        .evenprod-navbar .dropdown-divider {

            margin: 6px 0;

            border-color: #eeeeee;
        }


        /*
        |--------------------------------------------------------------------------
        | PROFIL UTILISATEUR
        |--------------------------------------------------------------------------
        */

        .evenprod-user {

            display: flex;

            align-items: center;

            gap: 9px;

            padding: 7px 10px !important;

            border-radius: 8px;
        }

        .evenprod-user:hover {

            background-color: #f1f5f9;
        }

        .evenprod-user-icon {

            width: 34px;

            height: 34px;

            border-radius: 50%;

            background: #eef5ff;

            color: #0d6efd;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 14px;
        }

        .evenprod-user-info {

            display: flex;

            flex-direction: column;

            line-height: 1.2;
        }

        .evenprod-user-name {

            font-size: 12px;

            font-weight: 600;

            color: #343a40;
        }

        .evenprod-user-role {

            font-size: 10px;

            color: #8a94a6;

            text-transform: capitalize;
        }


        /*
        |--------------------------------------------------------------------------
        | BOUTON DECONNEXION
        |--------------------------------------------------------------------------
        */

        .logout-item {

            color: #dc3545 !important;
        }

        .logout-item:hover {

            background-color: #fff1f2 !important;

            color: #dc3545 !important;
        }

        .logout-item i {

            color: #dc3545 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        .navbar-toggler {

            border: none;

            padding: 8px;

            box-shadow: none !important;
        }

        .navbar-toggler:focus {

            box-shadow: none;
        }

        .navbar-toggler-icon {

            width: 22px;

            height: 22px;
        }


        @media (max-width: 991px) {

            .evenprod-navbar .navbar-collapse {

                padding: 15px 0;
            }

            .evenprod-navbar .navbar-nav {

                gap: 3px;
            }

            .evenprod-navbar .nav-link {

                padding: 11px 12px !important;
            }

            .evenprod-navbar .dropdown-menu {

                border: none;

                box-shadow: none;

                background-color: #f8f9fa;

                margin-top: 0;

                padding-left: 10px;
            }

            .evenprod-user {

                margin-top: 8px;
            }
        }

    </style>

</head>


<body>


<!-- ============================================================
     NAVBAR EVENPROD
============================================================ -->

<nav class="navbar navbar-expand-lg evenprod-navbar sticky-top">

    <div class="container-fluid px-4">


        <!-- ====================================================
             LOGO
        ===================================================== -->

        <a class="evenprod-logo"
           href="<?= $url_base ?>public/appManager/series/home">

            <img
                src="<?= $url_base ?>assets/images/logo2.png"
                alt="EvenProd">

            <div class="evenprod-logo-text">

                <span class="evenprod-logo-title">
                    EvenProd
                </span>

                <span class="evenprod-logo-subtitle">
                    Production
                </span>

            </div>

        </a>


        <!-- ====================================================
             BOUTON MOBILE
        ===================================================== -->

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#evenprodNavbar"
            aria-controls="evenprodNavbar"
            aria-expanded="false"
            aria-label="Menu">

            <i class="fa-solid fa-bars"></i>

        </button>


        <!-- ====================================================
             MENU
        ===================================================== -->

        <div
            class="collapse navbar-collapse"
            id="evenprodNavbar">


            <ul class="navbar-nav ms-auto align-items-lg-center">


                <!-- =================================================
                     ACCUEIL
                ================================================== -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="<?= $url_base ?>public/appManager/series/home">

                        <i class="fa-solid fa-house"></i>

                        <span>Accueil</span>

                    </a>

                </li>


                <!-- =================================================
                     SERIES
                ================================================== -->

                <?php if (
                    $role === 'admin' ||
                    $role === 'tournage' ||
                    $role === 'comptable' ||
                    $role === 'caisse'
                ): ?>

                    <li class="nav-item dropdown">

                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <i class="fa-solid fa-film"></i>

                            <span>Séries</span>

                        </a>


                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $url_base ?>pages/add_serie">

                                    <i class="fa-solid fa-plus"></i>

                                    Ajouter une série

                                </a>

                            </li>


                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $url_base ?>pages/about-us">

                                    <i class="fa-solid fa-list"></i>

                                    Liste des séries

                                </a>

                            </li>

                        </ul>

                    </li>

                <?php endif; ?>


                <!-- =================================================
                     ACTEURS
                ================================================== -->

                <?php if (
                    $role === 'admin' ||
                    $role === 'tournage'
                ): ?>

                    <li class="nav-item dropdown">

                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <i class="fa-solid fa-users"></i>

                            <span>Acteurs</span>

                        </a>


                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $url_base ?>pages/acteur/add_act">

                                    <i class="fa-solid fa-user-plus"></i>

                                    Ajouter un acteur

                                </a>

                            </li>


                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $url_base ?>pages/acteur/liste">

                                    <i class="fa-solid fa-users"></i>

                                    Liste des acteurs

                                </a>

                            </li>

                        </ul>

                    </li>

                <?php endif; ?>


                <!-- =================================================
                     PARTENARIAT
                ================================================== -->

                <?php if (
                    $role === 'admin' ||
                    $role === 'comptable'
                ): ?>

                    <li class="nav-item dropdown">

                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <i class="fa-solid fa-handshake"></i>

                            <span>Partenariat</span>

                        </a>


                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $url_base ?>pages/sponsors/add_spon">

                                    <i class="fa-solid fa-plus"></i>

                                    Ajouter

                                </a>

                            </li>


                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $url_base ?>pages/sponsors/listes">

                                    <i class="fa-solid fa-list"></i>

                                    Liste des partenaires

                                </a>

                            </li>

                        </ul>

                    </li>

                <?php endif; ?>


                <!-- =================================================
                     UTILISATEURS
                ================================================== -->

                <?php if ($role === 'admin'): ?>

                    <li class="nav-item dropdown">

                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <i class="fa-solid fa-user-gear"></i>

                            <span>Utilisateurs</span>

                        </a>


                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $url_base ?>public/admin/add_user">

                                    <i class="fa-solid fa-user-plus"></i>

                                    Ajouter

                                </a>

                            </li>


                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $url_base ?>public/admin/users">

                                    <i class="fa-solid fa-users"></i>

                                    Liste des utilisateurs

                                </a>

                            </li>

                        </ul>

                    </li>

                <?php endif; ?>


                <!-- =================================================
                     COMPTE
                ================================================== -->

                <li class="nav-item dropdown ms-lg-2">

                    <a
                        class="nav-link dropdown-toggle evenprod-user"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">


                        <span class="evenprod-user-icon">

                            <i class="fa-solid fa-user"></i>

                        </span>


                        <span class="evenprod-user-info">

                            <span class="evenprod-user-name">
                                Mon compte
                            </span>

                            <span class="evenprod-user-role">
                                <?= htmlspecialchars($role) ?>
                            </span>

                        </span>

                    </a>


                    <ul class="dropdown-menu dropdown-menu-end">

                        <li>

                            <a
                                class="dropdown-item"
                                href="<?= $url_base ?>public/admin/profile">

                                <i class="fa-solid fa-user"></i>

                                Mon profil

                            </a>

                        </li>


                        <li>

                            <hr class="dropdown-divider">

                        </li>


                        <li>

                            <a
                                class="dropdown-item logout-item"
                                href="<?= $url_base ?>index.php?logout=1">

                                <i class="fa-solid fa-right-from-bracket"></i>

                                Déconnexion

                            </a>

                        </li>

                    </ul>

                </li>


            </ul>

        </div>

    </div>

</nav>


<!-- ============================================================
     BOOTSTRAP JS
============================================================ -->

<script src="<?= $url_base ?>assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>


<!-- ============================================================
     GESTION INACTIVITE
============================================================ -->

<script>

const INACTIVITY_LIMIT = 3 * 60 * 1000;

let inactivityTimer;


function resetTimer() {

    clearTimeout(inactivityTimer);

    inactivityTimer = setTimeout(function () {

        window.location.href = "<?= $url_base ?>index.php?logout=1";

    }, INACTIVITY_LIMIT);

}


window.addEventListener("load", resetTimer);

document.addEventListener("mousemove", resetTimer);

document.addEventListener("keypress", resetTimer);

document.addEventListener("scroll", resetTimer);

document.addEventListener("click", resetTimer);

document.addEventListener("touchstart", resetTimer);

</script>


</body>

</html>