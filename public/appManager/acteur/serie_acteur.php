<?php
include '../../../config/fonction.php';

$id = $_GET['id_serie'] ?? 0;
$serie = getSerieById($id);
$acteurs = getActeursNotInSerie($id);

// Récupérer le budget de la série
$budgetSerie = $serie['budget'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $serieId = (int)($_POST['serie_id'] ?? 0);
    $acteurs2 = $_POST['acteurs'] ?? [];
    $cachets = $_POST['cachet'] ?? [];
    $types_acteur = $_POST['type_acteur'] ?? [];
    $roles = $_POST['role'] ?? []; // ✅ AJOUT : Récupération des rôles
    $contrats = $_FILES['contrat'] ?? [];

    if ($serieId && !empty($acteurs2)) {

        foreach ($acteurs2 as $acteurId) {

            $acteurId = (int)$acteurId;
            $cachet = isset($cachets[$acteurId]) ? floatval($cachets[$acteurId]) : 0;
            $type = mysqli_real_escape_string($connexion, $types_acteur[$acteurId] ?? 'journalier');
            $role = mysqli_real_escape_string($connexion, $roles[$acteurId] ?? ''); // ✅ AJOUT : Récupération du rôle

            // ==========================
            // Gestion du contrat
            // ==========================
            $contratFile = null;

            if (
                isset($contrats['tmp_name'][$acteurId]) &&
                $contrats['error'][$acteurId] === 0
            ) {

                $ext = strtolower(pathinfo($contrats['name'][$acteurId], PATHINFO_EXTENSION));
                $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

                if (in_array($ext, $allowed)) {

                    $uploadDir = '../../../uploads/contrats/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $contratFile = 'contrat_' . uniqid() . '_' . $acteurId . '.' . $ext;

                    move_uploaded_file(
                        $contrats['tmp_name'][$acteurId],
                        $uploadDir . $contratFile
                    );
                }
            }

            // ==========================
            // Insertion acteur dans la série
            // ✅ AJOUT : champ role
            // ==========================
            $sql = "
                INSERT INTO serie_acteur (
                    serie_id,
                    acteur_id,
                    cachet,
                    type_acteur,
                    role,          -- ✅ AJOUT
                    contrat
                )
                VALUES (
                    $serieId,
                    $acteurId,
                    $cachet,
                    '$type',
                    '$role',       -- ✅ AJOUT
                    " . ($contratFile ? "'$contratFile'" : "NULL") . "
                )
            ";

            if (!mysqli_query($connexion, $sql)) {
                continue;
            }

            // ==========================
            // Dépense immédiate uniquement
            // pour les acteurs forfaitaires
            // ==========================
            if (strtolower($type) === 'forfaitaire' && $cachet > 0) {

                $sqlDepense = "
                    INSERT INTO depenses (
                        serie_id,
                        acteur_id,
                        type_depense,
                        montant,
                        date_depense
                    )
                    VALUES (
                        $serieId,
                        $acteurId,
                        'reglement_acteur',
                        $cachet,
                        CURDATE()
                    )
                ";

                mysqli_query($connexion, $sqlDepense);
            }
        }
    }

    header("Location: acteurs.php?id=" . $serieId);
    exit;
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

.serie-acteur-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.serie-acteur-container {
    max-width: 1200px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.serie-acteur-header {
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

.serie-acteur-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.serie-acteur-header-icon {
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

.serie-acteur-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.serie-acteur-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.serie-acteur-header p {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 14px;
}

.serie-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 30px;
    background: #f4f4f5;
    font-size: 12px;
    font-weight: 800;
}

.serie-badge i {
    color: var(--accent);
}

/* =========================================================
   CARD
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
   TABLE
========================================================= */

.table-responsive {
    overflow-x: auto;
}

.actor-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.actor-table thead {
    background: #fafafa;
    border-bottom: 2px solid var(--border);
}

.actor-table thead th {
    padding: 12px 14px;
    text-align: left;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--muted);
}

.actor-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}

.actor-table tbody tr:hover {
    background: #fafafa;
}

.actor-table tbody tr:last-child {
    border-bottom: 0;
}

.actor-table tbody td {
    padding: 10px 14px;
    vertical-align: middle;
}

.actor-table .checkbox-cell {
    text-align: center;
    width: 45px;
}

.actor-table input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--accent);
}

