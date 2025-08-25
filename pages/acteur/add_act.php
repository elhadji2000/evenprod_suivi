<head>
    <link rel="stylesheet" href="add.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
</head>
<?php include '../../includes/header.php'; ?>
<?php
include '../../config/fonction.php'; 
$lastSerie = getLastSerie(); // retourne la dernière série ajoutée
?>

<section class="section gray-bg" id="contactus">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="section-title">
                    <h2>Ajouter un acteur</h2>
                    <p>Remplissez le formulaire ci-dessous pour enregistrer un nouvel acteur.</p>
                </div>
            </div>
        </div>
        <!-- ✅ Affichage des messages Bootstrap -->
        <div class="row">
            <div class="col-12">
                <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="row flex-row-reverse">
            <div class="col-md-7 col-lg-8 m-15px-tb">
                <div class="contact-form">
                    <form action="trait_acteur.php" method="post" enctype="multipart/form-data"
                        class="contactform contact_form" id="contact_form">
                        <div class="row">
                            <!-- Prénom -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="prenom" placeholder="Prénom" class="form-control" required>
                                </div>
                            </div>
                            <!-- Nom -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="nom" placeholder="Nom" class="form-control" required>
                                </div>
                            </div>
                            <!-- Date de naissance -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="date" name="date_naissance" class="form-control" required>
                                </div>
                            </div>
                            <!-- Contact -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="contact" placeholder="Contact" class="form-control"
                                        required>
                                </div>
                            </div>
                            <!-- Adresse -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="adresse" placeholder="Adresse" class="form-control"
                                        required>
                                </div>
                            </div>
                            <!-- CV PDF -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="file" id="cv" name="cv" accept="application/pdf" class="form-control"
                                        required hidden>
                                    <label for="cv" class="custom-file-label form-control">
                                        <span><i class="fas fa-upload"></i> Choisir CV (PDF)</span>
                                        <span id="file-cv" class="file-name">Aucun fichier choisi</span>
                                    </label>
                                </div>
                            </div>
                            <!-- Photo -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="file" id="photo" name="photo" accept="image/*" class="form-control"
                                        required hidden>
                                    <label for="photo" class="custom-file-label form-control">
                                        <span><i class="fas fa-upload"></i> Choisir une photo</span>
                                        <span id="file-name" class="file-name">Aucun fichier choisi</span>
                                    </label>
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

            <!-- Partie gauche : info dernière série -->
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
const inputFileCV = document.getElementById("cv");
const fileNameCV = document.getElementById("file-cv");
inputFileCV.addEventListener("change", function() {
    fileNameCV.textContent = this.files.length > 0 ? this.files[0].name : "Aucun fichier choisi";
});

const inputFilePhoto = document.getElementById("photo");
const fileNamePhoto = document.getElementById("file-name");
inputFilePhoto.addEventListener("change", function() {
    fileNamePhoto.textContent = this.files.length > 0 ? this.files[0].name : "Aucun fichier choisi";
});
</script>