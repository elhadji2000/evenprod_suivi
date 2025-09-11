<?php include '../../../config/fonction.php'; 
$totaux = getTotaux($connexion);
$totaux2 = getTotauxDepenses($connexion);
?>
<?php
session_start();

// Redirection si l'utilisateur n'a pas mis à jour son mot de passe
if (!isset($_SESSION['updated']) || !$_SESSION['updated']) {
    header("Location: ../../../public/admin/profile.php?forceUpdate=1");
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EvenProd</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<?php include '../../../includes/header.php'; 
?>

<body>

    <div class="container">
        <div class="header-home">
            <br>
            <h1><i class="fas fa-chart-line"></i> Dashboard EvenProd</h1>
            <div class="header-actions">
                <!-- Vous pouvez ajouter des boutons d'action ici -->
            </div>
        </div>
        <section class="pt-4 pb-6" id="dashboard">
            <div class="container">
                <div class="row g-4 text-center">

                    <!-- Total Users -->
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="text-primary mb-2">
                                    <?php echo number_format($totaux['users'], 0, ',', ','); ?></h5>
                                <h6 class="card-title">Utilisateurs</h6>
                                <p class="card-text">Nombre total d'utilisateurs enregistrés.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Series -->
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="text-primary mb-2">
                                    <?php echo number_format($totaux['series'], 0, ',', ','); ?></h5>
                                <h6 class="card-title">Séries</h6>
                                <p class="card-text">Nombre total de séries enregistrées.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Clients -->
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="text-primary mb-2">
                                    <?php echo number_format($totaux['clients'], 0, ',', ','); ?></h5>
                                <h6 class="card-title">Clients</h6>
                                <p class="card-text">Nombre total de clients enregistrés.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Factures -->
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="text-primary mb-2">
                                    <?php echo number_format($totaux['acteurs'], 0, ',', ','); ?></h5>
                                <h6 class="card-title">Acteurs</h6>
                                <p class="card-text">Nombre total de Acteurs enregistrer.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <section class="pt-4 pb-6" id="count-stats">
            <div class="container">
                <section id="count-stats" class="py-5 bg-light">
                    <div class="container">
                        <div class="row justify-content-center text-center g-4">
                            <div class="col-md-3">
                                <h5 class="text-primary" id="state1" data-count="5234">
                                    <?php echo number_format($totaux2['cachet'], 0, ',', ','); ?>
                                </h5>
                                <h5>Cachets</h5>
                                <p>Dépenses liées aux cachets et honoraires.</p>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-primary" id="state2" data-count="3400">
                                    <?php echo number_format($totaux2['decor'], 0, ',', ','); ?>
                                </h5>
                                <h5>Décors</h5>
                                <p>Frais liés à la décoration et à l’aménagement.</p>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-primary" id="state3" data-count="24">
                                    <?php echo number_format($totaux2['transport'], 0, ',', ','); ?>
                                </h5>
                                <h5>Transports</h5>
                                <p>Coûts de déplacement et de logistique.</p>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-primary" id="state4" data-count="24">
                                    <?php echo number_format($totaux2['autre'], 0, ',', ','); ?>
                                </h5>
                                <h5>Autres</h5>
                                <p>Dépenses diverses non catégorisées.</p>
                            </div>
                        </div>

                    </div>
                </section>
            </div>
        </section>
    </div>

</body>

</html>