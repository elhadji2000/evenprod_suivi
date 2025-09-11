<?php
include '../../../includes/header.php';
include '../../../config/fonction.php';

$role = $_SESSION['role'] ?? 'guest';

$id = $_GET['id'] ?? 0;
$serie = getSerieById($id);
$totauxtous = getTotauxGeneraux($connexion, $id);

// Définition des accès par rôle
$permissions = [
    'acteur' => ['admin', 'tournage'],
    'facture' => ['admin', 'comptable'],
    'tournages' => ['admin', 'tournage'],
    'depenses' => ['admin', 'comptable', 'caisse'],
    'recettes' => ['admin', 'comptable', 'caisse'],
];
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
                        <!-- Acteurs -->
                        <div class="col-md-4 col-sm-6">
                            <div class="team-member">
                                <?php if (in_array($role, $permissions['acteur'])): ?>
                                    <a href="<?php echo $url_base; ?>public/appManager/acteur/acteurs.php?id=<?php echo htmlspecialchars($serie['id'])?>">
                                        <img src="<?php echo $url_base; ?>assets/images/peoples.png" class="img-responsive" alt="Acteurs">
                                    </a>
                                <?php else: ?>
                                    <img src="<?php echo $url_base; ?>assets/images/peoples.png" class="img-responsive" alt="Acteurs">
                                <?php endif; ?>
                                <div class="member-details">
                                    <h4>
                                        <?php if (in_array($role, $permissions['acteur'])): ?>
                                            <a href="<?php echo $url_base; ?>public/appManager/acteur/acteurs.php?id=<?php echo htmlspecialchars($serie['id'])?>" class="text-info text-decoration-underline">Acteurs</a>
                                        <?php else: ?>
                                            <span class="text-muted">Acteurs</span>
                                        <?php endif; ?>
                                    </h4>
                                    <span>Gestion des acteurs de la série</span>
                                </div>
                            </div>
                        </div>

                        <!-- Factures & Devis -->
                        <div class="col-md-4 col-sm-6">
                            <div class="team-member">
                                <?php if (in_array($role, $permissions['facture'])): ?>
                                    <a href="<?php echo $url_base; ?>public/appManager/facture/all_devis_fac.php?id=<?php echo htmlspecialchars($serie['id'])?>">
                                        <img src="<?php echo $url_base; ?>assets/images/facture.png" class="img-responsive" alt="Factures et Devis">
                                    </a>
                                <?php else: ?>
                                    <img src="<?php echo $url_base; ?>assets/images/facture.png" class="img-responsive" alt="Factures et Devis">
                                <?php endif; ?>
                                <div class="member-details">
                                    <h4>
                                        <?php if (in_array($role, $permissions['facture'])): ?>
                                            <a href="<?php echo $url_base; ?>public/appManager/facture/all_devis_fac.php?id=<?php echo htmlspecialchars($serie['id'])?>" class="text-info text-decoration-underline">Factures & Devis</a>
                                        <?php else: ?>
                                            <span class="text-muted">Factures & Devis</span>
                                        <?php endif; ?>
                                    </h4>
                                    <span>Suivi des documents financiers</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tournages -->
                        <div class="col-md-4 col-sm-6">
                            <div class="team-member">
                                <?php if (in_array($role, $permissions['tournages'])): ?>
                                    <a href="<?php echo $url_base; ?>public/appManager/series/tournages.php?id=<?php echo htmlspecialchars($serie['id'])?>">
                                        <img src="<?php echo $url_base; ?>assets/images/tourn.jpg" class="img-responsive" alt="Tournages">
                                    </a>
                                <?php else: ?>
                                    <img src="<?php echo $url_base; ?>assets/images/tourn.jpg" class="img-responsive" alt="Tournages">
                                <?php endif; ?>
                                <div class="member-details">
                                    <h4>
                                        <?php if (in_array($role, $permissions['tournages'])): ?>
                                            <a href="<?php echo $url_base; ?>public/appManager/series/tournages.php?id=<?php echo htmlspecialchars($serie['id'])?>" class="text-info text-decoration-underline">Tournages</a>
                                        <?php else: ?>
                                            <span class="text-muted">Tournages</span>
                                        <?php endif; ?>
                                    </h4>
                                    <span>Organisation et suivi des tournages</span>
                                </div>
                            </div>
                        </div>

                        <!-- Dépenses -->
                        <div class="col-md-4 col-sm-6">
                            <div class="team-member">
                                <?php if (in_array($role, $permissions['depenses'])): ?>
                                    <a href="../depenses/liste_all.php?id=<?php echo htmlspecialchars($serie['id'])?>">
                                        <img src="<?php echo $url_base; ?>assets/images/depense.jpg" class="img-responsive" alt="Dépenses">
                                    </a>
                                <?php else: ?>
                                    <img src="<?php echo $url_base; ?>assets/images/depense.jpg" class="img-responsive" alt="Dépenses">
                                <?php endif; ?>
                                <div class="member-details">
                                    <h4>
                                        <?php if (in_array($role, $permissions['depenses'])): ?>
                                            <a href="../depenses/liste_all.php?id=<?php echo htmlspecialchars($serie['id'])?>" class="text-info text-decoration-underline">Dépenses</a>
                                        <?php else: ?>
                                            <span class="text-muted">Dépenses</span>
                                        <?php endif; ?>
                                    </h4>
                                    <span>Suivi des coûts de production</span>
                                </div>
                            </div>
                        </div>

                        <!-- Recettes -->
                        <div class="col-md-4 col-sm-6">
                            <div class="team-member">
                                <?php if (in_array($role, $permissions['recettes'])): ?>
                                    <a href="<?php echo $url_base; ?>public/appManager/recettes/details_spg.php?id=<?php echo htmlspecialchars($serie['id'])?>">
                                        <img src="<?php echo $url_base; ?>assets/images/recette.avif" class="img-responsive" alt="Recettes">
                                    </a>
                                <?php else: ?>
                                    <img src="<?php echo $url_base; ?>assets/images/recette.avif" class="img-responsive" alt="Recettes">
                                <?php endif; ?>
                                <div class="member-details">
                                    <h4>
                                        <?php if (in_array($role, $permissions['recettes'])): ?>
                                            <a href="<?php echo $url_base; ?>public/appManager/recettes/details_spg.php?id=<?php echo htmlspecialchars($serie['id'])?>" class="text-info text-decoration-underline">Recettes</a>
                                        <?php else: ?>
                                            <span class="text-muted">Recettes</span>
                                        <?php endif; ?>
                                    </h4>
                                    <span>Entrées financières générées</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Totaux -->
