<?php
include '../../config/fonction.php';

/*
 * |--------------------------------------------------------------------------
 * | DERNIÈRE SÉRIE
 * |--------------------------------------------------------------------------
 */

$lastSerie = getLastSerie();

/*
 * |--------------------------------------------------------------------------
 * | RÉCUPÉRATION DE L'UTILISATEUR (MODIFICATION)
 * |--------------------------------------------------------------------------
 */

$user = null;
$id = $_GET['id'] ?? 0;

if ($id > 0) {
    $user = getUserById($connexion, $id);
}

/*
 * |--------------------------------------------------------------------------
 * | TRAITEMENT DU FORMULAIRE
 * |--------------------------------------------------------------------------
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_user'] ?? null;
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? '';
    $telephone = trim($_POST['telephone'] ?? '');
    $photoFile = $_FILES['photo'] ?? null;

    if ($id) {
        $result = modifierUser($id, $nom, $prenom, $email, $telephone, $role, $photoFile);
    } else {
        $result = ajouterUser($nom, $prenom, $email, $telephone, $role, $photoFile);
    }

    if ($result === "success") {
        header("Location: add_user?success=1");
        exit;
    } elseif ($result === "exists") {
        header("Location: add_user?error=exists");
        exit;
    } else {
        header("Location: add_user?error=1");
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
    --radius: 18px;
    --shadow: 0 10px 30px rgba(0, 0, 0, .06);
}

/* =========================================================
   PAGE
========================================================= */

.user-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.user-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.user-header {
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

.user-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.user-header-icon {
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

.user-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.user-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.user-header p {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 14px;
}

.header-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 30px;
    background: #f4f4f5;
    font-size: 12px;
    font-weight: 800;
}

.header-status.edit {
    color: #b45309;
    background: #fff7ed;
}

/* =========================================================
   LAYOUT
========================================================= */

.user-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 360px;
    gap: 25px;
    align-items: start;
}

/* =========================================================
   CARDS
========================================================= */

.form-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 22px;
    overflow: hidden;
}

.card-header {
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 14px;
    border-bottom: 1px solid var(--border);
}

.card-header-icon {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    border-radius: 12px;
    background: #f4f4f5;
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-header h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 900;
}

.card-header p {
    margin: 3px 0 0;
    font-size: 12px;
    color: var(--muted);
}

.card-body {
    padding: 24px;
}

/* =========================================================
   FORM GRID
========================================================= */

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.form-group {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    font-size: 12px;
    font-weight: 800;
    margin-bottom: 8px;
}

.form-group label span {
    color: var(--accent);
}

.input-wrapper {
    position: relative;
}

.input-wrapper>i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #a1a1aa;
    font-size: 14px;
    pointer-events: none;
}

.modern-input {
    width: 100%;
    height: 48px;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: #fafafa;
    padding: 0 14px 0 42px;
    color: var(--text);
    font-size: 13px;
    outline: none;
    transition: .2s;
}

.modern-input:focus {
    background: white;
    border-color: #a1a1aa;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, .04);
}

.modern-select {
    width: 100%;
    height: 48px;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: #fafafa;
    padding: 0 14px 0 42px;
    color: var(--text);
    font-size: 13px;
    outline: none;
    transition: .2s;
    appearance: none;
    cursor: pointer;
}

.modern-select:focus {
    background: white;
    border-color: #a1a1aa;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, .04);
}

.select-wrapper {
    position: relative;
}

.select-wrapper>i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #a1a1aa;
    font-size: 14px;
    pointer-events: none;
    z-index: 1;
}

.select-wrapper::after {
    content: '\f078';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #a1a1aa;
    font-size: 12px;
    pointer-events: none;
}

/* =========================================================
   PHOTO
========================================================= */

.photo-section {
    display: grid;
    grid-template-columns: 190px 1fr;
    gap: 22px;
    align-items: center;
}

.photo-preview {
    width: 190px;
    height: 230px;
    border-radius: 15px;
    overflow: hidden;
    background: #f4f4f5;
    border: 1px solid var(--border);
}

.photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #b5b5b8;
}

.photo-placeholder i {
    font-size: 38px;
}

.photo-placeholder span {
    font-size: 10px;
    font-weight: 700;
}

.photo-upload {
    border: 1.5px dashed #d4d4d8;
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    background: #fafafa;
    cursor: pointer;
    transition: .2s;
}

.photo-upload:hover {
    border-color: #737373;
    background: #f7f7f7;
}

