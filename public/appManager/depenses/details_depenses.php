<head>
    <link rel="stylesheet" href="depenses.css">
</head>
<?php include '../../../includes/header.php'; ?>

<div class="container mt-4">
    <h3 class="mb-4">Dépenses : <?php echo ucfirst($_GET['type']); ?></h3> <!-- Affiche le type cliqué -->

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Montant</th>
                <th>Date</th>
                <th>Pièce Jointe (PDF)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Exemple : récupère les dépenses par type depuis la base de données
            $type = $_GET['type']; // cachet / transport / autre
            // Ici on met des exemples statiques
            $depenses = [
                ['id'=>1, 'description'=>'Cachet acteur principal', 'montant'=>'500 000 FCFA', 'date'=>'10/08/2025', 'pdf'=>'cachet1.pdf'],
                ['id'=>2, 'description'=>'Cachet acteur secondaire', 'montant'=>'300 000 FCFA', 'date'=>'12/08/2025', 'pdf'=>'cachet2.pdf'],
            ];
            $i = 1;
            foreach($depenses as $d) {
                echo '<tr>
                    <td>'.$i.'</td>
                    <td>'.$d['description'].'</td>
                    <td>'.$d['montant'].'</td>
                    <td>'.$d['date'].'</td>
                    <td><a href="fichiers/'.$d['pdf'].'" target="_blank" class="text-decoration-underline">Voir PDF</a></td>
                    <td>
                        <a href="voir_depense.php?id='.$d['id'].'" class="btn btn-sm btn-primary">Voir</a>
                        <a href="modifier_depense.php?id='.$d['id'].'" class="btn btn-sm btn-warning">Modifier</a>
                        <a href="supprimer_depense.php?id='.$d['id'].'" class="btn btn-sm btn-danger">Supprimer</a>
                    </td>
                </tr>';
                $i++;
            }
            ?>
        </tbody>
    </table>

    <div class="mt-3">
        <a href="ajouter_depense.php?type=<?php echo $type; ?>" class="btn btn-success">➕ Ajouter une Dépense</a>
        <a href="depenses.php" class="btn btn-secondary">🔙 Retour aux Types</a>
    </div>
</div>
