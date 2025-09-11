<?php
include '../../../config/fonction.php';

$id = $_GET['id'] ?? 0;
$serie = getSerieById($id);
$tournages = getTournagesBySerieId($id);
?>
<?php include '../../../includes/header.php'; ?>

<head>
    <link rel="stylesheet" href="<?= $url_base ?>pages/acteur/add.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
</head>

<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Tournages de la série : <?= htmlspecialchars($serie['titre']) ?></h2>
            <a class="btn btn-success" href="add_tourn.php?id_serie=<?= $id ?>">
                <i class="fas fa-plus"></i> Ajouter un tournage
            </a>
        </div>

        <div class="table-container">
            <table id="tournageTable" class="display table table-bordered">
                <thead class="table-warning">
                    <tr>
                        <th>#</th>
                        <th>Réf. Tournage</th>
                        <th>Date</th>
                        <th>Équipe</th>
                        <th>Dépenses</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($tournages as $t): 
                        $depense = getDepenseByTournage($id, $t['id']);
                        $equipeCount = getEquipeCountByTournage($t['id']);
                        $acteurs = getActeursByTournage($t['id']);
                    ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= htmlspecialchars($t['reference']) ?></td>
                        <td><?= date('d-m-Y', strtotime($t['date_tournage'])) ?></td>
                        <td>
                            <a href="#" class="text-success voir-acteurs"
                                data-acteurs='<?= htmlspecialchars(json_encode($acteurs)) ?>'
                                style="text-decoration:underline;">
                                +<?= $equipeCount ?>
                            </a>
                        </td>

                        <td>
                            <a href="" style="text-decoration:underline;" class="text-info">
                                <?= number_format($depense, 0, ',', ' ') ?> F CFA
                            </a>
                        </td>
                        <td>
                            <a href="add_tourn.php?id_serie=<?= $id ?>&id_tournage=<?= $t['id'] ?>" class="text-warning text-decoration-underline me-2">modifier</a>
                            <a href="<?php echo $url_base; ?>public/appManager/delete.php?table=tournages&id=<?php echo htmlspecialchars($t['id']); ?>&redirect=<?php echo $url_base; ?>public/appManager/series/tournages.php?id=<?php echo htmlspecialchars($id); ?>"
                                class="text-danger text-decoration-underline me-2"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.');">Supprimer</a>
                        </td>
                    </tr>
                    <?php $i++; endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Acteurs -->
    <div class="modal fade" id="modalActeurs" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Acteurs du tournage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="acteursContent">
                    <!-- Liste des acteurs injectée ici -->
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery + DataTables + Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#tournageTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50]
        });
    });
    </script>
    <script>
    $(document).on('click', '.voir-acteurs', function(e) {
        e.preventDefault();
        let acteurs = $(this).data('acteurs'); // JSON string
        if (typeof acteurs === "string") {
            acteurs = JSON.parse(acteurs);
        }

        let html = `
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Date de naissance</th>
                    <th>Contact</th>
                    <th>Adresse</th>
                    <th>Cachet</th>
                </tr>
            </thead>
            <tbody>
    `;

        acteurs.forEach(function(a) {
            html += `
            <tr>
                <td class="text-center">${a.prenom}</td>
                <td class="text-center">${a.nom}</td>
                <td class="text-center">${a.date_naissance}</td>
                <td class="text-center">${a.contact}</td>
                <td class="text-center">${a.adresse}</td>
                <td class="text-center">${Number(a.cachet).toLocaleString('fr-FR')} fcfa</td>
            </tr>
        `;
        });

        html += '</tbody></table>';

        $('#acteursContent').html(html);
        var modal = new bootstrap.Modal(document.getElementById('modalActeurs'));
        modal.show();
    });
    </script>
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Création de l'alerte
    const toast = document.createElement("div");
    toast.textContent = "Tournage suppimer avec succès !";
    toast.style.position = "fixed";
    toast.style.top = "80px";
    toast.style.right = "30px";
    toast.style.padding = "15px 25px";
    toast.style.backgroundColor = "#d21515ff"; // vert succès
    toast.style.color = "white";
    toast.style.borderRadius = "5px";
    toast.style.boxShadow = "0 2px 10px rgba(0,0,0,0.2)";
    toast.style.zIndex = 9999;
    toast.style.fontWeight = "bold";
    toast.style.opacity = 0;
    toast.style.transition = "opacity 0.5s";

    document.body.appendChild(toast);

    // Animation pour fade in
    setTimeout(() => {
        toast.style.opacity = 1;
    }, 100);

    // Disparition après 3 secondes
    setTimeout(() => {
        toast.style.opacity = 0;
        setTimeout(() => toast.remove(), 500);
    }, 5000);
});
</script>
<?php endif; ?>
</body>