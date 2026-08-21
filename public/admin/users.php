<?php
include '../../config/fonction.php';

/*
 * |--------------------------------------------------------------------------
 * | RÉCUPÉRATION DES UTILISATEURS
 * |--------------------------------------------------------------------------
 */

$users = getUsers($connexion);

/*
 * |--------------------------------------------------------------------------
 * | ACTIONS (ACTIVER / DÉSACTIVER)
 * |--------------------------------------------------------------------------
 */

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $userId = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === "enable") {
        mysqli_query($connexion, "UPDATE users SET statut = 1 WHERE id = $userId");
        header("Location: users");
        exit;
    } elseif ($action === "disable") {
        mysqli_query($connexion, "UPDATE users SET statut = 0 WHERE id = $userId");
        header("Location: users");
        exit;
    }
}

?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<?php include '../../includes/header.php'; ?>

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

.users-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.users-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.users-header {
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

.users-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.users-header-icon {
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

.users-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.users-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.users-header p {
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
   TABLE
========================================================= */

.table-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.table-responsive {
    overflow-x: auto;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.users-table thead {
    background: #fafafa;
    border-bottom: 2px solid var(--border);
}

.users-table thead th {
    padding: 16px 20px;
    text-align: left;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--muted);
}

.users-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}

.users-table tbody tr:hover {
    background: #fafafa;
}

.users-table tbody tr:last-child {
    border-bottom: 0;
}

.users-table tbody td {
    padding: 14px 20px;
    vertical-align: middle;
}

/* =========================================================
   USER CELL
========================================================= */

.user-cell {
    display: flex;
    align-items: center;
    gap: 14px;
}

.user-avatar {
    width: 44px;
    height: 44px;
    flex: 0 0 44px;
    border-radius: 50%;
    overflow: hidden;
    background: #f4f4f5;
    border: 2px solid var(--border);
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 18px;
}

.user-name {
    font-weight: 700;
    font-size: 13px;
}

.user-email {
    display: block;
    font-size: 11px;
    color: var(--muted);
    font-weight: 400;
    margin-top: 1px;
}

/* =========================================================
   BADGES
========================================================= */

.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.badge-role {
    font-size: 9px;
    padding: 4px 10px;
}

.badge-role.admin {
    background: #fef3c7;
    color: #92400e;
}

.badge-role.comptable {
    background: #dbeafe;
    color: #1e40af;
}

.badge-role.tournage {
    background: #d1fae5;
    color: #065f46;
}

.badge-role.caisse {
    background: #fce4ec;
    color: #9a1f3c;
}

.badge-status {
    font-size: 9px;
    padding: 4px 10px;
}

.badge-status.active {
    background: #d1fae5;
    color: #065f46;
}

.badge-status.active::before {
    content: '';
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--success);
    margin-right: 4px;
}

.badge-status.inactive {
    background: #fef2f2;
    color: #991b1b;
}

.badge-status.inactive::before {
    content: '';
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--danger);
    margin-right: 4px;
}

/* =========================================================
   ACTIONS BUTTONS
========================================================= */

.action-group {
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-action {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    text-decoration: none;
    transition: .2s;
    cursor: pointer;
    background: transparent;
}

.btn-action:hover {
    transform: translateY(-1px);
}

.btn-action.edit {
    color: var(--info);
    background: #eff6ff;
}

.btn-action.edit:hover {
    background: #dbeafe;
}

.btn-action.enable {
    color: var(--success);
    background: #f0fdf4;
}

.btn-action.enable:hover {
    background: #bbf7d0;
}

.btn-action.disable {
    color: var(--danger);
    background: #fef2f2;
}

.btn-action.disable:hover {
    background: #fecaca;
}

.btn-action .tooltip-text {
    display: none;
}

/* =========================================================
   EMPTY STATE
========================================================= */

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--muted);
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

@media (max-width: 992px) {
    .users-header {
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
}

@media (max-width: 768px) {
    .users-page {
        padding: 15px 0 35px;
    }

    .users-container {
        padding: 0 12px;
    }

    .users-header {
        padding: 18px;
    }

    .users-header h1 {
        font-size: 21px;
    }

    .users-header-icon {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
    }

    .users-table thead {
        display: none;
    }

    .users-table tbody tr {
        display: block;
        padding: 16px;
        border-bottom: 2px solid var(--border);
    }

    .users-table tbody tr:last-child {
        border-bottom: 0;
    }

    .users-table tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 0;
    }

    .users-table tbody td:last-child {
        border-bottom: 0;
    }

    .users-table tbody td::before {
        content: attr(data-label);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--muted);
        letter-spacing: 0.3px;
    }

    .users-table tbody td:first-child::before {
        display: none;
    }

    .users-table tbody td:first-child {
        padding-bottom: 12px;
    }

    .user-cell {
        width: 100%;
    }

    .action-group {
        gap: 4px;
    }
}

