<?php 
include '../../config/fonction.php'; 
session_start();

$userId = $_SESSION['id'] ?? 0;
$user = getUserById($connexion, $userId);

if (!$user) {
    die("Utilisateur introuvable");
}

// Préparer les variables pour le toast
$toastMessage = "";
$toastType = "success"; // success | danger | warning
$showToast = false;

// Traitement changement de mot de passe
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['old_password'])) {
    $old_password = sha1($_POST['old_password']);
    $new_password = sha1($_POST['new_password']);
    $confirm_password = sha1($_POST['confirm_password']);

    if ($old_password !== $user['password']) {
        $toastMessage = "❌ Ancien mot de passe incorrect.";
        $toastType = "danger";
        $showToast = true;
    } elseif ($new_password !== $confirm_password) {
        $toastMessage = "⚠️ Les mots de passe ne correspondent pas.";
        $toastType = "warning";
        $showToast = true;
    } else {
        $stmt = $connexion->prepare("UPDATE users SET mot_de_passe = ?, updated = 1 WHERE id = ?");
        $stmt->bind_param("si", $new_password, $userId);

        if ($stmt->execute()) {
            // Détruire la session pour forcer une reconnexion
            session_unset();
            session_destroy();

            // Rediriger vers la page de login
            header("Location: ../../index.php?passwordChanged=1");
            exit();
        } else {
            $toastMessage = "❌ Erreur lors de la mise à jour.";
            $toastType = "danger";
            $showToast = true;
        }
        $stmt->close();
    }
}

?>

<?php include '../../includes/header.php'; ?>

<head>
    <link rel="stylesheet" href="profile_style.css">
    <link rel="stylesheet" href="<?php echo $url_base; ?>pages/acteur/add.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<style>
/* Style profil */
.profile-card {
    max-width: 800px;
    margin: 30px auto;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    font-family: "Segoe UI", sans-serif;
}

.profile-header {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: #fff;
    text-align: center;
    padding: 40px 20px;
}

.profile-header img {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    border: 4px solid #fff;
    object-fit: cover;
    margin-bottom: 15px;
}

.profile-body {
    padding: 20px 30px;
}

.profile-body h5 {
    font-weight: bold;
    color: #333;
}

.info-item {
    margin-bottom: 15px;
}
</style>

<section class="section">
    <div class="container">
        <?php if (isset($_GET['forceUpdate'])): ?>
        <div class="alert alert-warning text-center">
            ⚠️ Vous devez modifier votre mot de passe avant de continuer.
        </div>
        <?php endif; ?>

        <div class="profile-card">
            <!-- Header -->
            <div class="profile-header">
                <img src="../../uploads/profile/<?= htmlspecialchars($user['profile']); ?>" alt="Photo de profil">
                <h3><?= htmlspecialchars($user['prenom']); ?> <?= htmlspecialchars($user['nom']); ?></h3>
                <small><?= htmlspecialchars($user['role']); ?></small>
            </div>

            <!-- Infos utilisateur -->
            <div class="profile-body">
                <div class="row">
                    <div class="col-md-6 info-item">
                        <h5><i class="bi bi-envelope"></i> Email</h5>
                        <p><?= htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div class="col-md-6 info-item">
                        <h5><i class="bi bi-telephone"></i> Téléphone</h5>
                        <p><?= htmlspecialchars($user['telephone']); ?></p>
                    </div>
                    <div class="col-md-6 info-item">
                        <h5><i class="bi bi-calendar"></i> Date d’inscription</h5>
                        <p><?= date("d/m/Y", strtotime($user['created_at'])); ?></p>
                    </div>
                    <div class="col-md-6 info-item">
                        <h5><i class="bi bi-shield-lock"></i> Statut</h5>
                        <p><?= $user['statut'] ? "Actif" : "Désactivé"; ?></p>
                    </div>
                </div>

                <!-- Bouton changer mot de passe -->
                <div class="text-center mt-4">
                    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#passwordModal">
                        <i class="bi bi-key"></i> Modifier le mot de passe
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal mot de passe -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" class="contact-form">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel"><i class="bi bi-key-fill"></i> Changer le mot de
                        passe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="old_password" class="form-label">Ancien mot de passe</label>
                        <input type="password" name="old_password" id="old_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Bootstrap -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="liveToast" class="toast align-items-center text-white bg-<?= $toastType ?> border-0" role="alert"
        aria-live="assertive" aria-atomic="true" <?php if($showToast) echo 'data-bs-autohide="true"'; ?>>
        <div class="d-flex">
            <div class="toast-body">
                <?= $toastMessage ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Fermer"></button>
        </div>
    </div>
</div>

<?php if ($showToast): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var toastEl = document.getElementById("liveToast");
    var toast = new bootstrap.Toast(toastEl);
    toast.show();
});
</script>
<?php endif; ?>