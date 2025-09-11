<?php 
include '../../../config/fonction.php';

$id = $_GET['id'] ?? 0;
$serie = getSerieById($id);

// Si on clique sur supprimer
/* if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];

    if ($deleteId > 0) {
        if (deleteActeurBySerie($deleteId, $id)) {
            $message = "Acteur retiré de la série avec succès.";
        } else {
            $message = "Erreur lors de la suppression.";
        }
    }

    // Recharge la page
    header("Location: acteurs.php?id=" . $id);
    exit();
} */

$acteurs = getActeursBySerieId($id);
?>

<head>
    <link rel="stylesheet" href="acteurs.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<?php 
include '../../../includes/header.php';
?>
<?php
// Tableau PHP des acteurs
$acteurs = getActeursBySerieId($id);
?>

<div class="container">
    <div id="content" class="content p-0">
        <!-- En-tête de la série / maison de production -->
        <div class="profile-header">
            <div class="profile-header-cover"></div>
            <div class="profile-header-content">
                <div class="profile-header-img mb-4">
                    <img src="<?php echo $url_base; ?>uploads/series/<?php echo htmlspecialchars($serie['logo'])?>"
                        class="mb-4" alt="Avatar" />
                </div>
                <div class="profile-header-info">
                    <h4 class="m-t-sm"><?php echo htmlspecialchars($serie['titre'])?> / EVENPROD</h4>
                    <p class="m-b-sm">Gérer les acteurs associés à cette série</p>
                    <a href="serie_acteur.php?id_serie=<?php echo htmlspecialchars($serie['id'])?>"
                        class="btn btn-xs btn-primary mb-2">
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
                            <a href="#" class="acteur-item" data-id="<?= htmlspecialchars($acteur['serie_acteur']); ?>"
                                data-nom="<?= htmlspecialchars($acteur['nom']); ?>"
                                data-prenom="<?= htmlspecialchars($acteur['prenom']); ?>"
                                data-date="<?= htmlspecialchars($acteur['date_naissance']); ?>"
                                data-adresse="<?= htmlspecialchars($acteur['adresse']); ?>"
                                data-contact="<?= htmlspecialchars($acteur['contact']); ?>"
                                data-cachet="<?= htmlspecialchars(number_format($acteur['cachet'], 0, ',', ','));?> fcfa"
                                data-cv="<?= htmlspecialchars($acteur['cv_file'] ?? ''); ?>"
                                data-piece="<?= htmlspecialchars($acteur['piece_jointe'] ?? ''); ?>"
                                data-photo="<?= htmlspecialchars($acteur['photo']); ?>">

                                <div class="friend-img">
                                    <img src="<?= !empty($acteur['photo']) ? "../../../uploads/photos/" . htmlspecialchars($acteur['photo']) : 'https://bootdey.com/img/Content/avatar/avatar2.png'; ?>"
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
                        <li>
                            <div class="field"><i class="bi bi-cash-stack"></i> Cachet :</div>
                            <div class="value" id="info-cachet">---</div>
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
                                <a href="#" id="delete-link" class="text-danger" style="text-decoration:underline;"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.');">
                                    Supprimer
                                </a>
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
const cachet = document.getElementById('info-cachet');
const cvSection = document.getElementById('cv-section');
const cvLink = document.getElementById('info-cv');
const deleteLink = document.getElementById('delete-link');
const redirectUrl = "<?php echo $url_base; ?>public/appManager/acteur/acteurs.php?id=<?php echo $id; ?>";

items.forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();

        // Mettre à jour les infos
        prenom.textContent = this.dataset.prenom;
        nom.textContent = this.dataset.nom;
        date.textContent = this.dataset.date;
        adresse.textContent = this.dataset.adresse;
        contact.textContent = this.dataset.contact;
        cachet.textContent = this.dataset.cachet;

        // CV
        if (this.dataset.cv) {
            cvSection.style.display = 'block';
            cvLink.href = "../../../uploads/cv/" + this.dataset.cv;
        } else {
            cvSection.style.display = 'none';
        }
        // Mettre à jour le lien de suppression avec l'ID sélectionné
        deleteLink.href = "<?php echo $url_base; ?>public/appManager/delete.php?table=serie_acteur&id=" +
            this.dataset.id + "&redirect=" + encodeURIComponent(redirectUrl);

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