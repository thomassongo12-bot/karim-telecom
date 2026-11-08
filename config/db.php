<?php
// =============================================
// Karim Telecom — Configuration
// =============================================

// Base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'karim_telecom');

// WhatsApp
define('WA_NUMBER', '237692091345');
define('SITE_NAME', 'Karim Telecom');

// URL de base
define('BASE_URL', '/Karim_Telecom');

// Connexion PDO
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            die('<div style="padding:20px;background:#fee2e2;border:1px solid #f87171;border-radius:8px;font-family:sans-serif;max-width:600px;margin:2rem auto;">
                <h3>Erreur de connexion</h3>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Vérifiez que MySQL est démarré et que la base <code>karim_telecom</code> existe.</p>
            </div>');
        }
    }
    return $pdo;
}

// Helpers
function formatPrix($prix) {
    return number_format($prix, 0, ',', ' ') . ' FCFA';
}

function getProduitImages($pdo, $produitId, $limit = 6) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM produit_images WHERE produit_id = ? ORDER BY is_main DESC, ordre ASC LIMIT ?");
        $stmt->execute([$produitId, $limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) { return []; }
}

function getMainImage($pdo, $produitId) {
    try {
        $stmt = $pdo->prepare("SELECT filename FROM produit_images WHERE produit_id = ? ORDER BY is_main DESC, ordre ASC LIMIT 1");
        $stmt->execute([$produitId]);
        $img = $stmt->fetchColumn();
        return $img ? BASE_URL . '/uploads/produits/' . $img : '';
    } catch (PDOException $e) { return ''; }
}

function waLink($message = '') {
    if (empty($message)) {
        $message = 'Bonjour Karim Telecom, je voudrais des informations sur vos téléphones.';
    }
    return 'https://wa.me/' . WA_NUMBER . '?text=' . rawurlencode($message);
}

// Formater le numéro WhatsApp pour l'affichage (237692091345 → +237 692 091 345)
function getSitePhone() {
    $num = WA_NUMBER;
    if (strlen($num) >= 12) {
        return '+' . substr($num, 0, 3) . ' ' . substr($num, 3, 3) . ' ' . substr($num, 6, 3) . ' ' . substr($num, 9);
    }
    return '+' . $num;
}

// Lien tel: basé sur WA_NUMBER
function telLink() {
    return 'tel:+' . WA_NUMBER;
}

// Récupérer un paramètre du site (logo, favicon, nom...)
function getParametre($pdo, $cle) {
    try {
        $stmt = $pdo->prepare("SELECT $cle FROM parametres WHERE id = 1");
        $stmt->execute();
        $val = $stmt->fetchColumn();
        return $val ?: '';
    } catch (PDOException $e) { return ''; }
}
?>
