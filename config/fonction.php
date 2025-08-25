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
?>