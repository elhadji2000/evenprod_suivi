<head>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
</head>
<?php include '../includes/header.php'; 
include '../config/fonction.php'; 
$lastSerie = getLastSerie();
?>
<link rel="stylesheet" href="add.css">
<section class="section gray-bg" id="contactus">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="section-title">
                    <h2>Ajouter une série</h2>
                    <p>Complétez le formulaire pour enregistrer une nouvelle série.</p>
                </div>
            </div>
        </div>
        <div class="row flex-row-reverse">
            <div class="col-md-7 col-lg-8 m-15px-tb">
                <div class="contact-form">
                    <form action="trait_serie.php" method="post" enctype="multipart/form-data"
                        class="contactform contact_form" id="contact_form">
                        <div class="returnmessage valid-feedback p-15px-b"
                            data-success="Votre série a été enregistrée avec succès."></div>
                        <div class="empty_notice invalid-feedback p-15px-b">
                            <span>Veuillez remplir tous les champs requis</span>
                        </div>
                        <div class="row">
                            <!-- Titre -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="titre" name="titre" type="text" placeholder="Titre" class="form-control"
                                        required>
                                </div>
                            </div>
                            <!-- Type (select) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select id="type" name="type" class="form-control" required>
                                        <option value="">-- Sélectionnez un type --</option>
                                        <option value="Film">Film</option>
                                        <option value="Série TV">Série TV</option>
                                        <option value="Documentaire">Documentaire</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Budget (nombre) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="budget" name="budget" type="number" min="0" placeholder="Budget initial"
                                        class="form-control" required>
                                </div>
                            </div>
                            <!-- Photo (upload) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <!-- Input file caché -->
                                    <input type="file" id="photo" name="photo" accept="image/*"
                                        class="form-control-file " required hidden>

                                    <!-- Label personnalisé -->
                                    <label for="photo" class="custom-file-label form-control">
                                        <span><i class="fas fa-upload"></i> Choisir
                                            une photo</span>
                                        <span id="file-name" class="file-name">Aucun fichier choisi</span>
                                    </label>

                                </div>
                            </div>
                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea id="description" name="description" placeholder="Description"
                                        class="form-control" rows="3" required></textarea>
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
                    <h5>Dernière série ajoutée</h5>
                    <?php if($lastSerie): ?>
                    <p>Nom : <?php echo htmlspecialchars($lastSerie['titre']); ?></p>
                    <p>Type : <?php echo htmlspecialchars($lastSerie['type']); ?></p>
                    <p>Budget : <?php echo htmlspecialchars($lastSerie['budget']); ?></p>
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