<br><br>
<div class="container">
    <section id="count-stats" class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center text-center g-4">
                <div class="col-md-3">
                    <h4 class="text-primary">
                        <?php echo number_format($serie['budget'], 0, ',', ','); ?>
                    </h4>
                    <h5>Budget</h5>
                    <p>série concerné.</p>
                </div>
                <div class="col-md-3">
                    <h4 class="text-primary">
                        <?php echo number_format($totauxtous['total_depenses'], 0, ',', ','); ?>
                    </h4>
                    <h5>Dépenses</h5>
                    <p>Montant total des dépenses effectuées.</p>
                </div>
                <div class="col-md-3">
                    <h4 class="text-primary">
                        <?php echo number_format($totauxtous['total_factures'], 0, ',', ','); ?>
                    </h4>
                    <h5>Recettes</h5>
                    <p>Montant total des factures validées.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Paramétrage série -->
<div class="serie-settings">
    <h4>Paramètres de la série</h4>
    <div class="settings-actions">
        <a href="<?php echo $url_base; ?>pages/add_serie.php?id=<?php echo htmlspecialchars($serie['id'])?>" class="btn btn-warning">Modifier</a>
        <a href="<?php echo $url_base; ?>public/appManager/delete.php?table=series&id=<?php echo htmlspecialchars($serie['id']); ?>&redirect=<?php echo $url_base; ?>pages/about-us.php"
           class="btn btn-danger"
           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.');">Supprimer</a>
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
.serie-settings h4 { margin-bottom: 15px; font-weight: bold; }
.settings-actions { display: flex; justify-content: center; gap: 15px; }
.btn { padding: 8px 18px; text-decoration: none; border-radius: 5px; font-size: 14px; }
.btn-warning { background-color: #f39c12; color: #fff; }
.btn-warning:hover { background-color: #e67e22; }
.btn-danger { background-color: #e74c3c; color: #fff; }
.btn-danger:hover { background-color: #c0392b; }
.text-info.text-decoration-underline { text-decoration: underline; color: #17a2b8 !important; }
.text-muted { color: #6c757d !important; }
</style>

<?php include '../../../includes/footer.php'; ?>