.actor-name {
    font-weight: 700;
    font-size: 13px;
}

.actor-birth {
    font-size: 12px;
    color: var(--muted);
}

/* =========================================================
   FORM INPUTS
========================================================= */

.form-control-modern {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--border);
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
    background: #fafafa;
    transition: .2s;
    outline: none;
    color: var(--text);
}

.form-control-modern:focus {
    background: white;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(229, 9, 20, .1);
}

.form-control-modern:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.cachet-input {
    width: 130px;
}

.role-input {
    width: 130px;
}

/* =========================================================
   CONTRAT UPLOAD
========================================================= */

.contrat-upload {
    display: flex;
    align-items: center;
    gap: 8px;
}

.contrat-upload .file-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
}

.contrat-upload .file-wrapper input[type="file"] {
    position: absolute;
    left: 0;
    top: 0;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.contrat-upload .file-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 8px;
    background: var(--primary);
    color: white;
    font-size: 9px;
    font-weight: 700;
    cursor: pointer;
    transition: .2s;
    border: none;
}

.contrat-upload .file-btn:hover {
    background: var(--accent);
}

.contrat-upload .file-name {
    font-size: 10px;
    color: var(--muted);
    max-width: 100px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.contrat-upload .file-name.has-file {
    color: var(--success);
    font-weight: 600;
}

/* =========================================================
   RADIO BUTTONS
========================================================= */

.radio-group {
    display: flex;
    gap: 8px;
    align-items: center;
}

.radio-label {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
    padding: 3px 8px;
    border-radius: 6px;
    transition: .2s;
}

.radio-label:hover {
    background: #f4f4f5;
}

.radio-label input[type="radio"] {
    accent-color: var(--accent);
    width: 13px;
    height: 13px;
    cursor: pointer;
}

.radio-label .badge-type {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 7px;
    font-weight: 800;
    text-transform: uppercase;
}

.badge-type.forfaitaire {
    background: #fef3c7;
    color: #92400e;
}

.badge-type.journalier {
    background: #dbeafe;
    color: #1e40af;
}

/* =========================================================
   BUDGET INFO
========================================================= */

.budget-info {
    background: #fafafa;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 14px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}

.budget-info .label {
    font-size: 11px;
    font-weight: 700;
    color: var(--muted);
}

.budget-info .value {
    font-size: 17px;
    font-weight: 900;
}

.budget-info .value.accent {
    color: var(--accent);
}

.budget-info .value.success {
    color: var(--success);
}

.budget-info .value.danger {
    color: var(--danger);
}

/* =========================================================
   EMPTY STATE
========================================================= */

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--muted);
}

.empty-state i {
    font-size: 38px;
    color: #d4d4d8;
    margin-bottom: 12px;
}

.empty-state h3 {
    font-size: 16px;
    font-weight: 900;
    color: var(--text);
    margin-bottom: 6px;
}

