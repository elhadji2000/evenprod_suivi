<?php
include '../../../config/fonction.php';

$id_fact = (int)($_GET['id_fact'] ?? 0);

// === AJOUT ===
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='add') {
    $type = mysqli_real_escape_string($connexion, $_POST['type']);
    $montant = (int)$_POST['montant'];

    // Référence unique basée sur l'id maximal existant
    $result = mysqli_query($connexion, "SELECT MAX(id) AS max_id FROM paiements");
    $row = mysqli_fetch_assoc($result);
    // si la table est vide, max_id sera NULL => on démarre à 0
    $nextId = ($row['max_id'] ?? 0) + 1;
    // génération de la référence
    $ref = "PAY-" . date("y") . "-" . str_pad($nextId, 3, "0", STR_PAD_LEFT);


    // upload pdf
    $pdf = null;
    if (!empty($_FILES['pdf']['name'])) {
        $dir = "../../../uploads/paiements/";
        if(!is_dir($dir)) mkdir($dir,0777,true);
        $pdf = time()."_".basename($_FILES['pdf']['name']);
        move_uploaded_file($_FILES['pdf']['tmp_name'],$dir.$pdf);
    }

    mysqli_query($connexion,"INSERT INTO paiements (facture_id,type,montant,reference,piece_jointe)
        VALUES ($id_fact,'$type',$montant,'$ref',".($pdf?"'$pdf'":"NULL").")");
    header("Location: versements.php?id_fact=$id_fact&success=1");
    exit;
}

// === MODIFICATION ===
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='edit') {
    $pid = (int)$_POST['id'];
    $type = mysqli_real_escape_string($connexion,$_POST['type']);
    $montant = (int)$_POST['montant'];

    // récupérer ancien pdf
    $old = mysqli_fetch_assoc(mysqli_query($connexion,"SELECT piece_jointe FROM paiements WHERE id=$pid"));
    $pdf = $old['piece_jointe'];

    if(!empty($_FILES['pdf']['name'])){
        $dir = "../../../uploads/paiements/";
        if($pdf && is_file($dir.$pdf)) unlink($dir.$pdf); // suppr ancien
        $pdf = time()."_".basename($_FILES['pdf']['name']);
        move_uploaded_file($_FILES['pdf']['tmp_name'],$dir.$pdf);
    }

    mysqli_query($connexion,"UPDATE paiements SET type='$type',montant=$montant,
        piece_jointe=".($pdf?"'$pdf'":"NULL")." WHERE id=$pid");
    header("Location: versements.php?id_fact=$id_fact");
    exit;
}

// === SUPPRESSION ===
if (isset($_GET['delete'])) {
    $pid = (int)$_GET['delete'];
    $old = mysqli_fetch_assoc(mysqli_query($connexion,"SELECT piece_jointe FROM paiements WHERE id=$pid"));
    if($old['piece_jointe'] && is_file("../../../uploads/paiements/".$old['piece_jointe'])){
        unlink("../../../uploads/paiements/".$old['piece_jointe']);
    }
    mysqli_query($connexion,"DELETE FROM paiements WHERE id=$pid");
    header("Location: versements.php?id_fact=$id_fact");
    exit;
}

// === récupération données ===
$paiements = getPaiementsByFactureId($connexion,$id_fact);
$facture   = getFactureWithPaiements($connexion,$id_fact);
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
                            Facture N° <?php echo $facture['reference']; ?>
                        </h6>
                        <span class="text-muted">
                            Client : <strong><?php echo htmlspecialchars($facture['client_nom']); ?></strong>
                        </span>
                        <span class="text-muted">
                            Total : <strong><?php echo number_format($facture['total'], 0, ',', ','); ?> fcfa</strong>
                        </span>
                        <span class="text-muted">
                            Payé : <strong><?php echo number_format($facture['verse'], 0, ',', ','); ?> fcfa</strong>
                        </span>
                        <span class="text-muted">
                            Reste :
                            <strong class="<?php echo ($facture['reste'] > 0) ? 'text-danger' : 'text-success'; ?>">
                                <?php echo number_format($facture['reste'], 0, ',', ','); ?> fcfa
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
                <?php if (isset($_GET['success']) && $_GET['success'] == 2): ?>
                <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                    ✅ Paiement modifier avec succès !
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
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($paiements)): ?>
                    <?php $i=1; foreach($paiements as $v): ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= htmlspecialchars($v['type']); ?></td>
                        <td><?= number_format($v['montant'],0,',',','); ?> f cfa</td>
                        <td><?= htmlspecialchars($v['reference']); ?></td>
                        <td>
                            <?php if (!empty($v['piece_jointe'])): ?>
                            <a href="../../../uploads/paiements/<?= $v['piece_jointe']; ?>" target="_blank"
                                class="text-info" style="text-decoration:underline;">Voir pièce jointe</a>
                            <?php else: ?>
                            <span class="text-muted">Aucune</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- bouton ouvrir modal édition -->
                            <a href="" class="text-warning text-decoration-underline" data-bs-toggle="modal"
                                data-bs-target="#editModal<?= $v['id']; ?>">Modifier
                            </a> |
                            <a href="?id_fact=<?= $id_fact; ?>&delete=<?= $v['id']; ?>"
                                onclick="return confirm('Supprimer ce versement ?');"
                                class="text-danger text-decoration-underline">Supprimer
                            </a>
                        </td>
                    </tr>

                    <!-- Modal édition -->
                    <div class="modal fade" id="editModal<?= $v['id']; ?>" tabindex="-1"
                        aria-labelledby="editVersementLabel<?= $v['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?= $v['id']; ?>">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editVersementLabel<?= $v['id']; ?>">Modifier un
                                            versement</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Fermer"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Type -->
                                        <div class="mb-3">
                                            <label for="type<?= $v['id']; ?>" class="form-label">Type de
                                                versement</label>
                                            <select name="type" id="type<?= $v['id']; ?>"
                                                class="form-control border border-secondary" required>
                                                <option value="">-- Sélectionner --</option>
                                                <option value="Virement bancaire"
                                                    <?= $v['type']==='Virement bancaire'?'selected':''; ?>>
                                                    Virement bancaire</option>
                                                <option value="Chèque" <?= $v['type']==='Chèque'?'selected':''; ?>>
                                                    Chèque</option>
                                                <option value="Espèces" <?= $v['type']==='Espèces'?'selected':''; ?>>
                                                    Espèces</option>
                                            </select>
                                        </div>
                                        <!-- Montant -->
                                        <div class="mb-3">
                                            <label for="montant<?= $v['id']; ?>" class="form-label">Montant (F
                                                CFA)</label>
                                            <input type="number" name="montant" id="montant<?= $v['id']; ?>"
                                                class="form-control border border-secondary" min="0"
                                                value="<?= htmlspecialchars($v['montant']); ?>" required>
                                        </div>
                                        <!-- Pièce jointe -->
                                        <div class="mb-3">
                                            <label for="pdf<?= $v['id']; ?>" class="form-label">Nouvelle pièce jointe
                                                (PDF)</label>
                                            <input type="file" name="pdf" id="pdf<?= $v['id']; ?>"
                                                class="form-control border border-secondary" accept=".pdf">
                                            <span class="text-muted" style="font-size:0.9rem;">
                                                Fichier actuel : <?= $v['piece_jointe'] ?? 'Aucun' ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Aucun versement trouvé</td>
                    </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>

        <!-- Modal ajouter versement -->
        <div class="modal fade" id="addVersementModal" tabindex="-1" aria-labelledby="addVersementLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="facture_id" value="<?php echo $id_fact; ?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addVersementLabel">Ajouter un versement</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Type -->
                            <div class="mb-3">
                                <label for="type" class="form-label">Type de versement</label>
                                <select name="type" id="type" class="form-control border border-secondary" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Virement bancaire">Virement bancaire</option>
                                    <option value="Chèque">Chèque</option>
                                    <option value="Espèces">Espèces</option>
                                </select>
                            </div>
                            <!-- Montant -->
                            <div class="mb-3">
                                <label for="montant" class="form-label">Montant (F CFA)</label>
                                <input type="number" name="montant" id="montant"
                                    class="form-control border border-secondary" min="0" required>
                            </div>
                            <!-- Pièce jointe -->
                            <div class="mb-3">
                                <label for="pdf" class="form-label">Pièce jointe (PDF)</label>
                                <input type="file" name="pdf" id="pdf" class="form-control border border-secondary"
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