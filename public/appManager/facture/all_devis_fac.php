<?php include '../../../includes/header.php'; ?>
<head>
    <link rel="stylesheet" href="<?php echo $url_base; ?>pages/acteur/add.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
</head>

<?php
// Données factices pour test (dans ta BDD tu auras plutôt type = devis ou facture)
$data = [
    ["id" => 1, "type" => "Devis", "date" => "2025-01-05", "montant" => 250000, "client" => "Coca Cola", "description" => "Achat de matériels"],
    ["id" => 2, "type" => "Facture", "date" => "2025-02-10", "montant" => 85000, "client" => "Samsung", "description" => "Déplacement équipe"],
    ["id" => 3, "type" => "Devis", "date" => "2025-03-01", "montant" => 120000, "client" => "Maggie", "description" => "Paiement comédiens"],
];
?>

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
                <a href="add_devis.php" class="btn btn-success">➕ Ajouter un devis</a>
            </div>
        </div>
        <div class="table-container">
            <table id="factureTable" class="display table table-bordered">
                <thead class="table-warning">
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Montant</th>
                        <th>Type</th>
                        <th>Valider</th>
                        <th>justificatif</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=1; foreach ($data as $row): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['client']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['date'])); ?></td>
                        <td><?php echo number_format($row['montant'], 0, ',', ' '); ?> FCFA</td>
                        <td>
                            <?php if ($row['type'] == "Facture"): ?>
                            <span class="text-warning">Facture</span>
                            <?php else: ?>
                            <span class="text-dark">Devis</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['type'] == "Devis"): ?>
                            <a href="valider_devis.php?id=<?php echo $row['id']; ?>" style="text-decoration:underline;"
                                class="text-primary">Valider</a>
                            <?php else: ?>
                            <span class="text-success">✅ Déjà validé</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="#" style="text-decoration:underline;" class="text-info">voir-pdf</a>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
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
                <p>Projet : FASSEMA</p>
                <p>Total Devis + Factures : <strong>
                        <?php echo number_format(array_sum(array_column($data, 'montant')), 0, ',', ' '); ?> FCFA
                    </strong></p>
            </div>
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