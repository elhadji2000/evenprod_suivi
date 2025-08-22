<head>
    <link rel="stylesheet" href="http://localhost/projet_suivi/pages/acteur/add.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
</head>
<?php include '../../includes/header.php'; ?>

<section class="section gray-bg" id="contactus">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="section-title">
                    <h2>Ajouter un utilisateur</h2>
                    <p>Complétez le formulaire pour enregistrer un nouvel utilisateur.</p>
                </div>
            </div>
        </div>
        <div class="row flex-row-reverse">
            <div class="col-md-7 col-lg-8 m-15px-tb">
                <div class="contact-form">
                    <form action="ajouter_utilisateur.php" method="post" enctype="multipart/form-data"
                        class="contactform contact_form" id="contact_form">
                        <div class="returnmessage valid-feedback p-15px-b"
                            data-success="Utilisateur enregistré avec succès."></div>
                        <div class="empty_notice invalid-feedback p-15px-b">
                            <span>Veuillez remplir tous les champs requis</span>
                        </div>
                        <div class="row">
                            <!-- Nom -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="nom" name="nom" type="text" placeholder="Nom" class="form-control"
                                        required>
                                </div>
                            </div>
                            <!-- Prénom -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="prenom" name="prenom" type="text" placeholder="Prénom"
                                        class="form-control" required>
                                </div>
                            </div>
                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="email" name="email" type="email" placeholder="Email" class="form-control"
                                        required>
                                </div>
                            </div>
                            <!-- Téléphone -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="telephone" name="telephone" type="tel" placeholder="Téléphone"
                                        class="form-control" required>
                                </div>
                            </div>
                            <!-- Rôle -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select id="role" name="role" class="form-control" required>
                                        <option value="">-- Sélectionnez un rôle --</option>
                                        <option value="admin">Administrateur</option>
                                        <option value="responsable">Responsable</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Photo de profil -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <!-- Input file caché -->
                                    <input type="file" id="photo" name="photo" accept="image/*"
                                        class="form-control-file " required hidden>

                                    <!-- Label personnalisé -->
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
            <!-- Partie droite (Aperçu / infos rapides) -->
            <div class="col-md-5 col-lg-4 m-15px-tb">
                <div class="contact-name">
                    <h5>Aperçu</h5>
                    <p>Utilisateurs déjà enregistrés : 15</p>
                    <p>Rôles actifs : Admin,Gerant</p>
                </div>
                <div class="contact-name shortcut-links">
                    <h5>Raccourcis</h5>
                    <p><a href="#">Voir tous les utilisateurs</a></p>
                    <p><a href="#">Gérer les rôles</a></p>
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