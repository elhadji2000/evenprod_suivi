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
        <h2 class="mb-4">Détails des versements du contrat</h2>
        <div class="table-container">
            <table id="factureTable" class="display table table-bordered">
                <thead class="table-warning">
                    <tr>
                        <th>#</th>
                        <th>Type de versement</th>
                        <th>Montant</th>
                        <th>Référence</th>
                        <th>Pièce jointe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                // Exemple de versements liés à un contrat
                $versements = [
                    ["type" => "Virement bancaire", "montant" => 300000, "ref" => "VIR-2025-001", "pdf" => "vir_001.pdf"],
                    ["type" => "Chèque", "montant" => 200000, "ref" => "CHQ-2025-014", "pdf" => "cheque_014.pdf"],
                    ["type" => "Espèces", "montant" => 100000, "ref" => "ESP-2025-055", "pdf" => "esp_055.pdf"],
                ];

                $i = 1;
                foreach ($versements as $v) {
                    echo "<tr>
                            <td>{$i}</td>
                            <td>{$v['type']}</td>
                            <td>{$v['montant']} F CFA</td>
                            <td>{$v['ref']}</td>
                            <td><a href='#' target='_blank' style='text-decoration:underline;' class='text-info'>Voir-pièce-jointe</a></td>
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