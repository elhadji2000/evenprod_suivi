<?php
include '../../../config/fonction.php';

$id = $_GET['id'] ?? 0;
$serie = getSerieById($id);

$acteurs = getActeursBySerieId($id);
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

.acteurs-page {
    min-height: 100vh;
    background: var(--background);
    padding: 25px 0 50px;
    color: var(--text);
}

.acteurs-container {
    max-width: 1500px;
    margin: auto;
    padding: 0 25px;
}

/* =========================================================
   HEADER
========================================================= */

.acteurs-header {
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

.acteurs-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.acteurs-header-avatar {
    width: 70px;
    height: 70px;
    flex: 0 0 70px;
    border-radius: 16px;
    overflow: hidden;
    border: 3px solid var(--accent);
    background: #f4f4f5;
}

.acteurs-header-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.acteurs-header-avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 30px;
}

.acteurs-breadcrumb {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 5px;
}

.acteurs-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: -.5px;
}

.acteurs-header p {
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
    background: var(--accent);
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
    background: var(--accent-hover);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(229, 9, 20, .3);
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
   LAYOUT
========================================================= */

.acteurs-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 380px;
    gap: 25px;
    align-items: start;
}

/* =========================================================
   LISTE DES ACTEURS
========================================================= */

.actors-list {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.actors-list-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.actors-list-header h2 {
    margin: 0;
    font-size: 15px;
    font-weight: 900;
}

.actors-list-header span {
    font-size: 12px;
    color: var(--muted);
    font-weight: 700;
}

.actors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
    padding: 20px 24px;
}

.actor-card {
    background: #fafafa;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 16px;
    cursor: pointer;
    transition: .3s;
    text-align: center;
    position: relative;
}

.actor-card:hover {
    transform: translateY(-3px);
    border-color: var(--accent);
    box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
}

.actor-card.active {
    border-color: var(--accent);
    background: #fef2f2;
    box-shadow: 0 0 0 3px rgba(229, 9, 20, .1);
}

.actor-card-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto 10px;
    border: 3px solid var(--border);
    background: #f4f4f5;
    transition: .3s;
}

.actor-card:hover .actor-card-avatar {
    border-color: var(--accent);
}

.actor-card-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.actor-card-avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 28px;
}

.actor-card-name {
    font-size: 14px;
    font-weight: 800;
    margin: 0;
}

.actor-card-role {
    font-size: 11px;
    color: var(--muted);
    margin: 2px 0 0;
}

.actor-card-cachet {
    display: inline-block;
    margin-top: 8px;
    padding: 3px 12px;
    border-radius: 12px;
    background: #f4f4f5;
    font-size: 10px;
    font-weight: 700;
    color: var(--text);
}

/* =========================================================
   DETAILS ACTEUR (SIDEBAR)
========================================================= */

.details-sidebar {
    position: sticky;
    top: 20px;
}

.details-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.details-header {
    padding: 18px 20px;
    border-bottom: 1px solid var(--border);
    background: #fafafa;
}

.details-header h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 900;
    display: flex;
    align-items: center;
    gap: 10px;
}

.details-header h3 i {
    color: var(--accent);
}

.details-body {
    padding: 20px;
}

.details-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto 16px;
    border: 4px solid var(--accent);
    background: #f4f4f5;
}

.details-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.details-avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4c4c7;
    font-size: 48px;
}

.details-name {
    text-align: center;
    font-size: 18px;
    font-weight: 900;
    margin: 0 0 4px;
}

.details-contact {
    text-align: center;
    font-size: 12px;
    color: var(--muted);
    margin: 0 0 16px;
}

.details-divider {
    border: 0;
    border-top: 1px solid var(--border);
    margin: 12px 0;
}

.details-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 8px 0;
    gap: 10px;
}

.details-row .label {
    font-size: 11px;
    font-weight: 700;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 6px;
}

.details-row .label i {
    font-size: 11px;
    color: var(--muted);
}

