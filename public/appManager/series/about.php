<?php include '../../../includes/header.php';
include '../../../config/fonction.php';
$id = $_GET['id'] ?? 0;
$serie = getSerieById($id);
 ?>
<head>
    <link rel="stylesheet" href="about.css">
</head>
<br>
<div class="container bootstrap snippets bootdey">
    <div class="panel panel-info panel-shadow">
        <div class="panel-heading">
            <h3>
                <img class="img-circle img-thumbnail img-fixed"
                    src="<?php echo $url_base; ?>uploads/series/<?php echo htmlspecialchars($serie['logo'])?>">
                Série : <?php echo htmlspecialchars($serie['titre'])?>
            </h3>
        </div>
        <div class="panel-body">
            <div class="team padd">
                <div class="container bootstrap snippets bootdey">
                    <div class="heading">
                        <h2>Tableau de bord de la série</h2>
                        <h6>Consultez et gérez toutes les informations liées à cette série</h6>
                        <div class="divider"></div>
                        <p>Accédez directement aux acteurs, tournages, documents financiers, dépenses, recettes et
                            sponsoring associés.</p>
                        <div class="divider"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="team-member">
                                <a href="<?php echo $url_base; ?>public/appManager/acteur/acteurs.php?id=<?php echo htmlspecialchars($serie['id'])?>"><img
                                        src="<?php echo $url_base; ?>assets/images/peoples.png" class="img-responsive"
                                        alt="Acteurs"></a>
                                <div class="member-details">
                                    <h4><a
                                            href="<?php echo $url_base; ?>public/appManager/acteur/acteurs.php?id=<?php echo htmlspecialchars($serie['id'])?>">Acteurs</a>
                                    </h4>
                                    <span>Gestion des acteurs de la série</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="team-member">
                                <a href="<?php echo $url_base; ?>public/appManager/series/factures_devis.php?id=<?php echo htmlspecialchars($serie['id'])?>"><img
                                        src="<?php echo $url_base; ?>assets/images/facture.png"
                                        class="img-responsive" alt="Factures et Devis"></a>
                                <div class="member-details">
                                    <h4><a
                                            href="<?php echo $url_base; ?>public/appManager/facture/all_devis_fac.php?id=<?php echo htmlspecialchars($serie['id'])?>">Factures
                                            & Devis</a></h4>
                                    <span>Suivi des documents financiers</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="team-member">
                                <a href="<?php echo $url_base; ?>public/appManager/series/tournages.php?id=<?php echo htmlspecialchars($serie['id'])?>"><img
                                        src="<?php echo $url_base; ?>assets/images/tourn.jpg"
                                        class="img-responsive" alt="Tournages"></a>
                                <div class="member-details">
                                    <h4><a
                                            href="<?php echo $url_base; ?>public/appManager/series/tournages.php?id=<?php echo htmlspecialchars($serie['id'])?>">Tournages</a>
                                    </h4>
                                    <span>Organisation et suivi des tournages</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="team-member">
                                <a href="../depenses/depenses.php?id=<?php echo htmlspecialchars($serie['id'])?>"><img src="<?php echo $url_base; ?>assets/images/depense.jpg"
                                        class="img-responsive" alt="Dépenses"></a>
                                <div class="member-details">
                                    <h4><a href="../depenses/liste_all.php?id=<?php echo htmlspecialchars($serie['id'])?>">Dépenses</a></h4>
                                    <span>Suivi des coûts de production</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="team-member">
                                <a href="<?php echo $url_base; ?>public/appManager/recettes/details_spg.php?id=<?php echo htmlspecialchars($serie['id'])?>"><img
                                        src="<?php echo $url_base; ?>assets/images/recette.avif"
                                        class="img-responsive" alt="Sponsoring"></a>
                                <div class="member-details">
                                    <h4><a href="<?php echo $url_base; ?>public/appManager/recettes/details_spg.php?id=<?php echo htmlspecialchars($serie['id'])?>">Recettes</a>
                                    </h4>
                                    <span>Entrées financières générées.</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bloc Paramétrage Série -->
<div class="serie-settings">
    <h4>Paramètres de la série</h4>
    <div class="settings-actions">
        <a href="<?php echo $url_base; ?>public/appManager/series/edit.php?id=<?php echo htmlspecialchars($serie['id'])?>" 
           class="btn btn-warning">Modifier</a>

        <a href="<?php echo $url_base; ?>public/appManager/series/delete.php?id=<?php echo htmlspecialchars($serie['id'])?>" 
           class="btn btn-danger" 
           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette série ? Cette action est irréversible.');">
           Supprimer
        </a>
    </div>
</div>

<style>
.serie-settings {
    margin: 30px auto;
    padding: 20px;
    border-top: 1px solid #ddd;
    text-align: center;
    max-width: 800px;
}
.serie-settings h4 {
    margin-bottom: 15px;
    font-weight: bold;
}
.settings-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
}
.btn {
    padding: 8px 18px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
}
.btn-warning {
    background-color: #f39c12;
    color: #fff;
}
.btn-warning:hover {
    background-color: #e67e22;
}
.btn-danger {
    background-color: #e74c3c;
    color: #fff;
}
.btn-danger:hover {
    background-color: #c0392b;
}
</style>

<?php include '../../../includes/footer.php';