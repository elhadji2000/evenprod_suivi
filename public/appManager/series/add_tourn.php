<?php
include '../../../config/fonction.php';

$serieId = $_GET['id_serie'] ?? 0;
$serie = getSerieById($serieId);

// Vérifie si on est en édition
$tournageId = $_GET['id_tournage'] ?? null;
if ($tournageId) {
    $tournage = getTournageById($tournageId);
    $reference = $tournage['reference'];
    $date = $tournage['date_tournage'];
    $acteursSelectionnes = getActeursByTournage($tournageId);
    $acteursSelectionnesIds = array_map(function($a){ return $a['id']; }, $acteursSelectionnes);
    // Récupérer les séquences pour chaque acteur
    $sequencesParActeur = getSequencesByTournage($tournageId);
} else {
    $reference = generateTournageReference();
    $date = '';
    $acteursSelectionnesIds = [];
    $sequencesParActeur = [];
}

// Récupérer tous les acteurs disponibles pour cette série
$acteurs = getActeursBySerieId($serieId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serieId = $_POST['serie_id'];
    $date = $_POST['date'];
    $reference = $_POST['reference'];
    $acteursIds = $_POST['acteurs'] ?? [];
    $sequences = $_POST['sequences'] ?? [];

    if ($tournageId) {
        $result = modifierTournageWithSequences($tournageId, $serieId, $date, $reference, $acteursIds, $sequences);
    } else {
        $result = ajouterTournage($serieId, $date, $reference, $acteursIds, $sequences);
    }

    if ($result['success']) {
        header("Location: tournages.php?id=$serieId");
        exit;
    } else {
        echo "Erreur : " . $result['message'];
    }
}

include '../../../includes/header.php';
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

