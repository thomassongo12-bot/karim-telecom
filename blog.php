<?php
require_once 'config/db.php';
$pdo = getDB();
$B   = BASE_URL;
$pageTitle = 'Blog — Guides & Conseils | Karim Telecom';
$pageDesc  = 'Guides d\'achat, comparatifs, conseils pour acheter un téléphone au Cameroun.';
$articles = $pdo->query("SELECT * FROM articles ORDER BY date_publication DESC")->fetchAll();
include 'includes/header.php';
require_once 'includes/icons.php';
?>

<div class="page-hero">
    <div class="container">
        <h1>Guides &amp; Conseils Téléphones</h1>
        <p>Tout ce qu'il faut savoir pour bien choisir votre smartphone au Cameroun</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if (empty($articles)): ?>
        <div style="text-align:center;padding:3rem;color:var(--gray-400);">
            <?= svg('article',48,'','opacity:.2;') ?>
            <p style="font-size:.9rem;">Aucun article pour le moment.</p>
        </div>
        <?php else: ?>
        <div class="blog-grid">
            <?php foreach ($articles as $art): ?>
            <a href="<?= $B ?>/article.php?slug=<?= urlencode($art['slug']) ?>" class="blog-card">
                <div class="blog-img">
                    <?php if (!empty($art['image'])): ?>
                        <img src="<?= $B ?>/uploads/articles/<?= htmlspecialchars($art['image']) ?>"
                             alt="<?= htmlspecialchars($art['titre']) ?>"
                             style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                        <div class="blog-img-text"><?= htmlspecialchars($art['titre']) ?></div>
                    <?php endif; ?>
                    <span class="blog-cat"><?= htmlspecialchars($art['categorie']) ?></span>
                </div>
                <div class="blog-body">
                    <p class="blog-date"><?= svg('calendar',13) ?> <?= date('d M Y', strtotime($art['date_publication'])) ?> · <?= $art['temps_lecture'] ?> min</p>
                    <h2 class="blog-title"><?= htmlspecialchars($art['titre']) ?></h2>
                    <p class="blog-excerpt"><?= htmlspecialchars(mb_substr($art['extrait'],0,120)) ?>...</p>
                    <span class="blog-read">Lire l'article <?= svg('arrow-r',13) ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
