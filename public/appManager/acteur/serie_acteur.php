<?php 
include '../../../config/fonction.php';

$id = $_GET['id_serie'] ?? 0;
$serie = getSerieById($id);
$acteurs = getActeursNotInSerie($id); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serieId = $_POST['serie_id'] ?? 0;
    $acteurs2 = $_POST['acteurs'] ?? [];
    $cachets = $_POST['cachet'] ?? [];

    if ($serieId && !empty($acteurs2)) {
        addActeursToSerie($serieId, $acteurs2, $cachets);
    }

    // redirection vers la liste des acteurs de la série
    header("Location: acteurs.php?id=" . $serieId);
    exit;
}


?>
<?php
include '../../../includes/header.php';
?>
<head>
    <link rel="stylesheet" href="<?php echo $url_base; ?>pages/acteur/add.css">
    <link rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
          integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" 
          crossorigin="anonymous" />
    <style>
    .invoice-table {
        width: 100%;
        margin-top: 15px;
        border-collapse: collapse;
    }
    .invoice-table th, .invoice-table td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: center;
    }
    .invoice-table input[type="number"] {
        width: 90%;
        padding: 4px;
        border-radius: 4px;
    }
    </style>
</head>

<section class="section gray-bg">
    <div class="container">
        <div class="section-title">
            <h2>Ajouter des Acteurs à la Série : <?php echo htmlspecialchars($serie['titre'] ?? ''); ?></h2>
            <p>Sélectionnez les acteurs disponibles et définissez leur cachet.</p>
        </div>

        <div class="contact-form">
            <form action="serie_acteur.php?id_serie=<?php echo htmlspecialchars($serie['id'])?>" method="post" class="contactform contact_form" id="actorForm">

                <!-- ID de la série caché -->
                <input type="hidden" name="serie_id" value="<?php echo htmlspecialchars($id); ?>">

                <!-- Tableau des acteurs -->
                <h4>Acteurs disponibles</h4>
                <table class="invoice-table" id="actorTable">
                    <thead>
                        <tr>
                            <th>Choix</th>
                            <th>Prénom & Nom</th>
                            <th>Date de naissance</th>
                            <th>Cachet (F CFA)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($acteurs)) : ?>
                            <?php foreach ($acteurs as $acteur) : ?>
                                <tr>
                                    <td><input type="checkbox" name="acteurs[]" value="<?php echo $acteur['id']; ?>"></td>
                                    <td><strong><?php echo htmlspecialchars($acteur['prenom']); ?> <?php echo htmlspecialchars($acteur['nom']); ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars($acteur['date_naissance']); ?></strong></td>
                                    <td><input type="number" name="cachet[<?php echo $acteur['id']; ?>]" placeholder="Montant" min="0" class="form-control"></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4">Tous les acteurs sont déjà associés à cette série 🎬</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Bouton Enregistrer -->
                <div class="form-group" style="margin-top:15px;">
                    <button type="submit" class="px-btn theme">
                        <span>ENREGISTRER</span> <i class="arrow"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
