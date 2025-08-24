<?php include '../../../includes/header.php'; ?>
<head>
    <link rel="stylesheet" href="about.css">
</head>

<div class="container bootstrap snippets bootdey">
    <div class="panel panel-info panel-shadow">
        <div class="panel-heading">
            <h3>
                <img class="img-circle img-thumbnail img-fixed"
                    src="<?php echo $url_base; ?>assets/images/images1.jpg">
                Série : Deyson Bejarano
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
                                <a href="<?php echo $url_base; ?>public/appManager/acteur/acteurs.php"><img
                                        src="https://www.bootdey.com/image/400x400/" class="img-responsive"
                                        alt="Acteurs"></a>
                                <div class="member-details">
                                    <h4><a
                                            href="<?php echo $url_base; ?>public/appManager/acteur/acteurs.php">Acteurs</a>
                                    </h4>
                                    <span>Gestion des acteurs de la série</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="team-member">
                                <a href="<?php echo $url_base; ?>public/appManager/series/factures_devis.php"><img
                                        src="<?php echo $url_base; ?>assets/images/facture.png"
                                        class="img-responsive" alt="Factures et Devis"></a>
                                <div class="member-details">
                                    <h4><a
                                            href="<?php echo $url_base; ?>public/appManager/facture/all_devis_fac.php">Factures
                                            & Devis</a></h4>
                                    <span>Suivi des documents financiers</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="team-member">
                                <a href="<?php echo $url_base; ?>public/appManager/series/tournages.php"><img
                                        src="<?php echo $url_base; ?>assets/images/tourn.jpg"
                                        class="img-responsive" alt="Tournages"></a>
                                <div class="member-details">
                                    <h4><a
                                            href="<?php echo $url_base; ?>public/appManager/series/tournages.php">Tournages</a>
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
                                <a href="../depenses/depenses.php"><img src="https://www.bootdey.com/image/400x400/"
                                        class="img-responsive" alt="Dépenses"></a>
                                <div class="member-details">
                                    <h4><a href="../depenses/liste_all.php">Dépenses</a></h4>
                                    <span>Suivi des coûts de production</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="team-member">
                                <a href="<?php echo $url_base; ?>pages/sponsors/listes.php"><img
                                        src="<?php echo $url_base; ?>assets/images/sponsor.webp"
                                        class="img-responsive" alt="Sponsoring"></a>
                                <div class="member-details">
                                    <h4><a href="<?php echo $url_base; ?>pages/sponsors/listes.php">Recettes</a>
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