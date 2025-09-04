<?php
include '../../config/fonction.php';

$clients = getClients($connexion);
?>

<head>
    <style>
        .client-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
        }
        .client-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        }
        .client-logo {
            width: 100%;
            height: 120px;
            object-fit: contain;
            background: #f8f9fa;
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .card-body h6 {
            font-weight: 600;
            font-size: 15px;
        }
        .card-footer {
            font-size: 12px;
        }
    </style>
</head>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-lg-8">
            <div class="section-title">
                <h2>Liste des partenariats</h2>
                <p>Clients, Sponsors et Partenaires enregistrés dans la plateforme.</p>
            </div>
        </div>
        <div class="col-lg-4 text-end">
            <a href="add_spon.php" class="btn btn-warning">
                <i class="fas fa-plus"></i> Ajouter
            </a>
        </div>
    </div>

    <div class="row g-3">
        <?php if (!empty($clients)) : ?>
            <?php foreach ($clients as $cl) : ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card client-card">
                        <a href="details_client.php?id=<?= htmlspecialchars($cl['id']) ?>">
                            <img src="<?= !empty($cl['logo']) ? '../../uploads/logos/' . htmlspecialchars($cl['logo']) : '../../assets/images/default_logo.png' ?>" 
                                 class="client-logo" 
                                 alt="<?= htmlspecialchars($cl['nom']) ?>">
                        </a>
                        <div class="card-body">
                            <h6 class="mb-1 text-truncate">
                                <a href="details_client.php?id=<?= htmlspecialchars($cl['id']) ?>" class="text-dark text-decoration-none">
                                    <?= htmlspecialchars($cl['nom']) ?>
                                </a>
                            </h6>
                            <span class="text-muted small d-block">
                                <strong>NINEA :</strong> <?= htmlspecialchars($cl['ninea']) ?>
                            </span>
                            <span class="text-muted small d-block mb-1">
                                <strong>Nom :</strong> <?= ucfirst(htmlspecialchars($cl['nom'])) ?>
                            </span>
                            <p class="small text-muted text-truncate">
                                <?= nl2br(htmlspecialchars($cl['contact'])) ?>
                            </p>
                        </div>
                        <div class="card-footer bg-light small text-muted">
                            Ajouté le <?= date("d/m/Y", strtotime($cl['created_at'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="text-center">Aucun partenariat trouvé.</p>
        <?php endif; ?>
    </div>
</div>
