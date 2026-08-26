<?php

$url_base = "http://localhost/projet_suivi/"; 
//$url_base = "https://evenapp.fr/";
// Connectez-vous à votre base de données MySQL
 function connexionBD()
{
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'evenprod_db';

    $connexion = mysqli_connect($host, $user, $pass, $db);

    if (!$connexion) {
        die('Erreur : Impossible de se connecter à la base distante. ' . mysqli_connect_error());
    }

    mysqli_set_charset($connexion, 'utf8mb4');
    return $connexion;
}

/* function connexionBD()
{
    $host = "localhost"; // Host distant
    $user = "u893234126_userep";
    $pass = "Pw@Ep@2025";
    $db   = "u893234126_bdep";

    $connexion = mysqli_connect($host, $user, $pass, $db);

    if (!$connexion) {
        die("Erreur : Impossible de se connecter à la base distante. " . mysqli_connect_error());
    }

    mysqli_set_charset($connexion, "utf8mb4");
    return $connexion;
} */

$connexion = connexionBD();

function login($username, $password)
{
    global $connexion;
    $hashed_password = sha1($password);

    // Requête SQL modifiée pour vérifier si l'utilisateur est actif
    $query = 'SELECT * FROM `users` WHERE `email` = ? AND `mot_de_passe` = ? AND `statut` = 1';

    // Préparer la requête pour éviter les injections SQL
    $stmt = $connexion->prepare($query);
    $stmt->bind_param('ss', $username, $hashed_password);
    $stmt->execute();

    // Récupérer les résultats
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Fermer la requête
    $stmt->close();

    return $user;  // Retourne les informations si l'utilisateur est trouvé et actif, sinon retourne null
}

/**
 * Récupère toutes les séries
 *
 * @return array Liste des séries sous forme de tableaux associatifs
 */
function getAllSeries()
{
    global $connexion;

    $series = [];
    $sql = 'SELECT id, titre, type, description,budget, logo FROM series ORDER BY id DESC';
    $result = mysqli_query($connexion, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $series[] = $row;
        }
    }

    return $series;
}

/**
 * Ajouter une nouvelle série
 */
function ajouterSerie(
    $titre,
    $type,
    $budget,
    $description,
    $logo,
    $transport,
    $decors,
    $reglement_acteurs,
    $accessoires,
    $hmc,
    $carburant,
    $pharmacie,
    $receptions,
    $autres_achats
) {
    global $connexion;

    $sql = '
        INSERT INTO series (
            titre,
            type,
            budget,
            description,
            logo,

            transport,
            decors,
            reglement_acteurs,
            accessoires,
            hmc,
            carburant,
            pharmacie,
            receptions,
            autres_achats
        )

        VALUES (
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?
        )
    ';

    $stmt = mysqli_prepare(
        $connexion,
        $sql
    );

    if (!$stmt) {
        return [
            'success' => false,
            'message' => mysqli_error($connexion)
        ];
    }

    mysqli_stmt_bind_param(
        $stmt,
        'ssdssddddddddd',
        $titre,
        $type,
        $budget,
        $description,
        $logo,
        $transport,
        $decors,
        $reglement_acteurs,
        $accessoires,
        $hmc,
        $carburant,
        $pharmacie,
        $receptions,
        $autres_achats
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        return [
            'success' => true,
            'message' => 'Série ajoutée avec succès.'
        ];
    }

    $error = mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);

    return [
        'success' => false,
        'message' => $error
    ];
}

/**
 * Modifier une série existante
 */
function modifierSerie(
    $serieId,
    $titre,
    $type,
    $budget,
    $description,
    $nouveauLogo = null,
    $transport = 0,
    $decors = 0,
    $reglement_acteurs = 0,
    $accessoires = 0,
    $hmc = 0,
    $carburant = 0,
    $pharmacie = 0,
    $receptions = 0,
    $autres_achats = 0
) {
    global $connexion;

    // =========================================================
    // Nettoyage des données
    // =========================================================

    $serieId = (int) $serieId;

    $titre = mysqli_real_escape_string(
        $connexion,
        $titre
    );

    $type = mysqli_real_escape_string(
        $connexion,
        $type
    );

    $description = mysqli_real_escape_string(
        $connexion,
        $description
    );

    // =========================================================
    // Conversion des montants
    // =========================================================

    $budget = max(0, floatval($budget));

    $transport = max(0, floatval($transport));

    $decors = max(0, floatval($decors));

    $reglement_acteurs = max(
        0,
        floatval($reglement_acteurs)
    );

    $accessoires = max(
        0,
        floatval($accessoires)
    );

    $hmc = max(
        0,
        floatval($hmc)
    );

    $carburant = max(
        0,
        floatval($carburant)
    );

    $pharmacie = max(
        0,
        floatval($pharmacie)
    );

    $receptions = max(
        0,
        floatval($receptions)
    );

    $autres_achats = max(
        0,
        floatval($autres_achats)
    );

    // =========================================================
    // Récupérer l'ancienne série
    // =========================================================

    $oldSerie = getSerieById($serieId);

    if (!$oldSerie) {
        return [
            'success' => false,
            'message' => 'Série introuvable'
        ];
    }

    // =========================================================
    // Gestion du logo
    // =========================================================

    $logo = $oldSerie['logo'];

    if ($nouveauLogo) {
        $uploadDir = '../uploads/series/';

        // Supprimer l'ancien logo
        if (
            !empty($logo) &&
            file_exists($uploadDir . $logo)
        ) {
            unlink(
                $uploadDir . $logo
            );
        }

        // Nouveau logo
        $logo = mysqli_real_escape_string(
            $connexion,
            $nouveauLogo
        );
    } else {
        $logo = mysqli_real_escape_string(
            $connexion,
            $logo
        );
    }

    // =========================================================
    // Recalcul du budget total
    // =========================================================
    //
    // Même si le formulaire envoie $budget,
    // on recalcule ici pour garantir la cohérence.
    //

    $budget =
        $transport
        + $decors
        + $reglement_acteurs
        + $accessoires
        + $hmc
        + $carburant
        + $pharmacie
        + $receptions
        + $autres_achats;

    // =========================================================
    // Mise à jour
    // =========================================================

    $sql = "
        UPDATE series
        SET
            titre = '$titre',
            type = '$type',
            budget = $budget,
            description = '$description',
            logo = '$logo',

            transport = $transport,
            decors = $decors,
            reglement_acteurs = $reglement_acteurs,
            accessoires = $accessoires,
            hmc = $hmc,
            carburant = $carburant,
            pharmacie = $pharmacie,
            receptions = $receptions,
            autres_achats = $autres_achats

        WHERE id = $serieId
    ";

    // =========================================================
    // Exécution
    // =========================================================

    if (mysqli_query($connexion, $sql)) {
        return [
            'success' => true
        ];
    }

    return [
        'success' => false,
        'message' => mysqli_error($connexion)
    ];
}

/**
 * Récupérer une série par son ID
 */
function getSerieById($serieId)
{
    global $connexion;
    $serieId = (int) $serieId;

    $sql = "SELECT * FROM series WHERE id = $serieId";
    $res = mysqli_query($connexion, $sql);
    return mysqli_fetch_assoc($res);
}

/**
 * Récupère la dernière série ajoutée
 *
 * @return array|null Tableau associatif de la série ou null si aucune série
 */
function getLastSerie()
{
    global $connexion;

    $sql = 'SELECT id, titre, type, budget, description, logo 
            FROM series 
            ORDER BY id DESC 
            LIMIT 1';

    $result = mysqli_query($connexion, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }

    return null;
}

function getActeursBySerieId($serieId)
{
    global $connexion;

    $serieId = (int) $serieId;

    $sql = "
        SELECT a.*, sa.id AS serie_acteur,type_acteur,contrat, sa.cachet, sa.type_acteur, sa.role
        FROM acteurs a
        INNER JOIN serie_acteur sa ON a.id = sa.acteur_id
        INNER JOIN series s ON s.id = sa.serie_id
        WHERE s.id = $serieId
    ";

    $result = mysqli_query($connexion, $sql);

    $acteurs = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $acteurs[] = $row;
        }
    }

    return $acteurs;
}

function getActeursNotInSerie($serieId)
{
    global $connexion;

    $serieId = (int) $serieId;

    $sql = "
        SELECT a.*
        FROM acteurs a
        WHERE a.id NOT IN (
            SELECT sa.acteur_id
            FROM serie_acteur sa
            WHERE sa.serie_id = $serieId
        )
    ";

    $result = mysqli_query($connexion, $sql);

    $acteurs = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $acteurs[] = $row;
        }
    }

    return $acteurs;
}

