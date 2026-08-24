<?php
session_start();

// Vérifier si l'utilisateur veut se déconnecter
if (isset($_GET['logout'])) {
    // Supprimer toutes les variables de session
    $_SESSION = [];

    // Détruire la session
    session_destroy();

    // Rediriger vers la page de connexion sans paramètre
    header('Location: index.php');
    exit;
}
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Connexion - Evenprod</title>

    <!-- Bootstrap CSS -->
    <link href="assets/bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    body {
        background: #f5f7fa;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', sans-serif;
    }

    .login-container {
        background: #ffffff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 420px;
        text-align: center;
    }

    .login-logo img {
        width: 100px;
        margin-bottom: 15px;
    }

    .company-name {
        font-size: 22px;
        font-weight: 600;
        color: #4a0a03b1;
        /* Rouge Evenprod */
        margin-bottom: 30px;
    }

    .input-group-text {
        background-color: #f0f3f7;
        border: none;
    }

    .form-control {
        border-left: none;
        border-radius: 0 10px 10px 0;
        background-color: #f9fbfc;
        border: 1px solid #e0e6ed;
    }

    .input-group {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e0e6ed;
        background-color: #f9fbfc;
    }

    .btn-primary {
        border-radius: 10px;
        padding: 10px;
        width: 100%;
        background-color: #322424d4;
        /* Rouge Evenprod */
        border: none;
    }

    .btn-primary:hover {
        background-color: #941e11ff;
        /* Rouge foncé */
    }

    .forgot-password {
        font-size: 14px;
        margin-top: 10px;
        color: #2c3e50;
        /* Noir pour le texte */
    }

    .forgot-password a {
        color: #e74c3c;
        text-decoration: none;
    }

    .forgot-password a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-logo">
            <!-- Logo de la société -->
            <img src="assets/images/logo2.png" alt="Logo Evenprod">
        </div>
        <div class="company-name">Connexion</div>

        <!-- Message d'erreur -->
        <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
        <div class="alert alert-danger text-center" role="alert" style="font-size:11px !important;">
            <i class="bi bi-exclamation-triangle-fill"></i> Identifiant ou mot de passe incorrect.
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['reset_success'])): ?>

        <div class="alert alert-success text-center" style="font-size:11px !important;">
            <i class="bi bi-check-circle-fill"></i>
            Un nouveau mot de passe vous a été envoyé par email.
        </div>

        <?php endif; ?>


        <?php if (isset($_GET['reset_error'])): ?>

        <?php if ($_GET['reset_error'] === 'not_found'): ?>

        <div class="alert alert-danger text-center" style="font-size:11px !important;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            Aucun compte n'est associé à cette adresse email.
        </div>

        <?php elseif ($_GET['reset_error'] === 'mail'): ?>

        <div class="alert alert-danger text-center" style="font-size:11px !important;">
            <i class="bi bi-envelope-x-fill"></i>
            Le mot de passe a été réinitialisé, mais l'envoi du mail a échoué.
            Veuillez contacter l'administrateur.
        </div>

        <?php elseif ($_GET['reset_error'] === 'email'): ?>

        <div class="alert alert-danger text-center" style="font-size:11px !important;">
            <i class="bi bi-envelope-exclamation-fill"></i>
            Veuillez fournir une adresse email valide.
        </div>

        <?php else: ?>

        <div class="alert alert-danger text-center" style="font-size:11px !important;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            Une erreur est survenue. Veuillez réessayer.
        </div>

        <?php endif; ?>

        <?php endif; ?>

        <form method="post" action="config/connect">
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" name="email" class="form-control" placeholder="Identifiant" required>
                </div>
            </div>

            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="mot_de_passe" class="form-control" placeholder="Mot de passe" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>

        <div class="forgot-password">
            <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                Mot de passe oublié ?
            </a>
        </div>
    </div>


    <!-- Modal Mot de passe oublié -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">
                        <i class="bi bi-key-fill me-2"></i>
                        Mot de passe oublié
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer">
                    </button>
                </div>

                <form method="post" action="config/mot_de_passe_oublie">

                    <div class="modal-body" style="font-size:11px !important;">

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle-fill me-2"></i>

                            <strong>Réinitialisation du mot de passe</strong>

                            <p class="mb-0 mt-2">
                                Si vous cliquez sur <strong>Valider</strong>,
                                un nouveau mot de passe de réinitialisation
                                vous sera envoyé par email.
                            </p>

                            <p class="mb-0 mt-2">
                                Vous pourrez ensuite utiliser ce mot de passe
                                pour vous connecter et modifier votre mot de passe.
                            </p>
                        </div>

                        <div class="mb-3">

                            <label for="email_reset" class="form-label fw-bold">
                                Adresse email
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-envelope-fill"></i>
                                </span>

                                <input type="email" name="email" id="email_reset" class="form-control"
                                    placeholder="exemple@email.com" required>

                            </div>

                            <div class="form-text">
                                Utilisez l'adresse email associée à votre compte Evenprod.
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Annuler
                        </button>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send-fill me-1"></i>
                            Valider
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>
    <script src="assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>