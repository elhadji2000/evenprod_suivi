<?php
session_start();

// Redirection si l'utilisateur n'a pas mis à jour son mot de passe
if (!isset($_SESSION['updated']) || !$_SESSION['updated']) {
    header("Location: ../../../public/admin/profile.php?forceUpdate=1");
    exit;
}

include '../../../includes/header.php';
include '../../../config/fonction.php';

$role = $_SESSION['role'] ?? 'guest';

$id = $_GET['id'] ?? 0;
$serie = getSerieById($id);
$totauxtous = getTotauxGenerauxComplet($connexion, $id);
$totauxDepenses = getTotauxDepensesSerie($connexion, $id);

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<style>
/* =========================================================
   VARIABLES
========================================================= */

:root {
    --primary: #171717;
    --primary-hover: #000;
    --accent: #e50914;
    --accent-hover: #b20710;
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

.serie-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.serie-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.serie-header {
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

.serie-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.serie-header-avatar {
    width: 70px;
    height: 70px;
    flex: 0 0 70px;
    border-radius: 16px;
    overflow: hidden;
    border: 3px solid var(--accent);
    background: #f4f4f5;
}

.serie-header-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.serie-header-avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 30px;
}

.serie-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.serie-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.serie-header p {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 14px;
}

.serie-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-action {
    height: 44px;
    padding: 0 20px;
    border-radius: 12px;
    border: 0;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: .2s;
}

.btn-action.edit {
    background: var(--warning);
    color: white;
}

.btn-action.edit:hover {
    background: #d97706;
    transform: translateY(-1px);
}

.btn-action.delete {
    background: var(--danger);
    color: white;
}

.btn-action.delete:hover {
    background: #b91c1c;
    transform: translateY(-1px);
}

.btn-action.back {
    background: var(--primary);
    color: white;
}

.btn-action.back:hover {
    background: var(--primary-hover);
    transform: translateY(-1px);
}

/* =========================================================
   NAV GRID
========================================================= */

.nav-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
    margin-bottom: 30px;
}

.nav-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px 16px;
    text-align: center;
    box-shadow: var(--shadow);
    transition: .3s;
    text-decoration: none;
    color: var(--text);
    position: relative;
    overflow: hidden;
}

.nav-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--accent);
    opacity: 0;
    transition: .3s;
}

.nav-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(0, 0, 0, .08);
}

.nav-card:hover::before {
    opacity: 1;
}

.nav-card.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.nav-card.disabled:hover {
    transform: none;
}

.nav-card-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 10px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    background: #f4f4f5;
    color: var(--primary);
    transition: .3s;
}

.nav-card:hover .nav-card-icon {
    background: var(--accent);
    color: white;
}

.nav-card-title {
    font-size: 13px;
    font-weight: 800;
    margin: 0;
}

.nav-card-desc {
    font-size: 10px;
    color: var(--muted);
    margin: 4px 0 0;
}

.nav-card .badge-lock {
    display: inline-block;
    margin-top: 6px;
    font-size: 9px;
    color: var(--muted);
}

/* =========================================================
   STATS SECTION
========================================================= */

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    text-align: center;
    box-shadow: var(--shadow);
    transition: .3s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, .08);
}

.stat-card .stat-number {
    font-size: 28px;
    font-weight: 900;
    color: var(--text);
    margin: 0;
}

.stat-card .stat-number.accent {
    color: var(--accent);
}

.stat-card .stat-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--muted);
    margin: 4px 0 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-card .stat-desc {
    font-size: 10px;
    color: var(--muted);
    margin: 6px 0 0;
}

/* =========================================================
   EXPENSES DETAIL
========================================================= */

.expenses-section {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    margin-top: 10px;
}

.expenses-header {
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
}

.expenses-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

.expenses-header-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: var(--accent);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.expenses-header h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 900;
}

.expenses-header p {
    margin: 3px 0 0;
    font-size: 12px;
    color: var(--muted);
}

.expenses-total {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 16px;
    border-radius: 30px;
    background: var(--primary);
    color: white;
    font-size: 14px;
    font-weight: 800;
}

.expenses-total i {
    font-size: 13px;
}

.expenses-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    padding: 20px 24px;
}

.expense-item {
    background: #fafafa;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: .2s;
}

.expense-item:hover {
    border-color: var(--accent);
    background: #fef2f2;
}

.expense-item-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.expense-item-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: #f4f4f5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    color: var(--muted);
    transition: .2s;
}

.expense-item:hover .expense-item-icon {
    background: var(--accent);
    color: white;
}

