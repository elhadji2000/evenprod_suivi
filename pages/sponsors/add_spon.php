<?php
include '../../config/fonction.php';

$partenaire = null;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $res = mysqli_query($connexion, "SELECT * FROM clients WHERE id=$id");
    $partenaire = mysqli_fetch_assoc($res);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_partenaire'] ?? null;
    $ninea = $_POST['ninea'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $adresse = $_POST['adresse'];
    $logoFile = $_FILES['logo'] ?? null;

    if ($id) {
        if (modifierPartenaire($id, $ninea, $nom, $email, $contact, $adresse, $logoFile)) {
            header("Location: listes?success=3");
            exit;
        } else {
            echo "<p style='color:red'>Erreur lors de la modification du partenariat.</p>";
        }
    } else {
        if (ajouterPartenaire($ninea, $nom, $email, $contact, $adresse, $logoFile)) {
            header("Location: listes?success=2");
            exit;
        } else {
            echo "<p style='color:red'>Erreur lors de l'enregistrement du partenariat.</p>";
        }
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

.spon-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.spon-container {
    max-width: 1200px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.spon-header {
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

.spon-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.spon-header-icon {
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

.spon-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.spon-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.spon-header p {
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

.spon-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 320px;
    gap: 25px;
    align-items: start;
}

/* =========================================================
   FORM CARD
========================================================= */

.form-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
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
   FORM
========================================================= */

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
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
    color: var(--text);
}

.form-group label span {
    color: var(--accent);
}

.form-group .label-icon {
    margin-right: 6px;
    color: var(--muted);
}

.modern-input {
    width: 100%;
    height: 48px;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: #fafafa;
    padding: 0 14px;
    color: var(--text);
    font-size: 13px;
    outline: none;
    transition: .2s;
}

.modern-input:focus {
    background: white;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(229, 9, 20, .1);
}

/* =========================================================
   LOGO UPLOAD
========================================================= */

.logo-section {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 20px;
    align-items: center;
}

.logo-preview {
    width: 120px;
    height: 120px;
    border-radius: 15px;
    overflow: hidden;
    background: #f4f4f5;
    border: 1px solid var(--border);
}

.logo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.logo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #b5b5b8;
}

.logo-placeholder i {
    font-size: 32px;
}

.logo-placeholder span {
    font-size: 10px;
    font-weight: 700;
}

.logo-upload {
    border: 1.5px dashed #d4d4d8;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    background: #fafafa;
    cursor: pointer;
    transition: .2s;
}

.logo-upload:hover {
    border-color: var(--accent);
    background: #fef2f2;
}

.logo-upload-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: var(--accent);
    color: white;
    font-size: 18px;
}

.logo-upload strong {
    display: block;
    font-size: 12px;
    font-weight: 900;
    margin-bottom: 4px;
}

.logo-upload span {
    display: block;
    color: #999;
    font-size: 10px;
}

.logo-upload .file-name-display {
    display: block;
    margin-top: 6px;
    font-size: 10px;
    font-weight: 700;
    color: var(--success);
}

/* =========================================================
   ACTIONS
========================================================= */

.form-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
    margin-top: 10px;
}

.btn {
    height: 48px;
    padding: 0 24px;
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
    background: var(--accent);
    color: white;
}

.btn-submit:hover {
    background: var(--accent-hover);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(229, 9, 20, .3);
    color: white;
}

/* =========================================================
   SIDEBAR
========================================================= */

.sidebar {
    position: sticky;
    top: 20px;
}

.side-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    margin-bottom: 20px;
}

.side-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
}

.side-header h3 {
    margin: 0;
    font-size: 13px;
    font-weight: 900;
    display: flex;
    align-items: center;
    gap: 10px;
}

.side-header h3 i {
    color: var(--accent);
}

.side-body {
    padding: 20px;
}

