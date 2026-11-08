<?php
require_once 'config/db.php';
$pdo = getDB();
$B   = BASE_URL;
require_once 'includes/tracker.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: ' . $B . '/catalogue.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM produits WHERE slug = ?");
$stmt->execute([$slug]);
$p = $stmt->fetch();
if (!$p) { http_response_code(404); die('<p style="padding:2rem;font-family:sans-serif;">Produit introuvable. <a href="'.$B.'/catalogue.php">Retour</a></p>'); }

trackVisit($pdo, '/produit/' . $slug);
trackClic($pdo, $p['id']);

$sim = $pdo->prepare("SELECT * FROM produits WHERE categorie = ? AND slug != ? LIMIT 4");
$sim->execute([$p['categorie'], $slug]);
$similaires = $sim->fetchAll();

$avis   = $pdo->query("SELECT * FROM avis ORDER BY RAND() LIMIT 3")->fetchAll();
$photos = getProduitImages($pdo, $p['id']);

$pageTitle  = $p['nom'] . ' — ' . formatPrix($p['prix']) . ' | Karim Telecom Douala';
$pageDesc   = 'Achetez le ' . $p['nom'] . ' à ' . formatPrix($p['prix']) . ' chez Karim Telecom Douala. Paiement à la livraison, garantie ' . $p['garantie'] . '. 100% original, boîte scellée.';
$waMsg      = 'Bonjour Karim Telecom, je voudrais commander le ' . $p['nom'] . ' à ' . formatPrix($p['prix']) . '. Est-il disponible ?';

// Image OG
$mainImgUrl = getMainImage($pdo, $p['id']);
if ($mainImgUrl) $pageOgImage = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $mainImgUrl;