.empty-state p {
    font-size: 12px;
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
   RESPONSIVE
========================================================= */

@media (max-width: 992px) {
    .serie-acteur-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .budget-info {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }
}

@media (max-width: 768px) {
    .serie-acteur-page {
        padding: 15px 0 35px;
    }
    .serie-acteur-container {
        padding: 0 12px;
    }
    .serie-acteur-header {
        padding: 18px;
    }
    .serie-acteur-header h1 {
        font-size: 21px;
    }
    .serie-acteur-header-icon {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
    }
    .actor-table thead {
        display: none;
    }
    .actor-table tbody tr {
        display: block;
        padding: 14px 0;
        border-bottom: 2px solid var(--border);
    }
    .actor-table tbody tr:last-child {
        border-bottom: 0;
    }
    .actor-table tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 0;
        flex-wrap: wrap;
    }
    .actor-table tbody td::before {
        content: attr(data-label);
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--muted);
        letter-spacing: 0.3px;
    }
    .actor-table tbody td:first-child::before {
        display: none;
    }
    .actor-table tbody td:first-child {
        justify-content: center;
        padding-bottom: 8px;
    }
    .radio-group {
        flex-wrap: wrap;
    }
    .cachet-input {
        width: 100%;
    }
    .role-input {
        width: 100%;
    }
    .contrat-upload {
        flex-wrap: wrap;
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
    .serie-acteur-header-left {
        gap: 12px;
    }
    .serie-acteur-header-icon {
        display: none;
    }
}
</style>