.expense-item-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--text);
}

.expense-item-value {
    font-size: 14px;
    font-weight: 900;
    color: var(--text);
}

.expense-item-value small {
    font-size: 8px;
    font-weight: 400;
    color: var(--muted);
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1200px) {
    .nav-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .expenses-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 992px) {
    .serie-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .serie-header-actions {
        width: 100%;
        flex-wrap: wrap;
    }
    .btn-action {
        flex: 1;
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .serie-page {
        padding: 15px 0 35px;
    }
    .serie-container {
        padding: 0 12px;
    }
    .serie-header {
        padding: 18px;
    }
    .serie-header h1 {
        font-size: 21px;
    }
    .serie-header-avatar {
        width: 56px;
        height: 56px;
        flex-basis: 56px;
    }
    .nav-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .nav-card {
        padding: 14px 10px;
    }
    .nav-card-icon {
        width: 38px;
        height: 38px;
        font-size: 16px;
    }
    .stats-grid {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .stat-card .stat-number {
        font-size: 22px;
    }
    .expenses-grid {
        grid-template-columns: 1fr;
        padding: 12px 16px;
    }
    .expenses-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    .expenses-total {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .serie-header-left {
        gap: 12px;
    }
    .serie-header-avatar {
        display: none;
    }
    .serie-header-actions {
        flex-direction: column;
    }
    .btn-action {
        width: 100%;
    }
    .nav-grid {
        grid-template-columns: 1fr 1fr;
    }
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<section class="serie-page">
    <div class="serie-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="serie-header">
            <div class="serie-header-left">
                <div class="serie-header-avatar">
                    <?php if (!empty($serie['logo']) && file_exists('../../../uploads/series/' . $serie['logo'])): ?>
                    <img src="../../../uploads/series/<?= htmlspecialchars($serie['logo']) ?>" alt="<?= htmlspecialchars($serie['titre']) ?>">
                    <?php else: ?>
                    <div class="serie-header-avatar-placeholder">
                        <i class="fas fa-film"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="serie-breadcrumb">
                        EVENPROD / PRODUCTIONS / SÉRIES
                    </div>
                    <h1><?= htmlspecialchars($serie['titre'] ?? 'Série introuvable') ?></h1>
                    <p>
                        <i class="fas fa-tag" style="color:var(--accent);"></i>
                        <?= htmlspecialchars($serie['type'] ?? 'Non défini') ?>
                        &nbsp;&bull;&nbsp;
                        <i class="fas fa-coins"></i>
                        Budget : <?= number_format($serie['budget'] ?? 0, 0, ',', ' ') ?> FCFA
                    </p>
                </div>
            </div>
            <div class="serie-header-actions">
                <a href="<?= $url_base ?>pages/series_list" class="btn-action back">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
                <a href="<?= $url_base ?>pages/add_serie.php?id=<?= htmlspecialchars($serie['id']) ?>" class="btn-action edit">
                    <i class="fas fa-pen"></i>
                    Modifier
                </a>
                <?php if ($role === 'admin'): ?>
                <a href="<?= $url_base ?>public/appManager/delete.php?table=series&id=<?= htmlspecialchars($serie['id']) ?>&redirect=<?= $url_base ?>pages/series_list"
                   class="btn-action delete"
                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette série ? Cette action est irréversible.')">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </a>
                <?php endif; ?>
            </div>
        </header>

        <!-- =========================================================
        NAVIGATION PAR MODULES
        ========================================================= -->

        <div class="nav-grid">
            <!-- Acteurs -->
            <a href="<?= in_array($role, $permissions['acteur']) ? $url_base . 'public/appManager/acteur/acteurs.php?id=' . htmlspecialchars($serie['id']) : '#' ?>"
               class="nav-card <?= !in_array($role, $permissions['acteur']) ? 'disabled' : '' ?>">
                <div class="nav-card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="nav-card-title">Acteurs</h4>
                <p class="nav-card-desc">Gestion des acteurs de la série</p>
                <?php if (!in_array($role, $permissions['acteur'])): ?>
                <span class="badge-lock"><i class="fas fa-lock"></i> Accès restreint</span>
                <?php endif; ?>
            </a>

            <!-- Factures & Devis -->
            <a href="<?= in_array($role, $permissions['facture']) ? $url_base . 'public/appManager/facture/all_devis_fac.php?id=' . htmlspecialchars($serie['id']) : '#' ?>"
               class="nav-card <?= !in_array($role, $permissions['facture']) ? 'disabled' : '' ?>">
                <div class="nav-card-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <h4 class="nav-card-title">Factures & Devis</h4>
                <p class="nav-card-desc">Suivi des documents financiers</p>
                <?php if (!in_array($role, $permissions['facture'])): ?>
                <span class="badge-lock"><i class="fas fa-lock"></i> Accès restreint</span>
                <?php endif; ?>
            </a>

            <!-- Tournages -->
            <a href="<?= in_array($role, $permissions['tournages']) ? $url_base . 'public/appManager/series/tournages.php?id=' . htmlspecialchars($serie['id']) : '#' ?>"
               class="nav-card <?= !in_array($role, $permissions['tournages']) ? 'disabled' : '' ?>">
                <div class="nav-card-icon">
                    <i class="fas fa-video"></i>
                </div>
                <h4 class="nav-card-title">Tournages</h4>
                <p class="nav-card-desc">Organisation et suivi des tournages</p>
                <?php if (!in_array($role, $permissions['tournages'])): ?>
                <span class="badge-lock"><i class="fas fa-lock"></i> Accès restreint</span>
                <?php endif; ?>
            </a>

            <!-- Dépenses -->
            <a href="<?= in_array($role, $permissions['depenses']) ? '../depenses/liste_all.php?id=' . htmlspecialchars($serie['id']) : '#' ?>"
               class="nav-card <?= !in_array($role, $permissions['depenses']) ? 'disabled' : '' ?>">
                <div class="nav-card-icon">
                    <i class="fas fa-arrow-down" style="color:var(--danger);"></i>
                </div>
                <h4 class="nav-card-title">Dépenses</h4>
                <p class="nav-card-desc">Suivi des coûts de production</p>
                <?php if (!in_array($role, $permissions['depenses'])): ?>
                <span class="badge-lock"><i class="fas fa-lock"></i> Accès restreint</span>
                <?php endif; ?>
            </a>

            <!-- Recettes -->
            <a href="<?= in_array($role, $permissions['recettes']) ? $url_base . 'public/appManager/recettes/details_spg.php?id=' . htmlspecialchars($serie['id']) : '#' ?>"
               class="nav-card <?= !in_array($role, $permissions['recettes']) ? 'disabled' : '' ?>">
                <div class="nav-card-icon">
                    <i class="fas fa-arrow-up" style="color:var(--success);"></i>
                </div>
                <h4 class="nav-card-title">Recettes</h4>
                <p class="nav-card-desc">Entrées financières générées</p>
                <?php if (!in_array($role, $permissions['recettes'])): ?>
                <span class="badge-lock"><i class="fas fa-lock"></i> Accès restreint</span>
                <?php endif; ?>
            </a>
        </div>

        <!-- =========================================================
        STATISTIQUES GÉNÉRALES
        ========================================================= -->

        <div class="stats-grid">
            <div class="stat-card">
                <h4 class="stat-number"><?= number_format($serie['budget'] ?? 0, 0, ',', ' ') ?></h4>
                <p class="stat-label">Budget</p>
                <p class="stat-desc">Budget prévisionnel de la série</p>
            </div>
            <div class="stat-card">
                <h4 class="stat-number accent"><?= number_format($totauxtous['total_depenses'] ?? 0, 0, ',', ' ') ?></h4>
                <p class="stat-label">Dépenses</p>
                <p class="stat-desc">Montant total des dépenses effectuées</p>
            </div>
            <div class="stat-card">
                <h4 class="stat-number" style="color:var(--success);"><?= number_format($totauxtous['total_factures'] ?? 0, 0, ',', ' ') ?></h4>
                <p class="stat-label">Recettes</p>
                <p class="stat-desc">Montant total des factures validées</p>
            </div>
            <div class="stat-card">
                <h4 class="stat-number">
                    <?php 
                        $solde = ($serie['budget'] ?? 0) - ($totauxtous['total_depenses'] ?? 0) + ($totauxtous['total_factures'] ?? 0);
                        echo number_format($solde, 0, ',', ' ');
                    ?>
                </h4>
                <p class="stat-label">Solde</p>
                <p class="stat-desc">Budget - Dépenses + Recettes</p>
            </div>
        </div>

        <!-- =========================================================
        DÉTAIL DES DÉPENSES PAR CATÉGORIE
        ========================================================= -->

        <div class="expenses-section">
            <div class="expenses-header">
                <div class="expenses-header-left">
                    <div class="expenses-header-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <h2>Détail des dépenses par catégorie</h2>
                        <p>Répartition détaillée des coûts de production</p>
                    </div>
                </div>
                <div class="expenses-total">
                    <i class="fas fa-calculator"></i>
                    <?= number_format($totauxtous['total_depenses'] ?? 0, 0, ',', ' ') ?> FCFA
                </div>
            </div>

            <div class="expenses-grid">
                <!-- Cachets -->
                <!-- <div class="expense-item">
                    <div class="expense-item-left">
                        <div class="expense-item-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <span class="expense-item-label">Cachets</span>
                    </div>
                    <span class="expense-item-value">
                        <?= number_format($totauxDepenses['cachet'] ?? 0, 0, ',', ' ') ?>
                        <small>FCFA</small>
                    </span>
                </div> -->

                <!-- Décors -->
                <div class="expense-item">
                    <div class="expense-item-left">
                        <div class="expense-item-icon">
                            <i class="fas fa-paint-roller"></i>
                        </div>
                        <span class="expense-item-label">Décors</span>
                    </div>
                    <span class="expense-item-value">
                        <?= number_format($totauxDepenses['decor'] ?? 0, 0, ',', ' ') ?>
                        <small>FCFA</small>
                    </span>
                </div>

                <!-- Transport -->
                <div class="expense-item">
                    <div class="expense-item-left">
                        <div class="expense-item-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <span class="expense-item-label">Transport</span>
                    </div>
                    <span class="expense-item-value">
                        <?= number_format($totauxDepenses['transport'] ?? 0, 0, ',', ' ') ?>
                        <small>FCFA</small>
                    </span>
                </div>

                <!-- Réceptions -->
                <div class="expense-item">
                    <div class="expense-item-left">
                        <div class="expense-item-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <span class="expense-item-label">Réceptions</span>
                    </div>
                    <span class="expense-item-value">
                        <?= number_format($totauxDepenses['reception'] ?? 0, 0, ',', ' ') ?>
                        <small>FCFA</small>
                    </span>
                </div>

                <!-- Accessoires -->
                <div class="expense-item">
                    <div class="expense-item-left">
                        <div class="expense-item-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <span class="expense-item-label">Accessoires</span>
                    </div>
                    <span class="expense-item-value">
                        <?= number_format($totauxDepenses['accessoire'] ?? 0, 0, ',', ' ') ?>
                        <small>FCFA</small>
                    </span>
                </div>

                <!-- Règlement Acteurs -->
                <div class="expense-item">
                    <div class="expense-item-left">
                        <div class="expense-item-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <span class="expense-item-label">Règlement acteurs</span>
                    </div>
                    <span class="expense-item-value">
                        <?= number_format($totauxDepenses['reglement_acteur'] ?? 0, 0, ',', ' ') ?>
                        <small>FCFA</small>
                    </span>
                </div>

                <!-- HMC -->
                <div class="expense-item">
                    <div class="expense-item-left">
                        <div class="expense-item-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <span class="expense-item-label">HMC</span>
                    </div>
                    <span class="expense-item-value">
                        <?= number_format($totauxDepenses['hmc'] ?? 0, 0, ',', ' ') ?>
                        <small>FCFA</small>
                    </span>
                </div>

                <!-- Carburant -->
                <div class="expense-item">
                    <div class="expense-item-left">
                        <div class="expense-item-icon">
                            <i class="fas fa-gas-pump"></i>
                        </div>
                        <span class="expense-item-label">Carburant</span>
                    </div>
                    <span class="expense-item-value">
                        <?= number_format($totauxDepenses['carburant'] ?? 0, 0, ',', ' ') ?>
                        <small>FCFA</small>
                    </span>
                </div>

                <!-- Pharmacie -->
                <div class="expense-item">
                    <div class="expense-item-left">
                        <div class="expense-item-icon">
                            <i class="fas fa-medkit"></i>
                        </div>
                        <span class="expense-item-label">Pharmacie</span>
                    </div>
                    <span class="expense-item-value">
                        <?= number_format($totauxDepenses['pharmacie'] ?? 0, 0, ',', ' ') ?>
                        <small>FCFA</small>
                    </span>
                </div>

                <!-- Autres -->
                <div class="expense-item" style="grid-column: span 1;">
                    <div class="expense-item-left">
                        <div class="expense-item-icon">
                            <i class="fas fa-ellipsis-h"></i>
                        </div>
                        <span class="expense-item-label">Autres</span>
                    </div>
                    <span class="expense-item-value">
                        <?= number_format($totauxDepenses['autre'] ?? 0, 0, ',', ' ') ?>
                        <small>FCFA</small>
                    </span>
                </div>
            </div>
        </div>

    </div>
</section>

<?php include '../../../includes/footer.php'; ?>