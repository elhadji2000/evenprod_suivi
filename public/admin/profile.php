<?php
include '../../config/fonction.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['id'] ?? 0;
$user = getUserById($connexion, $userId);

if (!$user) {
    die("Utilisateur introuvable");
}

/*
|--------------------------------------------------------------------------
| Variables toast
|--------------------------------------------------------------------------
*/
$toastMessage = "";
$toastType = "success";
$showToast = false;

/*
|--------------------------------------------------------------------------
| Changement de mot de passe
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['old_password'])) {

    $old_password = sha1($_POST['old_password']);
    $new_password = sha1($_POST['new_password']);
    $confirm_password = sha1($_POST['confirm_password']);

    if ($old_password !== $user['password']) {

        $toastMessage = "Ancien mot de passe incorrect.";
        $toastType = "danger";
        $showToast = true;

    } elseif ($new_password !== $confirm_password) {

        $toastMessage = "Les mots de passe ne correspondent pas.";
        $toastType = "warning";
        $showToast = true;

    } elseif ($_POST['new_password'] === $_POST['old_password']) {

        $toastMessage = "Le nouveau mot de passe doit être différent de l'ancien.";
        $toastType = "warning";
        $showToast = true;

    } else {

        $stmt = $connexion->prepare("
            UPDATE users
            SET mot_de_passe = ?, updated = 1
            WHERE id = ?
        ");

        $stmt->bind_param("si", $new_password, $userId);

        if ($stmt->execute()) {

            $stmt->close();

            /*
             * Déconnexion afin de forcer une nouvelle authentification
             */
            session_unset();
            session_destroy();

            header("Location: ../../index.php?passwordChanged=1");
            exit();

        } else {

            $toastMessage = "Une erreur est survenue lors de la modification.";
            $toastType = "danger";
            $showToast = true;

            $stmt->close();
        }
    }
}
?>

<?php include '../../includes/header.php'; ?>