.photo-upload-icon {
    width: 52px;
    height: 52px;
    margin: 0 auto 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: #171717;
    color: white;
    font-size: 18px;
}

.photo-upload strong {
    display: block;
    font-size: 13px;
    font-weight: 900;
    margin-bottom: 5px;
}

.photo-upload span {
    display: block;
    color: #999;
    font-size: 11px;
}

.photo-upload .file-name-display {
    display: block;
    margin-top: 8px;
    font-size: 10px;
    color: var(--accent);
    font-weight: 700;
}

/* =========================================================
   ACTIONS
========================================================= */

.form-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-bottom: 20px;
}

.btn {
    height: 48px;
    padding: 0 20px;
    border-radius: 12px;
    border: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: .2s;
}

.btn-cancel {
    background: white;
    color: #52525b;
    border: 1px solid var(--border);
}

.btn-cancel:hover {
    background: #f4f4f5;
    color: #171717;
}

.btn-submit {
    min-width: 210px;
    background: #171717;
    color: white;
}

.btn-submit:hover {
    background: #000;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
}

/* =========================================================
   SIDEBAR
========================================================= */

.sidebar {
    position: sticky;
    top: 20px;
}

.side-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 20px;
    overflow: hidden;
}

.side-header {
    padding: 18px 20px;
    border-bottom: 1px solid var(--border);
}

.side-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.side-header-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #f4f4f5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.side-header h3 {
    margin: 0;
    font-size: 13px;
    font-weight: 900;
}

.side-header p {
    margin: 3px 0 0;
    font-size: 10px;
    color: #999;
}

/* =========================================================
   USER PREVIEW
========================================================= */

.user-preview-body {
    padding: 20px;
    text-align: center;
}

.user-preview-photo {
    width: 135px;
    height: 165px;
    margin: 0 auto 15px;
    border-radius: 14px;
    overflow: hidden;
    background: #f4f4f5;
}

.user-preview-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-preview-placeholder {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 35px;
}

.user-preview-name {
    margin: 0;
    font-size: 18px;
    font-weight: 900;
}

.user-preview-email {
    margin-top: 5px;
    color: #999;
    font-size: 11px;
}

.user-meta {
    margin-top: 18px;
    padding-top: 15px;
    border-top: 1px solid var(--border);
    text-align: left;
}

.user-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 7px 0;
}

.user-meta-row span:first-child {
    color: #999;
    font-size: 10px;
}

.user-meta-row strong {
    font-size: 10px;
    text-align: right;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
}

.role-badge.admin {
    background: #fef3c7;
    color: #92400e;
}

.role-badge.comptable {
    background: #dbeafe;
    color: #1e40af;
}

.role-badge.tournage {
    background: #d1fae5;
    color: #065f46;
}

.role-badge.caisse {
    background: #fce4ec;
    color: #9a1f3c;
}

/* =========================================================
   DERNIERE SERIE
========================================================= */

.series-body {
    padding: 20px;
}

.series-poster {
    width: 100%;
    height: 150px;
    border-radius: 12px;
    background: #f4f4f5;
    overflow: hidden;
    margin-bottom: 15px;
}

.series-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.series-poster-placeholder {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 35px;
}

.series-title {
    margin: 0 0 5px;
    font-size: 17px;
    font-weight: 900;
}

.series-type {
    display: inline-flex;
    padding: 5px 9px;
    border-radius: 20px;
    background: #f4f4f5;
    font-size: 9px;
    font-weight: 800;
}

.series-budget {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid var(--border);
}

.series-budget-label {
    display: block;
    color: #999;
    font-size: 10px;
}

.series-budget-value {
    display: block;
    margin-top: 3px;
    font-size: 17px;
    font-weight: 900;
}

.series-budget-value small {
    color: #999;
    font-size: 9px;
}

/* =========================================================
   CONSEIL
========================================================= */

