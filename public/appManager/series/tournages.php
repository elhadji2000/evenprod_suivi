<?php
include '../../../config/fonction.php';

$id = $_GET['id'] ?? 0;
$serie = getSerieById($id);
$tournages = getTournagesBySerieId($id);
?>
<?php include '../../../includes/header.php'; ?>
<head>
    <link rel="stylesheet" href="<?php echo $url_base; ?>pages/acteur/add.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
          integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
</head>

<body>
    <div class="container mt-5">
        <!-- Titre et bouton ajouter -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Tournages de la série : <?php echo htmlspecialchars($serie['titre']); ?></h2>
            <a class="btn btn-success" href="add_tourn.php?id_serie=<?php echo $id; ?>">
                <i class="fas fa-plus"></i> Ajouter un tournage
            </a>
        </div>

        <!-- Tableau des tournages -->
        <div class="table-container">
            <table id="tournageTable" class="display table table-bordered">
                <thead class="table-warning">
                    <tr>
                        <th>#</th>
                        <th>Réf. Tournage</th>
                        <th>Date</th>
                        <th>Équipe</th>
                        <th>Dépenses</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($tournages as $t) {
                        $depense = getDepenseByTournage($id, $t['id']);
                        $equipeCount = getEquipeCountByTournage($t['id']);
                        echo "<tr>
                                <td>{$i}</td>
                                <td>".htmlspecialchars($t['reference'])."</td>
                                <td>".date('d-m-Y', strtotime($t['date_tournage']))."</td>
                                <td>+{$equipeCount}</td>
                                <td><a href='depenses.php?id_tournage={$t['id']}&id_serie={$id}' 
                                       style='text-decoration:underline;' class='text-info'>"
                                       .number_format($depense, 0, ',', ' ')." F CFA</a></td>
                                <td>
                                    <a href='edit_tourn.php?id={$t['id']}' class='text-primary me-2'>modif</a>
                                    <a href='delete_tourn.php?id={$t['id']}&id_serie={$id}' class='text-danger'>suppr</a>
                                </td>
                              </tr>";
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- jQuery + DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#tournageTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50]
        });
    });
    </script>
</body>
