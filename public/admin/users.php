<?php include '../../config/fonction.php'; 
$users = getUsers($connexion);
?>
<?php 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $userId = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === "enable") {
        mysqli_query($connexion, "UPDATE users SET statut = 1 WHERE id = $userId");
        header("Location: users.php");
        exit;
    } elseif ($action === "disable") {
        mysqli_query($connexion, "UPDATE users SET statut = 0 WHERE id = $userId");
        header("Location: users.php");
        exit;
    }
}
?>

<head>
    <link rel="stylesheet" href="users_style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<?php include '../../includes/header.php'; ?>
<style>
.user-img {
    width: 150px;
    /* largeur fixe */
    height: 150px;
    /* hauteur fixe */
    border-radius: 50%;
    /* rend l'image ronde */
    object-fit: cover;
    /* ajuste l'image pour remplir sans déformation */
    border: 5px solid #fff;
    /* garde ta bordure blanche */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    /* effet d’ombre */
}
</style>
<section id="team" class="section bg-gray-100">
    <div class="container">
        <div class="row section-heading justify-content-center text-center wow fadeInUp" data-wow-duration="0.3s"
            data-wow-delay="0.3s"
            style="visibility: visible; animation-duration: 0.3s; animation-delay: 0.3s; animation-name: fadeInUp;">
            <div class="col-lg-8 col-xl-6">
                <br>
                <h3 class="h1 bg-primary-after after-50px pb-3 mb-3">Nos Utilisateurs</h3>
                <div class="lead">Voici la liste des utilisateurs enregistrés dans le système.</div>
            </div>
        </div>
        <div class="row">
            <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
            <div class="col-lg-3 col-sm-6 my-3 wow fadeInUp" data-wow-duration="0.3s" data-wow-delay="0.3s"
                style="visibility: visible; animation-duration: 0.3s; animation-delay: 0.3s; animation-name: fadeInUp;">
                <div class="hover-top-in text-center">
                    <div class="overflow-hidden z-index-1 position-relative px-5">
                        <img class="user-img"
                            src="../../uploads/profile/<?= htmlspecialchars($user['profile']); ?>" 
                            alt="Photo de <?= htmlspecialchars($user['prenom']); ?>">
                    </div>
                    <div
                        class="mx-2 mx-xl-3 shadow rounded-3 position-relative mt-n4 bg-white p-4 pt-6 mx-4 text-center hover-top--in">
                        <h6 class="fw-700 dark-color mb-1">
                            <?= htmlspecialchars($user['prenom']); ?> <?= htmlspecialchars($user['nom']); ?>
                        </h6>
                        <small><?= htmlspecialchars($user['role']); ?></small>
                        
                        <!-- Boutons d’action -->
                        <div class="pt-3 d-flex justify-content-center gap-2">
                            <!-- Bouton Activer / Désactiver -->
                            <?php if ($user['statut'] == 1): ?>
                                <a href="users.php?id=<?= $user['id']; ?>&action=disable" 
                                   class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur ? .');">
                                    <i class="bi bi-x-circle"></i> Désactiver
                                </a>
                            <?php else: ?>
                                <a href="users.php?id=<?= $user['id']; ?>&action=enable" 
                                   class="btn btn-sm btn-outline-success" onclick="return confirm('Êtes-vous sûr de vouloir activer cet utilisateur ? .');">
                                    <i class="bi bi-check-circle"></i> Activer
                                </a>
                            <?php endif; ?>

                            <!-- Bouton Modifier -->
                            <a href="add_user.php?id=<?= htmlspecialchars($user['id']); ?>" 
                               class="btn btn-sm btn-outline-info">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p>Aucun utilisateur trouvé.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