function addActeursToSerie($serieId, $acteurs, $cachets)
{
    global $connexion;

    foreach ($acteurs as $acteurId) {
        $acteurId = (int) $acteurId;
        $serieId = (int) $serieId;
        $cachet = isset($cachets[$acteurId]) ? (int) $cachets[$acteurId] : 0;

        $sql = "INSERT INTO serie_acteur (serie_id, acteur_id, cachet) 
                VALUES ($serieId, $acteurId, $cachet)";
        mysqli_query($connexion, $sql);
    }
}

function getDepenseByTournage($serieId, $tournageId)
{
    global $connexion;
    $serieId = (int) $serieId;
    $tournageId = (int) $tournageId;

    $sql = "SELECT SUM(montant) as total 
            FROM depenses 
            WHERE serie_id = $serieId AND tournage_id = $tournageId";
    $result = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function getTournagesBySerieId($serieId)
{
    global $connexion;
    $serieId = (int) $serieId;

    $sql = "SELECT * FROM tournages WHERE serie_id = $serieId ORDER BY id DESC";
    $result = mysqli_query($connexion, $sql);

    $tournages = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $tournages[] = $row;
        }
    }
    return $tournages;
}

function getEquipeCountByTournage($tournageId)
{
    global $connexion;
    $tournageId = (int) $tournageId;

    $sql = "SELECT COUNT(*) as total FROM tournage_acteur WHERE tournage_id = $tournageId";
    $result = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function getActeursByTournage($tournageId)
{
    global $connexion;
    $tournageId = (int) $tournageId;

    $sql = "SELECT a.id, a.nom, a.prenom,ta.sequence, a.date_naissance, a.contact, a.adresse, sa.cachet
            FROM acteurs a
            INNER JOIN tournage_acteur ta ON ta.acteur_id = a.id
            LEFT JOIN serie_acteur sa ON sa.acteur_id = a.id
            WHERE ta.tournage_id = $tournageId";
    $res = mysqli_query($connexion, $sql);

    $acteurs = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $acteurs[] = $row;  // retourne tout le tableau avec l'id
    }
    return $acteurs;
}

function getTournageById($tournageId)
{
    global $connexion;
    $tournageId = (int) $tournageId;

    $sql = "SELECT * FROM tournages WHERE id = $tournageId LIMIT 1";
    $result = mysqli_query($connexion, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        return null;
    }
}

function generateTournageReference()
{
    global $connexion;
    $year = date('y');  // année actuelle sur 2 chiffres

    // Récupérer l'ID max de la table tournages
    $sql = 'SELECT MAX(id) as max_id FROM tournages';
    $result = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($result);
    $lastId = $row['max_id'] ?? 0;

    $num = $lastId + 1;  // On ajoute 1 à l'ID max pour la nouvelle référence

    // Formater en 3 chiffres
    $numFormatted = str_pad($num, 3, '0', STR_PAD_LEFT);

    return "RF-$year-$numFormatted";
}

function ajouterTournage(
    $serieId,
    $date,
    $reference,
    $acteursIds,
    $sequences = []
) {
    global $connexion;

    $serieId = (int) $serieId;

    $date = mysqli_real_escape_string(
        $connexion,
        $date
    );

    $reference = mysqli_real_escape_string(
        $connexion,
        $reference
    );

    // =========================================================
    // 1. Ajouter le tournage
    // =========================================================

    $sqlTournage = "
        INSERT INTO tournages (
            serie_id,
            date_tournage,
            reference
        )
        VALUES (
            $serieId,
            '$date',
            '$reference'
        )
    ";

    if (!mysqli_query($connexion, $sqlTournage)) {
        return [
            'success' => false,
            'message' =>
                'Erreur insertion tournage : '
                . mysqli_error($connexion)
        ];
    }

    $tournageId = mysqli_insert_id($connexion);

    // =========================================================
    // 2. Ajouter les acteurs du tournage
    // =========================================================

    foreach ($acteursIds as $acteurId) {
        $acteurId = (int) $acteurId;

        // -----------------------------------------------------
        // Nombre de séquences jouées
        // -----------------------------------------------------

        $sequence = isset($sequences[$acteurId])
            ? (int) $sequences[$acteurId]
            : 0;

        // Empêcher une valeur négative
        $sequence = max(0, $sequence);

        // =====================================================
        // Récupérer les informations de l'acteur dans la série
        // =====================================================

        $sqlActeurSerie = "
            SELECT
                cachet,
                type_acteur
            FROM serie_acteur
            WHERE serie_id = $serieId
              AND acteur_id = $acteurId
            LIMIT 1
        ";

        $res = mysqli_query(
            $connexion,
            $sqlActeurSerie
        );

        if (!$res) {
            return [
                'success' => false,
                'message' =>
                    'Erreur récupération acteur : '
                    . mysqli_error($connexion)
            ];
        }

        $row = mysqli_fetch_assoc($res);

        if (!$row) {
            return [
                'success' => false,
                'message' =>
                    "L'acteur ID $acteurId "
                    . "n'est pas associé à cette série."
            ];
        }

        $cachet = isset($row['cachet'])
            ? (float) $row['cachet']
            : 0;

        $typeActeur = strtolower(
            trim($row['type_acteur'] ?? '')
        );

        // =====================================================
        // Ajouter l'acteur au tournage
        // =====================================================

        $sqlTA = "
            INSERT INTO tournage_acteur (
                tournage_id,
                acteur_id,
                sequence
            )
            VALUES (
                $tournageId,
                $acteurId,
                $sequence
            )
        ";

        if (!mysqli_query($connexion, $sqlTA)) {
            return [
                'success' => false,
                'message' =>
                    'Erreur insertion acteur tournage : '
                    . mysqli_error($connexion)
            ];
        }

        // =====================================================
        // CRÉER UNE DÉPENSE POUR CHAQUE ACTEUR JOURNALIER
        // =====================================================

        if (
            $typeActeur === 'journalier' &&
            $cachet > 0
        ) {
            $sqlDepense = "
                INSERT INTO depenses (
                    serie_id,
                    tournage_id,
                    acteur_id,
                    type_depense,
                    montant,
                    date_depense
                )
                VALUES (
                    $serieId,
                    $tournageId,
                    $acteurId,
                    'reglement_acteur',
                    $cachet,
                    '$date'
                )
            ";

            if (!mysqli_query($connexion, $sqlDepense)) {
                return [
                    'success' => false,
                    'message' =>
                        'Erreur insertion dépense acteur : '
                        . mysqli_error($connexion)
                ];
            }
        }
    }

    // =========================================================
    // 3. Retour
    // =========================================================

    return [
        'success' => true,
        'tournage_id' => $tournageId
    ];
}

function modifierTournage($tournageId, $serieId, $date, $reference, $acteursIds)
{
    global $connexion;

    $tournageId = (int) $tournageId;
    $serieId = (int) $serieId;
    $date = mysqli_real_escape_string($connexion, $date);
    $reference = mysqli_real_escape_string($connexion, $reference);

    // 1️⃣ Mettre à jour le tournage
    $sqlUpdate = "UPDATE tournages 
                  SET date_tournage = '$date', reference = '$reference' 
                  WHERE id = $tournageId";
    if (!mysqli_query($connexion, $sqlUpdate)) {
        return ['success' => false, 'message' => 'Erreur mise à jour tournage : ' . mysqli_error($connexion)];
    }

    // 2️⃣ Supprimer les anciens acteurs du tournage
    $sqlDelete = "DELETE FROM tournage_acteur WHERE tournage_id = $tournageId";
    mysqli_query($connexion, $sqlDelete);

    // 3️⃣ Réinsérer les nouveaux acteurs et calculer le total des cachets
    $totalCachet = 0;
    foreach ($acteursIds as $acteurId) {
        $acteurId = (int) $acteurId;

        // Récupérer le cachet depuis serie_acteur
        $sqlCachet = "SELECT cachet FROM serie_acteur WHERE serie_id = $serieId AND acteur_id = $acteurId";
        $res = mysqli_query($connexion, $sqlCachet);
        $row = mysqli_fetch_assoc($res);
        $cachet = $row['cachet'] ?? 0;
        $totalCachet += $cachet;

        // Insérer dans tournage_acteur
        $sqlTA = "INSERT INTO tournage_acteur (tournage_id, acteur_id) VALUES ($tournageId, $acteurId)";
        mysqli_query($connexion, $sqlTA);
    }

    // 4️⃣ Mettre à jour la dépense "cachet" du tournage
    $sqlDepenseCheck = "SELECT id FROM depenses WHERE tournage_id = $tournageId AND type_depense = 'cachet'";
    $resDepense = mysqli_query($connexion, $sqlDepenseCheck);
    if (mysqli_num_rows($resDepense) > 0) {
        // Mise à jour
        $rowDepense = mysqli_fetch_assoc($resDepense);
        $depenseId = $rowDepense['id'];
        $sqlUpdateDepense = "UPDATE depenses SET montant = $totalCachet, date_depense = '$date' WHERE id = $depenseId";
        mysqli_query($connexion, $sqlUpdateDepense);
    } else if ($totalCachet > 0) {
        // Nouvelle insertion si inexistante
        $sqlInsertDepense = "INSERT INTO depenses (serie_id, tournage_id, type_depense, montant, date_depense) 
                             VALUES ($serieId, $tournageId, 'cachet', $totalCachet, '$date')";
        mysqli_query($connexion, $sqlInsertDepense);
    }

    return ['success' => true, 'tournage_id' => $tournageId];
}

