<?php include '../../config/fonction.php'; 
$lastSerie = getLastSerie(); 
$id = $_GET['id'] ?? 0;
$user = null;
if ($id) {
    $user = getUserById($connexion, $id);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_user'] ?? null;
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $telephone = $_POST['telephone'];
    $photoFile = $_FILES['photo'] ?? null;

    if ($id) {
        // Modification
        $result = modifierUser($id, $nom, $prenom, $email, $telephone, $role, $photoFile);
    } else {
        // Ajout
        $result = ajouterUser($nom, $prenom, $email, $telephone, $role, $photoFile);
    }

    if ($result === "success") {
        header("Location: add_user?success=1");
        exit;
    } elseif ($result === "exists") {
        header("Location: add_user?error=exists");
        exit;
    } else {
        header("Location: add_user?error=1");
        exit;
    }
}

?>
<?php include '../../includes/header.php'; ?>

<head>
    <link rel="stylesheet" href="<?php echo $url_base; ?>pages/acteur/add.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
</head>

<section class="section gray-bg" id="contactus">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="section-title">
                    <h2>Ajouter un utilisateur</h2>
                    <p>Complétez le formulaire pour enregistrer un nouvel utilisateur.</p>
                </div>
            </div>
        </div>
        <div class="row flex-row-reverse">
            <div class="col-md-7 col-lg-8 m-15px-tb">
                <div class="contact-form">
                    <form action="add_user" method="post" enctype="multipart/form-data"
                        class="contactform contact_form" id="contact_form">

                        <!-- Champ caché pour l'id (null si ajout) -->
                        <input type="hidden" name="id_user" value="<?php echo $user['id'] ?? ''; ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="nom" name="nom" type="text" placeholder="Nom"
                                        value="<?php echo $user['nom'] ?? ''; ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="prenom" name="prenom" type="text" placeholder="Prénom"
                                        value="<?php echo $user['prenom'] ?? ''; ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="email" name="email" type="email" placeholder="Email"
                                        value="<?php echo $user['email'] ?? ''; ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="telephone" name="telephone" type="tel" placeholder="Téléphone"
                                        value="<?php echo $user['telephone'] ?? ''; ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select id="role" name="role" class="form-control" required>
                                        <option value="">-- Sélectionnez un rôle --</option>
                                        <option value="tournage"
                                            <?php echo (isset($user['role']) && $user['role']=='tournage')?'selected':''; ?>>
                                            Tournage</option>
                                        <option value="comptable"
                                            <?php echo (isset($user['role']) && $user['role']=='comptable')?'selected':''; ?>>
                                            Comptable</option>
                                        <option value="caisse"
                                            <?php echo (isset($user['role']) && $user['role']=='caisse')?'selected':''; ?>>
                                            Caisse</option>
                                        <option value="admin"
                                            <?php echo (isset($user['role']) && $user['role']=='admin')?'selected':''; ?>>
                                            Admin</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Photo de profil -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="file" id="photo" name="photo" accept="image/*"
                                        class="form-control-file" <?php echo empty($user) ? 'required' : ''; ?> hidden>

                                    <label for="photo" class="custom-file-label form-control">
                                        <span><i class="fas fa-upload"></i> Choisir une photo</span>
                                        <span id="file-name" class="file-name"><?php echo $user['profile'] ?? 'Aucun fichier choisi'; ?></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="send">
                                    <button type="submit" class="px-btn theme">
                                        <span><?php echo empty($user) ? 'ENREGISTRER' : 'MODIFIER'; ?></span> <i
                                            class="arrow"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <!-- Partie droite (Aperçu / infos rapides) -->
            <div class="col-md-5 col-lg-4 m-15px-tb">
                <div class="contact-name">
                    <h5>Dernière série ajoutée</h5>
                    <?php if($lastSerie): ?>
                    <p>Nom : <?= htmlspecialchars($lastSerie['titre']) ?></p>
                    <p>Type : <?= htmlspecialchars($lastSerie['type']) ?></p>
                    <p>Budget : <?= number_format($lastSerie['budget'], 0, ',', ',') ?> fcfa</p>
                    <?php else: ?>
                    <p>Aucune série enregistrée pour le moment.</p>
                    <?php endif; ?>
                </div>
                <div class="contact-name shortcut-links">
                    <h5>Raccourcis</h5>
                    <p><a href="#">Voir tout</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const inputFile = document.getElementById("photo");
const fileName = document.getElementById("file-name");

inputFile.addEventListener("change", function() {
    if (this.files && this.files.length > 0) {
        fileName.textContent = this.files[0].name;
    } else {
        fileName.textContent = "Aucun fichier choisi";
    }
});
</script>
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    showToast("Utilisateur ajouté avec succès !", "#1ad11dff");
});
</script>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == "exists"): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    showToast("⚠️ Cet utilisateur existe déjà !", "#e74c3c");
});
</script>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    showToast("❌ Erreur lors de l’enregistrement.", "#e67e22");
});
</script>
<?php endif; ?>

<script>
function showToast(message, bgColor) {
    const toast = document.createElement("div");
    toast.textContent = message;
    toast.style.position = "fixed";
    toast.style.top = "80px";
    toast.style.right = "30px";
    toast.style.padding = "15px 25px";
    toast.style.backgroundColor = bgColor;
    toast.style.color = "white";
    toast.style.borderRadius = "5px";
    toast.style.boxShadow = "0 2px 10px rgba(0,0,0,0.2)";
    toast.style.zIndex = 9999;
    toast.style.fontWeight = "bold";
    toast.style.opacity = 0;
    toast.style.transition = "opacity 0.5s";

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = 1;
    }, 100);
    setTimeout(() => {
        toast.style.opacity = 0;
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}
</script>