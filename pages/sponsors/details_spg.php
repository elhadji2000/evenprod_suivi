<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<?php include '../../includes/header.php'; ?>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-3 card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <a href="details_spg.php">
                                <img src="../../assets/images/Coca.png" class="width-90 rounded-3" alt="">
                            </a>
                        </div>
                        <div class="col">
                            <div class="overflow-hidden flex-nowrap">
                                <h6 class="mb-1">
                                    <a href="details_spg.php" class="text-reset">Entreprise Coca_Cola</a>
                                </h6>
                                <span class="text-muted d-block mb-2 small">
                                    Total contrat:3
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h2 class="mb-4">Détails des contrats du sponsor</h2>
        <div class="table-container">
            <table id="factureTable" class="display table table-bordered">
                <thead class="table-warning">
                    <tr>
                        <th>#</th>
                        <th>Description du contrat</th>
                        <th>Montant Total</th>
                        <th>Montant versé</th>
                        <th>Reste à payer</th>
                        <th>Contrat (PDF)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                // Exemple de contrats d’un sponsor donné
                $contrats = [
                    ["desc" => "Sponsoring OR - Événement 2025", "montantContrat" => 1000000, "verse" => 600000, "pdf" => "contrat_or.pdf"],
                    ["desc" => "Sponsoring ARGENT - Événement 2024", "montantContrat" => 500000, "verse" => 200000, "pdf" => "contrat_argent.pdf"],
                    ["desc" => "Sponsoring BRONZE - Événement 2023", "montantContrat" => 250000, "verse" => 250000, "pdf" => "contrat_bronze.pdf"],
                ];

                $i = 1;
                foreach ($contrats as $c) {
                    $restant = $c["montantContrat"] - $c["verse"];
                    echo "<tr>
                            <td>{$i}</td>
                            <td>{$c['desc']}</td>
                            <td>{$c['montantContrat']} F CFA</td>
                            <td><a href='versements.php' class='text-decoration-underline text-primary fw-bold'>{$c['verse']} F CFA</a></td>
                            <td>{$restant} F CFA</td>
                            <td><a href='#' target='_blank' style='text-decoration:underline;' class='text-info'>📄Voir-contrat</a></td>
                          </tr>";
                    $i++;
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

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