<?php 

// Connectez-vous à votre base de données MySQL
function connexionBD()
{
    $host = "localhost"; // Host distant
    //$user = "u893234126_ep_user";
    $user = "root";
    //$pass = "Pw@Ep@2025";
    $pass = "";
    //$db   = "u893234126_sygep";
    $db   = "evenprod_db";

    $connexion = mysqli_connect($host, $user, $pass, $db);

    if (!$connexion) {
        die("Erreur : Impossible de se connecter à la base distante. " . mysqli_connect_error());
    }

    mysqli_set_charset($connexion, "utf8mb4");
    return $connexion;
}


$connexion = connexionBD();

function login($username, $password)
{
    global $connexion;
    $hashed_password = sha1($password);

    // Requête SQL modifiée pour vérifier si l'utilisateur est actif
    $query = "SELECT * FROM `users` WHERE `email` = ? AND `mot_de_passe` = ? AND `statut` = 1";
    
    // Préparer la requête pour éviter les injections SQL
    $stmt = $connexion->prepare($query);
    $stmt->bind_param('ss', $username, $hashed_password);
    $stmt->execute();
    
    // Récupérer les résultats
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Fermer la requête
    $stmt->close();
    
    return $user; // Retourne les informations si l'utilisateur est trouvé et actif, sinon retourne null
}
/**
 * Récupère toutes les séries
 *
 * @return array Liste des séries sous forme de tableaux associatifs
 */
