<?php
require '../../../vendor/autoload.php';
use Dompdf\Dompdf;

include '../../../config/fonction.php'; // connexion etc

$facture_id = $_GET['id'] ?? 0;
$facture = getFactureDetails($connexion, $facture_id);
$url_base = "http://localhost/projet_suivi/";

if (!$facture) exit('Facture introuvable');

// Calculs des totaux
$total_ht = 0;
foreach ($facture['designations'] as $d) {
    $total_ht += $d['montant'];
}

$taux_tva = 0.18; // TVA à 18%
$montant_tva = $total_ht * $taux_tva;
$total_ttc = $total_ht + $montant_tva;

// Fonction pour convertir le nombre en lettres
function nombreEnLettres($nombre) {
    $unites = array('', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf');
    $dizaines = array('', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix');
    $dizainesSpeciales = array('onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf');
    
    if ($nombre == 0) {
        return 'zéro';
    }
    
    $texte = '';
    
    // Millions
    if ($nombre >= 1000000) {
        $millions = floor($nombre / 1000000);
        $texte .= nombreEnLettres($millions) . ' million' . ($millions > 1 ? 's' : '') . ' ';
        $nombre %= 1000000;
    }
    
    // Milliers
    if ($nombre >= 1000) {
        $milliers = floor($nombre / 1000);
        if ($milliers == 1) {
            $texte .= 'mille ';
        } else {
            $texte .= nombreEnLettres($milliers) . ' mille ';
        }
        $nombre %= 1000;
    }
    
    // Centaines
    if ($nombre >= 100) {
        $centaines = floor($nombre / 100);
        if ($centaines == 1) {
            $texte .= 'cent ';
        } else {
            $texte .= $unites[$centaines] . ' cent ';
        }
        $nombre %= 100;
    }
    
    // Dizaines et unités
    if ($nombre >= 10 && $nombre <= 19) {
        $texte .= $dizainesSpeciales[$nombre - 11] . ' ';
        $nombre = 0;
    } else if ($nombre >= 20) {
        $dizaine = floor($nombre / 10);
        $texte .= $dizaines[$dizaine] . ' ';
        $nombre %= 10;
        
        if ($dizaine == 7 || $dizaine == 9) {
            $nombre += 10;
            if ($nombre >= 10 && $nombre <= 19) {
                $texte .= $dizainesSpeciales[$nombre - 11] . ' ';
                $nombre = 0;
            }
        }
    }
    
    // Unités
    if ($nombre > 0) {
        $texte .= $unites[$nombre] . ' ';
    }
    
    return trim($texte);
}

// Générer le HTML
$html = '
<html>
<head>
    <style>
       body { 
    font-family: Arial, sans-serif; 
    font-size: 12px; 
}

.container { width: 100%; }

.header { margin-bottom: 20px; }

.logo { text-align: left; margin-bottom: 15px; }

.facture-title { 
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    padding: 5px;
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
}

.info-container { 
    display: flex; 
    justify-content: space-between; 
    margin-bottom: 20px;
}

.info-entreprise, .info-client {
    width: 48%;
}

.info-section { margin-bottom: 5px; }

/* Tableau des produits */
.products-table { 
    width: 100%; 
    border-collapse: collapse; 
    margin-top: 20px; 
    border-left: 1px solid #000;
    border-right: 1px solid #000;
    border-bottom: 1px solid #000;
}

.products-table th { 
    border: 1px solid #000; 
    padding: 8px; 
    background-color: #f2f2f2; 
    text-align: center;
}

.products-table td { padding: 8px; }

.description-cell {
    border-right: 1px solid #000;
    border-bottom: 1px solid #000;
    text-align: left;
}

.quantity-cell, .price-cell, .amount-cell { 
    text-align: center; 
    border-bottom: none; 
}

/* Tableau des totaux */
.total-table {
    width: 100%;
    margin-top: 10px;
}

.total-table td {
    padding: 5px;
}

.total-label {
    text-align: right;
    padding-right: 10px;
    width: 70%;
}

.total-value {
    text-align: right;
    font-weight: bold;
    width: 30%;
    border-bottom: 1px solid #000;
}

/* Texte en italique pour arrêtée */
.arrete-text {
    margin-top: 20px;
    font-style: italic;
}

/* Conditions / cachets */
.conditions-container {
    width: 100%;
    margin-top: 20px;
    text-align: justify; /* permet de séparer les blocs */
}

.conditions-left, .conditions-right {
    display: inline-block;
    width: 45%;
    text-decoration: underline;
    vertical-align: top;
}

.conditions-left { text-align: left; }
.conditions-right { text-align: right; }


/* Colonnes du tableau */
.col-description { width: 50%; }
.col-quantity { width: 15%; }
.col-price { width: 15%; }
.col-amount { width: 20%; }
.footer-text {
    position: fixed;
    bottom: 10px;       /* espace de 10px du bas de la page */
    left: 0;
    width: 100%;
    text-align: center;
    font-size: 10px;    /* tu peux ajuster la taille si besoin */
}
    .footer-text p {
    margin: 0px;
}


    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="' . $url_base . 'assets/images/logo2.png" width="120" height="120" alt="Logo">
            </div>
            <div class="facture-title">
                FACTURE PROFORMA ' . $facture['facture_id'] . '
            </div>
        </div>

        <div class="info-container">
            <div class="info-entreprise">
                <div class="info-section">
                    <strong>Client:</strong> ' . htmlspecialchars($facture['client_nom'] ?? '') . '
                </div>
                <div class="info-section">
                    <strong>Adresse:</strong> ' . htmlspecialchars($facture['entreprise_nom'] ?? '') . '
                </div>
                <div class="info-section">
                    <strong>Téléphone:</strong> ' . htmlspecialchars($facture['client_contact'] ?? '') . '
                </div>
                <div class="info-section">
                    <strong>Date:</strong> ' . htmlspecialchars($facture['date_facture'] ?? '') . '
                </div>
                <div class="info-section">
                    <strong>' . htmlspecialchars($facture['type'] ?? '') . ' : </strong> ' . htmlspecialchars($facture['reference'] ?? '') . '
                </div>
                <div class="info-section">
                    <strong>Marché:</strong> SENEGAL
                </div>
                <div class="info-section">
                    <strong>Validité:</strong> ' . htmlspecialchars($facture['date_validation'] ?? '') . '
                </div>
                <div class="info-section">
                    <strong>Serie:</strong> ' . htmlspecialchars($facture['serie_nom'] ?? '') . '
                </div>
            </div>
        </div>

        <table class="products-table">
            <thead>
                <tr>
                    <th class="col-description">Description</th>
                    <th class="col-quantity">Quantité</th>
                    <th class="col-price">Prix Unitaire</th>
                    <th class="col-amount">Montant</th>
                </tr>
            </thead>
            <tbody>';

foreach ($facture['designations'] as $d) {
    $html .= '<tr>
        <td class="description-cell">' . htmlspecialchars($d['libelle']) . '</td>
        <td class="quantity-cell">' . $d['quantite'] . '</td>
        <td class="price-cell">' . htmlspecialchars($d['prix_unitaire']) . '</td>
        <td class="amount-cell">' . (!empty($d['montant']) ? number_format($d['montant'], 0, ',', ' ') : '') . '</td>

    </tr>';
}

$html .= '
            </tbody>
        </table>

        <table class="total-table">
            <tr>
                <td class="total-label">Total HT:</td>
                <td class="total-value">' . number_format($total_ht, 0, ',', ' ') . ' FCFA</td>
            </tr>
            <tr>
                <td class="total-label">TVA (18%):</td>
                <td class="total-value">' . number_format($montant_tva, 0, ',', ' ') . ' FCFA</td>
            </tr>
            <tr>
                <td class="total-label">Total TTC:</td>
                <td class="total-value">' . number_format($total_ttc, 0, ',', ' ') . ' FCFA</td>
            </tr>
        </table>

        <div class="arrete-text">
            Arrêtée la présente facture proforma à la somme de: <strong>' . nombreEnLettres($total_ttc) . ' francs CFA</strong>
        </div>
        <div class="conditions-container">
            <div class="conditions-left">Condition de paiement</div>
            <div class="conditions-right">Service commercial</div>
        </div>
         <div class="footer-text">
    <p>Sarl au capital de 1 000 000 F CFA Sipres 2 Cité Villa N*36*BP : 15949 Dakar Fann.</p>
    <p>Tél : +221 33 827 60 61*RC: SNDKR 2012 B4100. NINEA 0045445252V2</p>
    <p>BIS : 342560100170*CBAO: 036185019101/34*</p>
    <p>E-MAIL : commercialetyprod19@gmail.com</p>
</div>



    </div>
</body>
</html>
';

// Générer le PDF
$dompdf = new Dompdf();
$dompdf->set_option('isRemoteEnabled', true);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('Facture_' . ($facture['reference'] ?? $facture['facture_id']) . '.pdf', ["Attachment" => false]);