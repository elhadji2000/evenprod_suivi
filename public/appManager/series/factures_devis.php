<head>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Notre style -->
    <link rel="stylesheet" href="factures.css">
</head>

<?php include '../../../includes/header.php'; ?>

<?php
// Données factices
$projet = [
    "titre" => "Fassema",
    "type" => "Série"
];

$data = [
    ["numero" => "F-001", "client" => "Studio Alpha", "type" => "facture", "statut" => "payé", "montant" => 1200, "date_emission" => "2025-01-05", "date_echeance" => "2025-02-05"],
    ["numero" => "F-002", "client" => "ProdVision", "type" => "facture", "statut" => "en_attente", "montant" => 850, "date_emission" => "2025-02-10", "date_echeance" => "2025-03-10"],
    ["numero" => "D-001", "client" => "CinéWorld", "type" => "devis", "statut" => "accepté", "montant" => 500, "date_emission" => "2025-01-15", "date_echeance" => "2025-01-30"],
    ["numero" => "D-002", "client" => "FilmArt", "type" => "devis", "statut" => "refusé", "montant" => 300, "date_emission" => "2025-02-01", "date_echeance" => "2025-02-15"],
];
?>

<body>

    <div class="page-header">
        <h1>📄 Factures & Devis - <?php echo htmlspecialchars($projet['titre']); ?>
            (<?php echo ucfirst($projet['type']); ?>)</h1>
        <a href="#" class="btn-add">+ Ajouter</a>
</div>

    <div class="table-container">
        <table id="factureTable" class="display">
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Montant</th>
                    <th>Date émission</th>
                    <th>Date échéance</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?php echo $row['numero']; ?></td>
                    <td><?php echo $row['client']; ?></td>
                    <td><?php echo ucfirst($row['type']); ?></td>
                    <td>
                        <span class="status <?php echo strtolower($row['statut']); ?>">
                            <?php echo ucfirst($row['statut']); ?>
                        </span>
                    </td>
                    <td><?php echo number_format($row['montant'], 2, ',', ' '); ?> €</td>
                    <td><?php echo date('d/m/Y', strtotime($row['date_emission'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['date_echeance'])); ?></td>
                    <td><a href="#" class="btn-view">Voir</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

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

</body>
