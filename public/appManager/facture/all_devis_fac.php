<?php 
include '../../../config/fonction.php';

$serieId = $_GET['id'] ?? 0;
$serie = getSerieById($serieId);
$factures = getFacturesBySerieId($connexion, $serieId); 
?>
<?php include '../../../includes/header.php'; ?>

<head>
    <link rel="stylesheet" href="<?php echo $url_base; ?>pages/acteur/add.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" />
</head>

<section class="section gray-bg" id="contactus">
    <div class="container">
        <div class="row align-items-center mb-3">
            <div class="col-lg-8">
                <div class="section-title">
                    <h2>Liste des Devis & Factures</h2>
                    <p>Consultez ci-dessous vos devis et factures enregistrés.</p>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <a href="add_devis.php?id=<?php echo htmlspecialchars($serie['id'])?>" class="btn btn-success">➕ Ajouter
                    un devis</a>
            </div>
        </div>
        <div class="table-container">
            <table id="factureTable" class="display table table-bordered">
                <thead class="table-warning">
                    <tr>
                        <th>#</th>
                        <th>Reference</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Montant</th>
                        <th>Type</th>
                        <th>Valider</th>
                        <th>Pdf_facture</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=1; foreach ($factures as $row): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['reference']?? "NULL"); ?></td>
                        <td><?php echo htmlspecialchars($row['client_nom']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['date_facture'])); ?></td>
                        <td><?php echo number_format($row['total'], 0, ',', ' '); ?> FCFA</td>
                        <td class="col-type">
                            <?php if ($row['type'] == "Facture"): ?>
                            <span class="text-warning">Facture</span>
                            <?php else: ?>
                            <span class="text-dark">Devis</span>
                            <?php endif; ?>
                        </td>
                        <td class="col-valider">
                            <?php if ($row['type'] == "devis"): ?>
                            <a href="javascript:void(0);" onclick="validerDevis(<?php echo $row['id']; ?>, this)"
                                style="text-decoration:underline;" class="text-primary">Valider</a>
                            <?php else: ?>
                            <span class="text-success">✅valide</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="facture_pdf.php?id=<?php echo $row['id']; ?>" class="text-info"
                                style="text-decoration:underline;">voir-pdf</a>
                        </td>
                        <td>
                            <a href="edit_facture.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i
                                    class="fas fa-edit"></i></a>
                            <a href="delete_facture.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Supprimer ?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Partie droite (Aperçu global) -->
        <div class="col-lg-12 mt-4">
            <div class="contact-name">
                <h5>Aperçu</h5>
                <p>Serie : <?php echo htmlspecialchars($serie['titre']); ?></p>
                <p>Total Devis + Factures : <strong>
                        <?php echo number_format(array_sum(array_column($factures, 'total')), 0, ',', ' '); ?> FCFA
                    </strong></p>
            </div>
        </div>
    </div>
</section>

<!-- jQuery + DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#factureTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
        }
    });
});
</script>

<script>
function validerDevis(id, el) {
    if (confirm("Voulez-vous vraiment valider ce devis ?")) {
        fetch("valider_devis.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Devis validé avec succès !");
                let row = el.closest("tr");

                // Mettre à jour la colonne "Valider"
                row.querySelector(".col-valider").innerHTML = '<span class="text-success">✅valide</span>';

                // Mettre à jour la colonne "Type"
                row.querySelector(".col-type").innerHTML = '<span class="text-warning">Facture</span>';
            } else {
                alert("Erreur : " + data.message);
            }
        })
        .catch(err => alert("Erreur serveur : " + err));
    }
}
</script>
