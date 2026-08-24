<?php
session_start();

// Redirection si l'utilisateur n'a pas mis à jour son mot de passe
/* if (!isset($_SESSION['updated']) || !$_SESSION['updated']) {
    header("Location: ../../../public/admin/profile.php?forceUpdate=1");
    exit;
} */

include '../../../config/fonction.php';

$totaux = getTotaux($connexion);
$totauxDepenses = getTotauxDepensesGlobal($connexion);
?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<?php include '../../../includes/header.php'; ?>

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

.dashboard-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.dashboard-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.dashboard-header {
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

.dashboard-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.dashboard-header-icon {
    width: 58px;
    height: 58px;
    flex: 0 0 58px;
    border-radius: 16px;
    background: var(--accent);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 23px;
}

.dashboard-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.dashboard-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.dashboard-header p {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 14px;
}

.header-date {
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
   STATS GRID
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
    padding: 22px 20px;
    box-shadow: var(--shadow);
    transition: .3s;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
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

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 40px rgba(0, 0, 0, .08);
}

.stat-card:hover::before {
    opacity: 1;
}

.stat-card .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 14px;
}

.stat-card .stat-icon.primary {
    background: #f4f4f5;
    color: var(--primary);
}

.stat-card .stat-icon.accent {
    background: #fef2f2;
    color: var(--accent);
}

.stat-card .stat-number {
    font-size: 28px;
    font-weight: 900;
    color: var(--text);
    margin: 0;
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
    font-size: 11px;
    color: var(--muted);
    margin: 6px 0 0;
}

/* =========================================================
   EXPENSES SECTION
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
    gap: 16px;
    padding: 24px;
}

.expense-item {
    background: #fafafa;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 16px 18px;
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
    gap: 12px;
}

.expense-item-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #f4f4f5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    color: var(--muted);
    transition: .2s;
}

.expense-item:hover .expense-item-icon {
    background: var(--accent);
    color: white;
}

.expense-item-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--text);
}

.expense-item-value {
    font-size: 15px;
    font-weight: 900;
    color: var(--text);
}

.expense-item-value small {
    font-size: 9px;
    font-weight: 400;
    color: var(--muted);
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .expenses-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 992px) {
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 768px) {
    .dashboard-page {
        padding: 15px 0 35px;
    }
    .dashboard-container {
        padding: 0 12px;
    }
    .dashboard-header {
        padding: 18px;
    }
    .dashboard-header h1 {
        font-size: 21px;
    }
    .dashboard-header-icon {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
    }
    .stats-grid {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .stat-card {
        padding: 16px 14px;
    }
    .stat-card .stat-number {
        font-size: 22px;
    }
    .expenses-grid {
        grid-template-columns: 1fr;
        padding: 16px;
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
    .dashboard-header-left {
        gap: 12px;
    }
    .dashboard-header-icon {
        display: none;
    }
    .stats-grid {
        grid-template-columns: 1fr;
    }
    .header-date {
        width: 100%;
        justify-content: center;
    }
    .expenses-header-left {
        width: 100%;
    }
}
</style>

<section class="dashboard-page">
    <div class="dashboard-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="dashboard-header">
            <div class="dashboard-header-left">
                <div class="dashboard-header-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                    <div class="dashboard-breadcrumb">
                        EVENPROD / ADMINISTRATION / DASHBOARD
                    </div>
                    <h1>Tableau de bord</h1>
                    <p>Vue d'ensemble des activités et performances de la production</p>
                </div>
            </div>
            <div class="header-date">
                <i class="fas fa-calendar-alt"></i>
                <?= date('d/m/Y') ?>
            </div>
        </header>

        <!-- =========================================================
        STATISTIQUES GÉNÉRALES
        ========================================================= -->

        <div class="stats-grid">
            <!-- Utilisateurs -->
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="stat-number">
                    <?= number_format($totaux['users'] ?? 0, 0, ',', ' ') ?>
                </h4>
                <p class="stat-label">Utilisateurs</p>
                <p class="stat-desc">Nombre total d'utilisateurs enregistrés</p>
            </div>

            <!-- Séries -->
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-film"></i>
                </div>
                <h4 class="stat-number">
                    <?= number_format($totaux['series'] ?? 0, 0, ',', ' ') ?>
                </h4>
                <p class="stat-label">Séries</p>
                <p class="stat-desc">Nombre total de séries enregistrées</p>
            </div>

            <!-- Clients -->
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-handshake"></i>
                </div>
                <h4 class="stat-number">
                    <?= number_format($totaux['clients'] ?? 0, 0, ',', ' ') ?>
                </h4>
                <p class="stat-label">Clients</p>
                <p class="stat-desc">Nombre total de clients enregistrés</p>
            </div>

            <!-- Acteurs -->
            <div class="stat-card">
                <div class="stat-icon accent">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h4 class="stat-number">
                    <?= number_format($totaux['acteurs'] ?? 0, 0, ',', ' ') ?>
                </h4>
                <p class="stat-label">Acteurs</p>
                <p class="stat-desc">Nombre total d'acteurs enregistrés</p>
            </div>
        </div>

        <!-- =========================================================
        DÉPENSES PAR CATÉGORIE
        ========================================================= -->

        <div class="expenses-section">
            <div class="expenses-header">
                <div class="expenses-header-left">
                    <div class="expenses-header-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <h2>Dépenses par catégorie</h2>
                        <p>Répartition des dépenses par type de poste</p>
                    </div>
                </div>
                <div class="expenses-total">
                    <i class="fas fa-calculator"></i>
                    <?php
                        $totalDepenses = ($totauxDepenses['cachet'] ?? 0) +
                                         ($totauxDepenses['decor'] ?? 0) +
                                         ($totauxDepenses['transport'] ?? 0) +
                                         ($totauxDepenses['reception'] ?? 0) +
                                         ($totauxDepenses['accessoire'] ?? 0) +
                                         ($totauxDepenses['reglement_acteur'] ?? 0) +
                                         ($totauxDepenses['hmc'] ?? 0) +
                                         ($totauxDepenses['carburant'] ?? 0) +
                                         ($totauxDepenses['pharmacie'] ?? 0) +
                                         ($totauxDepenses['autre'] ?? 0);
                        echo number_format($totalDepenses, 0, ',', ' ') . ' FCFA';
                    ?>
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