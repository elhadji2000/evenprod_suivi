<?php
include '../../../config/fonction.php';

$id_fact = $_GET['id_fact'] ?? 0;

// ---- Traitement ajout paiement ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type'], $_POST['montant'])) {
    $type = mysqli_real_escape_string($connexion, $_POST['type']);
    $montant = (int) $_POST['montant'];
    $facture_id = (int) $_POST['facture_id'];

    // 1. Générer une référence unique (ex: PAY-2025-001)
    $result = mysqli_query($connexion, "SELECT COUNT(*) as total FROM paiements");
    $row = mysqli_fetch_assoc($result);
    $nextId = $row['total'] + 1;
    $reference = "PAY-" . date("y") . "-" . str_pad($nextId, 3, "0", STR_PAD_LEFT);

    // 2. Gestion upload PDF
    $pdfFileName = null;
    if (!empty($_FILES['pdf']['name'])) {
        $uploadDir = "../../../uploads/paiements/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $pdfFileName = time() . "_" . basename($_FILES['pdf']['name']);
        move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadDir . $pdfFileName);
    }

    // 3. Insertion en base
    $sql = "INSERT INTO paiements (facture_id, type, montant, reference, piece_jointe) 
            VALUES ('$facture_id', '$type', '$montant', '$reference', " . 
            ($pdfFileName ? "'$pdfFileName'" : "NULL") . ")";
    
    if (mysqli_query($connexion, $sql)) {
        header("Location: versements.php?id_fact=$facture_id&success=1");
        exit();
    } else {
        echo "Erreur : " . mysqli_error($connexion);
    }
}

// ---- Récupération facture + paiements ----
/* $serie = getSerieById($id_fact); */
$paiements = getPaiementsByFactureId($connexion, $id_fact);
$facture = getFactureWithPaiements($connexion, $id_fact);
?>

<head>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Bootstrap CSS pour modal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .profile-header-img img {
        width: 90px;
        /* petite image pour économiser de l'espace */
        height: 50px;
        object-fit: cover;
        border-radius: 5px;
    }
    </style>
</head>
<?php include '../../../includes/header.php'; ?>

<body>
    <div class="container mt-5">
        <!-- Carte entreprise -->
        <?php if ($facture): ?>
        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="fw-bold mb-0">
                            Facture N° <?php echo $facture['id']; ?>
                        </h6>
                        <span class="text-muted">
                            Client : <strong><?php echo htmlspecialchars($facture['client_nom']); ?></strong>
                        </span>
                        <span class="text-muted">
                            Total : <strong><?php echo number_format($facture['total'], 0, ',', ' '); ?> F CFA</strong>
                        </span>
                        <span class="text-muted">
                            Payé : <strong><?php echo number_format($facture['verse'], 0, ',', ' '); ?> F CFA</strong>
                        </span>
                        <span class="text-muted">
                            Reste :
                            <strong class="<?php echo ($facture['reste'] > 0) ? 'text-danger' : 'text-success'; ?>">
                                <?php echo number_format($facture['reste'], 0, ',', ' '); ?> F CFA
                            </strong>
                        </span>
                    </div>
                </div>

                <!-- ✅ Message de succès après ajout d’un paiement -->
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                    ✅ Paiement enregistré avec succès !
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>



        <!-- Titre et bouton ajouter -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Détails des versements du facture</h2>

            <?php if ($facture['reste'] > 0): ?>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addVersementModal">
                <i class="fas fa-plus"></i> Ajouter un versement
            </button>
            <?php else: ?>
            <button class="btn btn-secondary" disabled>
                <i class="fas fa-check"></i> Facture entièrement payée
            </button>
            <?php endif; ?>
        </div>


        <!-- Tableau des versements -->
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
    if (!empty($paiements)) {
        $i = 1;
        foreach ($paiements as $v) {
            echo "<tr>
                    <td>{$i}</td>
                    <td>{$v['type']}</td>
                    <td>".number_format($v['montant'],0,',',' ')." F CFA</td>
                    <td>{$v['reference']}</td>
                    <td>";
            if (!empty($v['piece_jointe'])) {
                echo "<a href='../../../uploads/paiements/{$v['piece_jointe']}' target='_blank' 
                        style='text-decoration:underline;' class='text-info'>Voir pièce jointe</a>";
            } else {
                echo "<span class='text-muted'>Aucune</span>";
            }
            echo "</td></tr>";
            $i++;
        }
    } else {
        echo "<tr><td colspan='5' class='text-center text-muted'>Aucun versement trouvé</td></tr>";
    }
    ?>
                </tbody>

            </table>
        </div>
    </div>

    <!-- Modal ajouter versement -->
    <div class="modal fade" id="addVersementModal" tabindex="-1" aria-labelledby="addVersementLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="facture_id" value="<?php echo $id_fact; ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addVersementLabel">Ajouter un versement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type de versement</label>
                            <select name="type" id="type" class="form-control border border-secondary" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="Virement bancaire">Virement bancaire</option>
                                <option value="Chèque">Chèque</option>
                                <option value="Espèces">Espèces</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="montant" class="form-label">Montant (F CFA)</label>
                            <input type="number" name="montant" id="montant"
                                class="form-control border border-secondary" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="pdf" class="form-label">Pièce jointe (PDF)</label>
                            <input type="file" name="pdf" id="pdf" class="form-control bg-red border border-secondary"
                                accept=".pdf">
                            <span class="text-muted" style="font-size:0.9rem;">Choisissez un fichier PDF à
                                joindre</span>
                        </div>
                        <input type="hidden" name="contrat_id" value="$contrat_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


</body>

<!-- jQuery + DataTables JS + Bootstrap JS -->
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