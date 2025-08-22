<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<?php include '../../includes/header.php'; ?>

<?php
// Tableau PHP des acteurs (exemple)
$acteurs = [
    [
        'id' => 1,
        'nom' => 'Sancho Aldo',
        'role' => 'Principal',
        'date' => '1990/05/12',
        'pays' => 'France',
        'tel' => '(123) 456-7890',
        'avatar' => 'https://bootdey.com/img/Content/avatar/avatar2.png'
    ],
    [
        'id' => 2,
        'nom' => 'Jonty Augusto',
        'role' => 'Secondaire',
        'date' => '1985/02/20',
        'pays' => 'France',
        'tel' => '(987) 654-3210',
        'avatar' => 'https://bootdey.com/img/Content/avatar/avatar3.png'
    ],
    [
        'id' => 3,
        'nom' => 'Jonty Augusto',
        'role' => 'Secondaire',
        'date' => '1985/02/20',
        'pays' => 'France',
        'tel' => '(987) 654-3210',
        'avatar' => 'https://bootdey.com/img/Content/avatar/avatar3.png'
    ],
    [
        'id' => 4,
        'nom' => 'Jonty Augusto',
        'role' => 'Secondaire',
        'date' => '1985/02/20',
        'pays' => 'France',
        'tel' => '(987) 654-3210',
        'avatar' => 'https://bootdey.com/img/Content/avatar/avatar3.png'
    ],
    [
        'id' => 5,
        'nom' => 'Jonty Augusto',
        'role' => 'Secondaire',
        'date' => '1985/02/20',
        'pays' => 'France',
        'tel' => '(987) 654-3210',
        'avatar' => 'https://bootdey.com/img/Content/avatar/avatar3.png'
    ],
    [
        'id' => 6,
        'nom' => 'Jonty Augusto',
        'role' => 'Secondaire',
        'date' => '1985/02/20',
        'pays' => 'France',
        'tel' => '(987) 654-3210',
        'avatar' => 'https://bootdey.com/img/Content/avatar/avatar3.png'
    ],
    [
        'id' => 7,
        'nom' => 'Jonty Augusto',
        'role' => 'Secondaire',
        'date' => '1985/02/20',
        'pays' => 'France',
        'tel' => '(987) 654-3210',
        'avatar' => 'https://bootdey.com/img/Content/avatar/avatar3.png'
    ],
    [
        'id' => 8,
        'nom' => 'Androkles Allen',
        'role' => 'Cameo',
        'date' => '1992/08/15',
        'pays' => 'Belgique',
        'tel' => '(555) 123-4567',
        'avatar' => 'https://bootdey.com/img/Content/avatar/avatar4.png'
    ]
];
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
                    <a href="ajouter_acteur.php" class="btn btn-xs btn-primary mb-2">
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
                        <?php foreach ($acteurs as $acteur): ?>
                        <li>
                            <a href="#" class="acteur-item"
                               data-nom="<?= $acteur['nom']; ?>"
                               data-role="<?= $acteur['role']; ?>"
                               data-date="<?= $acteur['date']; ?>"
                               data-pays="<?= $acteur['pays']; ?>"
                               data-tel="<?= $acteur['tel']; ?>"
                               data-avatar="<?= $acteur['avatar']; ?>">
                                <div class="friend-img"><img src="<?= $acteur['avatar']; ?>" alt="Avatar" /></div>
                                <div class="friend-info">
                                    <h4><?= $acteur['nom']; ?></h4>
                                    <p>Rôle : <?= $acteur['role']; ?></p>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Détails de l’acteur sélectionné -->
                <div class="col-md-4 hidden-xs hidden-sm">
                    <ul class="profile-info-list" id="acteur-details">
                        <li class="title">INFORMATIONS ACTEUR</li>
                        <li>
                            <div class="field"><i class="bi bi-person-fill"></i> Nom :</div>
                            <div class="value" id="info-nom"><?= $acteurs[0]['nom']; ?></div>
                        </li>
                        <li>
                            <div class="field"><i class="bi bi-award-fill"></i> Rôle :</div>
                            <div class="value" id="info-role"><?= $acteurs[0]['role']; ?></div>
                        </li>
                        <li>
                            <div class="field"><i class="bi bi-calendar-fill"></i> Date de Naissance :</div>
                            <div class="value" id="info-date"><?= $acteurs[0]['date']; ?></div>
                        </li>
                        <li>
                            <div class="field"><i class="bi bi-geo-alt-fill"></i> Pays :</div>
                            <div class="value" id="info-pays"><?= $acteurs[0]['pays']; ?></div>
                        </li>
                        <li>
                            <div class="field"><i class="bi bi-telephone-fill"></i> Téléphone :</div>
                            <div class="value" id="info-tel"><?= $acteurs[0]['tel']; ?></div>
                        </li>
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
        document.getElementById('info-nom').textContent = this.dataset.nom;
        document.getElementById('info-role').textContent = this.dataset.role;
        document.getElementById('info-date').textContent = this.dataset.date;
        document.getElementById('info-pays').textContent = this.dataset.pays;
        document.getElementById('info-tel').textContent = this.dataset.tel;
    });
});
</script>
