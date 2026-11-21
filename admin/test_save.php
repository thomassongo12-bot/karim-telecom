<?php
/**
 * TEST ENREGISTREMENT PRODUIT — À SUPPRIMER APRÈS DEBUG
 * Accès : /admin/test_save.php
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/db.php';
require_once 'auth.php';
$pdo = getDB();

echo '<style>body{font-family:monospace;padding:1rem;background:#f8fafc;font-size:13px;}
.ok{color:green;font-weight:700;} .err{color:red;font-weight:700;} h2{margin:1rem 0 .5rem;}</style>';
echo '<h1>Test Enregistrement Produit</h1>';

// 1. Structure de la table
echo '<h2>1. Structure table produits</h2>';
try {
    $cols = $pdo->query("DESCRIBE produits")->fetchAll();
    foreach ($cols as $c) {
        $flag = '';
        if ($c['Field'] === 'categorie') {
            $flag = ' <span class="err">← VÉRIFIER TYPE</span>';
        }
        echo $c['Field'] . ' : <b>' . $c['Type'] . '</b>' . $flag . '<br>';
    }
} catch (Exception $e) {
    echo '<span class="err">ERREUR: ' . $e->getMessage() . '</span><br>';
}

// 2. Test INSERT minimal
echo '<h2>2. Test INSERT minimal</h2>';
try {
    $testSlug = 'test-produit-' . time();
    $pdo->prepare("INSERT INTO produits (nom, slug, marque, prix, categorie, etat, garantie, disponibilite, en_stock, vedette, description)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, 0, '')")
        ->execute(['Test Produit', $testSlug, 'Apple', 100000, 'telephone', 'Neuf', '1 an', 'Douala']);
    $newId = $pdo->lastInsertId();
    echo '<span class="ok">INSERT OK</span> — id=' . $newId . '<br>';
    // Nettoyer
    $pdo->prepare("DELETE FROM produits WHERE id=?")->execute([$newId]);
    echo 'Nettoyé.<br>';
} catch (Exception $e) {
    echo '<span class="err">ERREUR INSERT: ' . $e->getMessage() . '</span><br>';
}

// 3. Test avec categorie dynamique
echo '<h2>3. Catégories disponibles</h2>';
try {
    $cats = $pdo->query("SELECT * FROM categories ORDER BY ordre")->fetchAll();
    if (empty($cats)) {
        echo '<span class="err">Aucune catégorie en base !</span><br>';
    } else {
        foreach ($cats as $c) {
            // Tester si ce slug passe dans categorie
            try {
                $testSlug2 = 'test-cat-' . time() . '-' . $c['id'];
                $pdo->prepare("INSERT INTO produits (nom, slug, marque, prix, categorie, etat, garantie, disponibilite, en_stock, vedette, description)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, 0, '')")
                    ->execute(['Test ' . $c['nom'], $testSlug2, $c['nom'], 100000, $c['slug'], 'Neuf', '1 an', 'Douala']);
                $newId2 = $pdo->lastInsertId();
                echo 'Slug "' . $c['slug'] . '" : <span class="ok">OK</span><br>';
                $pdo->prepare("DELETE FROM produits WHERE id=?")->execute([$newId2]);
            } catch (Exception $e2) {
                echo 'Slug "' . $c['slug'] . '" : <span class="err">ERREUR — ' . $e2->getMessage() . '</span><br>';
            }
        }
    }
} catch (Exception $e) {
    echo '<span class="err">ERREUR catégories: ' . $e->getMessage() . '</span><br>';
}

// 4. Test produit_villes
echo '<h2>4. Test produit_villes</h2>';
try {
    $villes = $pdo->query("SELECT * FROM villes LIMIT 1")->fetchAll();
    echo count($villes) . ' ville(s) trouvée(s) : <span class="ok">OK</span><br>';
} catch (Exception $e) {
    echo '<span class="err">ERREUR villes: ' . $e->getMessage() . '</span><br>';
}

// 5. Dossier uploads
echo '<h2>5. Dossier uploads/produits</h2>';
$uploadDir = dirname(__DIR__) . '/uploads/produits/';
echo 'Chemin : ' . $uploadDir . '<br>';
echo 'Existe : ' . (is_dir($uploadDir) ? '<span class="ok">OUI</span>' : '<span class="err">NON</span>') . '<br>';
if (!is_dir($uploadDir)) {
    $ok = @mkdir($uploadDir, 0755, true);
    echo 'Création : ' . ($ok ? '<span class="ok">OK</span>' : '<span class="err">IMPOSSIBLE</span>') . '<br>';
} else {
    echo 'Inscriptible : ' . (is_writable($uploadDir) ? '<span class="ok">OUI</span>' : '<span class="err">NON</span>') . '<br>';
}

// 6. Simuler exactement le POST du formulaire
echo '<h2>6. Simulation POST complet</h2>';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
    try {
        $nom = 'Produit Test POST';
        $slug = 'produit-test-post-' . time();
        $marque = 'Apple';
        $prix = 500000;
        $categorie = $_POST['categorie_test'] ?? 'telephone';
        
        $pdo->prepare("INSERT INTO produits (nom,slug,marque,prix,categorie,stockage,couleur,etat,garantie,disponibilite,en_stock,vedette,description)
                       VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([$nom,$slug,$marque,$prix,$categorie,'128 Go','Noir','Neuf','1 an','Douala',1,0,'Test description']);
        $id = $pdo->lastInsertId();
        echo '<span class="ok">INSERT COMPLET OK — id=' . $id . '</span><br>';
        $pdo->prepare("DELETE FROM produits WHERE id=?")->execute([$id]);
        echo 'Nettoyé.<br>';
    } catch (Exception $e) {
        echo '<span class="err">ERREUR: ' . $e->getMessage() . '</span><br>';
    }
}

echo '<h2>Tester avec une catégorie</h2>';
echo '<form method="POST">';
echo '<select name="categorie_test">';
try {
    $cats = $pdo->query("SELECT slug, nom FROM categories ORDER BY ordre")->fetchAll();
    foreach ($cats as $c) echo '<option value="' . $c['slug'] . '">' . $c['nom'] . ' (' . $c['slug'] . ')</option>';
    echo '<option value="telephone">telephone</option>';
    echo '<option value="accessoire">accessoire</option>';
} catch (Exception $e) {}
echo '</select> ';
echo '<button type="submit">Tester ce slug</button>';
echo '</form>';

echo '<hr><p style="color:#94a3b8;font-size:.8rem;">⚠️ Supprimer ce fichier après debug</p>';
?>