$pageJsonLd = json_encode([
    '@context' => 'https://schema.org', '@type' => 'Product',
    'name'        => $p['nom'],
    'description' => $p['description'] ?: $pageDesc,
    'sku'         => 'KT-' . $p['id'],
    'brand'       => ['@type'=>'Brand','name'=>$p['marque']],
    'image'       => $mainImgUrl ? [(isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$mainImgUrl] : [],
    'offers'      => [
        '@type'         => 'Offer',
        'price'         => $p['prix'],
        'priceCurrency' => 'XAF',
        'availability'  => $p['en_stock'] ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        'seller'        => ['@type'=>'LocalBusiness','name'=>'Karim Telecom','address'=>['@type'=>'PostalAddress','addressLocality'=>'Douala','addressCountry'=>'CM']],
        'priceValidUntil' => date('Y-12-31'),
    ],
    'aggregateRating' => ['@type'=>'AggregateRating','ratingValue'=>'4.9','reviewCount'=>'127','bestRating'=>'5'],
]);

$avatarColors = ['#1a56db','#f97316','#16a34a','#7c3aed','#dc2626'];
include 'includes/header.php';
require_once 'includes/icons.php';
?>

<div class="container" style="padding-top:1.5rem;padding-bottom:1.5rem;">
    <div class="breadcrumb">
        <a href="<?= $B ?>/index.php">Accueil</a><span class="sep">/</span>
        <a href="<?= $B ?>/catalogue.php"><?= $p['categorie'] === 'telephone' ? 'Téléphones' : 'Accessoires' ?></a><span class="sep">/</span>
        <span><?= htmlspecialchars($p['nom']) ?></span>
    </div>

    <div class="product-detail-grid">
        <!-- COLONNE IMAGE -->
        <div class="product-gallery">
            <!-- Image principale -->
            <div class="product-main-img-wrap" id="mainImgWrap">
                <?php if (!empty($photos)): ?>
                    <img src="<?= BASE_URL ?>/uploads/produits/<?= htmlspecialchars($photos[0]['filename']) ?>"
                         alt="<?= htmlspecialchars($p['nom']) ?>"
                         id="main-product-img"
                         class="product-main-img-el">
                <?php else: ?>
                    <div class="product-img-placeholder" style="width:100%;height:100%;">
                        <?= svg($p['categorie'] === 'accessoire' ? 'headset' : 'phone', 80, '', 'opacity:.12;') ?>
                        <span style="font-size:.85rem;color:#6b7280;"><?= htmlspecialchars($p['nom']) ?></span>
                    </div>
                <?php endif; ?>
                <!-- Badge état -->
                <span style="position:absolute;top:.85rem;left:.85rem;background:#1a56db;color:#fff;font-size:.72rem;font-weight:700;padding:.25rem .7rem;border-radius:50px;z-index:2;">
                    <?= htmlspecialchars($p['etat']) ?>
                </span>
            </div>

            <!-- Thumbnails -->
            <?php if (count($photos) > 1): ?>
            <div class="product-thumbs">
                <?php foreach ($photos as $i => $photo): ?>
                <div onclick="switchPhoto('<?= BASE_URL ?>/uploads/produits/<?= htmlspecialchars($photo['filename']) ?>', this)"
                     class="thumb-item <?= $i===0?'thumb-active':'' ?>">
                    <img src="<?= BASE_URL ?>/uploads/produits/<?= htmlspecialchars($photo['filename']) ?>"
                         alt="Vue <?= $i+1 ?>">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Dispo -->
            <?php if ($p['en_stock']): ?>
            <div class="product-dispo">
                <?= svg('check-circle',16) ?> Disponible — Livraison possible aujourd'hui à Douala
            </div>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <p class="brand-label"><?= htmlspecialchars($p['marque']) ?></p>
            <h1><?= htmlspecialchars($p['nom']) ?></h1>
            <p class="big-price"><?= formatPrix($p['prix']) ?></p>

            <!-- Badges paiement -->
            <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1rem;">
                <span class="delivery-note"><?= svg('cash',14) ?> Paiement à la livraison</span>
                <span class="installment-badge">
                    <?= svg('receipt',14) ?>
                    Paiement en tranches — Avance 50% : <strong><?= formatPrix($p['prix'] / 2) ?></strong>
                </span>
            </div>

            <div style="display:flex;gap:.4rem;align-items:center;margin-bottom:1rem;">
                <?php for($i=0;$i<5;$i++): ?><?= svg('star-solid',16,'','color:#fbbf24;') ?><?php endfor; ?>
                <span style="font-size:.82rem;color:#6b7280;margin-left:.3rem;">4.9/5 (127 avis)</span>
            </div>

            <p style="color:#374151;font-size:.9rem;line-height:1.7;margin-bottom:1.25rem;"><?= htmlspecialchars($p['description']) ?></p>

            <table class="spec-table" style="margin-bottom:1.25rem;">
                <tr><td>Marque</td><td><?= htmlspecialchars($p['marque']) ?></td></tr>
                <tr><td>Modèle</td><td><?= htmlspecialchars($p['nom']) ?></td></tr>
                <?php if ($p['stockage']): ?><tr><td>Stockage</td><td><?= htmlspecialchars($p['stockage']) ?></td></tr><?php endif; ?>
                <tr><td>Couleur</td><td><?= htmlspecialchars($p['couleur']) ?></td></tr>
                <tr><td>État</td><td><?= htmlspecialchars($p['etat']) ?></td></tr>
                <tr><td>Garantie</td><td><?= htmlspecialchars($p['garantie']) ?></td></tr>
                <tr><td>Disponibilité</td><td><?= htmlspecialchars($p['disponibilite']) ?></td></tr>
                <tr><td>Stock</td><td style="color:<?= $p['en_stock']?'#16a34a':'#dc2626' ?>;font-weight:600;display:flex;align-items:center;gap:.3rem;">
                    <?= svg($p['en_stock']?'check':'x',14) ?> <?= $p['en_stock']?'En stock':'Rupture' ?>
                </td></tr>
            </table>

            <div style="display:flex;flex-direction:column;gap:.75rem;margin-bottom:1.25rem;">
                <a href="<?= waLink($waMsg) ?>" target="_blank" rel="noopener" class="btn btn-whatsapp btn-lg btn-full"><?= svg('whatsapp',18) ?> Commander maintenant sur WhatsApp</a>
                <a href="<?= telLink() ?>" class="btn btn-outline btn-lg btn-full"><?= svg('phone-call',18) ?> Appeler la boutique</a>
            </div>

            <div class="why-us">
                <h3>Pourquoi acheter chez Karim Telecom ?</h3>
                <div class="why-us-grid">
                    <div class="why-us-item"><?= svg('box',16) ?> Boîte scellée originale</div>
                    <div class="why-us-item"><?= svg('shield',16) ?> Garantie officielle</div>
                    <div class="why-us-item"><?= svg('receipt',16) ?> Facture fournie</div>
                    <div class="why-us-item"><?= svg('cash',16) ?> Paiement à la livraison</div>
                    <div class="why-us-item"><?= svg('headset',16) ?> SAV WhatsApp 7j/7</div>
                    <div class="why-us-item"><?= svg('store',16) ?> Boutique physique</div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($avis)): ?>
    <section style="margin-bottom:3rem;">
        <h2 style="font-size:1.4rem;font-weight:800;margin-bottom:1.25rem;">Avis clients</h2>
        <div class="reviews-grid">
            <?php foreach ($avis as $i => $a): ?>
            <div class="review-card">
                <div class="review-header">
                    <div class="review-avatar" style="background:<?= $avatarColors[$i%count($avatarColors)] ?>"><?= strtoupper(mb_substr($a['nom'],0,1)) ?></div>
                    <div class="review-meta">
                        <strong><?= htmlspecialchars($a['nom']) ?></strong>
                        <span class="review-city"><?= svg('map-pin',12) ?> <?= htmlspecialchars($a['ville']) ?></span>
                    </div>
                </div>
                <div class="review-stars"><?php for($s=1;$s<=5;$s++): ?><?= svg('star-solid',14,$s<=$a['note']?'':'empty',$s<=$a['note']?'color:#fbbf24;':'color:#e2e8f0;') ?><?php endfor; ?></div>
                <p class="review-text">"<?= htmlspecialchars($a['texte']) ?>"</p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($similaires): ?>
    <section style="margin-bottom:2rem;">
        <h2 style="font-size:1.4rem;font-weight:800;margin-bottom:1.25rem;">Produits similaires</h2>
        <div class="product-grid">
            <?php foreach ($similaires as $s): $sImg = getMainImage($pdo,$s['id']); ?>
            <div class="product-card">
                <div class="product-img">
                    <a href="<?= $B ?>/produit.php?slug=<?= urlencode($s['slug']) ?>" class="product-img-link" tabindex="-1" aria-label="Voir <?= htmlspecialchars($s['nom']) ?>">
                        <?php if ($sImg): ?>
                        <img src="<?= htmlspecialchars($sImg) ?>" alt="<?= htmlspecialchars($s['nom']) ?>" loading="lazy"
                             style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:contain;padding:.75rem;background:#f8fafc;">
                        <?php else: ?>
                        <div class="product-img-placeholder"><?= svg('phone',48) ?><span><?= htmlspecialchars($s['nom']) ?></span></div>
                        <?php endif; ?>
                    </a>
                    <span class="badge-stock <?= $s['en_stock']?'badge-instock':'badge-outstock' ?>"><?= svg($s['en_stock']?'check':'x',11) ?> <?= $s['en_stock']?'En stock':'Rupture' ?></span>
                    <span class="badge-new"><?= htmlspecialchars($s['etat']) ?></span>
                </div>
                <div class="product-body">
                    <p class="product-brand"><?= htmlspecialchars($s['marque']) ?></p>
                    <h3 class="product-name"><?= htmlspecialchars($s['nom']) ?></h3>
                    <p class="product-price"><?= formatPrix($s['prix']) ?></p>
                    <div class="product-installment">
                        <?= svg('receipt',11) ?> Tranches — 50% : <strong><?= formatPrix($s['prix']/2) ?></strong>
                    </div>
                    <div class="product-actions">
                        <a href="<?= $B ?>/produit.php?slug=<?= urlencode($s['slug']) ?>" class="btn btn-outline btn-sm btn-full">Voir le produit</a>
                        <a href="<?= waLink('Bonjour, je suis intéressé par le '.$s['nom'].' à '.formatPrix($s['prix']).'.') ?>" target="_blank" rel="noopener" class="btn btn-whatsapp btn-sm btn-full"><?= svg('whatsapp',15) ?> WhatsApp</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
function switchPhoto(url, el) {
    var img = document.getElementById('main-product-img');
    if (img) {
        img.style.opacity = '0';
        img.style.transform = 'scale(.97)';
        setTimeout(function(){
            img.src = url;
            img.style.transition = 'opacity .25s, transform .25s';
            img.style.opacity = '1';
            img.style.transform = 'scale(1)';
        }, 150);
    }
    document.querySelectorAll('.thumb-item').forEach(function(t){ t.classList.remove('thumb-active'); });
    el.classList.add('thumb-active');
}
</script>
<?php include 'includes/footer.php'; ?>
