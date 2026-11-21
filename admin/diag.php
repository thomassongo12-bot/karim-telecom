<?php
/**
 * Karim Telecom — Diagnostic Hostinger
 * Accès : /admin/diag.php
 * SUPPRIMER ce fichier après diagnostic !
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<style>body{font-family:monospace;padding:1rem;background:#f8fafc;}
.ok{color:#16a34a;font-weight:700;}
.err{color:#dc2626;font-weight:700;}
.warn{color:#d97706;font-weight:700;}
h2{margin:1rem 0 .5rem;border-bottom:1px solid #e2e8f0;}
</style>';

echo '<h1>Diagnostic Karim Telecom</h1>';

// PHP
echo '<h2>PHP</h2>';
echo 'Version PHP : <b>' . phpversion() . '</b><br>';
echo 'PDO MySQL : ' . (extension_loaded('pdo_mysql') ? '<span class="ok">OK</span>' : '<span class="err">MANQUANT</span>') . '<br>';
echo 'GD (images) : ' . (extension_loaded('gd') ? '<span class="ok">OK</span>' : '<span class="warn">Absent</span>') . '<br>';
echo 'mbstring : ' . (extension_loaded('mbstring') ? '<span class="ok">OK</span>' : '<span class="warn">Absent</span>') . '<br>';

// Chemins
echo '<h2>Chemins</h2>';
echo '__DIR__ : <b>' . __DIR__ . '</b><br>';
echo 'dirname(__DIR__) : <b>' . dirname(__DIR__) . '</b><br>';
$root = dirname(__DIR__);

// Dossiers uploads
echo '<h2>Dossiers uploads</h2>';
$dirs = ['uploads', 'uploads/produits', 'uploads/site', 'uploads/categories', 'uploads/articles', 'uploads/avis'];
foreach ($dirs as $d) {
    $path = $root . '/' . $d;
    if (!is_dir($path)) {
        $ok = @mkdir($path, 0755, true);
        echo $d . ' : ' . ($ok ? '<span class="ok">Créé</span>' : '<span class="err">IMPOSSIBLE DE CRÉER</span>') . ' — ' . $path . '<br>';
    } else {
        $writable = is_writable($path);
        echo $d . ' : <span class="ok">Existe</span> — ' . ($writable ? '<span class="ok">Inscriptible</span>' : '<span class="err">NON INSCRIPTIBLE</span>') . '<br>';
    }
}

// DB
echo '<h2>Base de données</h2>';
require_once $root . '/config/db.php';
try {
    $pdo = getDB();
    echo 'Connexion : <span class="ok">OK</span><br>';
    
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo 'Tables trouvées : <b>' . implode(', ', $tables) . '</b><br>';
    
    $required = ['produits','produit_images','categories','avis','articles','villes','produit_villes','site_visits','produit_clics'];
    foreach ($required as $t) {
        echo $t . ' : ' . (in_array($t, $tables) ? '<span class="ok">OK</span>' : '<span class="err">MANQUANTE</span>') . '<br>';
    }
} catch (Exception $e) {
    echo 'Connexion : <span class="err">ERREUR — ' . htmlspecialchars($e->getMessage()) . '</span><br>';
}

// Config
echo '<h2>Configuration</h2>';
echo 'BASE_URL : <b>"' . BASE_URL . '"</b><br>';
echo 'WA_NUMBER : <b>' . WA_NUMBER . '</b><br>';

echo '<hr><p style="color:#94a3b8;font-size:.8rem;">⚠️ Supprimez ce fichier après diagnostic : /admin/diag.php</p>';
?>