<section class="serie-acteur-page">
    <div class="serie-acteur-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="serie-acteur-header">
            <div class="serie-acteur-header-left">
                <div class="serie-acteur-header-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <div class="serie-acteur-breadcrumb">
                        EVENPROD / SÉRIES / ACTEURS
                    </div>
                    <h1>Ajouter des acteurs</h1>
                    <p>
                        <i class="fas fa-film" style="color:var(--accent);"></i>
                        Série : <strong><?= htmlspecialchars($serie['titre'] ?? 'Série introuvable') ?></strong>
                    </p>
                </div>
            </div>
            <div class="serie-badge">
                <i class="fas fa-coins"></i>
                Budget : <?= number_format($budgetSerie, 0, ',', ' ') ?> FCFA
            </div>
        </header>

        <!-- =========================================================
        FORMULAIRE
        ========================================================= -->

        <div class="form-card">
            <div class="card-header">
                <div class="card-header-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h2>Sélection des acteurs</h2>
                    <p>Choisissez les acteurs à ajouter et définissez leur type de rémunération</p>
                </div>
            </div>

            <div class="card-body">
                <form action="serie_acteur?id_serie=<?= htmlspecialchars($serie['id'] ?? 0) ?>" method="post" id="actorForm" enctype="multipart/form-data">

                    <input type="hidden" name="serie_id" value="<?= htmlspecialchars($id) ?>">

                    <!-- =====================================================
                    BUDGET INFO
                    ====================================================== -->

                    <div class="budget-info">
                        <div>
                            <span class="label"><i class="fas fa-coins"></i> Budget total</span>
                            <span class="value accent"><?= number_format($budgetSerie, 0, ',', ' ') ?> FCFA</span>
                        </div>
                        <div>
                            <span class="label"><i class="fas fa-calculator"></i> Total cachets</span>
                            <span class="value" id="totalCachets">0 FCFA</span>
                        </div>
                        <div>
                            <span class="label"><i class="fas fa-arrow-right"></i> Budget restant</span>
                            <span class="value" id="budgetRestant"><?= number_format($budgetSerie, 0, ',', ' ') ?> FCFA</span>
                        </div>
                    </div>

                    <!-- =====================================================
                    TABLEAU DES ACTEURS AVEC CONTRAT INDIVIDUEL
                    ====================================================== -->

                    <div class="table-responsive">
                        <?php if (!empty($acteurs)): ?>
                        <table class="actor-table" id="actorTable">
                            <thead>
                                <tr>
                                    <th style="text-align:center; width:40px;">Choix</th>
                                    <th>Acteur</th>
                                    <th>Date naissance</th>
                                    <th>Type</th>
                                    <th>Cachet (FCFA)</th>
                                    <th>Rôle</th>
                                    <th>Contrat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($acteurs as $acteur): ?>
                                <tr>
                                    <td data-label="Choix" class="checkbox-cell">
                                        <input type="checkbox" name="acteurs[]" value="<?= $acteur['id'] ?>" 
                                               class="acteur-checkbox" data-id="<?= $acteur['id'] ?>">
                                    </td>
                                    <td data-label="Acteur">
                                        <div class="actor-name">
                                            <i class="fas fa-user" style="color:var(--muted); font-size:11px; margin-right:6px;"></i>
                                            <?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?>
                                        </div>
                                    </td>
                                    <td data-label="Date naissance">
                                        <span class="actor-birth">
                                            <i class="fas fa-calendar-alt" style="font-size:10px; margin-right:4px;"></i>
                                            <?= htmlspecialchars($acteur['date_naissance'] ?? 'Non renseignée') ?>
                                        </span>
                                    </td>
                                    <td data-label="Type">
                                        <div class="radio-group">
                                            <label class="radio-label">
                                                <input type="radio" name="type_acteur[<?= $acteur['id'] ?>]" 
                                                       value="forfaitaire" checked>
                                                <span class="badge-type forfaitaire">Forfaitaire</span>
                                            </label>
                                            <label class="radio-label">
                                                <input type="radio" name="type_acteur[<?= $acteur['id'] ?>]" 
                                                       value="journalier">
                                                <span class="badge-type journalier">Journalier</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td data-label="Cachet">
                                        <input type="number" 
                                               name="cachet[<?= $acteur['id'] ?>]" 
                                               class="form-control-modern cachet-input" 
                                               placeholder="Montant"
                                               min="0" step="1000"
                                               data-id="<?= $acteur['id'] ?>"
                                               disabled>
                                    </td>
                                    <td data-label="Rôle">
                                        <input type="text" 
                                               name="role[<?= $acteur['id'] ?>]" 
                                               class="form-control-modern role-input" 
                                               placeholder="ex: principal"
                                               data-id="<?= $acteur['id'] ?>"
                                               disabled>
                                    </td>
                                    <td data-label="Contrat">
                                        <div class="contrat-upload">
                                            <div class="file-wrapper">
                                                <input type="file" name="contrat[<?= $acteur['id'] ?>]" 
                                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                       class="contrat-input"
                                                       data-id="<?= $acteur['id'] ?>"
                                                       disabled>
                                                <button type="button" class="file-btn" onclick="this.parentElement.querySelector('input[type=file]').click()">
                                                    <i class="fas fa-upload"></i> Choisir
                                                </button>
                                            </div>
                                            <span class="file-name" id="contratName_<?= $acteur['id'] ?>">Aucun</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <h3>Tous les acteurs sont déjà associés</h3>
                            <p>Tous les acteurs disponibles ont déjà été ajoutés à cette série.</p>
                            <a href="acteurs.php?id=<?= htmlspecialchars($id) ?>" class="btn btn-cancel" style="display:inline-flex; margin-top:10px;">
                                <i class="fas fa-arrow-left"></i>
                                Voir les acteurs de la série
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- =====================================================
                    ACTIONS
                    ====================================================== -->

                    <?php if (!empty($acteurs)): ?>
                    <div class="form-actions">
                        <a href="acteurs.php?id=<?= htmlspecialchars($id) ?>" class="btn btn-cancel">
                            <i class="fas fa-arrow-left"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-submit" id="submitBtn">
                            <i class="fas fa-save"></i>
                            <span>Enregistrer les acteurs</span>
                        </button>
                    </div>
                    <?php endif; ?>

                </form>
            </div>
        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const checkboxes = document.querySelectorAll('.acteur-checkbox');
    const cachetInputs = document.querySelectorAll('.cachet-input');
    const roleInputs = document.querySelectorAll('.role-input');
    const contratInputs = document.querySelectorAll('.contrat-input');
    const budgetTotal = <?= $budgetSerie ?>;
    const totalCachetsEl = document.getElementById('totalCachets');
    const budgetRestantEl = document.getElementById('budgetRestant');

    // Afficher le nom du fichier contrat pour chaque acteur
    contratInputs.forEach(input => {
        input.addEventListener('change', function() {
            const id = this.dataset.id;
            const fileNameEl = document.getElementById('contratName_' + id);
            if (this.files && this.files.length > 0) {
                fileNameEl.textContent = this.files[0].name;
                fileNameEl.className = 'file-name has-file';
            } else {
                fileNameEl.textContent = 'Aucun';
                fileNameEl.className = 'file-name';
            }
        });
    });

    // Mettre à jour le total des cachets et le budget restant
    function updateTotals() {
        let total = 0;

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const id = checkbox.dataset.id;
                const cachetInput = document.querySelector(`.cachet-input[data-id="${id}"]`);
                if (cachetInput && cachetInput.value) {
                    total += parseFloat(cachetInput.value) || 0;
                }
            }
        });

        if (totalCachetsEl) {
            totalCachetsEl.textContent = total.toLocaleString('fr-FR') + ' FCFA';
        }

        const restant = budgetTotal - total;
        if (budgetRestantEl) {
            budgetRestantEl.textContent = restant.toLocaleString('fr-FR') + ' FCFA';
            if (restant < 0) {
                budgetRestantEl.style.color = 'var(--danger)';
            } else if (restant < budgetTotal * 0.2) {
                budgetRestantEl.style.color = 'var(--warning)';
            } else {
                budgetRestantEl.style.color = 'var(--success)';
            }
        }
    }

    // Activer/Désactiver les champs selon la checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const id = this.dataset.id;
            const cachetInput = document.querySelector(`.cachet-input[data-id="${id}"]`);
            const roleInput = document.querySelector(`.role-input[data-id="${id}"]`);
            const contratInput = document.querySelector(`.contrat-input[data-id="${id}"]`);
            
            if (cachetInput) {
                cachetInput.disabled = !this.checked;
                if (!this.checked) {
                    cachetInput.value = '';
                    cachetInput.placeholder = 'Montant';
                }
            }
            
            if (roleInput) {
                roleInput.disabled = !this.checked;
                if (!this.checked) {
                    roleInput.value = '';
                    roleInput.placeholder = 'Rôle (ex: protagoniste)';
                }
            }
            
            if (contratInput) {
                contratInput.disabled = !this.checked;
                if (!this.checked) {
                    const fileNameEl = document.getElementById('contratName_' + id);
                    fileNameEl.textContent = 'Aucun';
                    fileNameEl.className = 'file-name';
                    contratInput.value = '';
                }
            }
            
            updateTotals();
        });
    });

    // Mettre à jour le total lors du changement des cachets
    cachetInputs.forEach(input => {
        input.addEventListener('input', updateTotals);
    });

    // Validation avant soumission
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            let hasError = false;
            let message = '';

            const checked = document.querySelectorAll('.acteur-checkbox:checked');
            if (checked.length === 0) {
                hasError = true;
                message = 'Veuillez sélectionner au moins un acteur.';
            }

            // Vérifier que chaque acteur sélectionné a un cachet
            checked.forEach(checkbox => {
                const id = checkbox.dataset.id;
                const cachetInput = document.querySelector(`.cachet-input[data-id="${id}"]`);
                if (!cachetInput.value || parseFloat(cachetInput.value) <= 0) {
                    hasError = true;
                    message = 'Veuillez définir un cachet pour chaque acteur sélectionné.';
                }
            });

            // Vérifier le budget
            let total = 0;
            checked.forEach(checkbox => {
                const id = checkbox.dataset.id;
                const cachetInput = document.querySelector(`.cachet-input[data-id="${id}"]`);
                if (cachetInput && cachetInput.value) {
                    total += parseFloat(cachetInput.value) || 0;
                }
            });
            
            if (total > budgetTotal) {
                hasError = true;
                message = 'Le total des cachets (' + total.toLocaleString('fr-FR') + ' FCFA) dépasse le budget de la série (' + budgetTotal.toLocaleString('fr-FR') + ' FCFA) !';
            }

            if (hasError) {
                e.preventDefault();
                alert(message);
            }
        });
    }

    // Initialiser le total
    updateTotals();

});
</script>

<?php include '../../../includes/footer.php'; ?>