function getDepensesBySerie($serieId)
{
    global $connexion;

    $serieId = (int) $serieId;

    $sql = "
        SELECT 
            d.id,
            d.libelle,
            d.type_depense,
            d.date_depense,
            d.montant,
            d.justificatif,
            d.acteur_id,

            t.reference AS tournage_reference,

            /*
             * BÉNÉFICIAIRE
             * Si acteur_id existe :
             * prénom + nom de l'acteur
             * Sinon :
             * beneficiaire enregistré dans depenses
             */
            CASE
                WHEN d.acteur_id IS NOT NULL
                     AND atr.id IS NOT NULL
                THEN TRIM(
                    CONCAT(
                        COALESCE(atr.prenom, ''),
                        ' ',
                        COALESCE(atr.nom, '')
                    )
                )

                ELSE COALESCE(
                    d.beneficiaire,
                    ''
                )
            END AS beneficiaire,


            /*
             * TÉLÉPHONE DU BÉNÉFICIAIRE
             * Si acteur_id existe :
             * contact de l'acteur
             * Sinon :
             * telephone_beneficiaire enregistré dans depenses
             */
            CASE
                WHEN d.acteur_id IS NOT NULL
                     AND atr.id IS NOT NULL
                THEN COALESCE(
                    atr.contact,
                    ''
                )

                ELSE COALESCE(
                    d.telephone_beneficiaire,
                    ''
                )
            END AS telephone_beneficiaire


        FROM depenses d

        LEFT JOIN tournages t
            ON d.tournage_id = t.id

        LEFT JOIN acteurs atr
            ON atr.id = d.acteur_id

        WHERE d.serie_id = $serieId

        ORDER BY d.id DESC
    ";

    $result = mysqli_query($connexion, $sql);

    $depenses = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $depenses[] = $row;
        }
    }

    return $depenses;
}

function ajouterDepense($serieId, $tournageId, $type, $montant, $description, $beneficiaire, $telephone_beneficiaire, $justificatif = null)
{
    global $connexion;

    $serieId = (int) $serieId;
    $tournageId = $tournageId !== '' ? (int) $tournageId : 'NULL';
    $type = mysqli_real_escape_string($connexion, $type);
    $montant = floatval($montant);
    $description = mysqli_real_escape_string($connexion, $description);
    $beneficiaire = mysqli_real_escape_string($connexion, $beneficiaire);
    $telephone_beneficiaire = mysqli_real_escape_string($connexion, $telephone_beneficiaire);
    $justificatif = $justificatif ? "'" . mysqli_real_escape_string($connexion, $justificatif) . "'" : 'NULL';

    $sql = "INSERT INTO depenses (serie_id, tournage_id, type_depense, beneficiaire, telephone_beneficiaire, montant, libelle, justificatif, date_depense)
            VALUES ($serieId, $tournageId, '$type', '$beneficiaire', '$telephone_beneficiaire', $montant, '$description', $justificatif, NOW())";

    if (!mysqli_query($connexion, $sql)) {
        return ['success' => false, 'message' => mysqli_error($connexion)];
    }

    return ['success' => true, 'depense_id' => mysqli_insert_id($connexion)];
}

function ajouterPartenaire($ninea, $nom, $email, $contact, $adresse, $logoFile)
{
    global $connexion;  // ta connexion MySQLi

    // --- 1. Gestion de l'upload du logo ---
    $logo = null;
    if ($logoFile && $logoFile['error'] === 0) {
        $ext = strtolower(pathinfo($logoFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $uploadDir = __DIR__ . '/../uploads/logos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $logoName = 'logo_' . time() . '.' . $ext;
            $destination = $uploadDir . $logoName;

            if (move_uploaded_file($logoFile['tmp_name'], $destination)) {
                $logo = $logoName;
            }
        }
    }

    // --- 2. Insertion dans la base ---
    $stmt = mysqli_prepare(
        $connexion,
        'INSERT INTO clients (ninea, nom, email, contact, adresse, logo) VALUES (?, ?, ?, ?, ?, ?)'
    );

    mysqli_stmt_bind_param($stmt, 'ssssss', $ninea, $nom, $email, $contact, $adresse, $logo);
    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $success;
}

function modifierPartenaire($id, $ninea, $nom, $email, $contact, $adresse, $logoFile = null)
{
    global $connexion;

    // Récupérer l'ancien partenaire pour savoir quel logo supprimer
    $id = (int) $id;
    $res = mysqli_query($connexion, "SELECT logo FROM clients WHERE id=$id");
    $old = mysqli_fetch_assoc($res);

    $logo = $old['logo'];  // par défaut garder l'ancien logo

    // S'il y a un nouveau fichier
    if ($logoFile && $logoFile['error'] === 0) {
        $ext = strtolower(pathinfo($logoFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $uploadDir = __DIR__ . '/../uploads/logos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Supprimer l’ancien logo
            if ($logo && file_exists($uploadDir . $logo)) {
                unlink($uploadDir . $logo);
            }

            $logoName = 'logo_' . time() . '.' . $ext;
            $destination = $uploadDir . $logoName;
            if (move_uploaded_file($logoFile['tmp_name'], $destination)) {
                $logo = $logoName;
            }
        }
    }

    // Mise à jour
    $stmt = mysqli_prepare(
        $connexion,
        'UPDATE clients SET ninea=?, nom=?, email=?, contact=?, adresse=?, logo=? WHERE id=?'
    );
    mysqli_stmt_bind_param($stmt, 'ssssssi', $ninea, $nom, $email, $contact, $adresse, $logo, $id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $success;
}

function getClients($connexion)
{
    $sql = 'SELECT id, ninea, nom, logo, email, contact, adresse, created_at 
            FROM clients 
            ORDER BY id DESC';
    $result = mysqli_query($connexion, $sql);

    $clients = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $clients[] = $row;
        }
    }
    return $clients;
}

function ajouterFacture($connexion, $client, $serie_id, $date, $description, $libelles, $quantites, $montants)
{
    try {
        // Calcul du total
        $total = 0;
        foreach ($montants as $m) {
            $total += (float) $m;
        }

        // Démarrer transaction
        $connexion->begin_transaction();

        // 1. Générer la référence automatique
        $result = $connexion->query("SELECT COUNT(*) AS total FROM factures WHERE YEAR(date_facture) = YEAR('$date')");
        $row = $result->fetch_assoc();
        $nextId = $row['total'] + 1;
        $reference = 'REF-' . date('y', strtotime($date)) . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        // 2. Enregistrer la facture avec la référence
        $sql = "INSERT INTO factures (client_id, serie_id, date_facture, description, total, reference) 
                VALUES ('$client', '$serie_id', '$date', '$description', '$total', '$reference')";
        if (!$connexion->query($sql)) {
            throw new Exception('Erreur facture : ' . $connexion->error);
        }

        // ID facture
        $facture_id = $connexion->insert_id;

        // 3. Enregistrer chaque ligne
        for ($i = 0; $i < count($libelles); $i++) {
            $lib = $connexion->real_escape_string(trim($libelles[$i]));
            /* $pu  = $connexion->real_escape_string(trim($prixUnitaires[$i])); */
            $qte = (int) $quantites[$i];
            $mt = (float) $montants[$i];

            $sql2 = "INSERT INTO designation (facture_id, libelle, quantite, montant) 
                     VALUES ('$facture_id', '$lib', '$qte', '$mt')";
            if (!$connexion->query($sql2)) {
                throw new Exception('Erreur designation : ' . $connexion->error);
            }
        }

        // Valider transaction
        $connexion->commit();

        return $facture_id;  // retourne l'ID facture
    } catch (Exception $e) {
        $connexion->rollback();
        throw new Exception("Erreur lors de l'enregistrement : " . $e->getMessage());
    }
}

function getFacturesBySerieId($connexion, $serieId)
{
    $sql = 'SELECT f.id, f.type, f.date_facture, f.reference, f.description, f.total, 
                   c.nom AS client_nom
            FROM factures f
            INNER JOIN clients c ON f.client_id = c.id
            WHERE f.serie_id = ' . (int) $serieId . '
            ORDER BY f.date_facture DESC';

    $result = $connexion->query($sql);
    $factures = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Récupérer les désignations de chaque facture
            $designations = [];
            $sql2 = 'SELECT libelle, prix_unitaire, quantite, montant
                     FROM designation WHERE facture_id = ' . (int) $row['id'];
            $res2 = $connexion->query($sql2);
            if ($res2 && $res2->num_rows > 0) {
                while ($d = $res2->fetch_assoc()) {
                    $designations[] = $d;
                }
            }
            $row['designations'] = $designations;
            $factures[] = $row;
        }
    }
    return $factures;
}

