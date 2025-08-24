<?php include '../../../includes/header.php'; ?>
<head>
    <link rel="stylesheet" href="<?php echo $url_base; ?>pages/acteur/add.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
</head>
<?php
// Données factices pour test
$data = [
    ["type" => "Décor", "date" => "2025-01-05", "montant" => 250000, "tournage" => "Film", "justificatif" => "decor1.jpg", "description" => "Achat de matériels"],
    ["type" => "Transport", "date" => "2025-02-10", "montant" => 85000, "tournage" => "Série TV", "justificatif" => "transport1.jpg", "description" => "Déplacement équipe"],
    ["type" => "Cachets", "date" => "2025-03-01", "montant" => 120000, "tournage" => "Documentaire", "justificatif" => "cachets1.jpg", "description" => "Paiement comédiens"],
];
?>
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
                <a href="add_depense.php" class="btn btn-success">➕ Ajouter une Dépense</a>
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
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Tournage</th>
                                    <th>Justificatif</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach ($data as $row): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $row['description']; ?></td>
                                    <td><?php echo $row['type']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['date'])); ?></td>
                                    <td><?php echo number_format($row['montant'], 0, ',', ' '); ?> FCFA</td>
                                    <td><?php echo $row['tournage']; ?></td>
                                    <td><a href="#" target="_blank" style="text-decoration:underline;" class="text-info">Voir-pdf</a></td>
                                    
                                    <td>
                                        <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                        <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
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
                    <p>Projet : FASSEMA</p>
                    <p>Total des Dépenses : <strong>455 000 FCFA</strong></p>
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
    $('#depenseTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
        }
    });
});
</script>
