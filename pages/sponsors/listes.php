<?php
include '../../config/fonction.php';

$clients = getClients($connexion);
?>

<?php include '../../includes/header.php'; 
$redirectUrl = "<?php echo $url_base; ?>pages/sponsors/listes.php";
?>

<body>
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-lg-8">
                <div class="section-title">
                    <h2>Liste des partenariats</h2>
                    <p>Clients, Sponsors et Partenaires enregistrés dans la plateforme.</p>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <a href="add_spon.php" class="btn btn-warning">
                    <i class="fas fa-plus"></i> Ajouter
                </a>
            </div>
        </div>

        <?php if (!empty($clients)) : ?>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-bordered align-middle mb-0">
                <thead>
                    <!-- même couleur que btn-warning -->
                    <tr class="bg-light">
                        <th scope="col" class="text-center">Logo</th>
                        <th scope="col" class="text-center">Nom</th>
                        <th scope="col" class="text-center">NINEA</th>
                        <th scope="col" class="text-center">Contact</th>
                        <th scope="col" class="text-center">Adresse</th>
                        <th scope="col" class="text-center">Ajouté le</th>
                        <th scope="col" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $cl) : ?>
                    <tr>
                        <td class="text-center">
                            <img src="<?= !empty($cl['logo']) ? '../../uploads/logos/' . htmlspecialchars($cl['logo']) : '../../assets/images/default_logo.png' ?>"
                                alt="<?= htmlspecialchars($cl['nom']) ?>" class="img-thumbnail"
                                style="width:60px; height:60px; object-fit:contain;">
                        </td>
                        <td class="text-center">
                            <a href="details_client.php?id=<?= htmlspecialchars($cl['id']) ?>"
                                class="text-dark fw-bold text-decoration-none">
                                <?= htmlspecialchars($cl['nom']) ?>
                            </a>
                        </td>
                        <td class="text-center"><?= htmlspecialchars($cl['ninea']) ?></td>
                        <td class="text-center"><?= nl2br(htmlspecialchars($cl['contact'])) ?></td>
                        <td class="text-center"><?= nl2br(htmlspecialchars($cl['adresse']?? "non defini")) ?></td>
                        <td class="text-center"><?= date("d/m/Y", strtotime($cl['created_at'])) ?></td>
                        <td class="text-center">
                            <a href="add_spon.php?id=<?= htmlspecialchars($cl['id']) ?>" class="text-decoration-underline text-primary me-3">
                                Modifier
                            </a>
                            <a href="<?php echo $url_base; ?>public/appManager/delete.php?table=clients&id=<?= htmlspecialchars($cl['id']) ?>&redirect=<?php echo $url_base; ?>pages/sponsors/listes.php"
                                class="text-decoration-underline text-danger"
                                onclick="return confirm('Voulez-vous vraiment supprimer ce client ?');">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
        <p class="text-center">Aucun partenariat trouvé.</p>
        <?php endif; ?>
    </div>
</body>


<?php /*  include '../../includes/footer.php'; */  ?>

</html>
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Création de l'alerte
    const toast = document.createElement("div");
    toast.textContent = "client suppimer avec succès !";
    toast.style.position = "fixed";
    toast.style.top = "80px";
    toast.style.right = "30px";
    toast.style.padding = "15px 25px";
    toast.style.backgroundColor = "#d21515ff"; // vert succès
    toast.style.color = "white";
    toast.style.borderRadius = "5px";
    toast.style.boxShadow = "0 2px 10px rgba(0,0,0,0.2)";
    toast.style.zIndex = 9999;
    toast.style.fontWeight = "bold";
    toast.style.opacity = 0;
    toast.style.transition = "opacity 0.5s";

    document.body.appendChild(toast);

    // Animation pour fade in
    setTimeout(() => {
        toast.style.opacity = 1;
    }, 100);

    // Disparition après 3 secondes
    setTimeout(() => {
        toast.style.opacity = 0;
        setTimeout(() => toast.remove(), 500);
    }, 5000);
});
</script>
<?php endif; ?>
<?php if (isset($_GET['success']) && $_GET['success'] == 2): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Création de l'alerte
    const toast = document.createElement("div");
    toast.textContent = "client ajouter avec succès !";
    toast.style.position = "fixed";
    toast.style.top = "80px";
    toast.style.right = "30px";
    toast.style.padding = "15px 25px";
    toast.style.backgroundColor = "#1ad11dff"; // vert succès
    toast.style.color = "white";
    toast.style.borderRadius = "5px";
    toast.style.boxShadow = "0 2px 10px rgba(0,0,0,0.2)";
    toast.style.zIndex = 9999;
    toast.style.fontWeight = "bold";
    toast.style.opacity = 0;
    toast.style.transition = "opacity 0.5s";

    document.body.appendChild(toast);

    // Animation pour fade in
    setTimeout(() => {
        toast.style.opacity = 1;
    }, 100);

    // Disparition après 3 secondes
    setTimeout(() => {
        toast.style.opacity = 0;
        setTimeout(() => toast.remove(), 500);
    }, 5000);
});
</script>
<?php endif; ?>
<?php if (isset($_GET['success']) && $_GET['success'] == 3): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Création de l'alerte
    const toast = document.createElement("div");
    toast.textContent = "client modifier avec succès !";
    toast.style.position = "fixed";
    toast.style.top = "80px";
    toast.style.right = "30px";
    toast.style.padding = "15px 25px";
    toast.style.backgroundColor = "#bcd11aff"; // vert succès
    toast.style.color = "white";
    toast.style.borderRadius = "5px";
    toast.style.boxShadow = "0 2px 10px rgba(0,0,0,0.2)";
    toast.style.zIndex = 9999;
    toast.style.fontWeight = "bold";
    toast.style.opacity = 0;
    toast.style.transition = "opacity 0.5s";

    document.body.appendChild(toast);

    // Animation pour fade in
    setTimeout(() => {
        toast.style.opacity = 1;
    }, 100);

    // Disparition après 3 secondes
    setTimeout(() => {
        toast.style.opacity = 0;
        setTimeout(() => toast.remove(), 500);
    }, 5000);
});
</script>
<?php endif; ?>