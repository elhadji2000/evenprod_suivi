<?php
session_start();

// Redirection si l'utilisateur n'a pas mis à jour son mot de passe
/* if (!isset($_SESSION['updated']) || !$_SESSION['updated']) {
    header("Location: ../public/admin/profile.php?forceUpdate=1");
    exit;
} */
?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<?php
include '../config/fonction.php';

$series = getAllSeries();
$totauxtous = getTotauxGeneraux($connexion);
?>

<?php include '../includes/header.php'; ?>

<style>
/* =========================================================
   VARIABLES
========================================================= */

:root {
    --primary: #171717;
    --primary-hover: #000;
    --accent: #e50914;
    --background: #f5f6f8;
    --white: #ffffff;
    --text: #171717;
    --muted: #737373;
    --border: #e5e7eb;
    --success: #16a34a;
    --danger: #dc2626;
    --warning: #f59e0b;
    --info: #3b82f6;
    --radius: 18px;
    --shadow: 0 10px 30px rgba(0, 0, 0, .06);
}

/* =========================================================
   PAGE
========================================================= */

.series-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.series-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.series-header {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 25px 30px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    box-shadow: var(--shadow);
}

.series-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.series-header-icon {
    width: 58px;
    height: 58px;
    flex: 0 0 58px;
    border-radius: 16px;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 23px;
}

.series-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.series-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.series-header p {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 14px;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-add {
    height: 48px;
    padding: 0 24px;
    border-radius: 12px;
    border: 0;
    background: var(--primary);
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: .2s;
}

.btn-add:hover {
    background: var(--primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
    color: white;
}

.header-count {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 30px;
    background: #f4f4f5;
    font-size: 12px;
    font-weight: 800;
}

/* =========================================================
   ALERT / TOAST
========================================================= */

.modern-alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    margin-bottom: 20px;
    border-radius: 13px;
    font-size: 12px;
    font-weight: 600;
}

.modern-alert.success {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.modern-alert.error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert-icon {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, .7);
}

.alert-close {
    margin-left: auto;
    border: 0;
    background: transparent;
    color: inherit;
    cursor: pointer;
}

/* =========================================================
   GRID SERIES
========================================================= */

.series-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
}

.series-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: .3s;
}

.series-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(0, 0, 0, .1);
}

.series-card-inner {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 0;
}

.series-card-image {
    height: 100%;
    min-height: 200px;
    overflow: hidden;
    background: #f4f4f5;
}

.series-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: .3s;
}

.series-card:hover .series-card-image img {
    transform: scale(1.03);
}

.series-card-image-placeholder {
    width: 100%;
    height: 100%;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    background: #f4f4f5;
}

.series-card-image-placeholder i {
    font-size: 48px;
    margin-bottom: 10px;
}

.series-card-image-placeholder span {
    font-size: 11px;
    font-weight: 700;
}

.series-card-body {
    padding: 20px 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.series-card-title {
    margin: 0 0 4px;
    font-size: 18px;
    font-weight: 900;
}

.series-card-title a {
    color: var(--text);
    text-decoration: none;
    transition: .2s;
}

.series-card-title a:hover {
    color: var(--primary-hover);
}

.series-card-type {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    background: #f4f4f5;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 10px;
}

.series-card-type i {
    font-size: 9px;
    color: var(--muted);
}

.series-card-desc {
    font-size: 13px;
    color: var(--muted);
    line-height: 1.6;
    margin: 0 0 14px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.series-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 14px;
    border-top: 1px solid var(--border);
}

.series-card-budget {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    color: var(--text);
}

.series-card-budget i {
    color: var(--muted);
    font-size: 11px;
}

.series-card-budget small {
    font-weight: 400;
    color: var(--muted);
    font-size: 10px;
}

.series-card-action {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 10px;
    background: var(--primary);
    color: white;
    font-size: 10px;
    font-weight: 800;
    text-decoration: none;
    transition: .2s;
}

.series-card-action:hover {
    background: var(--primary-hover);
    transform: translateY(-1px);
    color: white;
}

/* =========================================================
   STATS SECTION
========================================================= */

.stats-section {
    margin-top: 40px;
    padding-top: 40px;
    border-top: 2px solid var(--border);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.stat-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 24px 20px;
    text-align: center;
    box-shadow: var(--shadow);
    transition: .3s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, .08);
}

.stat-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 12px;
    border-radius: 14px;
    background: #f4f4f5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: var(--primary);
}

.stat-number {
    font-size: 28px;
    font-weight: 900;
    color: var(--text);
    margin: 0;
}

.stat-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--muted);
    margin: 4px 0 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-desc {
    font-size: 11px;
    color: var(--muted);
    margin: 6px 0 0;
}

/* =========================================================
   EMPTY STATE
========================================================= */

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--muted);
    background: var(--white);
    border-radius: var(--radius);
    border: 1px solid var(--border);
}

