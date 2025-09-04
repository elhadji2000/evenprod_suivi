<?php 

// Connectez-vous à votre base de données MySQL
function connexionBD()
{
    $connexion = mysqli_connect("localhost", "root", "", "evenprod_db");

    // Vérifiez la connexion
    if ($connexion === false) {
        die("Erreur : Impossible de se connecter. " . mysqli_connect_error());
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
    $query = "SELECT * FROM `users` WHERE `email` = ? AND `mot_de_passe` = ?";
    
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
// ************FONCTION POUR RECUPERER UNE SERIE PAR SON ID********************
function getSerieById($id)
{
    // on récupère la connexion globale
    global $connexion;

    // sécurisation de l'id
    $id = (int)$id;

    // requête SQL
    $sql = "SELECT * FROM series WHERE id = $id LIMIT 1";
    $result = mysqli_query($connexion, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result); // retourne un tableau associatif
    } else {
        return null; // rien trouvé
    }
}

function getActeursBySerieId($serieId)
{
    global $connexion;

    $serieId = (int)$serieId;

    $sql = "
        SELECT a.*
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
function generateTournageReference($serieId) {
    global $connexion;

    $serieId = (int)$serieId;
    $year = date('y'); // année actuelle sur 2 chiffres

    // Compter le nombre de tournages déjà existants pour cette série
    $sql = "SELECT COUNT(*) as count FROM tournages WHERE serie_id = $serieId";
    $result = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($result);
    $num = $row['count'] + 1;

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
                       VALUES ($serieId, $tournageId, 'cachet', $totalCachet, $date)";
        mysqli_query($connexion, $sqlDepense);
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

function ajouterPartenaire($ninea, $nom, $email, $contact, $logoFile)
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
        "INSERT INTO clients (ninea, nom, email, contact, logo) VALUES (?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param($stmt, "sssss", $ninea, $nom, $email, $contact, $logo);
    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $success;
}
function getClients($connexion) {
    $sql = "SELECT id, ninea, nom, logo, email, contact, created_at 
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

function ajouterFacture($connexion, $client, $serie_id, $date, $description, $libelles, $prixUnitaires, $quantites, $montants) {
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
            $pu  = $connexion->real_escape_string(trim($prixUnitaires[$i]));
            $qte = (int)$quantites[$i];
            $mt  = (float)$montants[$i];

            $sql2 = "INSERT INTO designation (facture_id, libelle, prix_unitaire, quantite, montant) 
                     VALUES ('$facture_id', '$lib', '$pu', '$qte', '$mt')";
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
    $sql = "SELECT f.id, f.type, f.date_facture, f.description, f.total, 
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
    $sql = "SELECT f.id, f.description, f.total, c.nom AS client_nom, c.ninea, c.contact
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
                   c.nom AS client_nom, c.contact AS client_contact, c.ninea AS client_ninea,
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

?>