.tournage-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.tournage-container {
    max-width: 1200px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.tournage-header {
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

.tournage-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.tournage-header-icon {
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

.tournage-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.tournage-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.tournage-header p {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 14px;
}

.tournage-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 30px;
    background: #f4f4f5;
    font-size: 12px;
    font-weight: 800;
}

.tournage-badge i {
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
   FORM
========================================================= */

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 24px;
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

.modern-input:disabled {
    background: #f4f4f5;
    cursor: not-allowed;
    opacity: 0.7;
}

/* =========================================================
   ACTORS TABLE
========================================================= */

.actors-section-title {
    font-size: 14px;
    font-weight: 900;
    margin: 0 0 12px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.actors-section-title i {
    color: var(--accent);
}

.actors-section-title .count {
    font-size: 11px;
    font-weight: 700;
    color: var(--muted);
}

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
    padding: 12px 16px;
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
    padding: 10px 16px;
    vertical-align: middle;
}

.actor-table .checkbox-cell {
    text-align: center;
    width: 50px;
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

.actor-cachet {
    font-size: 12px;
    font-weight: 700;
    color: var(--text);
}

.actor-cachet small {
    font-weight: 400;
    color: var(--muted);
    font-size: 10px;
}

/* =========================================================
   SEQUENCES INPUT
========================================================= */

.sequences-input {
    width: 80px;
    height: 38px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: #fafafa;
    padding: 0 10px;
    color: var(--text);
    font-size: 13px;
    font-weight: 700;
    text-align: center;
    outline: none;
    transition: .2s;
}

.sequences-input:focus {
    background: white;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(229, 9, 20, .1);
}

.sequences-input:disabled {
    background: #f4f4f5;
    cursor: not-allowed;
    opacity: 0.5;
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
   EMPTY STATE
========================================================= */

.empty-state {
    text-align: center;
    padding: 30px 20px;
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
   RESPONSIVE
========================================================= */

@media (max-width: 992px) {
    .tournage-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .form-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .tournage-page {
        padding: 15px 0 35px;
    }
    .tournage-container {
        padding: 0 12px;
    }
    .tournage-header {
        padding: 18px;
    }
    .tournage-header h1 {
        font-size: 21px;
    }
    .tournage-header-icon {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
    }
    .actor-table thead {
        display: none;
    }
    .actor-table tbody tr {
        display: block;
        padding: 12px 0;
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
    }
    .actor-table tbody td::before {
        content: attr(data-label);
        font-size: 10px;
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
    .checkbox-cell {
        width: 100%;
    }
    .sequences-input {
        width: 70px;
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
    .tournage-header-left {
        gap: 12px;
    }
    .tournage-header-icon {
        display: none;
    }
}
</style>

<section class="tournage-page">
    <div class="tournage-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="tournage-header">
            <div class="tournage-header-left">
                <div class="tournage-header-icon">
                    <i class="fas fa-video"></i>
                </div>
                <div>
                    <div class="tournage-breadcrumb">
                        EVENPROD / SÉRIES / TOURNAGES
                    </div>
                    <h1><?= $tournageId ? 'Modifier' : 'Ajouter' ?> un tournage</h1>
                    <p>
                        <i class="fas fa-film" style="color:var(--accent);"></i>
                        Série : <strong><?= htmlspecialchars($serie['titre'] ?? 'Série introuvable') ?></strong>
                    </p>
                </div>
            </div>
            <div class="tournage-badge">
                <i class="fas fa-calendar-alt"></i>
                <?= $tournageId ? 'Modification' : 'Nouveau tournage' ?>
            </div>
        </header>

        <!-- =========================================================
        FORMULAIRE
        ========================================================= -->

        <div class="form-card">
            <div class="card-header">
                <div class="card-header-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h2>Informations du tournage</h2>
                    <p>Définissez la date, la référence et les acteurs participants</p>
                </div>
            </div>

            <div class="card-body">
                <form action="add_tourn?id_serie=<?= htmlspecialchars($serie['id']) ?><?= $tournageId ? '&id_tournage='.$tournageId : '' ?>" 
                      method="post" class="contactform contact_form" id="tournageForm">
                    
                    <input type="hidden" name="serie_id" value="<?= $serieId ?>">

                    <!-- =====================================================
                    DATE ET REFERENCE
                    ====================================================== -->

                    <div class="form-row">
                        <div class="form-group">
                            <label for="date">
                                <i class="fas fa-calendar-alt label-icon"></i>
                                Date du tournage
                                <span>*</span>
                            </label>
                            <input id="date" name="date" type="date" class="modern-input" required value="<?= htmlspecialchars($date ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="reference">
                                <i class="fas fa-hashtag label-icon"></i>
                                Référence
                                <span>*</span>
                            </label>
                            <input id="reference" name="reference" type="text" class="modern-input" 
                                   value="<?= htmlspecialchars($reference ?? '') ?>" readonly required>
                        </div>
                    </div>

                    <!-- =====================================================
                    ACTEURS
                    ====================================================== -->

                    <div class="actors-section-title">
                        <i class="fas fa-users"></i>
                        Acteurs participants
                        <span class="count">(<?= count($acteurs) ?> disponibles)</span>
                    </div>

                    <div class="table-responsive">
                        <?php if (!empty($acteurs)): ?>
                        <table class="actor-table" id="actorTable">
                            <thead>
                                <tr>
                                    <th style="text-align:center; width:50px;">Choix</th>
                                    <th>Acteur</th>
                                    <th>Date naissance</th>
                                    <th>Type</th>
                                    <th>Cachet</th>
                                    <th style="text-align:center;">Séquences</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($acteurs as $acteur): ?>
                                <tr>
                                    <td data-label="Choix" class="checkbox-cell">
                                        <input type="checkbox" name="acteurs[]" value="<?= $acteur['id'] ?>" 
                                               class="acteur-checkbox" data-id="<?= $acteur['id'] ?>"
                                               <?= in_array($acteur['id'], $acteursSelectionnesIds) ? 'checked' : '' ?>>
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
                                        <span class="actor-birth">
                                            <?= htmlspecialchars($acteur['type_acteur'] ?? 'Non renseignée') ?>
                                        </span>
                                    </td>
                                    <td data-label="Cachet">
                                        <span class="actor-cachet">
                                            <i class="fas fa-coins" style="font-size:10px; color:var(--muted);"></i>
                                            <?= number_format($acteur['cachet'] ?? 0, 0, ',', ' ') ?>
                                            <small>FCFA</small>
                                        </span>
                                    </td>
                                    <td data-label="Séquences" style="text-align:center;">
                                        <input type="number" 
                                               name="sequences[<?= $acteur['id'] ?>]" 
                                               class="sequences-input sequences-input-<?= $acteur['id'] ?>"
                                               placeholder="0"
                                               min="0" step="1"
                                               value="<?= $sequencesParActeur[$acteur['id']] ?? 0 ?>"
                                               <?= in_array($acteur['id'], $acteursSelectionnesIds) ? '' : 'disabled' ?>>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <h3>Aucun acteur disponible</h3>
                            <p>Aucun acteur n'est associé à cette série.</p>
                            <a href="serie_acteur?id_serie=<?= htmlspecialchars($serie['id']) ?>" class="btn btn-cancel" style="display:inline-flex; margin-top:10px;">
                                <i class="fas fa-user-plus"></i>
                                Ajouter des acteurs à la série
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- =====================================================
                    ACTIONS
                    ====================================================== -->

                    <?php if (!empty($acteurs)): ?>
                    <div class="form-actions">
                        <a href="tournages.php?id=<?= htmlspecialchars($serie['id']) ?>" class="btn btn-cancel">
                            <i class="fas fa-arrow-left"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-submit" id="submitBtn">
                            <i class="fas <?= $tournageId ? 'fa-save' : 'fa-plus' ?>"></i>
                            <span><?= $tournageId ? 'Modifier le tournage' : 'Enregistrer le tournage' ?></span>
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

    // Activer/Désactiver le champ séquences selon la checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const id = this.dataset.id;
            const sequencesInput = document.querySelector(`.sequences-input-${id}`);
            if (sequencesInput) {
                sequencesInput.disabled = !this.checked;
                if (!this.checked) {
                    sequencesInput.value = 0;
                }
            }
        });
    });

    // Validation avant soumission
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            const checked = document.querySelectorAll('.acteur-checkbox:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert('Veuillez sélectionner au moins un acteur pour ce tournage.');
                return;
            }

            // Vérifier que chaque acteur sélectionné a un nombre de séquences valide
            let hasError = false;
            checked.forEach(checkbox => {
                const id = checkbox.dataset.id;
                const sequencesInput = document.querySelector(`.sequences-input-${id}`);
                if (sequencesInput && (!sequencesInput.value || parseInt(sequencesInput.value) < 0)) {
                    hasError = true;
                }
            });

            if (hasError) {
                e.preventDefault();
                alert('Veuillez définir un nombre de séquences valide (0 ou plus) pour chaque acteur sélectionné.');
            }
        });
    }

});
</script>

<?php include '../../../includes/footer.php'; ?>