function getFacturesWithPaiementsBySerie($connexion, $serieId)
{
    $factures = [];
    $sql = 'SELECT f.id, f.description, f.reference, f.total, c.nom, c.ninea, c.contact
            FROM factures f
            JOIN clients c ON f.client_id=c.id
            WHERE f.serie_id = ' . (int) $serieId . " AND f.type = 'facture'";
    $res = $connexion->query($sql);

    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            // Somme des paiements
            $sql2 = 'SELECT SUM(montant) AS total_verse FROM paiements WHERE facture_id = ' . (int) $row['id'];
            $res2 = $connexion->query($sql2);
            $totalVerse = ($res2 && $res2->num_rows > 0) ? $res2->fetch_assoc()['total_verse'] : 0;

            $row['verse'] = (float) $totalVerse;
            $row['reste'] = $row['total'] - $row['verse'];
            $factures[] = $row;
        }
    }
    return $factures;
}

function getPaiementsByFactureId($connexion, $factureId)
{
    $factureId = (int) $factureId;  // sécurisation
    $sql = "SELECT p.id, p.type, p.montant, p.reference, p.piece_jointe
            FROM paiements p
            INNER JOIN factures f ON f.id = p.facture_id
            WHERE f.id = $factureId";

    $result = mysqli_query($connexion, $sql);

    $paiements = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $paiements[] = $row;
        }
    }

    return $paiements;
}

function getFactureWithPaiements($connexion, $factId)
{
    $sql = 'SELECT f.id, f.description, f.reference, f.total, c.nom AS client_nom, c.ninea, c.contact
            FROM factures f
            JOIN clients c ON f.client_id = c.id
            WHERE f.id = ' . (int) $factId . " AND f.type = 'facture'
            LIMIT 1";

    $res = $connexion->query($sql);

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();

        // Somme des paiements liés à cette facture
        $sql2 = 'SELECT SUM(montant) AS total_verse 
                 FROM paiements 
                 WHERE facture_id = ' . (int) $row['id'];
        $res2 = $connexion->query($sql2);
        $totalVerse = ($res2 && $res2->num_rows > 0) ? (float) $res2->fetch_assoc()['total_verse'] : 0;

        $row['verse'] = $totalVerse;
        $row['reste'] = $row['total'] - $totalVerse;

        return $row;
    }

    return null;
}

function getFactureDetails($connexion, $facture_id)
{
    $facture_id = (int) $facture_id;

    // Récupérer la facture avec client et série
    $sql = "SELECT f.id AS facture_id, f.reference, f.date_facture, f.date_validation, f.type, f.description, f.total, 
                   c.nom AS client_nom, c.contact AS client_contact, c.adresse, c.ninea AS client_ninea,
                   s.titre AS serie_nom, s.logo
            FROM factures f
            JOIN clients c ON f.client_id = c.id
            LEFT JOIN series s ON f.serie_id = s.id
            WHERE f.id = $facture_id
            LIMIT 1";

    $res = $connexion->query($sql);
    if (!$res || $res->num_rows == 0) {
        return null;
    }

    $facture = $res->fetch_assoc();

    // Récupérer les désignations
    $sql2 = "SELECT libelle, prix_unitaire, quantite, montant
             FROM designation
             WHERE facture_id = $facture_id";
    $res2 = $connexion->query($sql2);

    $designations = [];
    if ($res2 && $res2->num_rows > 0) {
        while ($row = $res2->fetch_assoc()) {
            $designations[] = $row;
        }
    }

    $facture['designations'] = $designations;

    return $facture;
}

function deleteActeurBySerie($acteurId, $serieId)
{
    global $connexion;  // connexion mysqli

    // Sécuriser les valeurs
    $acteurId = (int) $acteurId;
    $serieId = (int) $serieId;

    $sql = "DELETE FROM serie_acteur WHERE acteur_id = $acteurId AND serie_id = $serieId";
    if (mysqli_query($connexion, $sql)) {
        return true;
    } else {
        error_log('Erreur suppression acteur: ' . mysqli_error($connexion));
        return false;
    }
}

function ajouterUser($nom, $prenom, $email, $telephone, $role, $photoFile)
{
    global $connexion;

    // =========================================================
    // 1. Vérifier si l'utilisateur existe déjà
    // =========================================================

    $stmt = mysqli_prepare(
        $connexion,
        'SELECT id FROM users WHERE email = ?'
    );

    if (!$stmt) {
        return 'error';
    }

    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        return 'exists';
    }

    mysqli_stmt_close($stmt);


    // =========================================================
    // 2. Gestion de la photo
    // =========================================================

    $photo = null;

    if (!empty($photoFile) && $photoFile['error'] === 0) {

        $ext = strtolower(
            pathinfo($photoFile['name'], PATHINFO_EXTENSION)
        );

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {

            $uploadDir = __DIR__ . '/../uploads/profile/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $photoName = 'profil_' . uniqid() . '.' . $ext;

            $destination = $uploadDir . $photoName;

            if (move_uploaded_file(
                $photoFile['tmp_name'],
                $destination
            )) {
                $photo = $photoName;
            }
        }
    }


    // =========================================================
    // 3. Générer un mot de passe aléatoire
    // =========================================================

    $motDePasseClair = genererMotDePasse(12);


    // =========================================================
    // 4. Hachage SHA1
    // =========================================================

    $password = sha1($motDePasseClair);


    // =========================================================
    // 5. Insertion dans la base
    // =========================================================

    $stmt = mysqli_prepare(
        $connexion,
        'INSERT INTO users 
        (nom, prenom, email, telephone, role, profile, mot_de_passe)
        VALUES (?, ?, ?, ?, ?, ?, ?)'
    );

    if (!$stmt) {
        return 'error';
    }

    mysqli_stmt_bind_param(
        $stmt,
        'sssssss',
        $nom,
        $prenom,
        $email,
        $telephone,
        $role,
        $photo,
        $password
    );

    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);


    // =========================================================
    // 6. Envoyer le mail après création
    // =========================================================

    if ($success) {

        $mailEnvoye = envoyerMailIdentifiants(
            $email,
            $nom,
            $prenom,
            $motDePasseClair
        );

        if ($mailEnvoye) {
            return 'success';
        }

        // Compte créé mais email non envoyé
        return 'mail_error';
    }


    return 'error';
}