.empty-state i {
    font-size: 48px;
    color: #d4d4d8;
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 18px;
    font-weight: 900;
    color: var(--text);
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 13px;
    margin-bottom: 20px;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1200px) {
    .series-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 992px) {
    .series-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .header-actions {
        width: 100%;
        flex-wrap: wrap;
    }
    .btn-add {
        flex: 1;
        justify-content: center;
    }
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .series-page {
        padding: 15px 0 35px;
    }
    .series-container {
        padding: 0 12px;
    }
    .series-header {
        padding: 18px;
    }
    .series-header h1 {
        font-size: 21px;
    }
    .series-header-icon {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
    }
    .series-card-inner {
        grid-template-columns: 1fr;
    }
    .series-card-image {
        min-height: 180px;
    }
    .series-card-image img {
        min-height: 180px;
    }
    .series-card-image-placeholder {
        min-height: 180px;
    }
    .series-card-body {
        padding: 16px 18px;
    }
    .stats-grid {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .stat-card {
        padding: 18px 14px;
    }
    .stat-number {
        font-size: 22px;
    }
}

@media (max-width: 480px) {
    .series-header-left {
        gap: 12px;
    }
    .series-header-icon {
        display: none;
    }
    .header-actions {
        flex-direction: column;
        width: 100%;
    }
    .btn-add {
        width: 100%;
    }
    .header-count {
        width: 100%;
        justify-content: center;
    }
    .stats-grid {
        grid-template-columns: 1fr;
    }
    .series-card-footer {
        flex-direction: column;
        gap: 12px;
        align-items: stretch;
    }
    .series-card-action {
        justify-content: center;
    }
}
</style>

<section class="series-page">
    <div class="series-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="series-header">
            <div class="series-header-left">
                <div class="series-header-icon">
                    <i class="fas fa-film"></i>
                </div>
                <div>
                    <div class="series-breadcrumb">
                        EVENPROD / PRODUCTIONS / SÉRIES
                    </div>
                    <h1>Liste des séries</h1>
                    <p>Retrouvez ici l'ensemble des séries disponibles avec leur description</p>
                </div>
            </div>
            <div class="header-actions">
                <span class="header-count">
                    <i class="fas fa-film"></i>
                    <?= count($series) ?> série<?= count($series) > 1 ? 's' : '' ?>
                </span>
                <a href="add_serie" class="btn-add">
                    <i class="fas fa-plus"></i>
                    Nouvelle série
                </a>
            </div>
        </header>

        <!-- =========================================================
        ALERTES
        ========================================================= -->

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="modern-alert success">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>Série supprimée avec succès !</div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
        <div class="modern-alert success">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>Série supprimée avec succès !</div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- =========================================================
        GRILLE DES SÉRIES
        ========================================================= -->

        <?php if (!empty($series)): ?>
        <div class="series-grid">
            <?php foreach ($series as $serie): ?>
            <div class="series-card">
                <div class="series-card-inner">
                    <div class="series-card-image">
                        <?php if (!empty($serie['logo']) && file_exists('../uploads/series/' . $serie['logo'])): ?>
                        <img src="../uploads/series/<?= htmlspecialchars($serie['logo']) ?>" alt="<?= htmlspecialchars($serie['titre']) ?>">
                        <?php else: ?>
                        <div class="series-card-image-placeholder">
                            <i class="fas fa-film"></i>
                            <span>Image non disponible</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="series-card-body">
                        <div>
                            <h3 class="series-card-title">
                                <a href="../public/appManager/series/about.php?id=<?= $serie['id'] ?>">
                                    <?= htmlspecialchars($serie['titre']) ?>
                                </a>
                            </h3>
                            <span class="series-card-type">
                                <i class="fas fa-tag"></i>
                                <?= htmlspecialchars($serie['type']) ?>
                            </span>
                            <p class="series-card-desc">
                                <?= htmlspecialchars($serie['description'] ?? 'Aucune description disponible.') ?>
                            </p>
                        </div>
                        <div class="series-card-footer">
                            <div class="series-card-budget">
                                <i class="fas fa-coins"></i>
                                <?= number_format((float)($serie['budget'] ?? 0), 0, ',', ' ') ?>
                                <small>FCFA</small>
                            </div>
                            <a href="../public/appManager/series/about.php?id=<?= $serie['id'] ?>" class="series-card-action">
                                <i class="fas fa-arrow-right"></i>
                                Voir détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-film"></i>
            <h3>Aucune série</h3>
            <p>Aucune série n'est encore enregistrée dans la maison de production.</p>
            <a href="../public/appManager/series/add_series" class="btn-add" style="display:inline-flex;">
                <i class="fas fa-plus"></i>
                Ajouter la première série
            </a>
        </div>
        <?php endif; ?>

        <!-- =========================================================
        STATISTIQUES
        ========================================================= -->

        <section class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-film"></i>
                    </div>
                    <h4 class="stat-number">
                        <?= number_format($totauxtous['total_series'] ?? 0, 0, ',', ' ') ?>
                    </h4>
                    <p class="stat-label">Séries</p>
                    <p class="stat-desc">Nombre total de séries enregistrées</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-arrow-down" style="color:var(--danger);"></i>
                    </div>
                    <h4 class="stat-number">
                        <?= number_format($totauxtous['total_depenses'] ?? 0, 0, ',', ' ') ?>
                        <small style="font-size:12px; font-weight:400; color:var(--muted);">FCFA</small>
                    </h4>
                    <p class="stat-label">Dépenses</p>
                    <p class="stat-desc">Montant total des dépenses effectuées</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-arrow-up" style="color:var(--success);"></i>
                    </div>
                    <h4 class="stat-number">
                        <?= number_format($totauxtous['total_factures'] ?? 0, 0, ',', ' ') ?>
                        <small style="font-size:12px; font-weight:400; color:var(--muted);">FCFA</small>
                    </h4>
                    <p class="stat-label">Recettes</p>
                    <p class="stat-desc">Montant total des factures validées</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="stat-number">
                        <?= number_format($totauxtous['total_acteurs'] ?? 0, 0, ',', ' ') ?>
                    </h4>
                    <p class="stat-label">Acteurs</p>
                    <p class="stat-desc">Nombre total d'acteurs enregistrés</p>
                </div>
            </div>
        </section>

    </div>
</section>

<?php include '../includes/footer.php'; ?>