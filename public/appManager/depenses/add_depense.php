<?php
include '../../../config/fonction.php';


$serieId = $_GET['id'] ?? 0;
$serie = getSerieById($serieId);
$tournages = getTournagesBySerieId($serieId); // fonction existante qui retourne array id + reference

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serieId = $_POST['serie_id'];
    $tournageId = $_POST['tournage_id'] ?? '';
    $type = $_POST['type'];
    $montant = $_POST['montant'];
    $description = $_POST['description'];

    // Upload du justificatif si présent
   // Upload du justificatif si présent
$justificatif = null;
if (isset($_FILES['justificatif']) && $_FILES['justificatif']['error'] == 0) {
    $ext = strtolower(pathinfo($_FILES['justificatif']['name'], PATHINFO_EXTENSION));

    if ($ext === 'pdf') {
        // Chemin absolu du dossier d’upload
        $uploadDir = __DIR__ . '/../../../uploads/justificatifs/';

        // Vérifie si le dossier existe, sinon le créer
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Nom unique
        $filename = 'depense_' . time() . '.pdf';
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['justificatif']['tmp_name'], $destination)) {
            $justificatif = $filename;
        } else {
            echo "Erreur lors de l’upload du fichier.";
        }
    } else {
        echo "Seuls les fichiers PDF sont autorisés.";
    }
}


    $result = ajouterDepense($serieId, $tournageId, $type, $montant, $description, $justificatif);

    if ($result['success']) {
        header("Location: liste_all?id=$serieId");
        exit;
    } else {
        echo "Erreur : ".$result['message'];
    }
}
?>
<?php
include '../../../includes/header.php';
?>

<head>
    <link rel="stylesheet" href="<?php echo $url_base; ?>pages/acteur/add.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
</head>

<section class="section gray-bg" id="contactus">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="section-title">
                    <h2>Ajouter une Dépense</h2>
                    <p>Complétez le formulaire pour enregistrer une nouvelle dépense.</p>
                </div>
            </div>
        </div>

        <div class="row flex-row-reverse">
            <div class="col-md-7 col-lg-8 m-15px-tb">
                <div class="contact-form">
                    <form action="add_depense?id=<?php echo htmlspecialchars($serie['id'])?>" method="post"
                        enctype="multipart/form-data" class="contactform contact_form" id="contact_form">

                        <input type="hidden" name="serie_id" value="<?php echo $serieId; ?>">

                        <div class="row">
                            <!-- Type de dépense -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select name="type" class="form-control" required>
                                        <option value="">-- Sélectionnez un type --</option>
                                        <option value="Decor">Décor</option>
                                        <option value="Transport">Transport</option>
                                        <option value="Autre(s)">Autre(s)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Montant -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="number" name="montant" min="0" placeholder="Montant"
                                        class="form-control" required>
                                </div>
                            </div>

                            <!-- Tournage -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select name="tournage_id" class="form-control">
                                        <option value="">-- Sélectionnez un tournage (optionnel) --</option>
                                        <?php foreach($tournages as $t): ?>
                                        <option value="<?php echo $t['id']; ?>">
                                            <?php echo htmlspecialchars($t['reference']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Justificatif (PDF) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="file" id="justificatif" name="justificatif" accept="application/pdf"
                                        class="form-control-file" required hidden>
                                    <label for="justificatif" class="custom-file-label form-control">
                                        <span><i class="fas fa-upload"></i> Justificatif (PDF)</span>
                                        <span id="file-name" class="file-name">Aucun fichier choisi</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea name="description" placeholder="libelle" class="form-control" rows="3"
                                        required></textarea>
                                </div>
                            </div>

                            <!-- Bouton -->
                            <div class="col-md-12">
                                <div class="send">
                                    <button type="submit" class="px-btn theme">
                                        <span>ENREGISTRER</span> <i class="arrow"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Partie droite (Aperçu) -->
            <div class="col-md-5 col-lg-4 m-15px-tb">
                <div class="contact-name">
                    <h5>Aperçu</h5>
                    <p>Série: <?php echo htmlspecialchars($serie['titre']); ?></p>
                    <p>Total des dépenses : ### FCFA</p>
                </div>
                <div class="contact-name shortcut-links">
                    <h5>Raccourcis</h5>
                    <p><a href="#">Voir toutes les séries</a></p>
                    <p><a href="#">Voir tous les acteurs</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const inputFile = document.getElementById("justificatif");
const fileName = document.getElementById("file-name");

inputFile.addEventListener("change", function() {
    if (this.files && this.files.length > 0) {
        fileName.textContent = this.files[0].name;
    } else {
        fileName.textContent = "Aucun fichier choisi";
    }
});
</script>