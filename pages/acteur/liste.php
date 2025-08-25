<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<?php include '../../includes/header.php'; ?>
<?php include '../../config/fonction.php'; ?>

<?php
// Récupération des acteurs depuis la BDD
$sql = "SELECT id, nom, prenom, date_naissance, adresse, contact, photo FROM acteurs ORDER BY id DESC";
$result = mysqli_query($connexion, $sql);

$acteurs = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $acteurs[] = $row;
    }
}
?>

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
                    <a href="add_act.php" class="btn btn-xs btn-primary mb-2">
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
                            <a href="#" class="acteur-item" data-nom="<?= htmlspecialchars($acteur['nom']); ?>"
                                data-date="<?= htmlspecialchars($acteur['date_naissance']); ?>"
                                data-prenom="<?= htmlspecialchars($acteur['prenom']); ?>"
                                data-pays="<?= htmlspecialchars($acteur['adresse']); ?>"
                                data-tel="<?= htmlspecialchars($acteur['contact']); ?>"
                                data-avatar="<?= htmlspecialchars($acteur['photo']); ?>">
                                <div class="friend-img">
                                    <img src="<?= !empty($acteur['photo']) ? "../../uploads/photos/" . htmlspecialchars($acteur['photo']) : 'https://bootdey.com/img/Content/avatar/avatar2.png'; ?>" alt="Avatar" />
                                </div>

                                <div class="friend-info">
                                    <h4><?= htmlspecialchars($acteur['prenom']); ?> <?= htmlspecialchars($acteur['nom']); ?></h4>
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
                        <?php if (!empty($acteurs)): ?>
                            <li>
                            <div class="field"><i class="bi bi-person-fill"></i> prenom :</div>
                            <div class="value" id="info-prenom"><?= htmlspecialchars($acteurs[0]['prenom']); ?></div>
                        </li>
                        <li>
                            <div class="field"><i class="bi bi-person-fill"></i> Nom :</div>
                            <div class="value" id="info-nom"><?= htmlspecialchars($acteurs[0]['nom']); ?></div>
                        </li>
                        <li>
                            <div class="field"><i class="bi bi-calendar-fill"></i> Date de Naissance :</div>
                            <div class="value" id="info-date"><?= htmlspecialchars($acteurs[0]['date_naissance']); ?>
                            </div>
                        </li>
                        <li>
                            <div class="field"><i class="bi bi-geo-alt-fill"></i> Adresse :</div>
                            <div class="value" id="info-pays"><?= htmlspecialchars($acteurs[0]['adresse']); ?></div>
                        </li>
                        <li>
                            <div class="field"><i class="bi bi-telephone-fill"></i> Téléphone :</div>
                            <div class="value" id="info-tel"><?= htmlspecialchars($acteurs[0]['contact']); ?></div>
                        </li>
                        <?php else: ?>
                        <li>Aucune information à afficher</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const items = document.querySelectorAll('.acteur-item');
items.forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('info-prenom').textContent = this.dataset.prenom;
        document.getElementById('info-nom').textContent = this.dataset.nom;
        document.getElementById('info-role').textContent = this.dataset.role;
        document.getElementById('info-date').textContent = this.dataset.date;
        document.getElementById('info-tel').textContent = this.dataset.tel;
    });
});
</script>