<head>

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>

        /* =========================================================
           PROFIL UTILISATEUR
        ========================================================= */

        :root {
            --primary-color: #111827;
            --primary-hover: #1f2937;
            --text-color: #1f2937;
            --muted-color: #6b7280;
            --border-color: #e5e7eb;
            --background-color: #f6f7f9;
            --success-color: #198754;
        }

        .profile-page {
            background: var(--background-color);
            min-height: calc(100vh - 80px);
            padding: 35px 0 60px;
        }

        /* =========================================================
           TITRE DE PAGE
        ========================================================= */

        .profile-page-header {
            margin-bottom: 25px;
        }

        .profile-page-header .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted-color);
            margin-bottom: 7px;
        }

        .profile-page-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 750;
            color: var(--text-color);
        }

        .profile-page-header p {
            margin: 7px 0 0;
            color: var(--muted-color);
            font-size: 14px;
        }

        /* =========================================================
           CARTE PRINCIPALE
        ========================================================= */

        .profile-wrapper {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(15, 23, 42, .05);
        }

        /* =========================================================
           BLOC IDENTITE
        ========================================================= */

        .profile-identity {
            padding: 30px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .profile-avatar {
            width: 105px;
            height: 105px;
            min-width: 105px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #fff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .10);
            background: #f1f3f5;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-identity-content {
            flex: 1;
        }

        .profile-identity-content h2 {
            margin: 0 0 6px;
            font-size: 24px;
            font-weight: 750;
            color: var(--text-color);
        }

        .profile-role {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f3f4f6;
            color: #4b5563;
            border-radius: 20px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .profile-status {
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: var(--muted-color);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-active {
            background: #20c997;
        }

        .status-inactive {
            background: #dc3545;
        }

        /* =========================================================
           INFORMATIONS
        ========================================================= */

        .profile-section {
            padding: 30px;
        }

        .section-heading {
            margin-bottom: 22px;
        }

        .section-heading h3 {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
            color: var(--text-color);
        }

        .section-heading p {
            margin: 5px 0 0;
            font-size: 13px;
            color: var(--muted-color);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
        }

        .info-card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 17px;
            background: #fff;
            transition: all .2s ease;
        }

        .info-card:hover {
            border-color: #d1d5db;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(15, 23, 42, .04);
        }

        .info-card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 9px;
        }

        .info-icon {
            width: 35px;
            height: 35px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            color: var(--primary-color);
            font-size: 16px;
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--muted-color);
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-color);
            word-break: break-word;
        }

        /* =========================================================
           SECURITE
        ========================================================= */

        .security-section {
            border-top: 1px solid var(--border-color);
            padding: 25px 30px;
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .security-content {
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .security-icon {
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eef2ff;
            color: #4338ca;
            font-size: 18px;
        }

        .security-content h4 {
            margin: 0 0 3px;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-color);
        }

        .security-content p {
            margin: 0;
            color: var(--muted-color);
            font-size: 12px;
        }

        .btn-password {
            border: 0;
            border-radius: 9px;
            padding: 10px 17px;
            background: var(--primary-color);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all .2s ease;
            white-space: nowrap;
        }

        .btn-password:hover {
            background: var(--primary-hover);
            color: #fff;
            transform: translateY(-1px);
        }

        /* =========================================================
           MODAL
        ========================================================= */

        .password-modal .modal-content {
            border: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .15);
        }

        .password-modal .modal-header {
            padding: 20px 22px;
            border-bottom: 1px solid var(--border-color);
        }

        .password-modal .modal-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .password-modal .modal-body {
            padding: 22px;
        }

        .password-modal .modal-footer {
            padding: 15px 22px;
            border-top: 1px solid var(--border-color);
            background: #fafafa;
        }

        .password-field {
            margin-bottom: 18px;
        }

        .password-field:last-child {
            margin-bottom: 0;
        }

        .password-field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 7px;
        }

        .password-input-wrapper {
            position: relative;
        }

        .password-input {
            width: 100%;
            height: 45px;
            border: 1px solid var(--border-color);
            border-radius: 9px;
            padding: 0 43px 0 13px;
            font-size: 13px;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .password-input:focus {
            border-color: #9ca3af;
            box-shadow: 0 0 0 3px rgba(17, 24, 39, .06);
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #6b7280;
            padding: 3px;
            cursor: pointer;
        }

        .btn-save-password {
            border: 0;
            border-radius: 8px;
            background: var(--primary-color);
            color: #fff;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-save-password:hover {
            background: var(--primary-hover);
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 767px) {

            .profile-page {
                padding: 20px 0 40px;
            }

            .profile-page-header h1 {
                font-size: 23px;
            }

            .profile-wrapper {
                border-radius: 14px;
            }

            .profile-identity {
                padding: 24px 18px;
                flex-direction: column;
                text-align: center;
            }

            .profile-avatar {
                width: 90px;
                height: 90px;
                min-width: 90px;
            }

            .profile-identity-content h2 {
                font-size: 20px;
            }

            .profile-status {
                justify-content: center;
            }

            .profile-section {
                padding: 22px 18px;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .security-section {
                padding: 20px 18px;
                flex-direction: column;
                align-items: stretch;
            }

            .btn-password {
                justify-content: center;
                width: 100%;
            }
        }

    </style>

</head>


<section class="profile-page">

    <div class="container">

        <!-- =====================================================
             EN-TÊTE
        ====================================================== -->

        <div class="profile-page-header">

            <div class="eyebrow">
                <i class="bi bi-person-circle"></i>
                Mon espace
            </div>

            <h1>Mon profil</h1>

            <p>
                Consultez vos informations personnelles et gérez la sécurité de votre compte.
            </p>

        </div>


        <!-- =====================================================
             CARTE PROFIL
        ====================================================== -->

        <div class="profile-wrapper">


            <!-- IDENTITÉ -->

            <div class="profile-identity">

                <div class="profile-avatar">

                    <?php
                    $profileImage = !empty($user['profile'])
                        ? "../../uploads/profile/" . htmlspecialchars($user['profile'])
                        : "../../assets/images/logo.jpg";
                    ?>

                    <img
                        src="<?= $profileImage ?>"
                        alt="Photo de profil"
                    >

                </div>


                <div class="profile-identity-content">

                    <h2>
                        <?= htmlspecialchars($user['prenom']); ?>
                        <?= htmlspecialchars($user['nom']); ?>
                    </h2>

                    <span class="profile-role">
                        <i class="bi bi-shield-check"></i>
                        <?= htmlspecialchars($user['role']); ?>
                    </span>


                    <div class="profile-status">

                        <span class="status-dot <?= $user['statut'] ? 'status-active' : 'status-inactive'; ?>"></span>

                        <?= $user['statut'] ? 'Compte actif' : 'Compte désactivé'; ?>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 INFORMATIONS
            ================================================== -->

            <div class="profile-section">

                <div class="section-heading">

                    <h3>Informations personnelles</h3>

                    <p>
                        Les informations associées à votre compte.
                    </p>

                </div>


                <div class="info-grid">


                    <!-- EMAIL -->

                    <div class="info-card">

                        <div class="info-card-header">

                            <div class="info-icon">
                                <i class="bi bi-envelope"></i>
                            </div>

                            <span class="info-label">
                                Adresse e-mail
                            </span>

                        </div>

                        <div class="info-value">
                            <?= htmlspecialchars($user['email']); ?>
                        </div>

                    </div>


                    <!-- TELEPHONE -->

                    <div class="info-card">

                        <div class="info-card-header">

                            <div class="info-icon">
                                <i class="bi bi-telephone"></i>
                            </div>

                            <span class="info-label">
                                Téléphone
                            </span>

                        </div>

                        <div class="info-value">
                            <?= htmlspecialchars($user['telephone'] ?: 'Non renseigné'); ?>
                        </div>

                    </div>


                    <!-- DATE INSCRIPTION -->

                    <div class="info-card">

                        <div class="info-card-header">

                            <div class="info-icon">
                                <i class="bi bi-calendar3"></i>
                            </div>

                            <span class="info-label">
                                Date d'inscription
                            </span>

                        </div>

                        <div class="info-value">

                            <?php
                            if (!empty($user['created_at'])) {
                                echo date(
                                    "d/m/Y",
                                    strtotime($user['created_at'])
                                );
                            } else {
                                echo "Non renseignée";
                            }
                            ?>

                        </div>

                    </div>


                    <!-- ROLE -->

                    <div class="info-card">

                        <div class="info-card-header">

                            <div class="info-icon">
                                <i class="bi bi-person-badge"></i>
                            </div>

                            <span class="info-label">
                                Fonction
                            </span>

                        </div>

                        <div class="info-value">
                            <?= htmlspecialchars($user['role']); ?>
                        </div>

                    </div>


                </div>

            </div>


            <!-- =================================================
                 SECURITE
            ================================================== -->

            <div class="security-section">

                <div class="security-content">

                    <div class="security-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>

                    <div>

                        <h4>
                            Sécurité du compte
                        </h4>

                        <p>
                            Modifiez régulièrement votre mot de passe pour protéger votre compte.
                        </p>

                    </div>

                </div>


                <button
                    type="button"
                    class="btn-password"
                    data-bs-toggle="modal"
                    data-bs-target="#passwordModal"
                >

                    <i class="bi bi-key"></i>

                    Modifier le mot de passe

                </button>

            </div>


        </div>

    </div>

</section>


<!-- =========================================================
     MODAL MOT DE PASSE
========================================================= -->

<div
    class="modal fade password-modal"
    id="passwordModal"
    tabindex="-1"
    aria-labelledby="passwordModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form method="POST">


                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="passwordModalLabel"
                    >

                        <i class="bi bi-shield-lock"></i>

                        Modifier le mot de passe

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Fermer"
                    ></button>

                </div>


                <div class="modal-body">


                    <!-- ANCIEN MOT DE PASSE -->

                    <div class="password-field">

                        <label for="old_password">
                            Ancien mot de passe
                        </label>

                        <div class="password-input-wrapper">

                            <input
                                type="password"
                                name="old_password"
                                id="old_password"
                                class="password-input"
                                placeholder="Votre ancien mot de passe"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-target="old_password"
                            >
                                <i class="bi bi-eye"></i>
                            </button>

                        </div>

                    </div>


                    <!-- NOUVEAU MOT DE PASSE -->

                    <div class="password-field">

                        <label for="new_password">
                            Nouveau mot de passe
                        </label>

                        <div class="password-input-wrapper">

                            <input
                                type="password"
                                name="new_password"
                                id="new_password"
                                class="password-input"
                                placeholder="Votre nouveau mot de passe"
                                minlength="6"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-target="new_password"
                            >
                                <i class="bi bi-eye"></i>
                            </button>

                        </div>

                    </div>


                    <!-- CONFIRMATION -->

                    <div class="password-field">

                        <label for="confirm_password">
                            Confirmer le nouveau mot de passe
                        </label>

                        <div class="password-input-wrapper">

                            <input
                                type="password"
                                name="confirm_password"
                                id="confirm_password"
                                class="password-input"
                                placeholder="Confirmez le nouveau mot de passe"
                                minlength="6"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-target="confirm_password"
                            >
                                <i class="bi bi-eye"></i>
                            </button>

                        </div>

                    </div>


                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        Annuler
                    </button>

                    <button
                        type="submit"
                        class="btn-save-password"
                    >

                        <i class="bi bi-check2"></i>

                        Enregistrer

                    </button>

                </div>


            </form>

        </div>

    </div>

</div>


<!-- =========================================================
     TOAST
========================================================= -->

<div
    class="position-fixed top-0 end-0 p-3"
    style="z-index:9999;"
>

    <div
        id="liveToast"
        class="toast align-items-center text-white bg-<?= htmlspecialchars($toastType); ?> border-0"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
    >

        <div class="d-flex">

            <div class="toast-body">

                <?= htmlspecialchars($toastMessage); ?>

            </div>

            <button
                type="button"
                class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast"
                aria-label="Fermer"
            ></button>

        </div>

    </div>

</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    /*
     * Affichage du mot de passe
     */
    document.querySelectorAll(".toggle-password").forEach(function (button) {

        button.addEventListener("click", function () {

            const targetId = this.getAttribute("data-target");
            const input = document.getElementById(targetId);
            const icon = this.querySelector("i");

            if (input.type === "password") {

                input.type = "text";

                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");

            } else {

                input.type = "password";

                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");

            }

        });

    });


    /*
     * Toast
     */
    <?php if ($showToast): ?>

    const toastElement = document.getElementById("liveToast");

    if (toastElement && typeof bootstrap !== "undefined") {

        const toast = new bootstrap.Toast(toastElement, {
            delay: 5000
        });

        toast.show();

    }

    <?php endif; ?>


    /*
     * Validation simple du nouveau mot de passe
     */
    const passwordForm = document.querySelector("#passwordModal form");

    if (passwordForm) {

        passwordForm.addEventListener("submit", function (event) {

            const newPassword =
                document.getElementById("new_password").value;

            const confirmPassword =
                document.getElementById("confirm_password").value;

            if (newPassword !== confirmPassword) {

                event.preventDefault();

                alert("Les mots de passe ne correspondent pas.");

            }

        });

    }

});

</script>

<?php include '../../includes/footer.php'; ?>