.tip-card {
    background: #171717;
    color: white;
    padding: 20px;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.tip-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.tip-header i {
    color: #fbbf24;
}

.tip-header strong {
    font-size: 12px;
}

.tip-card p {
    margin: 0;
    color: #bdbdbd;
    font-size: 11px;
    line-height: 1.7;
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
   RESPONSIVE
========================================================= */

@media (max-width: 1100px) {
    .user-layout {
        grid-template-columns: 1fr;
    }
    .sidebar {
        position: static;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    .sidebar>* {
        margin-bottom: 0;
    }
}

@media (max-width: 768px) {
    .user-page {
        padding: 15px 0 35px;
    }
    .user-container {
        padding: 0 12px;
    }
    .user-header {
        padding: 18px;
        flex-direction: column;
        align-items: flex-start;
    }
    .user-header h1 {
        font-size: 21px;
    }
    .user-header-icon {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
    }
    .form-grid {
        grid-template-columns: 1fr;
    }
    .card-header,
    .card-body {
        padding: 18px;
    }
    .photo-section {
        grid-template-columns: 1fr;
    }
    .photo-preview {
        margin: auto;
    }
    .sidebar {
        grid-template-columns: 1fr;
    }
    .form-actions {
        flex-direction: column-reverse;
        align-items: stretch;
    }
    .btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .user-header-left {
        gap: 12px;
    }
    .user-header-icon {
        display: none;
    }
    .card-header {
        gap: 10px;
    }
    .card-header-icon {
        width: 36px;
        height: 36px;
        flex-basis: 36px;
    }
}
</style>

<section class="user-page">
    <div class="user-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="user-header">
            <div class="user-header-left">
                <div class="user-header-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <div class="user-breadcrumb">
                        EVENPROD / ADMINISTRATION / UTILISATEURS
                    </div>
                    <h1>
                        <?= $user ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' ?>
                    </h1>
                    <p>
                        <?= $user ? 'Modifiez les informations de cet utilisateur.' : 'Ajoutez un nouvel utilisateur à la plateforme.' ?>
                    </p>
                </div>
            </div>
            <div class="header-status <?= $user ? 'edit' : '' ?>">
                <i class="fas <?= $user ? 'fa-edit' : 'fa-user-plus' ?>"></i>
                <?= $user ? 'Mode modification' : 'Nouvel utilisateur' ?>
            </div>
        </header>

        <!-- =========================================================
        MESSAGES (via GET)
        ========================================================= -->

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="modern-alert success">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <?= $user ? 'Utilisateur modifié avec succès !' : 'Utilisateur ajouté avec succès !' ?>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'exists'): ?>
        <div class="modern-alert error">
            <div class="alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div>
                ⚠️ Cet utilisateur existe déjà !
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
        <div class="modern-alert error">
            <div class="alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div>
                ❌ Erreur lors de l'enregistrement.
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- =========================================================
        LAYOUT
        ========================================================= -->

        <div class="user-layout">

            <!-- =========================================================
            FORMULAIRE
            ========================================================= -->

            <main>
                <form action="add_user" method="post" enctype="multipart/form-data" id="userForm">

                    <input type="hidden" name="id_user" value="<?= htmlspecialchars($user['id'] ?? '') ?>">

                    <!-- =====================================================
                    INFORMATIONS PERSONNELLES
                    ====================================================== -->

                    <section class="form-card">
                        <div class="card-header">
                            <div class="card-header-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h2>Informations personnelles</h2>
                                <p>Identité et coordonnées de l'utilisateur</p>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="form-grid">

                                <!-- NOM -->
                                <div class="form-group">
                                    <label for="nom">
                                        Nom
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user-tag"></i>
                                        <input type="text" id="nom" name="nom" class="modern-input"
                                            placeholder="Ex : Diop" required
                                            value="<?= htmlspecialchars($user['nom'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- PRENOM -->
                                <div class="form-group">
                                    <label for="prenom">
                                        Prénom
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user"></i>
                                        <input type="text" id="prenom" name="prenom" class="modern-input"
                                            placeholder="Ex : Amadou" required
                                            value="<?= htmlspecialchars($user['prenom'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- EMAIL -->
                                <div class="form-group">
                                    <label for="email">
                                        Email
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-envelope"></i>
                                        <input type="email" id="email" name="email" class="modern-input"
                                            placeholder="Ex : amadou@evenprod.com" required
                                            value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- TELEPHONE -->
                                <div class="form-group">
                                    <label for="telephone">
                                        Téléphone
                                        <span>*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-phone"></i>
                                        <input type="tel" id="telephone" name="telephone" class="modern-input"
                                            placeholder="Ex : 77 000 00 00" required
                                            value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- ROLE -->
                                <div class="form-group">
                                    <label for="role">
                                        Rôle
                                        <span>*</span>
                                    </label>
                                    <div class="select-wrapper">
                                        <i class="fas fa-user-shield"></i>
                                        <select id="role" name="role" class="modern-select" required>
                                            <option value="">-- Sélectionnez un rôle --</option>
                                            <option value="admin" <?= (isset($user['role']) && $user['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                                            <option value="comptable" <?= (isset($user['role']) && $user['role'] == 'comptable') ? 'selected' : '' ?>>Comptable</option>
                                            <option value="caisse" <?= (isset($user['role']) && $user['role'] == 'caisse') ? 'selected' : '' ?>>Caisse</option>
                                            <option value="tournage" <?= (isset($user['role']) && $user['role'] == 'tournage') ? 'selected' : '' ?>>Tournage</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- =====================================================
                    PHOTO DE L'UTILISATEUR
                    ====================================================== -->

                    <section class="form-card">
                        <div class="card-header">
                            <div class="card-header-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div>
                                <h2>Photo de profil</h2>
                                <p>Vérifiez l'image avant d'enregistrer le profil</p>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="photo-section">

                                <div class="photo-preview" id="photoPreview">
                                    <?php if (!empty($user['profile'])): ?>
                                    <img src="../../uploads/profile/<?= htmlspecialchars($user['profile']) ?>" alt="Photo de <?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?>">
                                    <?php else: ?>
                                    <div class="photo-placeholder">
                                        <i class="fas fa-user"></i>
                                        <span>Aperçu</span>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <input type="file" id="photo" name="photo" accept="image/*" hidden>

                                    <label for="photo" class="photo-upload">
                                        <div class="photo-upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <strong>Importer la photo de profil</strong>
                                        <span>Cliquez ici pour sélectionner une image</span>
                                        <span id="file-name-display" class="file-name-display">
                                            <?= !empty($user['profile']) ? htmlspecialchars($user['profile']) : 'Aucun fichier choisi' ?>
                                        </span>
                                    </label>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- =====================================================
                    ACTIONS
                    ====================================================== -->

                    <div class="form-actions">
                        <a href="javascript:history.back()" class="btn btn-cancel">
                            <i class="fas fa-arrow-left"></i>
                            Annuler
                        </a>

                        <button type="submit" class="btn btn-submit" id="submitBtn">
                            <i class="fas <?= $user ? 'fa-save' : 'fa-user-plus' ?>"></i>
                            <span>
                                <?= $user ? 'Enregistrer les modifications' : 'Enregistrer l\'utilisateur' ?>
                            </span>
                        </button>
                    </div>

                </form>
            </main>

            <!-- =========================================================
            SIDEBAR
            ========================================================= -->

            <aside class="sidebar">

                <!-- =====================================================
                APERCU UTILISATEUR
                ====================================================== -->

                <div class="side-card">
                    <div class="side-header">
                        <div class="side-header-title">
                            <div class="side-header-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div>
                                <h3>Aperçu du profil</h3>
                                <p>Mise à jour en temps réel</p>
                            </div>
                        </div>
                    </div>

                    <div class="user-preview-body">
                        <div class="user-preview-photo" id="userPreviewPhoto">
                            <?php if (!empty($user['profile'])): ?>
                            <img src="../../uploads/profile/<?= htmlspecialchars($user['profile']) ?>" alt="Aperçu">
                            <?php else: ?>
                            <div class="user-preview-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                            <?php endif; ?>
                        </div>

                        <h2 class="user-preview-name" id="previewName">
                            <?= htmlspecialchars(trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?: 'Nom de l\'utilisateur') ?>
                        </h2>

                        <div class="user-preview-email" id="previewEmail">
                            <?= htmlspecialchars($user['email'] ?? 'Email non renseigné') ?>
                        </div>

                        <div class="user-meta">
                            <div class="user-meta-row">
                                <span><i class="fas fa-phone"></i> Téléphone</span>
                                <strong id="previewPhone">
                                    <?= !empty($user['telephone']) ? htmlspecialchars($user['telephone']) : 'Non renseigné' ?>
                                </strong>
                            </div>
                            <div class="user-meta-row">
                                <span><i class="fas fa-user-shield"></i> Rôle</span>
                                <strong id="previewRole">
                                    <?php if (!empty($user['role'])): ?>
                                    <span class="role-badge <?= htmlspecialchars($user['role']) ?>">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                    <?php else: ?>
                                    Non défini
                                    <?php endif; ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- =====================================================
                DERNIERE SERIE
                ====================================================== -->

                <div class="side-card">
                    <div class="side-header">
                        <div class="side-header-title">
                            <div class="side-header-icon">
                                <i class="fas fa-film"></i>
                            </div>
                            <div>
                                <h3>Dernière série</h3>
                                <p>Production récemment ajoutée</p>
                            </div>
                        </div>
                    </div>

                    <div class="series-body">
                        <?php if ($lastSerie): ?>
                        <div class="series-poster">
                            <?php if (!empty($lastSerie['logo'])): ?>
                            <img src="../../uploads/series/<?= htmlspecialchars($lastSerie['logo']) ?>" alt="<?= htmlspecialchars($lastSerie['titre']) ?>">
                            <?php else: ?>
                            <div class="series-poster-placeholder">
                                <i class="fas fa-film"></i>
                            </div>
                            <?php endif; ?>
                        </div>

                        <h2 class="series-title">
                            <?= htmlspecialchars($lastSerie['titre']) ?>
                        </h2>
                        <span class="series-type">
                            <i class="fas fa-tag"></i>
                            <?= htmlspecialchars($lastSerie['type']) ?>
                        </span>

                        <div class="series-budget">
                            <span class="series-budget-label">Budget prévisionnel</span>
                            <span class="series-budget-value">
                                <?= number_format((float)($lastSerie['budget'] ?? 0), 0, ',', ' ') ?>
                                <small>FCFA</small>
                            </span>
                        </div>
                        <?php else: ?>
                        <div style="text-align:center; padding:20px 5px; color:#999;">
                            <i class="fas fa-film" style="font-size:32px; margin-bottom:10px;"></i>
                            <p style="margin:0; font-size:11px; line-height:1.6;">
                                Aucune série n'est encore enregistrée dans la maison de production.
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- =====================================================
                CONSEIL
                ====================================================== -->

                <div class="tip-card">
                    <div class="tip-header">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Conseil d'administration</strong>
                    </div>
                    <p>
                        Assurez-vous que les informations saisies sont correctes.
                        Le rôle attribué déterminera les accès et permissions
                        de l'utilisateur sur la plateforme.
                    </p>
                </div>

            </aside>

        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const nom = document.getElementById('nom');
    const prenom = document.getElementById('prenom');
    const email = document.getElementById('email');
    const telephone = document.getElementById('telephone');
    const role = document.getElementById('role');
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const userPreviewPhoto = document.getElementById('userPreviewPhoto');
    const fileNameDisplay = document.getElementById('file-name-display');

    /* =====================================================
       NOM & PRENOM
    ===================================================== */

    function updateName() {
        const first = prenom.value.trim();
        const last = nom.value.trim();
        const fullName = (first + ' ' + last).trim();
        document.getElementById('previewName').textContent = fullName || 'Nom de l\'utilisateur';
    }

    prenom.addEventListener('input', updateName);
    nom.addEventListener('input', updateName);

    /* =====================================================
       EMAIL
    ===================================================== */

    email.addEventListener('input', function() {
        document.getElementById('previewEmail').textContent = this.value.trim() || 'Email non renseigné';
    });

    /* =====================================================
       TELEPHONE
    ===================================================== */

    telephone.addEventListener('input', function() {
        document.getElementById('previewPhone').textContent = this.value.trim() || 'Non renseigné';
    });

    /* =====================================================
       ROLE
    ===================================================== */

    role.addEventListener('change', function() {
        const selected = this.value;
        const previewRole = document.getElementById('previewRole');

        if (selected) {
            const labels = {
                'admin': 'Admin',
                'comptable': 'Comptable',
                'caisse': 'Caisse',
                'tournage': 'Tournage'
            };
            previewRole.innerHTML = `<span class="role-badge ${selected}">${labels[selected] || selected}</span>`;
        } else {
            previewRole.textContent = 'Non défini';
        }
    });

    /* =====================================================
       PHOTO
    ===================================================== */

    if (photoInput) {
        photoInput.addEventListener('change', function() {
            if (!this.files || !this.files.length) return;

            const file = this.files[0];

            // Afficher le nom du fichier
            if (fileNameDisplay) {
                fileNameDisplay.textContent = file.name;
            }

            const reader = new FileReader();

            reader.onload = function(event) {
                const html = `<img src="${event.target.result}" alt="Photo de l'utilisateur">`;
                photoPreview.innerHTML = html;
                userPreviewPhoto.innerHTML = html;
            };

            reader.readAsDataURL(file);
        });
    }

    /* =====================================================
       SUBMIT
    ===================================================== */

    const form = document.getElementById('userForm');

    if (form) {
        form.addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '.7';
                submitBtn.style.cursor = 'not-allowed';
                const span = submitBtn.querySelector('span');
                if (span) {
                    span.textContent = 'Enregistrement...';
                }
            }
        });
    }

});
</script>

<?php include '../../includes/footer.php'; ?>