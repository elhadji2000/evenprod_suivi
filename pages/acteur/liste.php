<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<?php include '../../includes/header.php'; ?>
<?php include '../../config/fonction.php'; ?>

<?php
// Récupération des acteurs depuis la BDD
$sql = "SELECT id, nom, prenom, date_naissance, cv_file, adresse, contact, photo FROM acteurs ORDER BY id DESC";
$result = mysqli_query($connexion, $sql);

$acteurs = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $acteurs[] = $row;
    }
}
?>
<br>
<div class="container">
    <div id="content" class="content p-0">
        <!-- En-tête des acteurs -->
        <div class="profile-header">
            <div class="profile-header-cover"></div>
            <div class="profile-header-content">
                <div class="profile-header-img mb-4">
                    <img src="../../assets/images/logo.jpg" class="mb-4" alt="Acteurs" />
                </div>
                <div class="profile-header-info">
                    <h4 class="m-t-sm">Gestion des Acteurs</h4>
                    <p class="m-b-sm">Liste et gestion des acteurs de la plateforme</p>
                    <a href="add_act" class="btn btn-xs btn-primary mb-2">
                        <i class="bi bi-person-plus"></i> Ajouter un Acteur
                    </a>
                </div>
            </div>
        </div>

        <div class="profile-container">
            <div class="row row-space-20">
                <!-- Liste des acteurs -->
                <div class="col-md-8">
                    <ul class="friend-list clearfix">
                        <?php if (!empty($acteurs)): ?>
                        <?php foreach ($acteurs as $acteur): ?>
                        <li>
                            <a href="#" class="acteur-item" data-id="<?= htmlspecialchars($acteur['id']); ?>"
                                data-nom="<?= htmlspecialchars($acteur['nom']); ?>"
                                data-prenom="<?= htmlspecialchars($acteur['prenom']); ?>"
                                data-date="<?= htmlspecialchars($acteur['date_naissance']); ?>"
                                data-adresse="<?= htmlspecialchars($acteur['adresse']); ?>"
                                data-contact="<?= htmlspecialchars($acteur['contact']); ?>"
                                data-cv="<?= htmlspecialchars($acteur['cv_file'] ?? ''); ?>"
                                data-piece="<?= htmlspecialchars($acteur['piece_jointe'] ?? ''); ?>"
                                data-photo="<?= htmlspecialchars($acteur['photo']); ?>">

                                <div class="friend-img">
                                    <img src="<?= !empty($acteur['photo']) ? "../../uploads/photos/" . htmlspecialchars($acteur['photo']) : 'https://bootdey.com/img/Content/avatar/avatar2.png'; ?>"
                                        alt="Avatar" />
                                </div>

                                <div class="friend-info">
                                    <h4><?= htmlspecialchars($acteur['prenom']); ?>
                                        <?= htmlspecialchars($acteur['nom']); ?></h4>
                                    <p>Tel : <?= htmlspecialchars($acteur['contact']); ?></p>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p>Aucun acteur trouvé.</p>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Détails de l’acteur sélectionné -->
                <div class="col-md-4 hidden-xs hidden-sm">
                    <ul class="profile-info-list" id="acteur-details">
                        <li class="title">INFORMATIONS ACTEUR</li>
                        <li>
                            <div class="field"><i class="bi bi-person-fill"></i> Prénom :</div>
                            <div class="value" id="info-prenom">---</div>
                        </li>
                        <li>
                            <div class="field"><i class="bi bi-person-fill"></i> Nom :</div>
                            <div class="value" id="info-nom">---</div>
                        </li>
                        <li>
                            <div class="field"><i class="bi bi-calendar-fill"></i> Date de Naissance :</div>
                            <div class="value" id="info-date">---</div>
                        </li>
                        <li>
                            <div class="field"><i class="bi bi-geo-alt-fill"></i> Adresse :</div>
                            <div class="value" id="info-adresse">---</div>
                        </li>
                        <li>
                            <div class="field"><i class="bi bi-telephone-fill"></i> Téléphone :</div>
                            <div class="value" id="info-contact">---</div>
                        </li>

                        <!-- CV PDF -->
                        <li id="cv-section" style="display:none;">
                            <div class="field"><i class="bi bi-file-earmark-pdf"></i> CV :</div>
                            <div class="value"><a id="info-cv" href="#" target="_blank"
                                    style="text-decoration:underline;">Télécharger le CV</a></div>
                        </li>

                        <!-- Suppression -->
                        <li>
                            <div class="field"><i class="bi bi-trash-fill"></i> Action :</div>
                            <div class="value">
                                <!-- Supprimer -->
                                <a href="#" id="delete-link" class="text-danger" style="text-decoration:underline;"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.');">
                                    Supprimer
                                </a>

                                <!-- Modifier -->
                                <a href="#" id="edit-link" class="text-warning ms-3" style="text-decoration:underline;">
                                    Modifier
                                </a>
                            </div>
                        </li>

                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
const items = document.querySelectorAll('.acteur-item');
const prenom = document.getElementById('info-prenom');
const nom = document.getElementById('info-nom');
const date = document.getElementById('info-date');
const adresse = document.getElementById('info-adresse');
const contact = document.getElementById('info-contact');
const cvSection = document.getElementById('cv-section');
const cvLink = document.getElementById('info-cv');
const deleteLink = document.getElementById('delete-link');
const editLink = document.getElementById('edit-link');
const redirectUrl = "<?php echo $url_base; ?>pages/acteur/liste.php";
items.forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();

        // Mettre à jour les infos
        prenom.textContent = this.dataset.prenom;
        nom.textContent = this.dataset.nom;
        date.textContent = this.dataset.date;
        adresse.textContent = this.dataset.adresse;
        contact.textContent = this.dataset.contact;

        // CV
        if (this.dataset.cv) {
            cvSection.style.display = 'block';
            cvLink.href = "../../uploads/cv/" + this.dataset.cv;
        } else {
            cvSection.style.display = 'none';
        }
        // Mettre à jour le lien de suppression avec l'ID sélectionné
        deleteLink.href = "<?php echo $url_base; ?>public/appManager/delete.php?table=acteurs&id=" +
            this.dataset.id + "&redirect=" + encodeURIComponent(redirectUrl);
        editLink.href = "<?php echo $url_base; ?>pages/acteur/add_act.php?id=" + this.dataset.id;
    });
});
</script>
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Création de l'alerte
    const toast = document.createElement("div");
    toast.textContent = "acteur suppimer avec succès !";
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