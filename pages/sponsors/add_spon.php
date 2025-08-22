<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
    <link rel="stylesheet" href="add.css">
</head>
<?php include '../../includes/header.php'; ?>

<section class="section gray-bg" id="contactus">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="section-title">
                    <h2>Ajouter un partenariat</h2>
                    <p>Complétez le formulaire pour enregistrer un nouveau partenariat (client ou sponsor).</p>
                </div>
            </div>
        </div>
        <div class="row flex-row-reverse">
            <div class="col-md-7 col-lg-8 m-15px-tb">
                <div class="contact-form">
                    <form action="ajouter_partenariat.php" method="post" enctype="multipart/form-data"
                        class="contactform contact_form" id="contact_form">

                        <div class="returnmessage valid-feedback p-15px-b"
                            data-success="Partenariat enregistré avec succès."></div>
                        <div class="empty_notice invalid-feedback p-15px-b">
                            <span>Veuillez remplir tous les champs requis</span>
                        </div>

                        <div class="row">
                            <!-- NINEA -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="ninea" name="ninea" type="text" placeholder="NINEA" class="form-control"
                                        required>
                                </div>
                            </div>
                            <!-- Nom -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="nom" name="nom" type="text" placeholder="Nom du partenaire"
                                        class="form-control" required>
                                </div>
                            </div>
                            <!-- Logo (upload) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <!-- Input file caché -->
                                    <input type="file" id="logo" name="logo" accept="image/*" class="form-control-file"
                                        required hidden>

                                    <!-- Label personnalisé -->
                                    <label for="logo" class="custom-file-label form-control">
                                        <span><i class="fas fa-upload"></i> Choisir un logo</span>
                                        <span id="file-name" class="file-name">Aucun fichier choisi</span>
                                    </label>
                                </div>
                            </div>
                            <!-- Type de partenariat (optionnel) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select id="type" name="type" class="form-control" required>
                                        <option value="">-- Sélectionnez un type --</option>
                                        <option value="client">Client</option>
                                        <option value="sponsor">Sponsor</option>
                                        <option value="partenaire">Partenaire</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea id="description" name="description"
                                        placeholder="Description du partenariat" class="form-control" rows="3"
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
            <!-- Partie droite (Aperçu / infos rapides) -->
            <div class="col-md-5 col-lg-4 m-15px-tb">
                <div class="contact-name">
                    <h5>Aperçu</h5>
                    <p>Partenariats enregistrés : 12</p>
                    <p>Types actifs : Clients, Sponsors, Partenaires</p>
                </div>
                <div class="contact-name shortcut-links">
                    <h5>Raccourcis</h5>
                    <p><a href="#">Voir tous les partenariats</a></p>
                    <p><a href="#">Ajouter un sponsor</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const inputFile = document.getElementById("logo");
const fileName = document.getElementById("file-name");

inputFile.addEventListener("change", function() {
    if (this.files && this.files.length > 0) {
        fileName.textContent = this.files[0].name;
    } else {
        fileName.textContent = "Aucun fichier choisi";
    }
});
</script>