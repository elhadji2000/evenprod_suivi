<?php
include '../../../config/fonction.php';

$serieId = $_GET['id'] ?? 0;
$serie = getSerieById($serieId);
$tournages = getTournagesBySerieId($serieId);

// Récupérer le total des dépenses
$depenses = getDepensesBySerie($serieId);
$totalDepenses = 0;
foreach ($depenses as $d) {
    $totalDepenses += $d['montant'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serieId = $_POST['serie_id'];
    $tournageId = $_POST['tournage_id'] ?? '';
    $type = $_POST['type'];
    $description = $_POST['description'];

    $beneficiaire = trim($_POST['beneficiaire'] ?? '');
    $telephone_beneficiaire = trim($_POST['telephone_beneficiaire'] ?? '');
    $montant = floatval($_POST['montant'] ?? 0);

    // Upload du justificatif si présent
    $justificatif = null;
    if (isset($_FILES['justificatif']) && $_FILES['justificatif']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['justificatif']['name'], PATHINFO_EXTENSION));

        if ($ext === 'pdf') {
            $uploadDir = __DIR__ . '/../../../uploads/justificatifs/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = 'depense_' . time() . '.pdf';
            $destination = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['justificatif']['tmp_name'], $destination)) {
                $justificatif = $filename;
            }
        }
    }

    $result = ajouterDepense($serieId, $tournageId, $type, $montant, $description, $beneficiaire, $telephone_beneficiaire, $justificatif);

    if ($result['success']) {
        header("Location: liste_all?id=$serieId");
        exit;
    } else {
        echo 'Erreur : ' . $result['message'];
    }
}
?>

<?php include '../../../includes/header.php'; ?>

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