@media (max-width: 480px) {
    .users-header-left {
        gap: 12px;
    }

    .users-header-icon {
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
}
</style>

<section class="users-page">
    <div class="users-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="users-header">
            <div class="users-header-left">
                <div class="users-header-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <div class="users-breadcrumb">
                        EVENPROD / ADMINISTRATION / UTILISATEURS
                    </div>
                    <h1>Gestion des utilisateurs</h1>
                    <p>Liste de tous les utilisateurs enregistrés sur la plateforme</p>
                </div>
            </div>
            <div class="header-actions">
                <span class="header-count">
                    <i class="fas fa-users"></i>
                    <?= count($users) ?> utilisateur<?= count($users) > 1 ? 's' : '' ?>
                </span>
                <a href="add_user" class="btn-add">
                    <i class="fas fa-user-plus"></i>
                    Nouvel utilisateur
                </a>
            </div>
        </header>

        <!-- =========================================================
        TABLEAU
        ========================================================= -->

        <div class="table-card">
            <div class="table-responsive">
                <?php if (!empty($users)): ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>E-mail</th>
                            <th>Contact</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th style="text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td data-label="Utilisateur">
                                <div class="user-cell">
                                    <div class="user-avatar">
                                        <?php if (!empty($user['profile']) && file_exists('../../uploads/profile/' . $user['profile'])): ?>
                                        <img src="../../uploads/profile/<?= htmlspecialchars($user['profile']) ?>"
                                            alt="<?= htmlspecialchars($user['prenom']) ?>">
                                        <?php else: ?>
                                        <div class="user-avatar-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="user-name">
                                            <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Email">
                                <span class="user-email">
                                    <i class="fas fa-envelope"></i>
                                    <?= htmlspecialchars($user['email']) ?>
                                </span>
                            </td>
                            <td data-label="Contact">
                                <i class="fas fa-phone"
                                    style="color:var(--muted); font-size:11px; margin-right:6px;"></i>
                                <?= htmlspecialchars($user['telephone'] ?? 'Non renseigné') ?>
                            </td>
                            <td data-label="Rôle">
                                <span class="badge badge-role <?= htmlspecialchars($user['role'] ?? '') ?>">
                                    <?php
                                    $roleLabels = [
                                        'admin' => 'Admin',
                                        'comptable' => 'Comptable',
                                        'caisse' => 'Caisse',
                                        'tournage' => 'Tournage'
                                    ];
                                    echo htmlspecialchars($roleLabels[$user['role']] ?? $user['role'] ?? 'Non défini');
                                    ?>
                                </span>
                            </td>
                            <td data-label="Statut">
                                <span
                                    class="badge badge-status <?= ($user['statut'] ?? 0) == 1 ? 'active' : 'inactive' ?>">
                                    <?= ($user['statut'] ?? 0) == 1 ? 'Actif' : 'Inactif' ?>
                                </span>
                            </td>
                            <td data-label="Actions">
                                <div class="action-group" style="justify-content:center;">
                                    <!-- Activer / Désactiver -->
                                    <?php if (($user['statut'] ?? 0) == 1): ?>
                                    <a href="users?id=<?= $user['id'] ?>&action=disable" class="btn-action disable"
                                        onclick="return confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur ?')"
                                        title="Désactiver">
                                        <i class="fas fa-user-slash"></i>
                                    </a>
                                    <?php else: ?>
                                    <a href="users?id=<?= $user['id'] ?>&action=enable" class="btn-action enable"
                                        onclick="return confirm('Êtes-vous sûr de vouloir activer cet utilisateur ?')"
                                        title="Activer">
                                        <i class="fas fa-user-check"></i>
                                    </a>
                                    <?php endif; ?>

                                    <!-- Modifier -->
                                    <a href="add_user?id=<?= htmlspecialchars($user['id']) ?>" class="btn-action edit"
                                        title="Modifier">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>Aucun utilisateur</h3>
                    <p>Aucun utilisateur n'est encore enregistré dans le système.</p>
                    <a href="add_user" class="btn-add" style="display:inline-flex;">
                        <i class="fas fa-user-plus"></i>
                        Ajouter le premier utilisateur
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>