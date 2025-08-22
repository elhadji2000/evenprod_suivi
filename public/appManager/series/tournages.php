<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
    <link rel="stylesheet" href="tournages.css">
</head>

<?php include '../../../includes/header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Liste des Tournages de Séries</h4>
                    <a href="add_tourn.php" class="btn btn-sm btn-primary">
                        <i class="bi bi-person-plus"></i> Ajouter
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="proTeamScroll" tabindex="2"
                        style="height: 400px; overflow: hidden; outline: none;">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Réf. Tournage</th>
                                    <th>Date</th>
                                    <th>Équipe</th>
                                    <th>Dépenses</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Exemple 1 -->
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <h6 class="mb-0 font-13">T2023-001</h6>
                                    </td>
                                    <td>20-02-2023</td>
                                    <td class="text-truncate">
                                        <ul class="list-unstyled order-list m-b-0">
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="https://bootdey.com/img/Content/avatar/avatar1.png"
                                                    alt="Réalisateur" data-toggle="tooltip" title="Réalisateur"></li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="https://bootdey.com/img/Content/avatar/avatar2.png"
                                                    alt="Caméraman" data-toggle="tooltip" title="Caméraman"></li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="https://bootdey.com/img/Content/avatar/avatar3.png"
                                                    alt="Acteur principal" data-toggle="tooltip"
                                                    title="Acteur principal"></li>
                                            <li class="avatar avatar-sm"><span class="badge badge-primary">+3</span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="depense.php?id=1" class="text-info"> Voir</a></td>
                                    <td>
                                        <a href="edit_tournage.php?id=2" class="text-primary mr-2"
                                            title="Modifier">modif </a>
                                        <a href="delete_tournage.php?id=2" class="text-danger"
                                            title="Supprimer">suppr</a>
                                    </td>
                                </tr>
                                <!-- Exemple 2 -->
                                <tr>
                                    <td>2</td>
                                    <td>
                                        <h6 class="mb-0 font-13">T2023-002</h6>
                                    </td>
                                    <td>05-03-2023</td>
                                    <td class="text-truncate">
                                        <ul class="list-unstyled order-list m-b-0">
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="https://bootdey.com/img/Content/avatar/avatar4.png"
                                                    alt="Réalisateur" data-toggle="tooltip" title="Réalisateur"></li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="https://bootdey.com/img/Content/avatar/avatar5.png"
                                                    alt="Assistant" data-toggle="tooltip" title="Assistant"></li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="https://bootdey.com/img/Content/avatar/avatar6.png"
                                                    alt="Acteur secondaire" data-toggle="tooltip"
                                                    title="Acteur secondaire"></li>
                                            <li class="avatar avatar-sm"><span class="badge badge-primary">+7</span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="depense.php?id=2" class=""> Voir</a></td>
                                    <td>
                                        <a href="edit_tournage.php?id=2" class="text-primary mr-2"
                                            title="Modifier">modif </a>
                                        <a href="delete_tournage.php?id=2" class="text-danger"
                                            title="Supprimer">suppr</a>
                                    </td>
                                </tr>
                                <!-- Ajoute d’autres lignes dynamiquement avec PHP -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>