function genererMotDePasse($longueur = 12)
{
    $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#$%';

    $motDePasse = '';

    $max = strlen($caracteres) - 1;

    for ($i = 0; $i < $longueur; $i++) {
        $motDePasse .= $caracteres[random_int(0, $max)];
    }

    return $motDePasse;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function envoyerMailIdentifiants($email, $nom, $prenom, $motDePasse)
{
    $mail = new PHPMailer(true);

    try {

        // ============================================
        // Configuration SMTP
        // ============================================

        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';

        $mail->SMTPAuth = true;

        // Votre adresse Gmail
        $mail->Username = 'diopelhadjimadiop@gmail.com';

        // Mot de passe d'application Gmail
        $mail->Password = 'xfuy gpeo oisv gvya';

        // TLS
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        // Port SMTP Gmail
        $mail->Port = 587;


        // ============================================
        // Expéditeur
        // ============================================

        $mail->setFrom(
            'diopelhadjimadiop@gmail.com',
            'Evenprod'
        );


        // ============================================
        // Destinataire
        // ============================================

        $mail->addAddress(
            $email,
            $prenom . ' ' . $nom
        );


        // ============================================
        // Contenu
        // ============================================

        $mail->isHTML(true);

        $mail->CharSet = 'UTF-8';

        $mail->Subject = 'Création de votre compte Evenprod';


        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Compte Evenprod</title>
        </head>

        <body style="font-family: Arial, sans-serif;">

            <h2>Bienvenue sur Evenprod</h2>

            <p>
                Bonjour <strong>' .
                htmlspecialchars($prenom . ' ' . $nom) .
                '</strong>,
            </p>

            <p>
                Votre compte utilisateur Evenprod a été créé avec succès.
            </p>

            <p>
                Voici vos identifiants de connexion :
            </p>

            <table
                cellpadding="10"
                cellspacing="0"
                border="1"
                style="border-collapse: collapse;"
            >

                <tr>
                    <td>
                        <strong>Email</strong>
                    </td>

                    <td>' .
                        htmlspecialchars($email) .
                    '</td>
                </tr>

                <tr>
                    <td>
                        <strong>Mot de passe</strong>
                    </td>

                    <td>
                        <strong>' .
                            htmlspecialchars($motDePasse) .
                        '</strong>
                    </td>
                </tr>

            </table>

            <p>
                Pour des raisons de sécurité, nous vous recommandons
                de modifier votre mot de passe après votre première
                connexion.
            </p>

            <p>
                Cordialement,<br>
                <strong>Équipe Evenprod</strong>
            </p>

        </body>
        </html>
        ';


        // Version texte pour les clients qui ne supportent pas HTML
        $mail->AltBody =
            "Bonjour $prenom $nom,\n\n" .
            "Votre compte Evenprod a été créé.\n\n" .
            "Email : $email\n" .
            "Mot de passe : $motDePasse\n\n" .
            "Nous vous recommandons de modifier votre mot de passe après votre première connexion.";


        // ============================================
        // Envoi
        // ============================================

        $mail->send();

        return true;

    } catch (Exception $e) {

        // Pour le développement
        error_log(
            "Erreur PHPMailer : " . $mail->ErrorInfo
        );

        return false;
    }
}

function envoyerMailMotDePasseReset($email, $nom, $prenom, $motDePasse)
{
    $mail = new PHPMailer(true);

    try {

        // SMTP
        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = 'diopelhadjimadiop@gmail.com';
        $mail->Password = 'xfuy gpeo oisv gvya';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Expéditeur
        $mail->setFrom(
            'diopelhadjimadiop@gmail.com',
            'Evenprod'
        );

        // Destinataire
        $mail->addAddress(
            $email,
            $prenom . ' ' . $nom
        );

        // Contenu
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $mail->Subject = 'Réinitialisation de votre mot de passe Evenprod';

        $mail->Body = '
        <!DOCTYPE html>

        <html>

        <head>
            <meta charset="UTF-8">
        </head>

        <body style="font-family: Arial, sans-serif;">

            <h2>Réinitialisation du mot de passe</h2>

            <p>
                Bonjour <strong>' .
                htmlspecialchars($prenom . ' ' . $nom) .
                '</strong>,
            </p>

            <p>
                Une demande de réinitialisation de votre mot de passe
                Evenprod a été effectuée.
            </p>

            <p>
                Voici votre nouveau mot de passe :
            </p>

            <div style="
                background:#f1f1f1;
                padding:15px;
                font-size:20px;
                font-weight:bold;
                text-align:center;
                letter-spacing:2px;
            ">
                ' . htmlspecialchars($motDePasse) . '
            </div>

            <p style="margin-top:20px;">
                Utilisez ce mot de passe pour vous connecter à votre compte.
            </p>

            <p>
                Après votre connexion, nous vous recommandons de modifier
                ce mot de passe.
            </p>

            <p>
                Si vous n\'êtes pas à l\'origine de cette demande,
                veuillez contacter l\'administrateur Evenprod.
            </p>

            <p>
                Cordialement,<br>
                <strong>Équipe Evenprod</strong>
            </p>

        </body>

        </html>
        ';

        $mail->AltBody =
            "Bonjour $prenom $nom,\n\n" .
            "Votre mot de passe Evenprod a été réinitialisé.\n\n" .
            "Nouveau mot de passe : $motDePasse\n\n" .
            "Connectez-vous puis modifiez votre mot de passe.\n\n" .
            "Équipe Evenprod";

        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log(
            "Erreur PHPMailer réinitialisation : " .
            $mail->ErrorInfo
        );

        return false;
    }
}

function modifierUser($id, $nom, $prenom, $email, $telephone, $role, $photoFile)
{
    global $connexion;

    // 1. Récupérer l’ancien profil
    $stmt = mysqli_prepare($connexion, 'SELECT profile FROM users WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $oldProfile);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // 2. Gestion du nouveau fichier photo
    $photo = $oldProfile;  // par défaut garder l’ancienne photo
    if (!empty($photoFile) && $photoFile['error'] === 0) {
        $ext = strtolower(pathinfo($photoFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $uploadDir = __DIR__ . '/../uploads/profile/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $photoName = 'profil_' . uniqid() . '.' . $ext;
            $destination = $uploadDir . $photoName;

            if (move_uploaded_file($photoFile['tmp_name'], $destination)) {
                // supprimer l’ancien si existe
                if (!empty($oldProfile) && file_exists($uploadDir . $oldProfile)) {
                    unlink($uploadDir . $oldProfile);
                }
                $photo = $photoName;
            }
        }
    }

    // 3. Update dans la base
    $stmt = mysqli_prepare(
        $connexion,
        'UPDATE users SET nom=?, prenom=?, email=?, telephone=?, role=?, profile=? WHERE id=?'
    );
    if (!$stmt) {
        return 'error';
    }
    mysqli_stmt_bind_param($stmt, 'ssssssi', $nom, $prenom, $email, $telephone, $role, $photo, $id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $success ? 'success' : 'error';
}

function getUsers($connexion)
{
    $users = [];
    $sql = 'SELECT id, nom, prenom,telephone, role, profile, email, statut
            FROM users';
    $result = mysqli_query($connexion, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    return $users;
}

function getTotauxDepenses($connexion)
{
    $totaux = [
        'decor' => 0,
        'transport' => 0,
        'cachet' => 0,
        'autre' => 0,
    ];

    // Préparer la requête
    $sql = "SELECT type_depense, SUM(montant) as total 
            FROM depenses 
            WHERE type_depense IN ('decor','transport','cachet','autre')
            GROUP BY type_depense";

    $result = mysqli_query($connexion, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $type = strtolower($row['type_depense']);
            $totaux[$type] = $row['total'];
        }
    }

    return $totaux;
}

function getTotauxDepensesBySerie($connexion, $serie_id)
{
    $totaux = [
        'decor' => 0,
        'transport' => 0,
        'cachet' => 0,
        'autre' => 0,
    ];

    // Préparer la requête avec condition sur la série
    $sql = "SELECT type_depense, SUM(montant) as total 
            FROM depenses 
            WHERE serie_id = ? 
              AND type_depense IN ('decor','transport','cachet','autre')
            GROUP BY type_depense";

    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $serie_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $type = strtolower($row['type_depense']);
            $totaux[$type] = $row['total'];
        }
    }

    mysqli_stmt_close($stmt);

    return $totaux;
}

function getTotaux($connexion)
{
    $totaux = [];

    // Total utilisateurs
    $sql = 'SELECT COUNT(*) as total FROM users';
    $res = $connexion->query($sql);
    $totaux['users'] = $res->fetch_assoc()['total'];

    // Total acteurs
    $sql = 'SELECT COUNT(*) as total FROM acteurs';
    $res = $connexion->query($sql);
    $totaux['acteurs'] = $res->fetch_assoc()['total'];

    // Total séries
    $sql = 'SELECT COUNT(*) as total FROM series';
    $res = $connexion->query($sql);
    $totaux['series'] = $res->fetch_assoc()['total'];

    // Total clients
    $sql = 'SELECT COUNT(*) as total FROM clients';
    $res = $connexion->query($sql);
    $totaux['clients'] = $res->fetch_assoc()['total'];

    // Total factures
    $sql = 'SELECT COUNT(*) as total FROM factures';
    $res = $connexion->query($sql);
    $totaux['factures'] = $res->fetch_assoc()['total'];

    // Total paiements
    $sql = 'SELECT COUNT(*) as total FROM paiements';
    $res = $connexion->query($sql);
    $totaux['paiements'] = $res->fetch_assoc()['total'];

    // Total dépenses
    $sql = 'SELECT SUM(montant) as total FROM depenses';
    $res = $connexion->query($sql);
    $totaux['depenses'] = $res->fetch_assoc()['total'] ?? 0;

    return $totaux;
}

function getTotauxGeneraux($connexion, $serie_id = null)
{
    $totaux = [
        'total_series' => 0,
        'total_depenses' => 0,
        'total_factures' => 0,
    ];

    // Filtre pour la série si fournie
    $filter = '';
    if ($serie_id !== null) {
        $filter = 'WHERE id = ' . intval($serie_id);
    }

    // Total séries
    $sqlSeries = "SELECT COUNT(*) as total FROM series $filter";
    $result = mysqli_query($connexion, $sqlSeries);
    if ($row = mysqli_fetch_assoc($result)) {
        $totaux['total_series'] = $row['total'];
    }

    // Total dépenses
    $filterDepenses = ($serie_id !== null) ? 'WHERE serie_id = ' . intval($serie_id) : '';
    $sqlDepenses = "SELECT SUM(montant) as total_depenses FROM depenses $filterDepenses";
    $result = mysqli_query($connexion, $sqlDepenses);
    if ($row = mysqli_fetch_assoc($result)) {
        $totaux['total_depenses'] = $row['total_depenses'] ?? 0;
    }

    // Total factures type='Facture'
    $filterFactures = ($serie_id !== null) ? 'WHERE serie_id = ' . intval($serie_id) . " AND type='Facture'" : "WHERE type='Facture'";
    $sqlFactures = "SELECT SUM(total) as total_factures FROM factures $filterFactures";
    $result = mysqli_query($connexion, $sqlFactures);
    if ($row = mysqli_fetch_assoc($result)) {
        $totaux['total_factures'] = $row['total_factures'] ?? 0;
    }

    return $totaux;
}

function getUserById($connexion, $id)
{
    $id = intval($id);  // sécurité de base

    $sql = 'SELECT id, nom, prenom, email, telephone,mot_de_passe AS password, role, profile, statut, created_at 
            FROM users 
            WHERE id = ?';

    if ($stmt = mysqli_prepare($connexion, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $user;
    } else {
        return null;
    }
}

/** Récupère le total des dépenses par catégorie pour une série spécifique */
/* function getTotauxDepensesSerie($connexion, $serie_id) {
    $types = [
        'cachet' => 0,
        'decor' => 0,
        'transport' => 0,
        'reception' => 0,
        'accessoire' => 0,
        'reglement_acteur' => 0,
        'hmc' => 0,
        'carburant' => 0,
        'pharmacie' => 0,
        'autre' => 0
    ];

    $sql = "SELECT type_depense, SUM(montant) as total
            FROM depenses
            WHERE serie_id = ?
            GROUP BY type_depense";

    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $serie_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $type = strtolower(trim($row['type_depense'] ?? ''));

        // Correspondance des types
        $mapping = [
            'cachet' => 'cachet',
            'decors' => 'decor',
            'decor' => 'decor',
            'transport' => 'transport',
            'reception' => 'reception',
            'accessoire' => 'accessoire',
            'accessoires' => 'accessoire',
            'reglement_acteur' => 'reglement_acteur',
            'reglement acteur' => 'reglement_acteur',
            'hmc' => 'hmc',
            'carburant' => 'carburant',
            'pharmacie' => 'pharmacie',
            'autre' => 'autre',
            'autres' => 'autre'
        ];

        $key = $mapping[$type] ?? 'autre';
        $types[$key] = floatval($row['total']);
    }

    mysqli_stmt_close($stmt);
    return $types;
} */

/**
 * Récupère tous les totaux généraux pour une série
 */
function getTotauxGeneraux_2($connexion, $serie_id = null)
{
    $totaux = [
        'total_series' => 0,
        'total_depenses' => 0,
        'total_factures' => 0,
        'total_acteurs' => 0
    ];

    if ($serie_id) {
        // Dépenses totales
        $sql = 'SELECT COALESCE(SUM(montant), 0) as total FROM depenses WHERE serie_id = ?';
        $stmt = mysqli_prepare($connexion, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $serie_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $totaux['total_depenses'] = floatval($row['total'] ?? 0);
        mysqli_stmt_close($stmt);

        // Factures / Recettes
        $sql = "SELECT COALESCE(SUM(total), 0) as total FROM factures WHERE serie_id = ? AND type = 'Facture'";
        $stmt = mysqli_prepare($connexion, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $serie_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $totaux['total_factures'] = floatval($row['total'] ?? 0);
        mysqli_stmt_close($stmt);

        // Acteurs
        $sql = 'SELECT COUNT(*) as total FROM acteurs WHERE id = ?';
        $stmt = mysqli_prepare($connexion, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $serie_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $totaux['total_acteurs'] = intval($row['total'] ?? 0);
        mysqli_stmt_close($stmt);
    }

    return $totaux;
}

/**
 * Récupère les dépenses par catégorie pour une série avec les 10 types
 */
function getTotauxDepensesSerie($connexion, $serie_id)
{
    $types = [
        'cachet' => 0,
        'decor' => 0,
        'transport' => 0,
        'reception' => 0,
        'accessoire' => 0,
        'reglement_acteur' => 0,
        'hmc' => 0,
        'carburant' => 0,
        'pharmacie' => 0,
        'autre' => 0
    ];

    $sql = 'SELECT type_depense, SUM(montant) as total 
            FROM depenses 
            WHERE serie_id = ? 
            GROUP BY type_depense';

    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $serie_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $type = strtolower(trim($row['type_depense'] ?? ''));

        // Correspondance des types
        $mapping = [
            'cachet' => 'cachet',
            'decors' => 'decor',
            'decor' => 'decor',
            'transport' => 'transport',
            'reception' => 'reception',
            'accessoire' => 'accessoire',
            'accessoires' => 'accessoire',
            'reglement_acteur' => 'reglement_acteur',
            'reglement acteur' => 'reglement_acteur',
            'hmc' => 'hmc',
            'carburant' => 'carburant',
            'pharmacie' => 'pharmacie',
            'autre' => 'autre',
            'autres' => 'autre'
        ];

        $key = $mapping[$type] ?? 'autre';
        $types[$key] = floatval($row['total']);
    }

    mysqli_stmt_close($stmt);
    return $types;
}

/**
 * Récupère tous les totaux généraux pour une série (version complète)
 */
function getTotauxGenerauxComplet($connexion, $serie_id = null)
{
    $totaux = [
        'total_series' => 0,
        'total_depenses' => 0,
        'total_factures' => 0,
        'total_acteurs' => 0
    ];

    // Nombre total de séries
    $sql = 'SELECT COUNT(*) as total FROM series';
    $result = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($result);
    $totaux['total_series'] = $row['total'] ?? 0;

    if ($serie_id) {
        // Dépenses totales pour la série
        $sql = 'SELECT COALESCE(SUM(montant), 0) as total FROM depenses WHERE serie_id = ?';
        $stmt = mysqli_prepare($connexion, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $serie_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $totaux['total_depenses'] = floatval($row['total'] ?? 0);
        mysqli_stmt_close($stmt);

        // Factures / Recettes pour la série
        $sql = "SELECT COALESCE(SUM(total), 0) as total FROM factures WHERE serie_id = ? AND type = 'Facture'";
        $stmt = mysqli_prepare($connexion, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $serie_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $totaux['total_factures'] = floatval($row['total'] ?? 0);
        mysqli_stmt_close($stmt);

        // Nombre d'acteurs pour la série
        $sql = 'SELECT COUNT(*) as total FROM serie_acteur WHERE serie_id = ?';
        $stmt = mysqli_prepare($connexion, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $serie_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $totaux['total_acteurs'] = intval($row['total'] ?? 0);
        mysqli_stmt_close($stmt);
    }

    return $totaux;
}

/**
 * Récupère le total des dépenses par catégorie (dashboard global avec 10 types)
 */
function getTotauxDepensesGlobal($connexion)
{
    $types = [
        'cachet' => 0,
        'decor' => 0,
        'transport' => 0,
        'reception' => 0,
        'accessoire' => 0,
        'reglement_acteur' => 0,
        'hmc' => 0,
        'carburant' => 0,
        'pharmacie' => 0,
        'autre' => 0
    ];

    $sql = 'SELECT type_depense, SUM(montant) as total FROM depenses GROUP BY type_depense';
    $result = mysqli_query($connexion, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $type = strtolower(trim($row['type_depense'] ?? ''));

        $mapping = [
            'cachet' => 'cachet',
            'decors' => 'decor',
            'decor' => 'decor',
            'transport' => 'transport',
            'reception' => 'reception',
            'accessoire' => 'accessoire',
            'accessoires' => 'accessoire',
            'reglement_acteur' => 'reglement_acteur',
            'reglement acteur' => 'reglement_acteur',
            'hmc' => 'hmc',
            'carburant' => 'carburant',
            'pharmacie' => 'pharmacie',
            'autre' => 'autre',
            'autres' => 'autre'
        ];

        $key = $mapping[$type] ?? 'autre';
        $types[$key] = floatval($row['total']);
    }

    return $types;
}

/**
 * Récupère le nombre total d'acteurs
 */
function getTotalActeurs($connexion)
{
    $sql = 'SELECT COUNT(*) as total FROM acteurs';
    $result = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

/**
 * Récupère le nombre total de clients
 */
function getTotalClients($connexion)
{
    $sql = 'SELECT COUNT(*) as total FROM clients';
    $result = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

/**
 * Récupère le nombre total d'utilisateurs
 */
function getTotalUsers($connexion)
{
    $sql = 'SELECT COUNT(*) as total FROM users';
    $result = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

/**
 * Récupère le nombre total de séries
 */
function getTotalSeries($connexion)
{
    $sql = 'SELECT COUNT(*) as total FROM series';
    $result = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

/**
 * Récupère toutes les séries avec tous les champs
 */
function getAllSeriesComplete()
{
    global $connexion;
    $sql = 'SELECT * FROM series ORDER BY id DESC';
    $result = mysqli_query($connexion, $sql);
    $series = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $series[] = $row;
    }
    return $series;
}

/**
 * Récupère les détails d'un acteur par son ID
 */
function getActeurById($id)
{
    global $connexion;
    $id = (int) $id;
    $sql = "SELECT * FROM acteurs WHERE id = $id";
    $result = mysqli_query($connexion, $sql);
    return mysqli_fetch_assoc($result);
}

/**
 * Ajoute un acteur
 */
function ajouterActeur($nom, $prenom, $date_naissance, $contact, $adresse, $photo, $cv)
{
    global $connexion;

    $nom = mysqli_real_escape_string($connexion, $nom);
    $prenom = mysqli_real_escape_string($connexion, $prenom);
    $date_naissance = mysqli_real_escape_string($connexion, $date_naissance);
    $contact = mysqli_real_escape_string($connexion, $contact);
    $adresse = mysqli_real_escape_string($connexion, $adresse);
    $photo = mysqli_real_escape_string($connexion, $photo);
    $cv = mysqli_real_escape_string($connexion, $cv);

    $sql = "INSERT INTO acteurs (nom, prenom, date_naissance, contact, adresse, photo, cv_file) 
            VALUES ('$nom', '$prenom', '$date_naissance', '$contact', '$adresse', '$photo', '$cv')";

    if (mysqli_query($connexion, $sql)) {
        return ['success' => true, 'id' => mysqli_insert_id($connexion)];
    }
    return ['success' => false, 'message' => mysqli_error($connexion)];
}

/**
 * Modifie un acteur
 */
function modifierActeur($id, $nom, $prenom, $date_naissance, $contact, $adresse, $photo = null, $cv = null)
{
    global $connexion;

    $id = (int) $id;
    $nom = mysqli_real_escape_string($connexion, $nom);
    $prenom = mysqli_real_escape_string($connexion, $prenom);
    $date_naissance = mysqli_real_escape_string($connexion, $date_naissance);
    $contact = mysqli_real_escape_string($connexion, $contact);
    $adresse = mysqli_real_escape_string($connexion, $adresse);

    // Récupérer l'ancien acteur
    $old = getActeurById($id);
    if (!$old) {
        return ['success' => false, 'message' => 'Acteur introuvable'];
    }

    $photoFile = $old['photo'];
    if ($photo) {
        // Supprimer l'ancienne photo
        if ($photoFile && file_exists('../../uploads/acteurs/' . $photoFile)) {
            unlink('../../uploads/acteurs/' . $photoFile);
        }
        $photoFile = $photo;
    }

    $cvFile = $old['cv_file'];
    if ($cv) {
        if ($cvFile && file_exists('../../uploads/cv/' . $cvFile)) {
            unlink('../../uploads/cv/' . $cvFile);
        }
        $cvFile = $cv;
    }

    $sql = "UPDATE acteurs SET 
            nom = '$nom', 
            prenom = '$prenom', 
            date_naissance = '$date_naissance', 
            contact = '$contact', 
            adresse = '$adresse', 
            photo = '$photoFile', 
            cv_file = '$cvFile' 
            WHERE id = $id";

    if (mysqli_query($connexion, $sql)) {
        return ['success' => true];
    }
    return ['success' => false, 'message' => mysqli_error($connexion)];
}

/**
 * Supprime un acteur
 */
function supprimerActeur($id)
{
    global $connexion;
    $id = (int) $id;

    // Récupérer l'acteur pour supprimer les fichiers
    $acteur = getActeurById($id);
    if ($acteur) {
        if ($acteur['photo'] && file_exists('../../uploads/acteurs/' . $acteur['photo'])) {
            unlink('../../uploads/acteurs/' . $acteur['photo']);
        }
        if ($acteur['cv_file'] && file_exists('../../uploads/cv/' . $acteur['cv_file'])) {
            unlink('../../uploads/cv/' . $acteur['cv_file']);
        }
    }

    $sql = "DELETE FROM acteurs WHERE id = $id";
    return mysqli_query($connexion, $sql);
}

/**
 * Vérifie si une série existe
 */
function serieExists($id)
{
    global $connexion;
    $id = (int) $id;
    $sql = "SELECT id FROM series WHERE id = $id";
    $result = mysqli_query($connexion, $sql);
    return mysqli_num_rows($result) > 0;
}

/**
 * Récupère les statistiques pour le dashboard
 */
function getDashboardStats($connexion)
{
    $stats = [
        'users' => 0,
        'series' => 0,
        'clients' => 0,
        'acteurs' => 0,
        'depenses_total' => 0,
        'recettes_total' => 0
    ];

    // Utilisateurs
    $result = mysqli_query($connexion, 'SELECT COUNT(*) as total FROM users');
    $row = mysqli_fetch_assoc($result);
    $stats['users'] = $row['total'] ?? 0;

    // Séries
    $result = mysqli_query($connexion, 'SELECT COUNT(*) as total FROM series');
    $row = mysqli_fetch_assoc($result);
    $stats['series'] = $row['total'] ?? 0;

    // Clients
    $result = mysqli_query($connexion, 'SELECT COUNT(*) as total FROM clients');
    $row = mysqli_fetch_assoc($result);
    $stats['clients'] = $row['total'] ?? 0;

    // Acteurs
    $result = mysqli_query($connexion, 'SELECT COUNT(*) as total FROM acteurs');
    $row = mysqli_fetch_assoc($result);
    $stats['acteurs'] = $row['total'] ?? 0;

    // Dépenses totales
    $result = mysqli_query($connexion, 'SELECT COALESCE(SUM(montant), 0) as total FROM depenses');
    $row = mysqli_fetch_assoc($result);
    $stats['depenses_total'] = floatval($row['total'] ?? 0);

    // Recettes (factures validées)
    $result = mysqli_query($connexion, "SELECT COALESCE(SUM(total), 0) as total FROM factures WHERE type = 'Facture'");
    $row = mysqli_fetch_assoc($result);
    $stats['recettes_total'] = floatval($row['total'] ?? 0);

    return $stats;
}

/**
 * Ajoute des acteurs à une série avec leur type (forfaitaire/journalier)
 * Chaque acteur a son propre cachet individuel
 */
function addActeursToSerieWithType($serieId, $acteurs, $cachets, $types)
{
    global $connexion;

    $serieId = (int) $serieId;

    foreach ($acteurs as $acteurId) {
        $acteurId = (int) $acteurId;
        $type = $types[$acteurId] ?? 'forfaitaire';
        $cachet = isset($cachets[$acteurId]) ? floatval($cachets[$acteurId]) : 0;

        $sql = "INSERT INTO serie_acteur (serie_id, acteur_id, cachet, type_acteur) 
                VALUES ($serieId, $acteurId, $cachet, '$type')";
        mysqli_query($connexion, $sql);
    }
}

/**
 * Récupère un salarié par son ID
 */
function getSalarieById($connexion, $id)
{
    $stmt = $connexion->prepare("
        SELECT *
        FROM salaries
        WHERE id = ?
        LIMIT 1
    ");

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        $stmt->close();
        return null;
    }

    $result = $stmt->get_result();

    $salarie = $result->fetch_assoc();

    $stmt->close();

    return $salarie ?: null;
}

/**
 * Ajoute un nouveau salarié
 */
function ajouterSalarie($nom, $prenom, $telephone, $email, $adresse, $date_naissance, $fonction, $date_embauche, $type_contrat, $salaire, $statut, $photoFile, $contratFile) {
    // Vérification existence
    global $connexion;
    $stmt = $connexion->prepare("SELECT id FROM salaries WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return "exists";
    }

    // Upload photo
    $photo = null;
    if ($photoFile && $photoFile['error'] === UPLOAD_ERR_OK) {
        $photo = uploadFile($photoFile, 'uploads/salaries/');
    }

    // Upload contrat
    $contrat = null;
    if ($contratFile && $contratFile['error'] === UPLOAD_ERR_OK) {
        $contrat = uploadFile($contratFile, 'uploads/contrats/');
    }

    $stmt = $connexion->prepare("
        INSERT INTO salaries 
        (nom, prenom, telephone, email, adresse, date_naissance, fonction, date_embauche, type_contrat, salaire, statut, photo, contrat, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    return $stmt->execute([
        $nom, $prenom, $telephone, $email, $adresse, $date_naissance,
        $fonction, $date_embauche, $type_contrat, $salaire, $statut,
        $photo, $contrat
    ]) ? "success" : "error";
}

/**
 * Modifie un salarié existant
 */
function modifierSalarie($id, $nom, $prenom, $telephone, $email, $adresse, $date_naissance, $fonction, $date_embauche, $type_contrat, $salaire, $statut, $photoFile, $contratFile) {
    global $connexion;
    // Récupérer l'ancien salarié
    $old = getSalarieById($connexion, $id);
    if (!$old) return "error";

    // Upload nouvelle photo
    $photo = $old['photo'];
    if ($photoFile && $photoFile['error'] === UPLOAD_ERR_OK) {
        $photo = uploadFile($photoFile, 'uploads/salaries/');
        if ($old['photo'] && file_exists('uploads/salaries/' . $old['photo'])) {
            unlink('uploads/salaries/' . $old['photo']);
        }
    }

    // Upload nouveau contrat
    $contrat = $old['contrat'];
    if ($contratFile && $contratFile['error'] === UPLOAD_ERR_OK) {
        $contrat = uploadFile($contratFile, 'uploads/contrats/');
        if ($old['contrat'] && file_exists('uploads/contrats/' . $old['contrat'])) {
            unlink('uploads/contrats/' . $old['contrat']);
        }
    }

    $stmt = $connexion->prepare("
        UPDATE salaries SET
            nom = ?, prenom = ?, telephone = ?, email = ?,
            adresse = ?, date_naissance = ?, fonction = ?,
            date_embauche = ?, type_contrat = ?, salaire = ?,
            statut = ?, photo = ?, contrat = ?,
            updated_at = NOW()
        WHERE id = ?
    ");

    return $stmt->execute([
        $nom, $prenom, $telephone, $email, $adresse, $date_naissance,
        $fonction, $date_embauche, $type_contrat, $salaire, $statut,
        $photo, $contrat, $id
    ]) ? "success" : "error";
}

/**
 * Fonction utilitaire pour uploader un fichier
 */
function uploadFile($file, $targetDir) {
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $targetPath = $targetDir . $filename;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $filename;
    }
    return null;
}

/**
 * Récupère la liste des salariés avec filtres et pagination
 */
function getSalariesFiltres($search = '', $statut = '', $contrat = '', $fonction = '', $limit = 15, $offset = 0) {
    global $connexion;

    $conditions = [];
    $params = [];
    $types = "";

    if ($search) {
        $conditions[] = "(nom LIKE ? OR prenom LIKE ? OR email LIKE ? OR telephone LIKE ? OR fonction LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
        $types .= "sssss";
    }

    if ($statut) {
        $conditions[] = "statut = ?";
        $params[] = $statut;
        $types .= "s";
    }

    if ($contrat) {
        $conditions[] = "type_contrat = ?";
        $params[] = $contrat;
        $types .= "s";
    }

    if ($fonction) {
        $conditions[] = "fonction = ?";
        $params[] = $fonction;
        $types .= "s";
    }

    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    // Récupérer le total
    $countStmt = $connexion->prepare("SELECT COUNT(*) as total FROM salaries $whereClause");
    
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $row = $countResult->fetch_assoc();
    $total = $row['total'];

    // Récupérer les données
    $query = "SELECT * FROM salaries $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $connexion->prepare($query);

    // Ajouter les paramètres de limite
    $allParams = array_merge($params, [$limit, $offset]);
    $typesAll = $types . "ii";
    
    if (!empty($allParams)) {
        $stmt->bind_param($typesAll, ...$allParams);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);

    return [
        'total' => $total,
        'data' => $data
    ];
}

/**
 * Récupère les fonctions uniques avec leur nombre
 */
function getSalariesFonctions() {
    global $connexion;
    $result = $connexion->query("
        SELECT fonction, COUNT(*) as count 
        FROM salaries 
        WHERE statut = 'actif' 
        GROUP BY fonction 
        ORDER BY count DESC
    ");
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Récupère les statistiques des salariés
 */
function getSalariesStats() {
    global $connexion;

    $stats = [];

    // Total
    $result = $connexion->query("SELECT COUNT(*) as total FROM salaries");
    $row = $result->fetch_assoc();
    $stats['total'] = $row['total'];

    // Par statut
    foreach (['actif', 'inactif', 'en_conge'] as $status) {
        $stmt = $connexion->prepare("SELECT COUNT(*) as count FROM salaries WHERE statut = ?");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats[$status] = $row['count'];
    }

    // Masse salariale (salaires des actifs)
    $result = $connexion->query("SELECT SUM(salaire) as total FROM salaries WHERE statut = 'actif'");
    $row = $result->fetch_assoc();
    $stats['masse_salariale'] = (float)($row['total'] ?? 0);

    return $stats;
}

/**
 * Supprime un salarié
 */
function deleteSalarie($id) {
    global $connexion;

    // Récupérer les fichiers à supprimer
    $stmt = $connexion->prepare("SELECT photo, contrat FROM salaries WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $salarie = $result->fetch_assoc();

    if ($salarie) {
        // Supprimer les fichiers
        if ($salarie['photo'] && file_exists('uploads/salaries/' . $salarie['photo'])) {
            unlink('uploads/salaries/' . $salarie['photo']);
        }
        if ($salarie['contrat'] && file_exists('uploads/contrats/' . $salarie['contrat'])) {
            unlink('uploads/contrats/' . $salarie['contrat']);
        }
    }

    $stmt = $connexion->prepare("DELETE FROM salaries WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

/**
 * Supprime plusieurs salariés en masse
 */
function deleteSalariesBulk($ids) {
    global $connexion;

    if (empty($ids)) return false;

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    
    // Récupérer les fichiers
    $stmt = $connexion->prepare("SELECT photo, contrat FROM salaries WHERE id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    $salaries = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($salaries as $salarie) {
        if ($salarie['photo'] && file_exists('uploads/salaries/' . $salarie['photo'])) {
            unlink('uploads/salaries/' . $salarie['photo']);
        }
        if ($salarie['contrat'] && file_exists('uploads/contrats/' . $salarie['contrat'])) {
            unlink('uploads/contrats/' . $salarie['contrat']);
        }
    }

    $stmt = $connexion->prepare("DELETE FROM salaries WHERE id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    return $stmt->execute();
}
?>