<?php
include '../../../config/fonction.php';

$serieId = $_GET['id'] ?? 0;
$serie = getSerieById($serieId);
$clients = getClients($connexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client = $_POST['client'];
    $serie_id = $_POST['serie'];
    $date = $_POST['date'];
    $description = $_POST['description'];

    $libelles = $_POST['libelle'];
    $quantites = $_POST['quantite'];
    $montants = $_POST['montant'];

    try {
        $facture_id = ajouterFacture(
            $connexion,
            $client,
            $serie_id,
            $date,
            $description,
            $libelles,
            $quantites,
            $montants
        );

        echo "<script>
                alert('Devis enregistré avec succès !');
                window.location.href='all_devis_fac?id=" . $serie_id . "';
              </script>";
    } catch (Exception $e) {
        die($e->getMessage());
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

.devis-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.devis-container {
    max-width: 1200px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.devis-header {
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

.devis-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.devis-header-icon {
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

.devis-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.devis-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.devis-header p {
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
   INVOICE TABLE
========================================================= */

.invoice-section-title {
    font-size: 14px;
    font-weight: 900;
    margin: 20px 0 12px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.invoice-section-title i {
    color: var(--accent);
}

.table-responsive {
    overflow-x: auto;
}

.invoice-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.invoice-table thead {
    background: #fafafa;
    border-bottom: 2px solid var(--border);
}

.invoice-table thead th {
    padding: 12px 14px;
    text-align: left;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--muted);
}

.invoice-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}

.invoice-table tbody tr:hover {
    background: #fafafa;
}

.invoice-table tbody td {
    padding: 10px 14px;
    vertical-align: middle;
}

.invoice-table .table-input {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 12px;
    background: #fafafa;
    transition: .2s;
    outline: none;
    color: var(--text);
}

.invoice-table .table-input:focus {
    background: white;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(229, 9, 20, .1);
}

.invoice-table .table-input.montant {
    font-weight: 700;
    color: var(--accent);
}

.btn-remove-line {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 0;
    background: #fef2f2;
    color: var(--danger);
    cursor: pointer;
    transition: .2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
}

.btn-remove-line:hover {
    background: #fecaca;
}

/* =========================================================
   ADD LINE BUTTON
========================================================= */

.add-line-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 10px;
    border: 1.5px dashed var(--border);
    background: #fafafa;
    color: var(--info);
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: .2s;
    margin-top: 12px;
}

.add-line-btn:hover {
    border-color: var(--accent);
    background: #fef2f2;
    color: var(--accent);
}

.add-line-btn i {
    font-size: 16px;
}

/* =========================================================
   TOTAL
========================================================= */

.total-section {
    margin-top: 16px;
    padding: 16px 20px;
    background: #fafafa;
    border-radius: 12px;
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 20px;
}

.total-section .label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--muted);
}

.total-section .value {
    font-size: 22px;
    font-weight: 900;
    color: var(--accent);
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
    .devis-header {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 768px) {
    .devis-page {
        padding: 15px 0 35px;
    }
    .devis-container {
        padding: 0 12px;
    }
    .devis-header {
        padding: 18px;
    }
    .devis-header h1 {
        font-size: 21px;
    }
    .devis-header-icon {
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
    .invoice-table thead {
        display: none;
    }
    .invoice-table tbody tr {
        display: block;
        padding: 12px 0;
        border-bottom: 2px solid var(--border);
    }
    .invoice-table tbody tr:last-child {
        border-bottom: 0;
    }
    .invoice-table tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 0;
    }
    .invoice-table tbody td::before {
        content: attr(data-label);
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--muted);
        letter-spacing: 0.3px;
    }
    .invoice-table tbody td:last-child::before {
        display: none;
    }
    .invoice-table .table-input {
        width: 60%;
    }
    .total-section {
        flex-direction: column;
        text-align: center;
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
    .devis-header-left {
        gap: 12px;
    }
    .devis-header-icon {
        display: none;
    }
}
</style>

<section class="devis-page">
    <div class="devis-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="devis-header">
            <div class="devis-header-left">
                <div class="devis-header-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <div class="devis-breadcrumb">
                        EVENPROD / SÉRIES / DEVIS
                    </div>
                    <h1>Ajouter un devis</h1>
                    <p>
                        <i class="fas fa-film" style="color:var(--accent);"></i>
                        Série : <strong><?= htmlspecialchars($serie['titre'] ?? 'Série introuvable') ?></strong>
                    </p>
                </div>
            </div>
            <div class="header-status">
                <i class="fas fa-plus-circle"></i>
                Nouveau devis
            </div>
        </header>

        <!-- =========================================================
        FORMULAIRE
        ========================================================= -->

        <div class="form-card">
            <div class="card-header">
                <div class="card-header-icon">
                    <i class="fas fa-pen"></i>
                </div>
                <div>
                    <h2>Informations du devis</h2>
                    <p>Remplissez tous les champs pour enregistrer le devis</p>
                </div>
            </div>

            <div class="card-body">
                <form action="add_devis" method="post" enctype="multipart/form-data" id="devisForm">

                    <input type="hidden" name="serie" value="<?= $serieId ?>">

                    <!-- =====================================================
                    INFORMATIONS GÉNÉRALES
                    ====================================================== -->

                    <div class="form-grid">
                        <!-- Client -->
                        <div class="form-group">
                            <label for="client">
                                <i class="fas fa-building label-icon"></i>
                                Client
                                <span>*</span>
                            </label>
                            <div class="select-wrapper">
                                <select id="client" name="client" class="modern-select" required>
                                    <option value="">-- Sélectionnez un client --</option>
                                    <?php foreach($clients as $t): ?>
                                    <option value="<?= $t['id'] ?>">
                                        <?= htmlspecialchars($t['nom']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="form-group">
                            <label for="date">
                                <i class="fas fa-calendar-alt label-icon"></i>
                                Date du devis
                                <span>*</span>
                            </label>
                            <input type="date" id="date" name="date" class="modern-input" required>
                        </div>

                        <!-- Description (pleine largeur) -->
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="description">
                                <i class="fas fa-align-left label-icon"></i>
                                Description
                                <span>*</span>
                            </label>
                            <textarea id="description" name="description" class="modern-textarea" 
                                      placeholder="Décrivez le devis..." required></textarea>
                        </div>
                    </div>

                    <!-- =====================================================
                    DÉTAILS DU DEVIS
                    ====================================================== -->

                    <div class="invoice-section-title">
                        <i class="fas fa-list"></i>
                        Détails du devis
                        <span style="font-size:11px; font-weight:400; color:var(--muted);">
                            (Ajoutez les lignes de prestations)
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="invoice-table" id="invoiceTable">
                            <thead>
                                <tr>
                                    <th style="width:40%;">Libellé</th>
                                    <th style="width:20%;">Quantité</th>
                                    <th style="width:30%;">Montant (FCFA)</th>
                                    <th style="width:10%;text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-label="Libellé">
                                        <input name="libelle[]" type="text" placeholder="Ex : Prestation..." 
                                               class="table-input" required>
                                    </td>
                                    <td data-label="Quantité">
                                        <input name="quantite[]" type="number" placeholder="1" 
                                               class="table-input" min="1" value="1">
                                    </td>
                                    <td data-label="Montant">
                                        <input name="montant[]" type="number" placeholder="0" 
                                               class="table-input montant" min="0" step="100" required>
                                    </td>
                                    <td data-label="Action" style="text-align:center;">
                                        <button type="button" class="btn-remove-line remove-line">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Ajouter une ligne -->
                    <div class="add-line-btn" id="addLine">
                        <i class="fas fa-plus-circle"></i>
                        Ajouter une ligne
                    </div>

                    <!-- Total -->
                    <div class="total-section">
                        <span class="label"><i class="fas fa-calculator"></i> Total du devis</span>
                        <span class="value" id="totalDevis">0 FCFA</span>
                    </div>

                    <!-- =====================================================
                    ACTIONS
                    ====================================================== -->

                    <div class="form-actions">
                        <a href="all_devis_fac?id=<?= htmlspecialchars($serieId) ?>" class="btn btn-cancel">
                            <i class="fas fa-arrow-left"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-submit" id="submitBtn">
                            <i class="fas fa-save"></i>
                            <span>Enregistrer le devis</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const tableBody = document.querySelector("#invoiceTable tbody");
    const addLineBtn = document.getElementById("addLine");
    const totalDevis = document.getElementById("totalDevis");
    const submitBtn = document.getElementById("submitBtn");
    const form = document.getElementById("devisForm");

    // Ajouter une ligne
    addLineBtn.addEventListener("click", function() {
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td data-label="Libellé">
                <input name="libelle[]" type="text" placeholder="Ex : Prestation..." 
                       class="table-input" required>
            </td>
            <td data-label="Quantité">
                <input name="quantite[]" type="number" placeholder="1" 
                       class="table-input" min="1" value="1">
            </td>
            <td data-label="Montant">
                <input name="montant[]" type="number" placeholder="0" 
                       class="table-input montant" min="0" step="100" required>
            </td>
            <td data-label="Action" style="text-align:center;">
                <button type="button" class="btn-remove-line remove-line">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(newRow);
        updateTotal();
    });

    // Suppression d'une ligne (délégation d'événements)
    document.addEventListener("click", function(e) {
        if (e.target.closest(".remove-line")) {
            const rows = tableBody.querySelectorAll("tr");
            if (rows.length > 1) {
                e.target.closest("tr").remove();
                updateTotal();
            } else {
                alert("Vous devez conserver au moins une ligne.");
            }
        }
    });

    // Mettre à jour le total
    function updateTotal() {
        let total = 0;
        const montantInputs = document.querySelectorAll('input[name="montant[]"]');
        montantInputs.forEach(input => {
            const val = parseFloat(input.value) || 0;
            total += val;
        });
        totalDevis.textContent = total.toLocaleString('fr-FR') + ' FCFA';
    }

    // Écouter les changements sur les montants
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('montant')) {
            updateTotal();
        }
    });

    // Validation avant soumission
    if (form) {
        form.addEventListener('submit', function(e) {
            const client = document.getElementById('client').value;
            const date = document.getElementById('date').value;
            const description = document.getElementById('description').value.trim();
            const libelles = document.querySelectorAll('input[name="libelle[]"]');
            
            if (!client) {
                e.preventDefault();
                alert('Veuillez sélectionner un client.');
                return;
            }

            if (!date) {
                e.preventDefault();
                alert('Veuillez sélectionner une date.');
                return;
            }

            if (!description) {
                e.preventDefault();
                alert('Veuillez saisir une description.');
                return;
            }

            // Vérifier que chaque ligne a un libellé et un montant
            let hasError = false;
            libelles.forEach((input, index) => {
                if (!input.value.trim()) {
                    hasError = true;
                }
                const montantInput = document.querySelectorAll('input[name="montant[]"]')[index];
                if (montantInput && (!montantInput.value || parseFloat(montantInput.value) <= 0)) {
                    hasError = true;
                }
            });

            if (hasError) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs de chaque ligne (libellé et montant).');
                return;
            }

            // Calculer le total
            let total = 0;
            document.querySelectorAll('input[name="montant[]"]').forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            if (total <= 0) {
                e.preventDefault();
                alert('Le total du devis doit être supérieur à 0.');
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

    // Initialiser le total
    updateTotal();

});
</script>

<?php include '../../../includes/footer.php'; ?>