.depense-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.depense-container {
    max-width: 1200px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.depense-header {
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

.depense-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.depense-header-icon {
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

.depense-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.depense-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.depense-header p {
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

.header-status i {
    color: var(--accent);
}

/* =========================================================
   LAYOUT
========================================================= */

.depense-layout {
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

.modern-textarea {
    width: 100%;
    min-height: 80px;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: #fafafa;
    padding: 12px 14px;
    color: var(--text);
    font-size: 13px;
    outline: none;
    transition: .2s;
    resize: vertical;
    font-family: inherit;
}

.modern-textarea:focus {
    background: white;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(229, 9, 20, .1);
}

.modern-select {
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
    appearance: none;
    cursor: pointer;
}

.modern-select:focus {
    background: white;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(229, 9, 20, .1);
}

.select-wrapper {
    position: relative;
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
   FILE UPLOAD
========================================================= */

.file-upload-wrapper {
    position: relative;
    cursor: pointer;
}

.file-upload-wrapper input[type="file"] {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    z-index: 2;
}

.file-upload-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 48px;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: #fafafa;
    padding: 0 14px;
    color: var(--text);
    font-size: 13px;
    transition: .2s;
}

.file-upload-wrapper:hover .file-upload-label {
    border-color: var(--accent);
    background: #fef2f2;
}

.file-upload-label .file-info {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--muted);
}

.file-upload-label .file-info i {
    font-size: 16px;
}

.file-upload-label .file-name {
    font-weight: 600;
    color: var(--text);
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.file-upload-label .file-btn {
    padding: 6px 14px;
    border-radius: 8px;
    background: var(--primary);
    color: white;
    font-size: 10px;
    font-weight: 700;
}

/* =========================================================
   ACTIONS
========================================================= */

.form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
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
    min-width: 180px;
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

.side-info-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--border);
}

.side-info-row:last-child {
    border-bottom: 0;
}

.side-info-row .label {
    font-size: 11px;
    color: var(--muted);
    font-weight: 600;
}

.side-info-row .value {
    font-size: 12px;
    font-weight: 700;
}

.side-info-row .value.accent {
    color: var(--accent);
}

.side-info-row .value.danger {
    color: var(--danger);
}

.side-info-row .value.success {
    color: var(--success);
}

.side-links {
    margin-top: 10px;
}

.side-links a {
    display: block;
    padding: 8px 0;
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
    .depense-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .depense-layout {
        grid-template-columns: 1fr;
    }

    .sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .depense-page {
        padding: 15px 0 35px;
    }

    .depense-container {
        padding: 0 12px;
    }

    .depense-header {
        padding: 18px;
    }

    .depense-header h1 {
        font-size: 21px;
    }

    .depense-header-icon {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
    }

    .form-grid {
        grid-template-columns: 1fr;
        gap: 16px;
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

    .file-upload-label .file-name {
        max-width: 100px;
    }
}

@media (max-width: 480px) {
    .depense-header-left {
        gap: 12px;
    }

    .depense-header-icon {
        display: none;
    }
}
</style>

<section class="depense-page">
    <div class="depense-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="depense-header">
            <div class="depense-header-left">
                <div class="depense-header-icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div>
                    <div class="depense-breadcrumb">
                        EVENPROD / SÉRIES / DÉPENSES
                    </div>
                    <h1>Ajouter une dépense</h1>
                    <p>
                        <i class="fas fa-film" style="color:var(--accent);"></i>
                        Série : <strong><?= htmlspecialchars($serie['titre'] ?? 'Série introuvable') ?></strong>
                        &nbsp;&bull;&nbsp;
                        <i class="fas fa-coins"></i>
                        Budget : <?= number_format($serie['budget'] ?? 0, 0, ',', ' ') ?> FCFA
                    </p>
                </div>
            </div>
            <div class="header-status">
                <i class="fas fa-plus-circle"></i>
                Nouvelle dépense
            </div>
        </header>

        <!-- =========================================================
        LAYOUT
        ========================================================= -->

        <div class="depense-layout">

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
                            <h2>Informations de la dépense</h2>
                            <p>Remplissez tous les champs pour enregistrer la dépense</p>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="add_depense?id=<?= htmlspecialchars($serie['id']) ?>" method="post"
                            enctype="multipart/form-data" id="depenseForm">

                            <input type="hidden" name="serie_id" value="<?= $serieId ?>">

                            <div class="form-grid">

                                <!-- Type de dépense -->
                                <div class="form-group">
                                    <label for="type">
                                        <i class="fas fa-tag label-icon"></i>
                                        Type de dépense
                                        <span>*</span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select id="type" name="type" class="modern-select" required>
                                            <option value="">-- Sélectionnez --</option>
                                            <option value="Cachet">Cachet</option>
                                            <option value="Decor">Décor</option>
                                            <option value="Transport">Transport</option>
                                            <option value="Reception">Réception</option>
                                            <option value="Accessoire">Accessoire</option>
                                            <option value="Reglement acteur">Règlement acteur</option>
                                            <option value="HMC">HMC</option>
                                            <option value="Carburant">Carburant</option>
                                            <option value="Pharmacie">Pharmacie</option>
                                            <option value="Autre">Autre</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Montant -->
                                <div class="form-group">
                                    <label for="montant">
                                        <i class="fas fa-coins label-icon"></i>
                                        Montant
                                        <span>*</span>
                                    </label>
                                    <input type="number" id="montant" name="montant" class="modern-input"
                                        placeholder="Ex : 50000" min="0" step="100" required>
                                </div>

                                <!-- Tournage -->
                                <div class="form-group">
                                    <label for="tournage_id">
                                        <i class="fas fa-video label-icon"></i>
                                        Tournage associé
                                    </label>
                                    <div class="select-wrapper">
                                        <select id="tournage_id" name="tournage_id" class="modern-select">
                                            <option value="">-- Aucun --</option>
                                            <?php foreach ($tournages as $t): ?>
                                            <option value="<?= $t['id'] ?>">
                                                <?= htmlspecialchars($t['reference']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Bénéficiaire -->
                                <div class="form-group">
                                    <label for="beneficiaire">
                                        <i class="fas fa-user label-icon"></i>
                                        Bénéficiaire
                                        <span>*</span>
                                    </label>

                                    <input type="text" id="beneficiaire" name="beneficiaire" class="modern-input"
                                        placeholder="Ex : Madiop Diop" maxlength="150" required>
                                </div>

                                <!-- Téléphone du bénéficiaire -->
                                <div class="form-group">
                                    <label for="telephone_beneficiaire">
                                        <i class="fas fa-phone label-icon"></i>
                                        Téléphone du bénéficiaire
                                        <span>*</span>
                                    </label>

                                    <input type="tel" id="telephone_beneficiaire" name="telephone_beneficiaire"
                                        class="modern-input" placeholder="Ex : 221784413400" maxlength="12" minlength="12" required>
                                </div>

                                <!-- Justificatif -->
                                <div class="form-group">
                                    <label for="justificatif">
                                        <i class="fas fa-file-pdf label-icon"></i>
                                        Justificatif (PDF)
                                    </label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" id="justificatif" name="justificatif"
                                            accept="application/pdf">
                                        <div class="file-upload-label">
                                            <span class="file-info">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <span class="file-name" id="fileName">Aucun fichier</span>
                                            </span>
                                            <span class="file-btn">Parcourir</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description (pleine largeur) -->
                                <div class="form-group" style="grid-column: span 2;">
                                    <label for="description">
                                        <i class="fas fa-align-left label-icon"></i>
                                        Libellé / Description
                                        <span>*</span>
                                    </label>
                                    <textarea id="description" name="description" class="modern-textarea"
                                        placeholder="Décrivez brièvement la dépense..." required></textarea>
                                </div>

                            </div>

                            <!-- Actions -->
                            <div class="form-actions">
                                <a href="liste_all?id=<?= htmlspecialchars($serie['id']) ?>" class="btn btn-cancel">
                                    <i class="fas fa-arrow-left"></i>
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-submit" id="submitBtn">
                                    <i class="fas fa-save"></i>
                                    <span>Enregistrer</span>
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
                        <div class="side-info-row">
                            <span class="label">Série</span>
                            <span class="value"><?= htmlspecialchars($serie['titre'] ?? '-') ?></span>
                        </div>
                        <div class="side-info-row">
                            <span class="label">Budget total</span>
                            <span class="value accent"><?= number_format($serie['budget'] ?? 0, 0, ',', ' ') ?>
                                FCFA</span>
                        </div>
                        <div class="side-info-row">
                            <span class="label">Dépenses totales</span>
                            <span class="value danger"><?= number_format($totalDepenses, 0, ',', ' ') ?> FCFA</span>
                        </div>
                        <div class="side-info-row">
                            <span class="label">Budget restant</span>
                            <span
                                class="value success"><?= number_format(($serie['budget'] ?? 0) - $totalDepenses, 0, ',', ' ') ?>
                                FCFA</span>
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
                            <a href="<?= $url_base ?>pages/series_list">
                                <i class="fas fa-film"></i> Voir toutes les séries
                            </a>
                            <a href="liste_all?id=<?= htmlspecialchars($serie['id']) ?>">
                                <i class="fas fa-list"></i> Voir toutes les dépenses
                            </a>
                            <a href="../series/tournages.php?id=<?= htmlspecialchars($serie['id']) ?>">
                                <i class="fas fa-video"></i> Voir les tournages
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

    const fileInput = document.getElementById('justificatif');
    const fileName = document.getElementById('fileName');

    // Afficher le nom du fichier
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileName.textContent = this.files[0].name;
            } else {
                fileName.textContent = 'Aucun fichier';
            }
        });
    }

    // Validation avant soumission
    const form = document.getElementById('depenseForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form) {
        form.addEventListener('submit', function(e) {
            const type = document.getElementById('type').value;
            const montant = document.getElementById('montant').value;
            const description = document.getElementById('description').value.trim();
            const beneficiaire = document.getElementById('beneficiaire').value.trim();

            if (!type) {
                e.preventDefault();
                alert('Veuillez sélectionner un type de dépense.');
                return;
            }

            if (!montant || parseFloat(montant) <= 0) {
                e.preventDefault();
                alert('Veuillez saisir un montant valide.');
                return;
            }

            if (!description) {
                e.preventDefault();
                alert('Veuillez saisir une description.');
                return;
            }
             if (!beneficiaire) {
                e.preventDefault();
                alert('Veuillez saisir un beneficiaire.');
                return;
            }

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

<?php include '../../../includes/footer.php'; ?>