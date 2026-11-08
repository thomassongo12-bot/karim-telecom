<?php
require_once 'config/db.php';
$pdo = getDB();
$B   = BASE_URL;
$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: ' . $B . '/blog.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = ?");
$stmt->execute([$slug]);
$art = $stmt->fetch();
if (!$art) { http_response_code(404); die('<p style="padding:2rem;font-family:sans-serif;">Article introuvable.</p>'); }

$autres = $pdo->prepare("SELECT * FROM articles WHERE slug != ? ORDER BY date_publication DESC LIMIT 3");
$autres->execute([$slug]);
$autresArticles = $autres->fetchAll();

$pageTitle = $art['titre'] . ' | Karim Telecom';
$pageDesc  = mb_substr($art['extrait'],0,160);
$pageJsonLd = json_encode(['@context'=>'https://schema.org','@type'=>'Article','headline'=>$art['titre'],'description'=>$art['extrait'],'datePublished'=>$art['date_publication'],'author'=>['@type'=>'Organization','name'=>'Karim Telecom'],'publisher'=>['@type'=>'Organization','name'=>'Karim Telecom']]);

include 'includes/header.php';
require_once 'includes/icons.php';
?>

<div class="container" style="padding-top:2rem;padding-bottom:2rem;max-width:860px;">
    <div class="breadcrumb">
        <a href="<?= $B ?>/index.php">Accueil</a><span class="sep">/</span>
        <a href="<?= $B ?>/blog.php">Blog</a><span class="sep">/</span>
        <span><?= htmlspecialchars(mb_substr($art['titre'],0,50)) ?>...</span>
    </div>

    <div class="article-header">
        <span class="article-cat"><?= htmlspecialchars($art['categorie']) ?></span>
        <h1 class="article-title"><?= htmlspecialchars($art['titre']) ?></h1>
        <div class="article-meta">
            <span><?= svg('calendar',14) ?> <?= date('d M Y', strtotime($art['date_publication'])) ?></span>
            <span><?= svg('clock',14) ?> <?= $art['temps_lecture'] ?> min de lecture</span>
            <span><?= svg('users',14) ?> Karim Telecom</span>
        </div>
    </div>

    <?php if (!empty($art['image'])): ?>
    <div style="border-radius:var(--radius);overflow:hidden;margin-bottom:2rem;aspect-ratio:16/7;background:#f3f4f6;">
        <img src="<?= $B ?>/uploads/articles/<?= htmlspecialchars($art['image']) ?>"
             alt="<?= htmlspecialchars($art['titre']) ?>"
             style="width:100%;height:100%;object-fit:cover;display:block;">
    </div>
    <?php endif; ?>

    <div class="article-excerpt"><?= htmlspecialchars($art['extrait']) ?></div>
    <div class="article-content"><?= $art['contenu'] ?></div>

    <div class="cta-inline">
        <h3>Prêt à acheter votre téléphone ?</h3>
        <p>Contactez-nous sur WhatsApp pour disponibilité, prix et livraison.</p>
        <a href="<?= waLink('Bonjour Karim Telecom, j\'ai lu votre article et je voudrais commander.') ?>" target="_blank" rel="noopener" class="btn" style="background:#fff;color:#16a34a;font-weight:700;"><?= svg('whatsapp',16) ?> Commander sur WhatsApp</a>
    </div>

    <?php if ($autresArticles): ?>
    <h2 style="font-size:1.3rem;font-weight:800;margin:2rem 0 1.25rem;">Articles similaires</h2>
    <div class="blog-grid" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr));">
        <?php foreach ($autresArticles as $a): ?>
        <a href="<?= $B ?>/article.php?slug=<?= urlencode($a['slug']) ?>" class="blog-card">
            <div class="blog-img" style="aspect-ratio:16/7;">
                <?php if (!empty($a['image'])): ?>
                    <img src="<?= $B ?>/uploads/articles/<?= htmlspecialchars($a['image']) ?>"
                         alt="<?= htmlspecialchars($a['titre']) ?>"
                         style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;">
                <?php else: ?>
                    <div class="blog-img-text"><?= htmlspecialchars($a['titre']) ?></div>
                <?php endif; ?>
                <span class="blog-cat"><?= htmlspecialchars($a['categorie']) ?></span>
            </div>
            <div class="blog-body">
                <p class="blog-date"><?= svg('calendar',13) ?> <?= date('d M Y', strtotime($a['date_publication'])) ?></p>
                <h3 class="blog-title"><?= htmlspecialchars($a['titre']) ?></h3>
                <span class="blog-read">Lire <?= svg('arrow-r',13) ?></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
