<?php
session_start();

include '../../config/fonction.php';

/*
 * |--------------------------------------------------------------------------
 * | FONCTIONS
 * |--------------------------------------------------------------------------
 */

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/*
 * |--------------------------------------------------------------------------
 * | TRAITEMENT POST
 * |--------------------------------------------------------------------------
 */
/*
|--------------------------------------------------------------------------
| AJAX : RÉFÉRENCE
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'GET' &&
    isset($_GET['ajax']) &&
    $_GET['ajax'] === 'reference'
) {

    header('Content-Type: text/plain; charset=utf-8');
    
    echo genererReference();
    
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    /*
     * |--------------------------------------------------------------------------
     * | AJOUTER UN OUTIL
     * |--------------------------------------------------------------------------
     */

    if ($action === 'add') {
        $nom = trim($_POST['nom'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $proprietaire = trim($_POST['proprietaire'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $detenteur = trim($_POST['detenteur'] ?? '');
        $emplacement = trim($_POST['emplacement'] ?? '');
        $etat = trim($_POST['etat'] ?? 'disponible');

        $typesAutorises = ['materiel', 'costume'];
        $etatsAutorises = ['disponible', 'utilise', 'maintenance', 'perdu'];

        if ($nom === '') {
            $_SESSION['error'] = 'nom';
            redirectPage();
        }

        if (!in_array($type, $typesAutorises, true)) {
            $_SESSION['error'] = 'type';
            redirectPage();
        }

        if (!in_array($etat, $etatsAutorises, true)) {
            $_SESSION['error'] = 'etat';
            redirectPage();
        }

        $reference = genererReference();
        $date_enregistrement = date('Y-m-d H:i:s');
        $date_prise = ($etat === 'utilise') ? $date_enregistrement : null;

        $sql = 'INSERT INTO outils
                (nom, reference, type, description, proprietaire, detenteur, emplacement, etat, date_enregistrement, date_prise)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = mysqli_prepare($connexion, $sql);

        if (!$stmt) {
            $_SESSION['error'] = 'sql';
            redirectPage();
        }

        mysqli_stmt_bind_param(
            $stmt,
            'ssssssssss',
            $nom,
            $reference,
            $type,
            $description,
            $proprietaire,
            $detenteur,
            $emplacement,
            $etat,
            $date_enregistrement,
            $date_prise
        );

        $ok = mysqli_stmt_execute($stmt);
        $outil_id = mysqli_insert_id($connexion);
        mysqli_stmt_close($stmt);

        if (!$ok) {
            $_SESSION['error'] = 'insert';
            redirectPage();
        }

        enregistrerHistorique(
            $outil_id,
            'CREATION',
            null,
            $detenteur !== '' ? $detenteur : null,
            null,
            $emplacement !== '' ? $emplacement : null,
            null,
            $etat,
            "Création de l'outil"
        );

        $_SESSION['success'] = 'add';
        redirectPage();
    }

    /*
     * |--------------------------------------------------------------------------
     * | MODIFIER UN OUTIL
     * |--------------------------------------------------------------------------
     */

    if ($action === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $nom = trim($_POST['nom'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $proprietaire = trim($_POST['proprietaire'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $detenteur = trim($_POST['detenteur'] ?? '');
        $emplacement = trim($_POST['emplacement'] ?? '');
        $etat = trim($_POST['etat'] ?? 'disponible');

        $ancien = getOutil($id);

        if (!$ancien) {
            $_SESSION['error'] = 'notfound';
            redirectPage();
        }

        if ($nom === '') {
            $_SESSION['error'] = 'nom';
            redirectPage();
        }

        $typesAutorises = ['materiel', 'costume'];
        $etatsAutorises = ['disponible', 'utilise', 'maintenance', 'perdu'];

        if (!in_array($type, $typesAutorises, true)) {
            $_SESSION['error'] = 'type';
            redirectPage();
        }

        if (!in_array($etat, $etatsAutorises, true)) {
            $_SESSION['error'] = 'etat';
            redirectPage();
        }

        $date_prise = null;
        if ($etat === 'utilise') {
            $date_prise = !empty($ancien['date_prise']) ? $ancien['date_prise'] : date('Y-m-d H:i:s');
        }

        $sql = 'UPDATE outils SET
                nom = ?, type = ?, description = ?, proprietaire = ?, detenteur = ?,
                emplacement = ?, etat = ?, date_prise = ?
                WHERE id = ?';

        $stmt = mysqli_prepare($connexion, $sql);

        if (!$stmt) {
            $_SESSION['error'] = 'sql';
            redirectPage();
        }

        mysqli_stmt_bind_param(
            $stmt,
            'sssssssi',
            $nom,
            $type,
            $description,
            $proprietaire,
            $detenteur,
            $emplacement,
            $etat,
            $date_prise,
            $id
        );

        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if (!$ok) {
            $_SESSION['error'] = 'update';
            redirectPage();
        }

        $changement = ($ancien['detenteur'] ?? '') !== $detenteur ||
            ($ancien['emplacement'] ?? '') !== $emplacement ||
            ($ancien['etat'] ?? '') !== $etat;

        if ($changement) {
            enregistrerHistorique(
                $id,
                'MODIFICATION',
                $ancien['detenteur'] ?? null,
                $detenteur !== '' ? $detenteur : null,
                $ancien['emplacement'] ?? null,
                $emplacement !== '' ? $emplacement : null,
                $ancien['etat'] ?? null,
                $etat,
                "Modification / déplacement de l'outil"
            );
        }

        $_SESSION['success'] = 'edit';
        redirectPage();
    }

    /*
     * |--------------------------------------------------------------------------
     * | SUPPRIMER UN OUTIL
     * |--------------------------------------------------------------------------
     */

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $outil = getOutil($id);

        if (!$outil) {
            $_SESSION['error'] = 'notfound';
            redirectPage();
        }

        $sql = 'DELETE FROM outils WHERE id = ?';
        $stmt = mysqli_prepare($connexion, $sql);

        if (!$stmt) {
            $_SESSION['error'] = 'sql';
            redirectPage();
        }

        mysqli_stmt_bind_param($stmt, 'i', $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if (!$ok) {
            $_SESSION['error'] = 'delete';
            redirectPage();
        }

        $_SESSION['success'] = 'delete';
        redirectPage();
    }

    /*
     * |--------------------------------------------------------------------------
     * | MISE À JOUR DU SUIVI
     * |--------------------------------------------------------------------------
     */

    if ($action === 'tracking') {
        $id = (int) ($_POST['id'] ?? 0);
        $detenteur = trim($_POST['detenteur'] ?? '');
        $emplacement = trim($_POST['emplacement'] ?? '');
        $etat = trim($_POST['etat'] ?? 'disponible');

        $etatsAutorises = ['disponible', 'utilise', 'maintenance', 'perdu'];

        if ($id <= 0) {
            $_SESSION['error'] = 'tracking_id';
            redirectPage();
        }

        if ($emplacement === '') {
            $_SESSION['error'] = 'tracking_location';
            redirectPage();
        }

        if (!in_array($etat, $etatsAutorises, true)) {
            $_SESSION['error'] = 'tracking_etat';
            redirectPage();
        }

        $ancien = getOutil($id);

        if (!$ancien) {
            $_SESSION['error'] = 'notfound';
            redirectPage();
        }

        $date_prise = null;
        if ($etat === 'utilise') {
            $date_prise = !empty($ancien['date_prise']) ? $ancien['date_prise'] : date('Y-m-d H:i:s');
        }

        $sql = 'UPDATE outils SET detenteur = ?, emplacement = ?, etat = ?, date_prise = ? WHERE id = ?';
        $stmt = mysqli_prepare($connexion, $sql);

        if (!$stmt) {
            $_SESSION['error'] = 'sql';
            redirectPage();
        }

        mysqli_stmt_bind_param($stmt, 'ssssi', $detenteur, $emplacement, $etat, $date_prise, $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if (!$ok) {
            $_SESSION['error'] = 'update';
            redirectPage();
        }

        enregistrerHistorique(
            $id,
            'SUIVI',
            $ancien['detenteur'] ?? null,
            $detenteur !== '' ? $detenteur : null,
            $ancien['emplacement'] ?? null,
            $emplacement,
            $ancien['etat'] ?? null,
            $etat,
            'Mise à jour du suivi'
        );

        $_SESSION['success'] = 'tracking';
        redirectPage();
    }
}

/*
 * |--------------------------------------------------------------------------
 * | AJAX : HISTORIQUE (Uniquement pour la consultation)
 * |--------------------------------------------------------------------------
 */

if (
    $_SERVER['REQUEST_METHOD'] === 'GET' &&
    isset($_GET['ajax']) &&
    $_GET['ajax'] === 'historique'
) {
    header('Content-Type: application/json; charset=utf-8');

    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Identifiant invalide.'
        ]);
        exit;
    }

    $outil = getOutil($id);

    if (!$outil) {
        echo json_encode([
            'success' => false,
            'message' => 'Outil introuvable.'
        ]);
        exit;
    }

    $historique = [];

    $sql = 'SELECT * FROM outils_historique WHERE outil_id = ? ORDER BY date_action DESC, id DESC';
    $stmt = mysqli_prepare($connexion, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $historique[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }

    echo json_encode([
        'success' => true,
        'outil' => $outil,
        'historique' => $historique
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

/*
 * |--------------------------------------------------------------------------
 * | RÉCUPÉRER LES OUTILS
 * |--------------------------------------------------------------------------
 */

$outils = [];

$sql = 'SELECT * FROM outils ORDER BY id DESC';
$result = mysqli_query($connexion, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $outils[] = $row;
    }
}

/*
 * |--------------------------------------------------------------------------
 * | STATISTIQUES
 * |--------------------------------------------------------------------------
 */

$total = count($outils);
$disponibles = 0;
$utilises = 0;
$maintenance = 0;
$perdus = 0;

foreach ($outils as $outil) {
    switch ($outil['etat'] ?? '') {
        case 'disponible':
            $disponibles++;
            break;
        case 'utilise':
            $utilises++;
            break;
        case 'maintenance':
            $maintenance++;
            break;
        case 'perdu':
            $perdus++;
            break;
    }
}

// Récupérer les messages de session puis les effacer
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
clearMessages();

include '../../includes/header.php';
?>

<!-- LE RESTE DU CODE HTML ET JAVASCRIPT EST IDENTIQUE -->

<style>
:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --success: #16a34a;
    --warning: #d97706;
    --danger: #dc2626;
    --info: #0891b2;
    --dark: #1e293b;
    --muted: #64748b;
    --border: #e2e8f0;
    --background: #f8fafc;
    --white: #ffffff;
}


.outils-page {
    background: var(--background);
    min-height: 100vh;
    padding: 10px;
    font-size: 12px !important;
}

.outils-container {
    max-width: 1500px;
    margin: auto;
}

/* HEADER */
.outils-header {
    background: white;
    border-radius: 8px;
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
    margin-bottom: 10px;
}

.outils-header-left {
    display: flex;
    align-items: center;
    gap: 8px;
}

.outils-header-icon {
    width: 30px;
    height: 30px;
    background: #eff6ff;
    color: var(--primary);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px !important;
}

.outils-breadcrumb {
    font-size: 8px !important;
    color: var(--muted);
    margin-bottom: 2px;
}

.outils-header h1 {
    margin: 0;
    color: var(--dark);
    font-size: 12px !important;
    font-weight: 700;
}

.outils-header p {
    margin: 2px 0 0;
    color: var(--muted);
    font-size: 10px !important;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

.header-count {
    background: #f1f5f9;
    color: var(--dark);
    padding: 5px 8px;
    border-radius: 6px;
    font-size: 9px !important;
}

.btn-add {
    border: none;
    background: var(--primary);
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 12px !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none;
}

.btn-add i {
    font-size: 9px !important;
}

.btn-add:hover {
    background: var(--primary-dark);
}

/* STATISTIQUES */
.outils-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-bottom: 10px;
}

.outil-stat {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .03);
}

.outil-stat-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
}

.outil-stat-icon i {
    font-size: 10px !important;
}

.outil-stat strong {
    display: block;
    color: var(--dark);
    font-size: 12px !important;
}

.outil-stat span {
    color: var(--muted);
    font-size: 10px !important;
}

/* FILTRES */
.filters-bar {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 8px;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 8px;
    margin-bottom: 10px;
}

.filter-group label {
    display: block;
    font-size: 10px !important;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 3px;
}

.filter-group input,
.filter-group select {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: 5px;
    padding: 5px 8px;
    outline: none;
    background: white;
    font-size: 10px !important;
}

.filter-group input:focus,
.filter-group select:focus {
    border-color: var(--primary);
}

/* ALERT */
.modern-alert {
    background: #ecfdf5;
    color: #166534;
    border: 1px solid #bbf7d0;
    border-radius: 6px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
    font-size: 10px !important;
}

.modern-alert i {
    font-size: 10px !important;
}

.modern-alert.error {
    background: #fef2f2;
    color: #991b1b;
    border-color: #fecaca;
}

.alert-close {
    margin-left: auto;
    border: none;
    background: transparent;
    cursor: pointer;
}

/* TABLE */
.table-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid var(--border);
    box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
}

.table-header {
    padding: 8px 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border);
}

.table-header h2 {
    margin: 0;
    font-size: 9px !important;
    color: var(--dark);
}

.table-header h2 i {
    color: var(--primary);
    margin-right: 4px;
    font-size: 10px !important;
}

.table-header span {
    font-size: 9px !important;
    color: var(--muted);
}

.table-responsive {
    overflow-x: auto;
}

.outils-table {
    width: 100%;
    border-collapse: collapse;
}

.outils-table th {
    background: #f8fafc;
    color: #475569;
    font-size: 10px !important;
    text-transform: uppercase;
    padding: 6px 8px;
    text-align: left;
    white-space: nowrap;
}

.outils-table td {
    padding: 6px 8px;
    border-top: 1px solid #f1f5f9;
    font-size: 10px !important;
    color: #334155;
    vertical-align: middle;
}

.outils-table tr:hover {
    background: #fafcff;
}

.outils-table small {
    color: var(--muted);
    font-size: 9px !important;
}

.badge-reference {
    background: #eff6ff;
    color: #1d4ed8;
    border-radius: 4px;
    padding: 2px 6px;
    font-size: 9px !important;
    font-weight: 700;
}

.badge-type,
.badge-status {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 9px !important;
    font-weight: 700;
}

.badge-type.materiel {
    background: #eff6ff;
    color: #1d4ed8;
}

.badge-type.costume {
    background: #fdf2f8;
    color: #be185d;
}

.badge-status.disponible {
    background: #ecfdf5;
    color: #15803d;
}

.badge-status.utilise {
    background: #eff6ff;
    color: #1d4ed8;
}

.badge-status.maintenance {
    background: #fff7ed;
    color: #c2410c;
}

.badge-status.perdu {
    background: #fef2f2;
    color: #b91c1c;
}

.actions-cell {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 3px;
}

.btn-action {
    width: 24px;
    height: 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-action i {
    font-size: 8px !important;
}

.btn-action.history {
    background: #eef2ff;
    color: #4338ca;
}

.btn-action.location {
    background: #ecfeff;
    color: #0e7490;
}

.btn-action.edit {
    background: #eff6ff;
    color: #2563eb;
}

.btn-action.delete {
    background: #fef2f2;
    color: #dc2626;
}

.btn-action:hover {
    opacity: .75;
}

/* EMPTY */
.empty-state {
    padding: 30px 20px;
    text-align: center;
    color: var(--muted);
}

.empty-state i {
    font-size: 24px !important;
    margin-bottom: 10px;
}

.empty-state h3 {
    color: var(--dark);
    font-size: 10px !important;
}

.empty-state p {
    font-size: 10px !important;
}

/* MODALS */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .55);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 9999;
}

.modal-overlay.active {
    display: flex;
}

.custom-modal {
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    background: white;
    border-radius: 10px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, .25);
}

.modal-wide {
    max-width: 700px;
}

.modal-header {
    padding: 10px 15px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: var(--dark);
    font-size: 10px !important;
}

.modal-header h3 i {
    color: var(--primary);
    margin-right: 5px;
    font-size: 10px !important;
}

.modal-close {
    width: 24px;
    height: 24px;
    border: none;
    background: #f1f5f9;
    border-radius: 4px;
    cursor: pointer;
}

.modal-close i {
    font-size: 8px !important;
}

.modal-body {
    padding: 12px 15px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.form-group {
    margin-bottom: 8px;
}

.form-group label {
    display: block;
    font-size: 10px !important;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 3px;
}

.required {
    color: var(--danger);
}

.form-control {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: 5px;
    padding: 5px 8px;
    outline: none;
    box-sizing: border-box;
    font-size: 12px !important;
}

.form-control:focus {
    border-color: var(--primary);
}

.form-group small {
    font-size: 9px !important;
    color: var(--muted);
}

.form-actions-modal {
    display: flex;
    justify-content: flex-end;
    gap: 6px;
    padding-top: 5px;
}

.btn-modal {
    border: none;
    padding: 5px 12px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
    font-size: 10px !important;
}

.btn-modal.cancel {
    background: #f1f5f9;
    color: #475569;
}

.btn-modal.save {
    background: var(--primary);
    color: white;
}

.btn-modal.save:hover {
    background: var(--primary-dark);
}

/* SUIVI */
.tracking-info {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 8px;
    margin-bottom: 10px;
}

.tracking-info>strong {
    color: var(--dark);
    font-size: 9px !important;
}

.tracking-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-top: 6px;
}

.tracking-item small {
    display: block;
    color: var(--muted);
    font-size: 6px !important;
    margin-bottom: 2px;
}

.tracking-item strong {
    color: var(--dark);
    font-size: 8px !important;
}

/* HISTORIQUE */
.history-list {
    max-height: 350px;
    overflow-y: auto;
}

.history-item {
    position: relative;
    margin-left: 6px;
    padding: 0 0 10px 20px;
    border-left: 2px solid #e2e8f0;
}

.history-item:last-child {
    border-left-color: transparent;
}

.history-dot {
    position: absolute;
    left: -6px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--primary);
    border: 2px solid white;
}

