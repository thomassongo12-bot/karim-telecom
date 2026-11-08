<?php
/**
 * Karim Telecom — Sitemap XML dynamique
 * Accès : /Karim_Telecom/sitemap.php
 */
require_once 'config/db.php';
$pdo = getDB();

$host    = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$base    = $host . BASE_URL;
$today   = date('Y-m-d');

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Pages statiques -->
    <url><loc><?= $base ?>/index.php</loc><changefreq>daily</changefreq><priority>1.0</priority><lastmod><?= $today ?></lastmod></url>
    <url><loc><?= $base ?>/catalogue.php</loc><changefreq>daily</changefreq><priority>0.9</priority><lastmod><?= $today ?></lastmod></url>
    <url><loc><?= $base ?>/blog.php</loc><changefreq>weekly</changefreq><priority>0.7</priority><lastmod><?= $today ?></lastmod></url>
    <url><loc><?= $base ?>/a-propos.php</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
    <url><loc><?= $base ?>/contact.php</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>

    <!-- Produits -->
    <?php
    $produits = $pdo->query("SELECT slug, date_ajout FROM produits ORDER BY date_ajout DESC")->fetchAll();
    foreach ($produits as $p):
    ?>
    <url>
        <loc><?= $base ?>/produit.php?slug=<?= urlencode($p['slug']) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.85</priority>
        <lastmod><?= substr($p['date_ajout'], 0, 10) ?></lastmod>
    </url>
    <?php endforeach; ?>

    <!-- Articles -->
    <?php
    $articles = $pdo->query("SELECT slug, date_publication FROM articles ORDER BY date_publication DESC")->fetchAll();
    foreach ($articles as $a):
    ?>
    <url>
        <loc><?= $base ?>/article.php?slug=<?= urlencode($a['slug']) ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
        <lastmod><?= $a['date_publication'] ?></lastmod>
    </url>
    <?php endforeach; ?>

    <!-- Villes -->
    <?php
    $villes = $pdo->query("SELECT slug FROM villes")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($villes as $v):
    ?>
    <url>
        <loc><?= $base ?>/ville.php?v=<?= urlencode($v) ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.65</priority>
    </url>
    <?php endforeach; ?>
</urlset>