function getAllSeries() {
    global $connexion;

    $series = [];
    $sql = "SELECT id, titre, type, description, logo FROM series ORDER BY id DESC";
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
function ajouterSerie($titre, $type, $budget, $description, $logo) {
    global $connexion;

    $titre = mysqli_real_escape_string($connexion, $titre);
    $type = mysqli_real_escape_string($connexion, $type);
    $budget = floatval($budget);
    $description = mysqli_real_escape_string($connexion, $description);
    $logo = mysqli_real_escape_string($connexion, $logo);

    $sql = "INSERT INTO series (titre, type, budget, description, logo) 
            VALUES ('$titre', '$type', $budget, '$description', '$logo')";
    if (mysqli_query($connexion, $sql)) {
        return ['success' => true, 'serie_id' => mysqli_insert_id($connexion)];
    }
    return ['success' => false, 'message' => mysqli_error($connexion)];
}

/**
 * Modifier une série existante
 */
function modifierSerie($serieId, $titre, $type, $budget, $description, $nouveauLogo = null) {
    global $connexion;

    $serieId = (int)$serieId;
    $titre = mysqli_real_escape_string($connexion, $titre);
    $type = mysqli_real_escape_string($connexion, $type);
    $budget = floatval($budget);
    $description = mysqli_real_escape_string($connexion, $description);

    // Récupérer l'ancienne série pour le logo
    $oldSerie = getSerieById($serieId);
    if(!$oldSerie) return ['success' => false, 'message' => 'Série introuvable'];

    $logo = $oldSerie['logo']; // par défaut on garde l'ancien logo

    // Si un nouveau logo est uploadé, remplacer l'ancien
    if($nouveauLogo) {
        $uploadDir = '../uploads/series/';
        if(!empty($logo) && file_exists($uploadDir . $logo)) {
            unlink($uploadDir . $logo); // suppression de l'ancien fichier
        }
        $logo = $nouveauLogo; // remplacer par le nouveau
    }

    $sql = "UPDATE series 
            SET titre='$titre', type='$type', budget=$budget, description='$description', logo='$logo'
            WHERE id=$serieId";

    if(mysqli_query($connexion, $sql)) {
        return ['success' => true];
    } else {
        return ['success' => false, 'message' => mysqli_error($connexion)];
    }
}


/**
 * Récupérer une série par son ID
 */
function getSerieById($serieId) {
    global $connexion;
    $serieId = (int)$serieId;

    $sql = "SELECT * FROM series WHERE id = $serieId";
    $res = mysqli_query($connexion, $sql);
    return mysqli_fetch_assoc($res);
}

/**
 * Récupère la dernière série ajoutée
 *
 * @return array|null Tableau associatif de la série ou null si aucune série
 */
function getLastSerie() {
    global $connexion;

    $sql = "SELECT id, titre, type, budget, description, logo 
            FROM series 
            ORDER BY id DESC 
            LIMIT 1";

    $result = mysqli_query($connexion, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }

    return null;
}


function getActeursBySerieId($serieId)
{
    global $connexion;

    $serieId = (int)$serieId;

    $sql = "
        SELECT a.*, sa.id AS serie_acteur, sa.cachet
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

    $serieId = (int)$serieId;

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
function addActeursToSerie($serieId, $acteurs, $cachets) {
    global $connexion;

    foreach ($acteurs as $acteurId) {
        $acteurId = (int)$acteurId;
        $serieId = (int)$serieId;
        $cachet = isset($cachets[$acteurId]) ? (int)$cachets[$acteurId] : 0;

        $sql = "INSERT INTO serie_acteur (serie_id, acteur_id, cachet) 
                VALUES ($serieId, $acteurId, $cachet)";
        mysqli_query($connexion, $sql);
    }
}
function getDepenseByTournage($serieId, $tournageId) {
    global $connexion;
    $serieId = (int)$serieId;
    $tournageId = (int)$tournageId;

    $sql = "SELECT SUM(montant) as total 
            FROM depenses 
            WHERE serie_id = $serieId AND tournage_id = $tournageId";
    $result = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}
function getTournagesBySerieId($serieId) {
    global $connexion;
    $serieId = (int)$serieId;

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
function getEquipeCountByTournage($tournageId) {
    global $connexion;
    $tournageId = (int)$tournageId;

    $sql = "SELECT COUNT(*) as total FROM tournage_acteur WHERE tournage_id = $tournageId";
    $result = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}
function getActeursByTournage($tournageId) {
    global $connexion;
    $tournageId = (int)$tournageId;

    $sql = "SELECT a.id, a.nom, a.prenom, a.date_naissance, a.contact, a.adresse, sa.cachet
            FROM acteurs a
            INNER JOIN tournage_acteur ta ON ta.acteur_id = a.id
            LEFT JOIN serie_acteur sa ON sa.acteur_id = a.id
            WHERE ta.tournage_id = $tournageId";
    $res = mysqli_query($connexion, $sql);

    $acteurs = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $acteurs[] = $row; // retourne tout le tableau avec l'id
    }
    return $acteurs;
}

function getTournageById($tournageId) {
    global $connexion;
    $tournageId = (int)$tournageId;

    $sql = "SELECT * FROM tournages WHERE id = $tournageId LIMIT 1";
    $result = mysqli_query($connexion, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        return null;
    }
}
function generateTournageReference() {
    global $connexion;
    $year = date('y'); // année actuelle sur 2 chiffres

    // Récupérer l'ID max de la table tournages
    $sql = "SELECT MAX(id) as max_id FROM tournages";
    $result = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($result);
    $lastId = $row['max_id'] ?? 0;

    $num = $lastId + 1; // On ajoute 1 à l'ID max pour la nouvelle référence

    // Formater en 3 chiffres
    $numFormatted = str_pad($num, 3, '0', STR_PAD_LEFT);

    return "RF-$year-$numFormatted";
}

function ajouterTournage($serieId, $date, $reference, $acteursIds) {
    global $connexion;

    $serieId = (int)$serieId;
    $date = mysqli_real_escape_string($connexion, $date);
    $reference = mysqli_real_escape_string($connexion, $reference);

    // 1️⃣ Insérer le tournage
    $sqlTournage = "INSERT INTO tournages (serie_id, date_tournage, reference) 
                    VALUES ($serieId, '$date', '$reference')";
    if (!mysqli_query($connexion, $sqlTournage)) {
        return ['success' => false, 'message' => 'Erreur insertion tournage : '.mysqli_error($connexion)];
    }

    $tournageId = mysqli_insert_id($connexion);

    // 2️⃣ Insérer les acteurs et calculer la somme des cachets
    $totalCachet = 0;
    foreach ($acteursIds as $acteurId) {
        $acteurId = (int)$acteurId;

        // Récupérer le cachet depuis serie_acteur
        $sqlCachet = "SELECT cachet FROM serie_acteur WHERE serie_id = $serieId AND acteur_id = $acteurId";
        $res = mysqli_query($connexion, $sqlCachet);
        $row = mysqli_fetch_assoc($res);
        $cachet = $row['cachet'] ?? 0;
        $totalCachet += $cachet;

        // Insérer dans tournage_acteur
        $sqlTA = "INSERT INTO tournage_acteur (tournage_id, acteur_id) 
                  VALUES ($tournageId, $acteurId)";
        mysqli_query($connexion, $sqlTA);
    }

    // 3️⃣ Ajouter la dépense "cachet"
    if ($totalCachet > 0) {
        $sqlDepense = "INSERT INTO depenses (serie_id, tournage_id, type_depense, montant, date_depense) 
                       VALUES ($serieId, $tournageId, 'cachet', $totalCachet, '$date')";
        mysqli_query($connexion, $sqlDepense);
    }

    return ['success' => true, 'tournage_id' => $tournageId];
}
function modifierTournage($tournageId, $serieId, $date, $reference, $acteursIds) {
    global $connexion;

    $tournageId = (int)$tournageId;
    $serieId = (int)$serieId;
    $date = mysqli_real_escape_string($connexion, $date);
    $reference = mysqli_real_escape_string($connexion, $reference);

    // 1️⃣ Mettre à jour le tournage
    $sqlUpdate = "UPDATE tournages 
                  SET date_tournage = '$date', reference = '$reference' 
                  WHERE id = $tournageId";
    if (!mysqli_query($connexion, $sqlUpdate)) {
        return ['success' => false, 'message' => 'Erreur mise à jour tournage : '.mysqli_error($connexion)];
    }

    // 2️⃣ Supprimer les anciens acteurs du tournage
    $sqlDelete = "DELETE FROM tournage_acteur WHERE tournage_id = $tournageId";
    mysqli_query($connexion, $sqlDelete);

    // 3️⃣ Réinsérer les nouveaux acteurs et calculer le total des cachets
    $totalCachet = 0;
    foreach ($acteursIds as $acteurId) {
        $acteurId = (int)$acteurId;

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

function getDepensesBySerie($serieId) {
    global $connexion;
    $serieId = (int)$serieId;

    $sql = "SELECT d.id, d.libelle, d.type_depense, d.date_depense, d.montant, d.justificatif,
                   t.reference AS tournage_reference
            FROM depenses d
            LEFT JOIN tournages t ON d.tournage_id = t.id
            WHERE d.serie_id = $serieId
            ORDER BY d.id DESC";

    $result = mysqli_query($connexion, $sql);
    $depenses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $depenses[] = $row;
    }
    return $depenses;
}

function ajouterDepense($serieId, $tournageId, $type, $montant, $description, $justificatif = null) {
    global $connexion;

    $serieId = (int)$serieId;
    $tournageId = $tournageId !== '' ? (int)$tournageId : "NULL";
    $type = mysqli_real_escape_string($connexion, $type);
    $montant = floatval($montant);
    $description = mysqli_real_escape_string($connexion, $description);
    $justificatif = $justificatif ? "'".mysqli_real_escape_string($connexion, $justificatif)."'" : "NULL";

    $sql = "INSERT INTO depenses (serie_id, tournage_id, type_depense, montant, libelle, justificatif, date_depense)
            VALUES ($serieId, $tournageId, '$type', $montant, '$description', $justificatif, NOW())";

    if (!mysqli_query($connexion, $sql)) {
        return ['success' => false, 'message' => mysqli_error($connexion)];
    }

    return ['success' => true, 'depense_id' => mysqli_insert_id($connexion)];
}

function ajouterPartenaire($ninea, $nom, $email, $contact, $adresse, $logoFile)
{
    global $connexion; // ta connexion MySQLi

    // --- 1. Gestion de l'upload du logo ---
    $logo = null;
    if ($logoFile && $logoFile['error'] === 0) {
        $ext = strtolower(pathinfo($logoFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $uploadDir = __DIR__ . "/../uploads/logos/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $logoName = "logo_" . time() . "." . $ext;
            $destination = $uploadDir . $logoName;

            if (move_uploaded_file($logoFile['tmp_name'], $destination)) {
                $logo = $logoName;
            }
        }
    }

    // --- 2. Insertion dans la base ---
    $stmt = mysqli_prepare(
        $connexion,
        "INSERT INTO clients (ninea, nom, email, contact, adresse, logo) VALUES (?, ?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param($stmt, "ssssss", $ninea, $nom, $email, $contact, $adresse, $logo);
    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $success;
}
function modifierPartenaire($id, $ninea, $nom, $email, $contact, $adresse, $logoFile = null)
{
    global $connexion;

    // Récupérer l'ancien partenaire pour savoir quel logo supprimer
    $id = (int)$id;
    $res = mysqli_query($connexion, "SELECT logo FROM clients WHERE id=$id");
    $old = mysqli_fetch_assoc($res);

    $logo = $old['logo']; // par défaut garder l'ancien logo

    // S'il y a un nouveau fichier
    if ($logoFile && $logoFile['error'] === 0) {
        $ext = strtolower(pathinfo($logoFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $uploadDir = __DIR__ . "/../uploads/logos/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Supprimer l’ancien logo
            if ($logo && file_exists($uploadDir . $logo)) {
                unlink($uploadDir . $logo);
            }

            $logoName = "logo_" . time() . "." . $ext;
            $destination = $uploadDir . $logoName;
            if (move_uploaded_file($logoFile['tmp_name'], $destination)) {
                $logo = $logoName;
            }
        }
    }

    // Mise à jour
    $stmt = mysqli_prepare(
        $connexion,
        "UPDATE clients SET ninea=?, nom=?, email=?, contact=?, adresse=?, logo=? WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, "ssssssi", $ninea, $nom, $email, $contact, $adresse, $logo, $id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $success;
}

function getClients($connexion) {
    $sql = "SELECT id, ninea, nom, logo, email, contact, adresse, created_at 
            FROM clients 
            ORDER BY id DESC";
    $result = mysqli_query($connexion, $sql);

    $clients = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $clients[] = $row;
        }
    }
    return $clients;
}

function ajouterFacture($connexion, $client, $serie_id, $date, $description, $libelles, $quantites, $montants) {
    try {
        // Calcul du total
        $total = 0;
        foreach ($montants as $m) {
            $total += (float)$m;
        }

        // Démarrer transaction
        $connexion->begin_transaction();

        // 1. Générer la référence automatique
        $result = $connexion->query("SELECT COUNT(*) AS total FROM factures WHERE YEAR(date_facture) = YEAR('$date')");
        $row = $result->fetch_assoc();
        $nextId = $row['total'] + 1;
        $reference = "REF-" . date("y", strtotime($date)) . "-" . str_pad($nextId, 3, "0", STR_PAD_LEFT);

        // 2. Enregistrer la facture avec la référence
        $sql = "INSERT INTO factures (client_id, serie_id, date_facture, description, total, reference) 
                VALUES ('$client', '$serie_id', '$date', '$description', '$total', '$reference')";
        if (!$connexion->query($sql)) {
            throw new Exception("Erreur facture : " . $connexion->error);
        }

        // ID facture
        $facture_id = $connexion->insert_id;

        // 3. Enregistrer chaque ligne
        for ($i = 0; $i < count($libelles); $i++) {
            $lib = $connexion->real_escape_string(trim($libelles[$i]));
            /* $pu  = $connexion->real_escape_string(trim($prixUnitaires[$i])); */
            $qte = (int)$quantites[$i];
            $mt  = (float)$montants[$i];

            $sql2 = "INSERT INTO designation (facture_id, libelle, quantite, montant) 
                     VALUES ('$facture_id', '$lib', '$qte', '$mt')";
            if (!$connexion->query($sql2)) {
                throw new Exception("Erreur designation : " . $connexion->error);
            }
        }

        // Valider transaction
        $connexion->commit();

        return $facture_id; // retourne l'ID facture

    } catch (Exception $e) {
        $connexion->rollback();
        throw new Exception("Erreur lors de l'enregistrement : " . $e->getMessage());
    }
}


function getFacturesBySerieId($connexion, $serieId) {
    $sql = "SELECT f.id, f.type, f.date_facture, f.reference, f.description, f.total, 
                   c.nom AS client_nom
            FROM factures f
            INNER JOIN clients c ON f.client_id = c.id
            WHERE f.serie_id = " . (int)$serieId . "
            ORDER BY f.date_facture DESC";

    $result = $connexion->query($sql);
    $factures = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Récupérer les désignations de chaque facture
            $designations = [];
            $sql2 = "SELECT libelle, prix_unitaire, quantite, montant
                     FROM designation WHERE facture_id = " . (int)$row['id'];
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

function getFacturesWithPaiementsBySerie($connexion, $serieId) {
    $factures = [];
    $sql = "SELECT f.id, f.description, f.reference, f.total, c.nom, c.ninea, c.contact
            FROM factures f
            JOIN clients c ON f.client_id=c.id
            WHERE f.serie_id = " . (int)$serieId . " AND f.type = 'facture'";
    $res = $connexion->query($sql);

    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            // Somme des paiements
            $sql2 = "SELECT SUM(montant) AS total_verse FROM paiements WHERE facture_id = " . (int)$row['id'];
            $res2 = $connexion->query($sql2);
            $totalVerse = ($res2 && $res2->num_rows > 0) ? $res2->fetch_assoc()['total_verse'] : 0;

            $row['verse'] = (float)$totalVerse;
            $row['reste'] = $row['total'] - $row['verse'];
            $factures[] = $row;
        }
    }
    return $factures;
}

function getPaiementsByFactureId($connexion, $factureId) {
    $factureId = (int)$factureId; // sécurisation
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

function getFactureWithPaiements($connexion, $factId) {
    $sql = "SELECT f.id, f.description, f.reference, f.total, c.nom AS client_nom, c.ninea, c.contact
            FROM factures f
            JOIN clients c ON f.client_id = c.id
            WHERE f.id = " . (int)$factId . " AND f.type = 'facture'
            LIMIT 1";

    $res = $connexion->query($sql);

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();

        // Somme des paiements liés à cette facture
        $sql2 = "SELECT SUM(montant) AS total_verse 
                 FROM paiements 
                 WHERE facture_id = " . (int)$row['id'];
        $res2 = $connexion->query($sql2);
        $totalVerse = ($res2 && $res2->num_rows > 0) ? (float)$res2->fetch_assoc()['total_verse'] : 0;

        $row['verse'] = $totalVerse;
        $row['reste'] = $row['total'] - $totalVerse;

        return $row;
    }

    return null;
}
function getFactureDetails($connexion, $facture_id) {
    $facture_id = (int)$facture_id;

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
function deleteActeurBySerie($acteurId, $serieId) {
    global $connexion; // connexion mysqli

    // Sécuriser les valeurs
    $acteurId = (int)$acteurId;
    $serieId = (int)$serieId;

    $sql = "DELETE FROM serie_acteur WHERE acteur_id = $acteurId AND serie_id = $serieId";
    if (mysqli_query($connexion, $sql)) {
        return true;
    } else {
        error_log("Erreur suppression acteur: " . mysqli_error($connexion));
        return false;
    }
}
function ajouterUser($nom, $prenom, $email, $telephone, $role, $photoFile)
{
    global $connexion; // connexion MySQLi

    // --- 1. Vérifier si l'utilisateur existe déjà ---
    $stmt = mysqli_prepare($connexion, "SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        return "error"; // erreur de préparation
    }
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        return "exists"; // déjà dans la base
    }
    mysqli_stmt_close($stmt);

    // --- 2. Gestion de l'upload du profil ---
    $photo = null;
    if (!empty($photoFile) && $photoFile['error'] === 0) {
        $ext = strtolower(pathinfo($photoFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $uploadDir = __DIR__ . "/../uploads/profile/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $photoName = "profil_" . uniqid() . "." . $ext; // éviter doublons
            $destination = $uploadDir . $photoName;

            if (move_uploaded_file($photoFile['tmp_name'], $destination)) {
                $photo = $photoName;
            }
        }
    }

    // --- 3. Générer un mot de passe par défaut avec SHA1 ---
    $password = sha1("Evenprod2025"); 

    // --- 4. Insertion dans la base ---
    $stmt = mysqli_prepare(
        $connexion,
        "INSERT INTO users (nom, prenom, email, telephone, role, profile, mot_de_passe) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt) {
        return "error"; // erreur de préparation
    }

    mysqli_stmt_bind_param($stmt, "sssssss", $nom, $prenom, $email, $telephone, $role, $photo, $password);
    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $success ? "success" : "error";
}

function modifierUser($id, $nom, $prenom, $email, $telephone, $role, $photoFile)
{
    global $connexion;

    // 1. Récupérer l’ancien profil
    $stmt = mysqli_prepare($connexion, "SELECT profile FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $oldProfile);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // 2. Gestion du nouveau fichier photo
    $photo = $oldProfile; // par défaut garder l’ancienne photo
    if (!empty($photoFile) && $photoFile['error'] === 0) {
        $ext = strtolower(pathinfo($photoFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $uploadDir = __DIR__ . "/../uploads/profile/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $photoName = "profil_" . uniqid() . "." . $ext;
            $destination = $uploadDir . $photoName;

            if (move_uploaded_file($photoFile['tmp_name'], $destination)) {
                // supprimer l’ancien si existe
                if (!empty($oldProfile) && file_exists($uploadDir.$oldProfile)) {
                    unlink($uploadDir.$oldProfile);
                }
                $photo = $photoName;
            }
        }
    }

    // 3. Update dans la base
    $stmt = mysqli_prepare(
        $connexion,
        "UPDATE users SET nom=?, prenom=?, email=?, telephone=?, role=?, profile=? WHERE id=?"
    );
    if (!$stmt) {
        return "error";
    }
    mysqli_stmt_bind_param($stmt, "ssssssi", $nom, $prenom, $email, $telephone, $role, $photo, $id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $success ? "success" : "error";
}


function getUsers($connexion) {
    $users = [];
    $sql = "SELECT id, nom, prenom, role, profile, email, statut
            FROM users";
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
        "decor" => 0,
        "transport" => 0,
        "cachet" => 0,
        "autre" => 0,
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
        "decor" => 0,
        "transport" => 0,
        "cachet" => 0,
        "autre" => 0,
    ];

    // Préparer la requête avec condition sur la série
    $sql = "SELECT type_depense, SUM(montant) as total 
            FROM depenses 
            WHERE serie_id = ? 
              AND type_depense IN ('decor','transport','cachet','autre')
            GROUP BY type_depense";

    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $serie_id);
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


function getTotaux($connexion) {
    $totaux = [];

    // Total utilisateurs
    $sql = "SELECT COUNT(*) as total FROM users";
    $res = $connexion->query($sql);
    $totaux['users'] = $res->fetch_assoc()['total'];

    // Total acteurs
    $sql = "SELECT COUNT(*) as total FROM acteurs";
    $res = $connexion->query($sql);
    $totaux['acteurs'] = $res->fetch_assoc()['total'];

    // Total séries
    $sql = "SELECT COUNT(*) as total FROM series";
    $res = $connexion->query($sql);
    $totaux['series'] = $res->fetch_assoc()['total'];

    // Total clients
    $sql = "SELECT COUNT(*) as total FROM clients";
    $res = $connexion->query($sql);
    $totaux['clients'] = $res->fetch_assoc()['total'];

    // Total factures
    $sql = "SELECT COUNT(*) as total FROM factures";
    $res = $connexion->query($sql);
    $totaux['factures'] = $res->fetch_assoc()['total'];

    // Total paiements
    $sql = "SELECT COUNT(*) as total FROM paiements";
    $res = $connexion->query($sql);
    $totaux['paiements'] = $res->fetch_assoc()['total'];

    // Total dépenses
    $sql = "SELECT SUM(montant) as total FROM depenses";
    $res = $connexion->query($sql);
    $totaux['depenses'] = $res->fetch_assoc()['total'] ?? 0;

    return $totaux;
}
function getTotauxGeneraux($connexion, $serie_id = null)
{
    $totaux = [
        "total_series" => 0,
        "total_depenses" => 0,
        "total_factures" => 0,
    ];

    // Filtre pour la série si fournie
    $filter = "";
    if ($serie_id !== null) {
        $filter = "WHERE id = " . intval($serie_id);
    }

    // Total séries
    $sqlSeries = "SELECT COUNT(*) as total FROM series $filter";
    $result = mysqli_query($connexion, $sqlSeries);
    if ($row = mysqli_fetch_assoc($result)) {
        $totaux['total_series'] = $row['total'];
    }

    // Total dépenses
    $filterDepenses = ($serie_id !== null) ? "WHERE serie_id = " . intval($serie_id) : "";
    $sqlDepenses = "SELECT SUM(montant) as total_depenses FROM depenses $filterDepenses";
    $result = mysqli_query($connexion, $sqlDepenses);
    if ($row = mysqli_fetch_assoc($result)) {
        $totaux['total_depenses'] = $row['total_depenses'] ?? 0;
    }

    // Total factures type='Facture'
    $filterFactures = ($serie_id !== null) ? "WHERE serie_id = " . intval($serie_id) . " AND type='Facture'" : "WHERE type='Facture'";
    $sqlFactures = "SELECT SUM(total) as total_factures FROM factures $filterFactures";
    $result = mysqli_query($connexion, $sqlFactures);
    if ($row = mysqli_fetch_assoc($result)) {
        $totaux['total_factures'] = $row['total_factures'] ?? 0;
    }

    return $totaux;
}
function getUserById($connexion, $id) {
    $id = intval($id); // sécurité de base

    $sql = "SELECT id, nom, prenom, email, telephone,mot_de_passe AS password, role, profile, statut, created_at 
            FROM users 
            WHERE id = ?";
    
    if ($stmt = mysqli_prepare($connexion, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $user;
    } else {
        return null;
    }
}
function getActeurById($id) {
  global $connexion;
  $id = (int)$id;
  $res = mysqli_query($connexion,"SELECT * FROM acteurs WHERE id=$id");
  return mysqli_fetch_assoc($res);
}

?>