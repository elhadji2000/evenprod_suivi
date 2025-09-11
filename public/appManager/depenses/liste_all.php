<?php
include '../../../config/fonction.php';

$serieId = $_GET['id'] ?? 0; 
$serie = getSerieById($serieId);
$depenses = getDepensesBySerie($serieId);
$totaux = getTotauxDepensesBySerie($connexion, $serieId);

// Calcul du total
$total = 0;
foreach ($depenses as $d) {
    $total += $d['montant'];
}
?>

<?php include '../../../includes/header.php'; ?>

<head>
    <link rel="stylesheet" href="<?php echo $url_base; ?>pages/acteur/add.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
</head>
<section class="section gray-bg" id="contactus">
    <div class="container">
        <div class="row align-items-center mb-3">
            <div class="col-lg-8">
                <div class="section-title">
                    <h2>La liste des Dépenses</h2>
                    <p>Consultez ci-dessous toutes les dépenses enregistrées.</p>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <a href="add_depense.php?id=<?php echo htmlspecialchars($serie['id'])?>" class="btn btn-success">➕
                    Ajouter une Dépense</a>
            </div>
        </div>

        <div class="row flex-row-reverse">
            <div class="col-lg-12">
                <div class="contact-form">
                    <div class="table-container">
                        <table id="depenseTable" class="display table table-bordered">
                            <thead class="table-warning">
                                <tr>
                                    <th>#</th>
                                    <th>Libellé</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Tournage</th>
                                    <th>Justificatif</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach ($depenses as $row): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $row['libelle'] ? $row['libelle'] : '<em>Aucun</em>'; ?></td>
                                    <td><?php echo htmlspecialchars($row['type_depense']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['date_depense'])); ?></td>
                                    <td><?php echo number_format($row['montant'], 0, ',', ','); ?> fcfa</td>
                                    <td><?php echo $row['tournage_reference'] ? $row['tournage_reference'] : '<em>Aucun</em>'; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['justificatif'])): ?>
                                        <a style="text-decoration:underline;" class="text-info"
                                            href="<?php echo $url_base . 'uploads/justificatifs/' . htmlspecialchars($row['justificatif']); ?>"
                                            target="_blank">Voir justificatif</a>
                                        <?php else: ?>
                                        -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $url_base; ?>public/appManager/delete.php?table=depenses&id=<?= htmlspecialchars($row['id']) ?>&redirect=<?php echo $url_base; ?>public/appManager/depenses/liste_all.php?id=<?php echo $serieId; ?>"
                                            class="text-decoration-underline text-danger"
                                            onclick="return confirm('Voulez-vous vraiment supprimer ce depense ?');">
                                            Supprimer
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            <!-- Partie droite (Aperçu global) -->
            <div class="col-lg-12 mt-4">
                <div class="contact-name">
                    <h5>Aperçu</h5>
                    <p>SERIE : <?php echo htmlspecialchars($serie['titre']); ?></p>

                    <p>Total des Dépenses :
                        <strong><?php echo number_format($total, 0, ',', ','); ?> fcfa</strong>
                    </p>

                    <p>Budget :
                        <strong><?php echo number_format($serie['budget'], 0, ',', ','); ?> fcfa</strong>
                    </p>

                    <?php 
                        if ($serie['budget'] > 0) {
                            $taux = ($total / $serie['budget']) * 100;
                        } else {
                            $taux = 0;
                        }
                    ?>

                    <p>Taux d’utilisation :
                        <strong><?php echo number_format($taux, 2, ',', ' '); ?> %</strong>
                    </p>
                </div>
            </div>

        </div>

        <!-- Logos & Stats -->
        <section class="pt-4 pb-6" id="count-stats">
            <div class="container">
                <section id="count-stats" class="py-5 bg-light">
                    <div class="container">
                        <div class="row justify-content-center text-center g-4">
                            <div class="col-md-3">
                                <h5 class="text-primary" id="state1" data-count="5234">
                                    <?php echo number_format($totaux['cachet'], 0, ',', ','); ?>
                                </h5>
                                <h5>Cachets</h5>
                                <p>Dépenses liées aux cachets et honoraires.</p>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-primary" id="state2" data-count="3400">
                                    <?php echo number_format($totaux['decor'], 0, ',', ','); ?>
                                </h5>
                                <h5>Décors</h5>
                                <p>Frais liés à la décoration et à l’aménagement.</p>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-primary" id="state3" data-count="24">
                                    <?php echo number_format($totaux['transport'], 0, ',', ','); ?>
                                </h5>
                                <h5>Transports</h5>
                                <p>Coûts de déplacement et de logistique.</p>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-primary" id="state4" data-count="24">
                                    <?php echo number_format($totaux['autre'], 0, ',', ','); ?>
                                </h5>
                                <h5>Autres</h5>
                                <p>Dépenses diverses non catégorisées.</p>
                            </div>
                        </div>

                    </div>
                </section>
            </div>
        </section>
    </div>
</section>

<!-- jQuery + DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#depenseTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
        }
    });
});
</script>
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Création de l'alerte
    const toast = document.createElement("div");
    toast.textContent = "depense suppimer avec succès !";
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