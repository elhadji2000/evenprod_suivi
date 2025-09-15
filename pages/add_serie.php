<?php

include '../config/fonction.php'; 

// Vérifier si on est en mode modification
$serieId = $_GET['id'] ?? null;
$serie = $serieId ? getSerieById($serieId) : null;

$lastSerie = getLastSerie();

include '../includes/header.php'; 
?>
<link rel="stylesheet" href="add.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
      integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />

<section class="section gray-bg" id="contactus">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="section-title">
                    <h2><?= $serieId ? "Modifier la série" : "Ajouter une série" ?></h2>
                    <p>Complétez le formulaire pour <?= $serieId ? "modifier" : "enregistrer" ?> la série.</p>
                </div>
            </div>
        </div>
        <div class="row flex-row-reverse">
            <div class="col-md-7 col-lg-8 m-15px-tb">
                <div class="contact-form">
                    <form action="trait_serie" method="post" enctype="multipart/form-data"
                          class="contactform contact_form" id="contact_form">
                        <?php if($serieId): ?>
                            <input type="hidden" name="serie_id" value="<?= $serieId ?>">
                        <?php endif; ?>

                        <div class="row">
                            <!-- Titre -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="titre" name="titre" type="text" placeholder="Titre" class="form-control"
                                           value="<?= htmlspecialchars($serie['titre'] ?? '') ?>" required>
                                </div>
                            </div>
                            <!-- Type (select) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select id="type" name="type" class="form-control" required>
                                        <option value="">-- Sélectionnez un type --</option>
                                        <?php 
                                        $types = ['Film', 'Série TV', 'Documentaire'];
                                        foreach($types as $t):
                                            $selected = ($serie['type'] ?? '') === $t ? 'selected' : '';
                                        ?>
                                            <option value="<?= $t ?>" <?= $selected ?>><?= $t ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Budget -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="budget" name="budget" type="number" min="0" placeholder="Budget initial"
                                           class="form-control" value="<?= htmlspecialchars($serie['budget'] ?? '') ?>" required>
                                </div>
                            </div>
                            <!-- Photo -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="file" id="photo" name="photo" accept="image/*"
                                           class="form-control-file" <?= $serieId ? '' : 'required' ?> hidden>

                                    <label for="photo" class="custom-file-label form-control">
                                        <span><i class="fas fa-upload"></i> Choisir une photo</span>
                                        <span id="file-name" class="file-name">
                                            <?= $serie['logo'] ?? 'Aucun fichier choisi' ?>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea id="description" name="description" placeholder="Description"
                                              class="form-control" rows="3" required><?= htmlspecialchars($serie['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <!-- Bouton -->
                            <div class="col-md-12">
                                <div class="send">
                                    <button type="submit" class="px-btn theme">
                                        <span><?= $serieId ? "MODIFIER" : "ENREGISTRER" ?></span> <i class="arrow"></i>
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
                    <h5>Dernière série ajoutée</h5>
                    <?php if($lastSerie): ?>
                        <p>Nom : <?= htmlspecialchars($lastSerie['titre']) ?></p>
                        <p>Type : <?= htmlspecialchars($lastSerie['type']) ?></p>
                        <p>Budget : <?= number_format($lastSerie['budget'], 0, ',', ',') ?> fcfa</p>
                    <?php else: ?>
                        <p>Aucune série enregistrée pour le moment.</p>
                    <?php endif; ?>
                </div>
                <div class="contact-name">
                    <h5>Conseil</h5>
                    <p>Ajoutez d’abord la série avant d’ajouter ses acteurs.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const inputFile = document.getElementById("photo");
const fileName = document.getElementById("file-name");

inputFile.addEventListener("change", function() {
    if (this.files && this.files.length > 0) {
        fileName.textContent = this.files[0].name;
    } else {
        fileName.textContent = "Aucun fichier choisi";
    }
});
</script>

<?php if (isset($_GET['reussi']) && $_GET['reussi'] == 1): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const toast = document.createElement("div");
    toast.textContent = "Série <?= $serieId ? 'modifiée' : 'ajoutée' ?> avec succès !";
    toast.style.position = "fixed";
    toast.style.top = "80px";
    toast.style.right = "30px";
    toast.style.padding = "15px 25px";
    toast.style.backgroundColor = "#28a745";
    toast.style.color = "white";
    toast.style.borderRadius = "5px";
    toast.style.boxShadow = "0 2px 10px rgba(0,0,0,0.2)";
    toast.style.zIndex = 9999;
    toast.style.fontWeight = "bold";
    toast.style.opacity = 0;
    toast.style.transition = "opacity 0.5s";

    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = 1; }, 100);
    setTimeout(() => { toast.style.opacity = 0; setTimeout(() => toast.remove(), 500); }, 5000);
});
</script>
<?php endif; ?>