.details-row .value {
    font-size: 12px;
    font-weight: 600;
    text-align: right;
    word-break: break-word;
}

.details-row .value a {
    color: var(--accent);
    text-decoration: none;
    font-weight: 700;
}

.details-row .value a:hover {
    text-decoration: underline;
}

.details-actions {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border);
    display: flex;
    gap: 10px;
}

.btn-detail {
    flex: 1;
    height: 40px;
    border-radius: 10px;
    border: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: .2s;
}

.btn-detail.delete {
    background: #fef2f2;
    color: var(--danger);
}

.btn-detail.delete:hover {
    background: #fecaca;
}

.btn-detail.edit {
    background: #eff6ff;
    color: var(--info);
}

.btn-detail.edit:hover {
    background: #dbeafe;
}

/* =========================================================
   EMPTY STATE
========================================================= */

.empty-state {
    text-align: center;
    padding: 50px 20px;
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

@media (max-width: 1200px) {
    .acteurs-layout {
        grid-template-columns: 1fr;
    }
    .details-sidebar {
        position: static;
    }
    .actors-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    }
}

@media (max-width: 992px) {
    .acteurs-header {
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
    .acteurs-page {
        padding: 15px 0 35px;
    }
    .acteurs-container {
        padding: 0 12px;
    }
    .acteurs-header {
        padding: 18px;
    }
    .acteurs-header h1 {
        font-size: 21px;
    }
    .acteurs-header-avatar {
        width: 56px;
        height: 56px;
        flex-basis: 56px;
    }
    .actors-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        padding: 12px 16px;
        gap: 10px;
    }
    .actor-card {
        padding: 12px;
    }
    .actor-card-avatar {
        width: 56px;
        height: 56px;
    }
    .details-avatar {
        width: 90px;
        height: 90px;
    }
    .details-row {
        flex-direction: column;
        align-items: flex-start;
    }
    .details-row .value {
        text-align: left;
        width: 100%;
    }
    .details-actions {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .acteurs-header-left {
        gap: 12px;
    }
    .acteurs-header-avatar {
        display: none;
    }
    .header-actions {
        flex-direction: column;
    }
    .btn-add {
        width: 100%;
    }
    .header-count {
        width: 100%;
        justify-content: center;
    }
    .actors-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<section class="acteurs-page">
    <div class="acteurs-container">

        <!-- =========================================================
        HEADER
        ========================================================= -->

        <header class="acteurs-header">
            <div class="acteurs-header-left">
                <div class="acteurs-header-avatar">
                    <?php if (!empty($serie['logo']) && file_exists('../../../uploads/series/' . $serie['logo'])): ?>
                    <img src="../../../uploads/series/<?= htmlspecialchars($serie['logo']) ?>" alt="<?= htmlspecialchars($serie['titre']) ?>">
                    <?php else: ?>
                    <div class="acteurs-header-avatar-placeholder">
                        <i class="fas fa-film"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="acteurs-breadcrumb">
                        EVENPROD / SÉRIES / ACTEURS
                    </div>
                    <h1>Gestion des acteurs</h1>
                    <p>
                        <i class="fas fa-film" style="color:var(--accent);"></i>
                        Série : <strong><?= htmlspecialchars($serie['titre'] ?? 'Série introuvable') ?></strong>
                        &nbsp;&bull;&nbsp;
                        <i class="fas fa-coins"></i>
                        Budget : <?= number_format($serie['budget'] ?? 0, 0, ',', ' ') ?> FCFA
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <span class="header-count">
                    <i class="fas fa-users"></i>
                    <?= count($acteurs) ?> acteur<?= count($acteurs) > 1 ? 's' : '' ?>
                </span>
                <a href="serie_acteur?id_serie=<?= htmlspecialchars($serie['id'] ?? 0) ?>" class="btn-add">
                    <i class="fas fa-user-plus"></i>
                    Ajouter un acteur
                </a>
            </div>
        </header>

        <!-- =========================================================
        ALERTES
        ========================================================= -->

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="modern-alert success">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>Acteur retiré de la série avec succès !</div>
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
            <div>Erreur lors de la suppression de l'acteur.</div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- =========================================================
        LAYOUT
        ========================================================= -->

        <div class="acteurs-layout">

            <!-- =========================================================
            LISTE DES ACTEURS
            ========================================================= -->

            <div class="actors-list">
                <div class="actors-list-header">
                    <h2><i class="fas fa-users" style="color:var(--accent);"></i> Acteurs de la série</h2>
                    <span><?= count($acteurs) ?> acteur<?= count($acteurs) > 1 ? 's' : '' ?></span>
                </div>

                <?php if (!empty($acteurs)): ?>
                <div class="actors-grid">
                    <?php foreach ($acteurs as $acteur): ?>
                    <div class="actor-card" 
                         data-id="<?= htmlspecialchars($acteur['serie_acteur'] ?? $acteur['id']) ?>"
                         data-nom="<?= htmlspecialchars($acteur['nom']) ?>"
                         data-prenom="<?= htmlspecialchars($acteur['prenom']) ?>"
                         data-date="<?= htmlspecialchars($acteur['date_naissance'] ?? 'Non renseignée') ?>"
                         data-adresse="<?= htmlspecialchars($acteur['adresse'] ?? 'Non renseignée') ?>"
                         data-contact="<?= htmlspecialchars($acteur['contact'] ?? 'Non renseigné') ?>"
                         data-cachet="<?= number_format($acteur['cachet'] ?? 0, 0, ',', ' ') ?>"
                         data-photo="<?= htmlspecialchars($acteur['photo'] ?? '') ?>"
                         data-contrat="<?= htmlspecialchars($acteur['contrat'] ?? '') ?>"
                         onclick="selectActor(this)">
                        
                        <div class="actor-card-avatar">
                            <?php if (!empty($acteur['photo']) && file_exists('../../../uploads/photos/' . $acteur['photo'])): ?>
                            <img src="../../../uploads/photos/<?= htmlspecialchars($acteur['photo']) ?>" alt="<?= htmlspecialchars($acteur['prenom']) ?>">
                            <?php else: ?>
                            <div class="actor-card-avatar-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <h4 class="actor-card-name"><?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?></h4>
                        <p class="actor-card-role">
                            <i class="fas fa-phone" style="font-size:10px;"></i>
                            <?= htmlspecialchars($acteur['contact'] ?? 'Non renseigné') ?>
                        </p>
                        <span class="actor-card-cachet">
                            <i class="fas fa-coins"></i>
                            <?= number_format($acteur['cachet'] ?? 0, 0, ',', ' ') ?> FCFA
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>Aucun acteur</h3>
                    <p>Aucun acteur n'est encore associé à cette série.</p>
                    <a href="serie_acteur?id_serie=<?= htmlspecialchars($serie['id'] ?? 0) ?>" class="btn-add" style="display:inline-flex;">
                        <i class="fas fa-user-plus"></i>
                        Ajouter le premier acteur
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- =========================================================
            DETAILS DE L'ACTEUR (SIDEBAR)
            ========================================================= -->

            <aside class="details-sidebar">
                <div class="details-card">
                    <div class="details-header">
                        <h3>
                            <i class="fas fa-id-card"></i>
                            Détails de l'acteur
                        </h3>
                    </div>
                    <div class="details-body" id="detailsBody">
                        <div class="details-avatar" id="detailAvatar">
                            <div class="details-avatar-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <h4 class="details-name" id="detailName">Sélectionnez un acteur</h4>
                        <p class="details-contact" id="detailContact">Cliquez sur un acteur pour voir ses détails</p>

                        <hr class="details-divider">

                        <div class="details-row">
                            <span class="label"><i class="fas fa-calendar-alt"></i> Date de naissance</span>
                            <span class="value" id="detailDate">---</span>
                        </div>
                        <div class="details-row">
                            <span class="label"><i class="fas fa-map-marker-alt"></i> Adresse</span>
                            <span class="value" id="detailAdresse">---</span>
                        </div>
                        <div class="details-row">
                            <span class="label"><i class="fas fa-phone"></i> Téléphone</span>
                            <span class="value" id="detailTel">---</span>
                        </div>
                        <div class="details-row">
                            <span class="label"><i class="fas fa-coins"></i> Cachet</span>
                            <span class="value" id="detailCachet">---</span>
                        </div>
                        <div class="details-row" id="detailcontratRow" style="display:none;">
                            <span class="label"><i class="fas fa-file-pdf"></i> Contrat</span>
                            <span class="value"><a href="#" id="detailcontratLink" target="_blank">Télécharger</a></span>
                        </div>

                        <hr class="details-divider">

                        <div class="details-actions">
                            <a href="#" id="deleteLink" class="btn-detail delete" onclick="return confirm('Êtes-vous sûr de vouloir retirer cet acteur de la série ? Cette action est irréversible.')">
                                <i class="fas fa-trash"></i>
                                Retirer
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

        </div>

    </div>
</section>

<script>
// Sélection d'un acteur
function selectActor(element) {
    // Retirer la classe active de tous les acteurs
    document.querySelectorAll('.actor-card').forEach(c => c.classList.remove('active'));
    element.classList.add('active');

    // Récupérer les données
    const id = element.dataset.id;
    const nom = element.dataset.nom;
    const prenom = element.dataset.prenom;
    const date = element.dataset.date;
    const adresse = element.dataset.adresse;
    const contact = element.dataset.contact;
    const cachet = element.dataset.cachet;
    const photo = element.dataset.photo;
    const contrat = element.dataset.contrat;

    // Mettre à jour l'avatar
    const avatarContainer = document.getElementById('detailAvatar');
    if (photo) {
        avatarContainer.innerHTML = `<img src="../../../uploads/photos/${photo}" alt="${prenom}">`;
    } else {
        avatarContainer.innerHTML = `<div class="details-avatar-placeholder"><i class="fas fa-user"></i></div>`;
    }

    // Mettre à jour les informations
    document.getElementById('detailName').textContent = prenom + ' ' + nom;
    document.getElementById('detailContact').textContent = '📱 ' + (contact || 'Contact non renseigné');
    document.getElementById('detailDate').textContent = date || 'Non renseignée';
    document.getElementById('detailAdresse').textContent = adresse || 'Non renseignée';
    document.getElementById('detailTel').textContent = contact || 'Non renseigné';
    document.getElementById('detailCachet').textContent = cachet + ' FCFA';

    // Gestion du contrat
    const contratRow = document.getElementById('detailcontratRow');
    const contratLink = document.getElementById('detailcontratLink');
    if (contrat) {
        contratRow.style.display = 'flex';
        contratLink.href = '../../../uploads/contrats/' + contrat;
    } else {
        contratRow.style.display = 'none';
    }

    // Mettre à jour le lien de suppression
    const deleteLink = document.getElementById('deleteLink');
    const redirectUrl = "<?= $url_base ?>public/appManager/acteur/acteurs.php?id=<?= $id ?>";
    deleteLink.href = "<?= $url_base ?>public/appManager/delete.php?table=serie_acteur&id=" + id + "&redirect=" + encodeURIComponent(redirectUrl);

    // Mettre à jour l'URL sans recharger la page
    if (history.pushState) {
        history.pushState(null, null, '?id=<?= $id ?>&acteur=' + id);
    }
}

// Si un acteur est sélectionné dans l'URL au chargement
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const acteurId = urlParams.get('acteur');
    if (acteurId) {
        const cards = document.querySelectorAll('.actor-card');
        cards.forEach(card => {
            if (card.dataset.id == acteurId) {
                selectActor(card);
            }
        });
    }
});
</script>

<?php include '../../../includes/footer.php'; ?>