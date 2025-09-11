<?php
include '../../../config/fonction.php';

$serieId = $_GET['id_serie'] ?? 0;
$serie = getSerieById($serieId);

// Vérifie si on est en édition
$tournageId = $_GET['id_tournage'] ?? null;
if ($tournageId) {
    $tournage = getTournageById($tournageId); // fonction à créer si elle n’existe pas
    $reference = $tournage['reference'];
    $date = $tournage['date_tournage'];
    $acteursSelectionnes = getActeursByTournage($tournageId); // récupère les IDs
    $acteursSelectionnesIds = array_map(function($a){ return $a['id']; }, $acteursSelectionnes);
} else {
    $reference = generateTournageReference();
    $date = '';
    $acteursSelectionnesIds = [];
}

// Récupérer tous les acteurs disponibles pour cette série
$acteurs = getActeursBySerieId($serieId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serieId = $_POST['serie_id'];
    $date = $_POST['date'];
    $reference = $_POST['reference'];
    $acteursIds = $_POST['acteurs'] ?? [];

    if ($tournageId) {
        // Modification
        $result = modifierTournage($tournageId, $serieId, $date, $reference, $acteursIds);
    } else {
        // Ajout
        $result = ajouterTournage($serieId, $date, $reference, $acteursIds);
    }

    if ($result['success']) {
        header("Location: tournages.php?id=$serieId");
        exit;
    } else {
        echo "Erreur : " . $result['message'];
    }
}

include '../../../includes/header.php';
?>

<head>
    <link rel="stylesheet" href="<?php echo $url_base; ?>pages/acteur/add.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
          integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
    <style>
    .invoice-table {
        width: 100%;
        margin-top: 15px;
        border-collapse: collapse;
    }

    .invoice-table th,
    .invoice-table td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: center;
    }

    .custom-multi-select {
        border: 1px solid #ced4da;
        background-color: #f7f7f7;
        border-radius: 5px;
        padding: 10px;
        max-height: 200px;
        overflow-y: auto;
    }

    .custom-multi-select div {
        margin-bottom: 5px;
    }

    .custom-multi-select input[type="checkbox"] {
        margin-right: 8px;
        cursor: pointer;
    }

    .custom-multi-select label {
        cursor: pointer;
        font-weight: 500;
    }
    </style>
</head>

<section class="section gray-bg">
    <div class="container">
        <div class="section-title">
            <h2><?= $tournageId ? "Modifier" : "Ajouter" ?> un Tournage pour : <?= htmlspecialchars($serie['titre']) ?></h2>
            <p>Sélectionnez la date et les acteurs participant au tournage.</p>
        </div>

        <div class="contact-form">
            <form action="add_tourn.php?id_serie=<?= htmlspecialchars($serie['id']) ?><?= $tournageId ? '&id_tournage='.$tournageId : '' ?>" 
                  method="post" class="contactform contact_form" id="tournageForm">
                <input type="hidden" name="serie_id" value="<?= $serieId ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="date">Date du tournage</label>
                        <input id="date" name="date" type="date" class="form-control" required value="<?= $date ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="reference">Référence</label>
                        <input id="ref" name="reference" type="text" class="form-control" 
                               value="<?= $reference ?>" readonly required>
                    </div>
                </div>

                <h4>Acteurs disponibles</h4>
                <div class="custom-multi-select">
                    <?php foreach ($acteurs as $acteur): ?>
                        <div>
                            <input type="checkbox" id="acteur_<?= $acteur['id']; ?>" 
                                   name="acteurs[]" value="<?= $acteur['id']; ?>" 
                                   <?= in_array($acteur['id'], $acteursSelectionnesIds) ? 'checked' : '' ?>>
                            <label for="acteur_<?= $acteur['id']; ?>">
                               <?= htmlspecialchars($acteur['prenom']); ?> <?= htmlspecialchars($acteur['nom']); ?> (<?= htmlspecialchars($acteur['date_naissance']); ?>)
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="px-btn theme">
                        <span><?= $tournageId ? "MODIFIER" : "ENREGISTRER" ?></span> <i class="arrow"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
