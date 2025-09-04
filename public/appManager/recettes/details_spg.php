<?php
include '../../../config/fonction.php';
$id = $_GET['id'] ?? 0;
$serie = getSerieById($id);
$factures = getFacturesWithPaiementsBySerie($connexion, $id);
?>
<head>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        .profile-header-img img {
            width: 50px; /* petite image pour économiser de l'espace */
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<?php include '../../../includes/header.php'; ?>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-3 card-body">
                    <div class="row align-items-center">
                        <div class="col-auto profile-header-img">
                            <a href="details_spg.php">
                                <img src="<?php echo $url_base; ?>uploads/series/<?php echo htmlspecialchars($serie['logo']); ?>" alt="Logo série">
                            </a>
                        </div>
                        <div class="col">
                            <div class="overflow-hidden flex-nowrap">
                                <h6 class="mb-1">
                                    <a href="#" class="text-reset">Série : <?php echo htmlspecialchars($serie['titre']); ?></a>
                                </h6>
                                <span class="text-muted d-block mb-2 small">
                                    Total recettes : <?php echo number_format(array_sum(array_column($factures, 'total')), 0, ',', ' '); ?> FCFA
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mb-4">Détails des Recettes</h2>
        <div class="table-container">
            <table id="factureTable" class="display table table-bordered">
                <thead class="table-warning">
                    <tr>
                        <th>#</th>
                        <th>Nom_client</th>
                        <th>Contact</th>
                        <th>Ref_Facture</th>
                        <th>Montant Total</th>
                        <th>Montant versé</th>
                        <th>Reste à payer</th>
                        <th>Facture (PDF)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($factures as $f) {
                        echo "<tr>
                                <td>{$i}</td>
                                <td>".htmlspecialchars($f['nom'])."</td>
                                <td>".htmlspecialchars($f['contact'])."</td>
                                <td>".htmlspecialchars($f['reference'])."</td>
                                <td>".number_format($f['total'],0,',',' ')." F CFA</td>
                                <td><a href='versements.php?id_fact={$f['id']}' class='text-decoration-underline text-primary fw-bold'>".number_format($f['verse'],0,',',' ')." F CFA</a></td>
                                <td>".number_format($f['reste'],0,',',' ')." F CFA</td>
                                <td><a href='../facture/facture_pdf.php?id={$f['id']}' target='_blank' style='text-decoration:underline;' class='text-info'>📄Voir facture</a></td>
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