.side-info {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.side-info .info-label {
    font-size: 11px;
    color: var(--muted);
    font-weight: 600;
}

.side-info .info-value {
    font-size: 14px;
    font-weight: 700;
}

.side-divider {
    border: 0;
    border-top: 1px solid var(--border);
    margin: 12px 0;
}

.side-links {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.side-links a {
    padding: 6px 0;
    font-size: 12px;
    color: var(--info);
    text-decoration: none;
    transition: .2s;
}

.side-links a:hover {
    color: var(--accent);
    padding-left: 6px;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 992px) {
    .spon-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .spon-layout {
        grid-template-columns: 1fr;
    }
    .sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .spon-page {
        padding: 15px 0 35px;
    }
    .spon-container {
        padding: 0 12px;
    }
    .spon-header {
        padding: 18px;
    }
    .spon-header h1 {
        font-size: 21px;
    }
    .spon-header-icon {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
    }
    .form-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .logo-section {
        grid-template-columns: 1fr;
    }
    .logo-preview {
        margin: auto;
    }
    .card-header,
    .card-body {
        padding: 16px;
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
    .spon-header-left {
        gap: 12px;
    }
    .spon-header-icon {
        display: none;
    }
}
</style>

<section class="spon-page">
    <div class="spon-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="spon-header">
            <div class="spon-header-left">
                <div class="spon-header-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <div class="spon-breadcrumb">
                        EVENPROD / PARTENARIATS
                    </div>
                    <h1><?= $partenaire ? 'Modifier le partenaire' : 'Nouveau partenaire' ?></h1>
                    <p>
                        <?= $partenaire 
                            ? 'Modifiez les informations de ce partenaire.' 
                            : 'Ajoutez un nouveau partenaire (client ou sponsor).' 
                        ?>
                    </p>
                </div>
            </div>
            <div class="header-status <?= $partenaire ? 'edit' : '' ?>">
                <i class="fas <?= $partenaire ? 'fa-edit' : 'fa-user-plus' ?>"></i>
                <?= $partenaire ? 'Mode modification' : 'Nouveau partenaire' ?>
            </div>
        </header>

        <!-- =========================================================
        LAYOUT
        ========================================================= -->

        <div class="spon-layout">

            <!-- =========================================================
            FORMULAIRE
            ========================================================= -->

            <main>
                <div class="form-card">
                    <div class="card-header">
                        <div class="card-header-icon">
                            <i class="fas fa-pen"></i>
                        </div>
                        <div>
                            <h2>Informations du partenaire</h2>
                            <p>Remplissez tous les champs pour enregistrer le partenaire</p>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="add_spon" method="post" enctype="multipart/form-data" id="sponForm">

                            <input type="hidden" name="id_partenaire" value="<?= $partenaire['id'] ?? '' ?>">

                            <div class="form-grid">

                                <!-- NINEA -->
                                <div class="form-group">
                                    <label for="ninea">
                                        <i class="fas fa-id-card label-icon"></i>
                                        NINEA
                                        <span>*</span>
                                    </label>
                                    <input type="text" id="ninea" name="ninea" class="modern-input" 
                                           placeholder="Ex : AA23456789JJ" required
                                           value="<?= htmlspecialchars($partenaire['ninea'] ?? '') ?>">
                                </div>

                                <!-- Nom -->
                                <div class="form-group">
                                    <label for="nom">
                                        <i class="fas fa-building label-icon"></i>
                                        Nom du partenaire
                                        <span>*</span>
                                    </label>
                                    <input type="text" id="nom" name="nom" class="modern-input" 
                                           placeholder="Ex : Mayfay Global" required
                                           value="<?= htmlspecialchars($partenaire['nom'] ?? '') ?>">
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <label for="email">
                                        <i class="fas fa-envelope label-icon"></i>
                                        E-mail
                                        <span>*</span>
                                    </label>
                                    <input type="email" id="email" name="email" class="modern-input" 
                                           placeholder="Ex : contact@mayfay.com" required
                                           value="<?= htmlspecialchars($partenaire['email'] ?? '') ?>">
                                </div>

                                <!-- Contact -->
                                <div class="form-group">
                                    <label for="contact">
                                        <i class="fas fa-phone label-icon"></i>
                                        Contact
                                        <span>*</span>
                                    </label>
                                    <input type="text" id="contact" name="contact" class="modern-input" 
                                           placeholder="Ex : 221784413400" required
                                           value="<?= htmlspecialchars($partenaire['contact'] ?? '') ?>">
                                </div>

                                <!-- Adresse (pleine largeur) -->
                                <div class="form-group" style="grid-column: span 2;">
                                    <label for="adresse">
                                        <i class="fas fa-map-marker-alt label-icon"></i>
                                        Adresse
                                        <span>*</span>
                                    </label>
                                    <input type="text" id="adresse" name="adresse" class="modern-input" 
                                           placeholder="Ex : Dakar, Sénégal" required
                                           value="<?= htmlspecialchars($partenaire['adresse'] ?? '') ?>">
                                </div>

                            </div>

                            <!-- =====================================================
                            LOGO
                            ====================================================== -->

                            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border);">
                                <div class="logo-section">
                                    <div class="logo-preview" id="logoPreview">
                                        <?php if ($partenaire && !empty($partenaire['logo']) && file_exists('../../uploads/logos/' . $partenaire['logo'])): ?>
                                        <img src="../../uploads/logos/<?= htmlspecialchars($partenaire['logo']) ?>" alt="Logo">
                                        <?php else: ?>
                                        <div class="logo-placeholder">
                                            <i class="fas fa-image"></i>
                                            <span>Aperçu</span>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <div>
                                        <input type="file" id="logo" name="logo" accept="image/*" <?= $partenaire ? '' : 'required' ?> hidden>
                                        <label for="logo" class="logo-upload">
                                            <div class="logo-upload-icon">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                            </div>
                                            <strong><?= $partenaire ? 'Changer le logo' : 'Importer le logo' ?></strong>
                                            <span>JPG, JPEG, PNG ou GIF</span>
                                            <span class="file-name-display" id="fileNameDisplay">
                                                <?= !empty($partenaire['logo']) ? htmlspecialchars($partenaire['logo']) : 'Aucun fichier choisi' ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- =====================================================
                            ACTIONS
                            ====================================================== -->

                            <div class="form-actions">
                                <a href="listes" class="btn btn-cancel">
                                    <i class="fas fa-arrow-left"></i>
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-submit" id="submitBtn">
                                    <i class="fas <?= $partenaire ? 'fa-save' : 'fa-save' ?>"></i>
                                    <span><?= $partenaire ? 'Modifier' : 'Enregistrer' ?></span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </main>

            <!-- =========================================================
            SIDEBAR
            ========================================================= -->

            <aside class="sidebar">

                <!-- Aperçu -->
                <div class="side-card">
                    <div class="side-header">
                        <h3><i class="fas fa-info-circle"></i> Aperçu</h3>
                    </div>
                    <div class="side-body">
                        <div class="side-info">
                            <span class="info-label">Statut</span>
                            <span class="info-value" style="color:var(--accent);">
                                <?= $partenaire ? 'Modification en cours' : 'Nouveau partenaire' ?>
                            </span>
                        </div>
                        <hr class="side-divider">
                        <div class="side-info">
                            <span class="info-label">Plateforme</span>
                            <span class="info-value">EVENPROD</span>
                        </div>
                        <div class="side-info">
                            <span class="info-label">Type</span>
                            <span class="info-value">Client / Sponsor</span>
                        </div>
                    </div>
                </div>

                <!-- Raccourcis -->
                <div class="side-card">
                    <div class="side-header">
                        <h3><i class="fas fa-link"></i> Raccourcis</h3>
                    </div>
                    <div class="side-body">
                        <div class="side-links">
                            <a href="listes">
                                <i class="fas fa-list"></i> Voir tous les partenaires
                            </a>
                            <a href="#">
                                <i class="fas fa-plus-circle"></i> Ajouter un sponsor
                            </a>
                        </div>
                    </div>
                </div>

            </aside>

        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logoPreview');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    // Prévisualisation du logo
    if (logoInput) {
        logoInput.addEventListener('change', function() {
            if (!this.files || !this.files.length) return;

            const file = this.files[0];
            
            // Afficher le nom du fichier
            fileNameDisplay.textContent = file.name;
            fileNameDisplay.style.color = 'var(--success)';

            // Prévisualiser l'image
            const reader = new FileReader();
            reader.onload = function(event) {
                logoPreview.innerHTML = `<img src="${event.target.result}" alt="Logo">`;
            };
            reader.readAsDataURL(file);
        });
    }

    // Validation avant soumission
    const form = document.getElementById('sponForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form) {
        form.addEventListener('submit', function(e) {
            const ninea = document.getElementById('ninea').value.trim();
            const nom = document.getElementById('nom').value.trim();
            const email = document.getElementById('email').value.trim();
            const contact = document.getElementById('contact').value.trim();
            const adresse = document.getElementById('adresse').value.trim();

            if (!ninea) {
                e.preventDefault();
                alert('Veuillez saisir le NINEA.');
                return;
            }

            if (!nom) {
                e.preventDefault();
                alert('Veuillez saisir le nom du partenaire.');
                return;
            }

            if (!email) {
                e.preventDefault();
                alert('Veuillez saisir l\'e-mail.');
                return;
            }

            if (!contact) {
                e.preventDefault();
                alert('Veuillez saisir le contact.');
                return;
            }

            if (!adresse) {
                e.preventDefault();
                alert('Veuillez saisir l\'adresse.');
                return;
            }

            <?php if (!$partenaire): ?>
            if (!logoInput.files || !logoInput.files.length) {
                e.preventDefault();
                alert('Veuillez choisir un logo pour le partenaire.');
                return;
            }
            <?php endif; ?>

            // Désactiver le bouton
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '.7';
                submitBtn.querySelector('span').textContent = 'Enregistrement...';
            }
        });
    }

});
</script>

<?php include '../../includes/footer.php'; ?>