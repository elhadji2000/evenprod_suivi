<?php include '../../../includes/header.php'; ?>
<head>
    <link rel="stylesheet" href="<?php echo $url_base; ?>pages/acteur/add.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
    /* Style pour ressembler aux autres champs input */
    .custom-multi-select {
        border: 1px solid #ced4da;
        background-color: #f7f7f7;
        border-radius: 5px;
        padding: 10px;
    }

    .custom-multi-select div {
        margin-bottom: 5px;
    }

    .custom-multi-select input[type="checkbox"] {
        margin-right: 8px;
        cursor: pointer;
    }

    .custom-multi-select label {
        cursor: pointer;
        font-weight: 500;
    }
    </style>
</head>
<section class="section gray-bg" id="contactus">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="section-title">
                    <h2>Ajouter un Tournage</h2>
                    <p>Complétez le formulaire pour enregistrer un nouveau tournage de série.</p>
                </div>
            </div>
        </div>
        <div class="row flex-row-reverse">
            <div class="col-md-7 col-lg-8 m-15px-tb">
                <div class="contact-form">
                    <form action="ajouter_tournage.php" method="post" class="contactform contact_form"
                        id="contact_form">
                        <div class="returnmessage valid-feedback p-15px-b"
                            data-success="Tournage enregistré avec succès."></div>
                        <div class="empty_notice invalid-feedback p-15px-b">
                            <span>Veuillez remplir tous les champs requis</span>
                        </div>
                        <div class="row">
                            <!-- Référence tournage -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="ref_tournage" name="ref_tournage" type="text"
                                        placeholder="Référence du tournage" class="form-control" required>
                                </div>
                            </div>
                            <!-- Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="date" name="date" type="date" class="form-control" required>
                                </div>
                            </div>
                            <?php
// Liste statique des membres
$membres = [
    ['id' => 1, 'nom' => 'Madiop Diop'],
    ['id' => 2, 'nom' => 'Elhadji Diop'],
    ['id' => 3, 'nom' => 'Moussa Faye'],
    ['id' => 4, 'nom' => 'Aissatou Diallo'],
    ['id' => 5, 'nom' => 'Fatou Ndiaye'],
    ['id' => 6, 'nom' => 'Seynabou Ba'],
    ['id' => 7, 'nom' => 'Ousmane Ndiaye']
];
?>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="equipe">Équipe de tournage</label>
                                    <div class="custom-multi-select"
                                        style="max-height:200px; overflow-y:auto; padding:10px; background:#f7f7f7; border:1px solid #ced4da; border-radius:5px;">
                                        <?php foreach($membres as $membre): ?>
                                        <div>
                                            <input type="checkbox" id="membre_<?= $membre['id'] ?>" name="equipe[]"
                                                value="<?= $membre['id'] ?>">
                                            <label for="membre_<?= $membre['id'] ?>"><?= $membre['nom'] ?></label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
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
                    <p>Tournages déjà enregistrés : 10</p>
                    <p>Membres de l'équipe : 25</p>
                </div>
                <div class="contact-name shortcut-links">
                    <h5>Raccourcis</h5>
                    <p><a href="#">Voir tous les tournages</a></p>
                    <p><a href="#">Gérer l'équipe</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// Initialiser Select2 pour l'équipe
$(document).ready(function() {
    $('#equipe').select2({
        placeholder: "Sélectionnez les membres de l'équipe",
        allowClear: false
    });
});
</script>