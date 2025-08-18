<?php
// Requêtes pour récupérer les totaux
$totalSeries = 5;
$totalFilms = 7;
$totalActeurs = 65;
$totalUtilisateurs = 5;
?>

<head>
    <link rel="stylesheet" href="style.css">
</head>
<?php include '../../../includes/header.php'; ?>
<div class="container">
    <h1>📊 Dashboard - EvenProd</h1>
    <div class="dashboard">
        <a href="#" class="card" style="text-decoration: none;">
            <div class="icon">📺</div>
            <h2><?php echo $totalSeries; ?></h2>
            <p>Total Séries</p>
        </a>
        <a href="#" class="card" style="text-decoration: none;">
            <div class="icon">🎬</div>
            <h2><?php echo $totalFilms; ?></h2>
            <p>Total Films</p>
        </a>
        <a href="http://localhost/projet_suivi/public/appManager/acteur/acteurs.php" class="card" style="text-decoration: none;">
            <div class="icon">🎭</div>
            <h2><?php echo $totalActeurs; ?></h2>
            <p>Total Acteurs</p>
        </a>
        <a href="#" class="card" style="text-decoration: none;">
            <div class="icon">👤</div>
            <h2><?php echo $totalUtilisateurs; ?></h2>
            <p>Total Utilisateurs</p>
        </a>
    </div>
</div>