.history-content {
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 8px;
}

.history-date {
    color: var(--muted);
    font-size: 8px !important;
}

.history-action {
    color: var(--dark);
    font-weight: 700;
    font-size: 8px !important;
    margin: 2px 0;
}

.history-change {
    color: #475569;
    font-size: 10px !important;
    line-height: 1.5;
}

.empty-history {
    text-align: center;
    padding: 20px;
    color: var(--muted);
}

.empty-history i {
    font-size: 16px !important;
}

.empty-history p {
    font-size: 10px !important;
}

/* TOAST */
.toast {
    position: fixed;
    right: 15px;
    bottom: 15px;
    background: white;
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 8px 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, .15);
    display: flex;
    align-items: center;
    gap: 6px;
    transform: translateY(80px);
    opacity: 0;
    transition: .3s;
    z-index: 10000;
    font-size: 8px !important;
}

.toast.show {
    transform: translateY(0);
    opacity: 1;
}

.toast i {
    font-size: 10px !important;
}

.toast .success {
    color: var(--success);
}

.toast .error {
    color: var(--danger);
}

/* RESPONSIVE */
@media(max-width: 1000px) {
    .outils-stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .filters-bar {
        grid-template-columns: 1fr 1fr;
    }
}

@media(max-width: 700px) {
    .outils-page {
        padding: 6px;
    }

    .outils-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .header-actions {
        width: 100%;
        flex-wrap: wrap;
    }

    .filters-bar {
        grid-template-columns: 1fr;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .tracking-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="outils-page">
    <div class="outils-container">

        <!-- =========================================================
     HEADER
========================================================= -->
        <div class="outils-header">
            <div class="outils-header-left">
                <div class="outils-header-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div>
                    <div class="outils-breadcrumb">EVENPROD / GESTION / OUTILS</div>
                    <h1>Suivi des outils</h1>
                    <p>Gérez et suivez les mouvements de vos outils.</p>
                </div>
            </div>
            <div class="header-actions">
                <span class="header-count">
                    <i class="fas fa-boxes"></i>
                    <?= $total ?> outil<?= $total > 1 ? 's' : '' ?>
                </span>
                <button type="button" class="btn-add" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Ajouter un outil
                </button>
            </div>
        </div>

        <!-- =========================================================
     STATISTIQUES
========================================================= -->
        <div class="outils-stats">
            <div class="outil-stat">
                <div class="outil-stat-icon"><i class="fas fa-boxes"></i></div>
                <div>
                    <strong><?= $total ?></strong>
                    <span>Total outils</span>
                </div>
            </div>
            <div class="outil-stat">
                <div class="outil-stat-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <strong><?= $disponibles ?></strong>
                    <span>Disponibles</span>
                </div>
            </div>
            <div class="outil-stat">
                <div class="outil-stat-icon"><i class="fas fa-hand-holding"></i></div>
                <div>
                    <strong><?= $utilises ?></strong>
                    <span>Utilisés</span>
                </div>
            </div>
            <div class="outil-stat">
                <div class="outil-stat-icon"><i class="fas fa-tools"></i></div>
                <div>
                    <strong><?= $maintenance ?></strong>
                    <span>Maintenance</span>
                </div>
            </div>
        </div>

        <!-- =========================================================
     MESSAGES
========================================================= -->
        <?php if ($success === 'add'): ?>
        <div class="modern-alert" id="alertMessage">
            <i class="fas fa-check-circle"></i>
            <span>L'outil a été ajouté avec succès.</span>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php elseif ($success === 'edit'): ?>
        <div class="modern-alert" id="alertMessage">
            <i class="fas fa-check-circle"></i>
            <span>L'outil a été modifié avec succès.</span>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php elseif ($success === 'delete'): ?>
        <div class="modern-alert" id="alertMessage">
            <i class="fas fa-check-circle"></i>
            <span>L'outil a été supprimé avec succès.</span>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php elseif ($success === 'tracking'): ?>
        <div class="modern-alert" id="alertMessage">
            <i class="fas fa-check-circle"></i>
            <span>Le suivi de l'outil a été mis à jour avec succès.</span>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php elseif ($error): ?>
        <div class="modern-alert error" id="alertMessage">
            <i class="fas fa-exclamation-circle"></i>
            <span>
                <?php
                $messages = [
                    'nom' => "Veuillez renseigner le nom de l'outil.",
                    'type' => "Le type d'outil est invalide.",
                    'etat' => "L'état de l'outil est invalide.",
                    'sql' => 'Une erreur SQL est survenue.',
                    'insert' => "Impossible d'ajouter l'outil.",
                    'update' => "Impossible de modifier l'outil.",
                    'delete' => "Impossible de supprimer l'outil.",
                    'notfound' => "L'outil demandé est introuvable.",
                    'tracking_id' => "Identifiant d'outil invalide.",
                    'tracking_location' => "Veuillez renseigner l'emplacement.",
                    'tracking_etat' => "L'état de l'outil est invalide."
                ];
                echo e($messages[$error] ?? 'Une erreur est survenue.');
                ?>
            </span>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- =========================================================
     FILTRES
========================================================= -->
        <div class="filters-bar">
            <div class="filter-group">
                <label><i class="fas fa-search"></i> Rechercher</label>
                <input type="text" id="searchInput" placeholder="Nom, référence, Détenteur..."
                    oninput="filterTable()">
            </div>
            <div class="filter-group">
                <label>Type</label>
                <select id="typeFilter" onchange="filterTable()">
                    <option value="">Tous</option>
                    <option value="materiel">Matériel</option>
                    <option value="costume">Costume</option>
                </select>
            </div>
            <div class="filter-group">
                <label>État</label>
                <select id="etatFilter" onchange="filterTable()">
                    <option value="">Tous</option>
                    <option value="disponible">Disponible</option>
                    <option value="utilise">Utilisé</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="perdu">Perdu</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Trier</label>
                <select id="sortFilter" onchange="filterTable()">
                    <option value="recent">Plus récent</option>
                    <option value="nom">Nom</option>
                    <option value="reference">Référence</option>
                    <option value="etat">État</option>
                </select>
            </div>
        </div>

        <!-- =========================================================
     TABLEAU
========================================================= -->
        <div class="table-card">
            <div class="table-header">
                <h2><i class="fas fa-list"></i> Liste des outils</h2>
                <span id="resultCount"><?= $total ?> résultat<?= $total > 1 ? 's' : '' ?></span>
            </div>

            <?php if (!empty($outils)): ?>
            <div class="table-responsive">
                <table class="outils-table" id="outilsTable">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Propiétaire</th>
                            <th>Détenteur</th>
                            <th>Emplacement</th>
                            <th>État</th>
                            <th>Date enregistrement</th>
                            <th>Date prise</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($outils as $outil): ?>
                        <?php
                        $id = (int) $outil['id'];
                        $etat = $outil['etat'] ?? '';
                        ?>
                        <tr data-id="<?= $id ?>">
                            <td data-label="Référence">
                                <span class="badge-reference"><?= e($outil['reference'] ?? '') ?></span>
                            </td>
                            <td data-label="Nom">
                                <strong><?= e($outil['nom'] ?? '') ?></strong>
                            </td>
                            <td data-label="Type">
                                <span class="badge-type <?= e($outil['type'] ?? '') ?>">
                                    <?= ($outil['type'] ?? '') === 'costume' ? 'Costume' : 'Matériel' ?>
                                </span>
                            </td>
                            <td data-label="Propriétaire"><?= e($outil['proprietaire'] ?: 'Non défini') ?></td>
                            <td data-label="Détenteur"><?= e($outil['detenteur'] ?: 'Non défini') ?></td>
                            <td data-label="Emplacement"><?= e($outil['emplacement'] ?: 'Non défini') ?></td>
                            <td data-label="État">
                                <span class="badge-status <?= e($etat) ?>">
                                    <?php
                                    $labels = ['disponible' => 'Disponible', 'utilise' => 'Utilisé', 'maintenance' => 'Maintenance', 'perdu' => 'Perdu'];
                                    echo e($labels[$etat] ?? $etat);
                                    ?>
                                </span>
                            </td>
                            <td data-label="Date enregistrement">
                                <?= !empty($outil['date_enregistrement']) ? e(date('d/m/Y H:i', strtotime($outil['date_enregistrement']))) : '-' ?>
                            </td>
                            <td data-label="Date prise">
                                <?= !empty($outil['date_prise']) ? e(date('d/m/Y H:i', strtotime($outil['date_prise']))) : 'Non pris' ?>
                            </td>
                            <td data-label="Actions">
                                <div class="actions-cell">
                                    <!-- HISTORIQUE -->
                                    <button type="button" class="btn-action history" title="Historique"
                                        onclick="openHistory(<?= $id ?>)">
                                        <i class="fas fa-history"></i>
                                    </button>
                                    <!-- SUIVI -->
                                    <button type="button" class="btn-action location" title="Suivre / déplacer"
                                        onclick="openTracking(<?= $id ?>)">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </button>
                                    <!-- MODIFIER -->
                                    <button type="button" class="btn-action edit" title="Modifier"
                                        onclick="openEdit(<?= $id ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <!-- SUPPRIMER -->
                                    <!-- <form method="POST" style="display:inline" onsubmit="return confirmDelete();">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <button type="submit" class="btn-action delete" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form> -->
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-tools"></i>
                <h3>Aucun outil enregistré</h3>
                <p>Commencez par ajouter votre premier outil.</p>
                <button type="button" class="btn-add" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Ajouter un outil
                </button>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- =========================================================
     MODAL AJOUT / MODIFICATION
========================================================= -->
<div class="modal-overlay" id="outilModal">
    <div class="custom-modal">
        <div class="modal-header">
            <h3 id="outilModalTitle"><i class="fas fa-plus"></i> Ajouter un outil</h3>
            <button type="button" class="modal-close" onclick="closeOutilModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" id="outilForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="formId">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nom de l'outil <span class="required">*</span></label>
                        <input type="text" class="form-control" name="nom" id="formNom" required
                            placeholder="Ex : Caméra Sony">
                    </div>
                    <div class="form-group">
                        <label>Référence</label>
                        <input type="text" class="form-control" id="formReference" readonly>
                        <small>Générée automatiquement.</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Type <span class="required">*</span></label>
                        <select class="form-control" name="type" id="formType" required>
                            <option value="materiel">Matériel</option>
                            <option value="costume">Costume</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Propriétaire <span class="required">*</span></label>
                        <select class="form-control" name="proprietaire" id="formProprietaire" required>
                            <option value="Evenprod">Evenprod</option>
                            <option value="Media Tv">Media Tv</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>État <span class="required">*</span></label>
                        <select class="form-control" name="etat" id="formEtat" required>
                            <option value="disponible">Disponible</option>
                            <option value="utilise">Utilisé</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="perdu">Perdu</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" name="description" id="formDescription" rows="3"
                        placeholder="Description de l'outil..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Détenteur</label>
                        <input type="text" class="form-control" name="detenteur" id="formdetenteur"
                            placeholder="Personne / service">
                    </div>
                    <div class="form-group">
                        <label>Emplacement</label>
                        <input type="text" class="form-control" name="emplacement" id="formEmplacement"
                            placeholder="Ex : Magasin">
                    </div>
                </div>

                <div class="form-actions-modal">
                    <button type="button" class="btn-modal cancel" onclick="closeOutilModal()">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn-modal save" id="submitButton">
                        <i class="fas fa-save"></i> <span id="submitText">Enregistrer</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================
     MODAL SUIVI
========================================================= -->
<div class="modal-overlay" id="trackingModal">
    <div class="custom-modal">
        <div class="modal-header">
            <h3><i class="fas fa-map-marker-alt"></i> Suivi de l'outil</h3>
            <button type="button" class="modal-close" onclick="closeTracking()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="tracking-info">
                <strong id="trackingName">-</strong>
                <div class="tracking-grid">
                    <div class="tracking-item">
                        <small>Référence</small>
                        <strong id="trackingReference">-</strong>
                    </div>
                    <div class="tracking-item">
                        <small>État actuel</small>
                        <strong id="trackingCurrentEtat">-</strong>
                    </div>
                    <div class="tracking-item">
                        <small>Emplacement actuel</small>
                        <strong id="trackingCurrentLocation">-</strong>
                    </div>
                </div>
            </div>

            <form method="POST" id="trackingForm">
                <input type="hidden" name="action" value="tracking">
                <input type="hidden" name="id" id="trackingId">

                <div class="form-group">
                    <label>Détenteur / Responsable</label>
                    <input type="text" class="form-control" name="detenteur" id="trackingdetenteur"
                        placeholder="Personne ou service">
                </div>

                <div class="form-group">
                    <label>Nouvel emplacement <span class="required">*</span></label>
                    <input type="text" class="form-control" name="emplacement" id="trackingEmplacement" required
                        placeholder="Ex : Plateau, Magasin, Tournage...">
                </div>

                <div class="form-group">
                    <label>État <span class="required">*</span></label>
                    <select class="form-control" name="etat" id="trackingEtat" required>
                        <option value="disponible">Disponible</option>
                        <option value="utilise">Utilisé</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="perdu">Perdu</option>
                    </select>
                    <small><i class="fas fa-info-circle"></i> Si vous passez à "Utilisé", la date de prise sera
                        enregistrée automatiquement.</small>
                </div>

                <div class="form-group">
                    <label>Commentaire / motif</label>
                    <textarea class="form-control" name="commentaire" id="trackingCommentaire" rows="3"
                        placeholder="Ex : Affecté au tournage..."></textarea>
                </div>

                <div class="form-actions-modal">
                    <button type="button" class="btn-modal cancel" onclick="closeTracking()">Annuler</button>
                    <button type="submit" class="btn-modal save">
                        <i class="fas fa-save"></i> Enregistrer le suivi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================
     MODAL HISTORIQUE (Version simplifiée - sans AJAX)
========================================================= -->
<div class="modal-overlay" id="historyModal">
    <div class="custom-modal modal-wide">
        <div class="modal-header">
            <h3><i class="fas fa-history"></i> Historique de l'outil</h3>
            <button type="button" class="modal-close" onclick="closeHistory()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" style="font-size:10px !important;">
            <div class="tracking-info" style="font-size:10px !important;">
                <strong id="historyName">-</strong>
                <div class="tracking-grid" style="font-size:8px !important;">
                    <div class="tracking-item">
                        <small>Référence</small>
                        <strong id="historyReference">-</strong>
                    </div>
                    <div class="tracking-item">
                        <small>Détenteur actuel</small>
                        <strong id="historyOwner">-</strong>
                    </div>
                    <div class="tracking-item">
                        <small>Emplacement actuel</small>
                        <strong id="historyLocation">-</strong>
                    </div>
                </div>
            </div>
            <div id="historyList" style="font-size:8px !important;">
                <div class="empty-history" style="font-size:10px !important;">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Chargement...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/*
|--------------------------------------------------------------------------
| DONNÉES PHP
|--------------------------------------------------------------------------
*/
const outils =
    <?= json_encode($outils, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const outilsMap = {};
outils.forEach(function(outil) {
    outilsMap[String(outil.id)] = outil;
});

const etatLabels = {
    disponible: 'Disponible',
    utilise: 'Utilisé',
    maintenance: 'Maintenance',
    perdu: 'Perdu'
};

/*
|--------------------------------------------------------------------------
| AJOUT
|--------------------------------------------------------------------------
*/
function openAddModal() {
    document.getElementById('outilForm').reset();
    document.getElementById('formAction').value = 'add';
    document.getElementById('formId').value = '';
    document.getElementById('formType').value = 'materiel';
    document.getElementById('formProprietaire').value = '';
    document.getElementById('formEtat').value = 'disponible';
    document.getElementById('outilModalTitle').innerHTML = '<i class="fas fa-plus"></i> Ajouter un outil';
    document.getElementById('submitText').textContent = 'Enregistrer';
    getNextReference();
    document.getElementById('outilModal').classList.add('active');
}

function getNextReference() {
    const input = document.getElementById('formReference');
    input.value = 'Génération...';

    fetch('add_outil.php?ajax=reference', {
            cache: 'no-store'
        })
        .then(response => response.text())
        .then(reference => {
            input.value = reference.trim();
        })
        .catch(() => {
            // Fallback si l'AJAX échoue
            const date = new Date();
            const ref = 'REF_' + String(date.getFullYear()).slice(-2) + '_' + String(outils.length + 1).padStart(4,
                '0');
            input.value = ref;
        });
}

/*
|--------------------------------------------------------------------------
| MODIFIER
|--------------------------------------------------------------------------
*/
function openEdit(id) {
    const outil = outilsMap[String(id)];
    if (!outil) {
        showToast('Outil introuvable.', 'error');
        return;
    }

    document.getElementById('formAction').value = 'edit';
    document.getElementById('formId').value = outil.id;
    document.getElementById('formNom').value = outil.nom || '';
    document.getElementById('formReference').value = outil.reference || '';
    document.getElementById('formType').value = outil.type || 'materiel';
    document.getElementById('formProprietaire').value = outil.proprietaire || '';
    document.getElementById('formDescription').value = outil.description || '';
    document.getElementById('formdetenteur').value = outil.detenteur || '';
    document.getElementById('formEmplacement').value = outil.emplacement || '';
    document.getElementById('formEtat').value = outil.etat || 'disponible';

    document.getElementById('outilModalTitle').innerHTML = '<i class="fas fa-edit"></i> Modifier l\'outil';
    document.getElementById('submitText').textContent = 'Modifier';
    document.getElementById('outilModal').classList.add('active');
}

function closeOutilModal() {
    document.getElementById('outilModal').classList.remove('active');
}

/*
|--------------------------------------------------------------------------
| SUIVI
|--------------------------------------------------------------------------
*/
function openTracking(id) {
    const outil = outilsMap[String(id)];
    if (!outil) {
        showToast('Outil introuvable.', 'error');
        return;
    }

    document.getElementById('trackingId').value = outil.id;
    document.getElementById('trackingName').textContent = outil.nom || '-';
    document.getElementById('trackingReference').textContent = outil.reference || '-';
    document.getElementById('trackingCurrentEtat').textContent = etatLabels[outil.etat] || outil.etat || '-';
    document.getElementById('trackingCurrentLocation').textContent = outil.emplacement || 'Non défini';
    document.getElementById('trackingdetenteur').value = outil.detenteur || '';
    document.getElementById('trackingEmplacement').value = outil.emplacement || '';
    document.getElementById('trackingEtat').value = outil.etat || 'disponible';
    document.getElementById('trackingCommentaire').value = '';

    document.getElementById('trackingModal').classList.add('active');
}

function closeTracking() {
    document.getElementById('trackingModal').classList.remove('active');
}

/*
|--------------------------------------------------------------------------
| HISTORIQUE (avec fetch pour récupérer l'historique)
|--------------------------------------------------------------------------
*/
function openHistory(id) {
    const outil = outilsMap[String(id)];
    if (!outil) {
        showToast('Outil introuvable.', 'error');
        return;
    }

    document.getElementById('historyName').textContent = outil.nom || '-';
    document.getElementById('historyReference').textContent = outil.reference || '-';
    document.getElementById('historyOwner').textContent = outil.detenteur || 'Non défini';
    document.getElementById('historyLocation').textContent = outil.emplacement || 'Non défini';

    document.getElementById('historyList').innerHTML = `
        <div class="empty-history">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Chargement de l'historique...</p>
        </div>
    `;

    document.getElementById('historyModal').classList.add('active');

    // Récupérer l'historique
    fetch('add_outil.php?ajax=historique&id=' + encodeURIComponent(id), {
            cache: 'no-store'
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.message);
            }
            renderHistory(data.historique);
        })
        .catch(error => {
            console.error('Erreur historique:', error);
            document.getElementById('historyList').innerHTML = `
                <div class="empty-history">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Impossible de charger l'historique.</p>
                </div>
            `;
        });
}

function renderHistory(items) {
    const container = document.getElementById('historyList');

    if (!items || items.length === 0) {
        container.innerHTML = `
            <div class="empty-history">
                <i class="fas fa-history fa-2x"></i>
                <p>Aucun historique pour cet outil.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = items.map(function(item) {
        const date = item.date_action ? new Date(item.date_action.replace(' ', 'T')).toLocaleString('fr-FR') :
            '-';
        const ancienEtat = etatLabels[item.ancien_etat] || item.ancien_etat || '-';
        const nouvelEtat = etatLabels[item.nouvel_etat] || item.nouvel_etat || '-';

        return `
            <div class="history-item">
                <span class="history-dot"></span>
                <div class="history-content">
                    <div class="history-date">${escapeHtml(date)}</div>
                    <div class="history-action">${escapeHtml(item.action || 'Mouvement')}</div>
                    <div class="history-change">
                        <strong>Détenteur :</strong> ${escapeHtml(item.ancien_detenteur || '-')} → ${escapeHtml(item.nouveau_detenteur || '-')}
                        <br>
                        <strong>Emplacement :</strong> ${escapeHtml(item.ancien_emplacement || '-')} → ${escapeHtml(item.nouvel_emplacement || '-')}
                        <br>
                        <strong>État :</strong> ${escapeHtml(ancienEtat)} → ${escapeHtml(nouvelEtat)}
                        ${item.commentaire ? `<br><strong>Motif :</strong> ${escapeHtml(item.commentaire)}` : ''}
                        ${item.utilisateur ? `<br><small><i class="fas fa-user"></i> ${escapeHtml(item.utilisateur)}</small>` : ''}
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function closeHistory() {
    document.getElementById('historyModal').classList.remove('active');
}

/*
|--------------------------------------------------------------------------
| RECHERCHE / FILTRE
|--------------------------------------------------------------------------
*/
function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase().trim();
    const type = document.getElementById('typeFilter').value;
    const etat = document.getElementById('etatFilter').value;

    const rows = Array.from(document.querySelectorAll('#outilsTable tbody tr'));
    let count = 0;

    rows.forEach(function(row) {
        const text = row.textContent.toLowerCase();
        const rowType = row.querySelector('[data-label="Type"]')?.textContent.toLowerCase() || '';
        const rowEtat = row.querySelector('[data-label="État"]')?.textContent.toLowerCase() || '';

        const searchOk = !search || text.includes(search);
        const typeOk = !type || rowType.includes(type === 'materiel' ? 'matériel' : 'costume');
        const etatOk = !etat || rowEtat.includes(etatLabels[etat].toLowerCase());

        const visible = searchOk && typeOk && etatOk;
        row.style.display = visible ? '' : 'none';
        if (visible) count++;
    });

    document.getElementById('resultCount').textContent = count + ' résultat' + (count > 1 ? 's' : '');
}

/*
|--------------------------------------------------------------------------
| SUPPRESSION
|--------------------------------------------------------------------------
*/
function confirmDelete() {
    return confirm('Voulez-vous vraiment supprimer cet outil ?\n\nCette opération est définitive.');
}

/*
|--------------------------------------------------------------------------
| TOAST
|--------------------------------------------------------------------------
*/
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');
    const text = document.getElementById('toastMessage');

    text.textContent = message;
    icon.className = type === 'error' ? 'fas fa-exclamation-circle error' : 'fas fa-check-circle success';

    toast.classList.add('show');
    clearTimeout(window.toastTimer);
    window.toastTimer = setTimeout(function() {
        toast.classList.remove('show');
    }, 3500);
}

/*
|--------------------------------------------------------------------------
| ESCAPE HTML
|--------------------------------------------------------------------------
*/
function escapeHtml(value) {
    return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
        '&quot;').replace(/'/g, '&#039;');
}

/*
|--------------------------------------------------------------------------
| FERMER LES MODALS EN CLIQUANT À L'EXTÉRIEUR
|--------------------------------------------------------------------------
*/
document.querySelectorAll('.modal-overlay').forEach(function(modal) {
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.remove('active');
        }
    });
});

/*
|--------------------------------------------------------------------------
| TOUCHE ESC
|--------------------------------------------------------------------------
*/
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(function(modal) {
            modal.classList.remove('active');
        });
    }
});

/*
|--------------------------------------------------------------------------
| FILTRE INITIAL
|--------------------------------------------------------------------------
*/
document.addEventListener('DOMContentLoaded', function() {
    filterTable();
});
</script>

<?php
include '../../includes/footer.php';
?>