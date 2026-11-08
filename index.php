<?php
require_once 'config/db.php';
$pdo = getDB();
require_once 'includes/tracker.php';
trackVisit($pdo, '/index');

$pageTitle = 'Karim Telecom — Téléphones & Accessoires à Douala | Paiement à la livraison';
$pageDesc  = 'Achetez vos téléphones iPhone, Samsung, Xiaomi à Douala. Paiement à la livraison, garantie officielle, boutique physique Akwa. +500 clients satisfaits.';

$pageJsonLd = json_encode([
    '@context' => 'https://schema.org', '@type' => 'LocalBusiness',
    'name' => 'Karim Telecom', 'telephone' => '+' . WA_NUMBER,
    'address' => ['@type'=>'PostalAddress','streetAddress'=>'Carrefour Warda, Akwa','addressLocality'=>'Douala','addressCountry'=>'CM'],
    'geo' => ['@type'=>'GeoCoordinates','latitude'=>4.0511,'longitude'=>9.7679],
    'openingHoursSpecification' => [
        ['@type'=>'OpeningHoursSpecification','dayOfWeek'=>['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],'opens'=>'08:00','closes'=>'20:00'],
        ['@type'=>'OpeningHoursSpecification','dayOfWeek'=>['Sunday'],'opens'=>'10:00','closes'=>'18:00'],
    ],
    'aggregateRating' => ['@type'=>'AggregateRating','ratingValue'=>'4.9','reviewCount'=>'127'],
]);

$vedettes = $pdo->query("SELECT * FROM produits WHERE vedette=1 AND en_stock=1 LIMIT 6")->fetchAll();
foreach ($vedettes as &$pv) { $pv['_main_image'] = getMainImage($pdo, $pv['id']); } unset($pv);

$avis     = $pdo->query("SELECT * FROM avis ORDER BY date_avis DESC LIMIT 3")->fetchAll();
$articles = $pdo->query("SELECT * FROM articles ORDER BY date_publication DESC LIMIT 3")->fetchAll();
$villes   = $pdo->query("SELECT * FROM villes")->fetchAll();

include 'includes/header.php';
require_once 'includes/icons.php';
$B = BASE_URL;

$avatarColors = ['#1a56db','#f97316','#16a34a','#7c3aed','#dc2626','#0891b2'];
$villeIcons   = ['douala'=>'building','yaounde'=>'building','bafoussam'=>'map-pin','bamenda'=>'map-pin','garoua'=>'map-pin'];
?>

<!-- HERO SLIDER -->
<?php
$imgDir   = __DIR__ . '/images/';
$imgFiles = ['h1.png','h2.png','h3.png','h4.jpg'];
$sliderImages = [];
foreach ($imgFiles as $f) {
    if (file_exists($imgDir . $f)) {
        $sliderImages[] = ['src' => $B . '/images/' . $f, 'file' => $f];
    }
}
if (empty($sliderImages)) {
    $sliderImages = [
        ['src' => 'https://placehold.co/1400x560/1a56db/ffffff?text=Karim+Telecom+Douala', 'file' => 'fallback1'],
        ['src' => 'https://placehold.co/1400x560/0f172a/f97316?text=Paiement+a+la+livraison', 'file' => 'fallback2'],
    ];
}
$sliderData = [
    ['title'=>'L\'excellence à portée de main.','sub'=>'Découvrez l\'iPhone 18 Pro Max. Un design exceptionnel, une technologie de pointe et une expérience qui redéfinit les standards du smartphone.'],
    ['title'=>'Passez à un autre niveau.','sub'=>'Repoussez les limites de votre expérience mobile. Profitez d\'un concentré de puissance, d\'élégance et d\'innovation.'],
    ['title'=>'Faites vibrer chaque instant.','sub'=>'Un son puissant, des basses profondes et une expérience audio immersive. La JBL Boombox transforme chaque moment en une expérience inoubliable.'],
    ['title'=>'Le plaisir d\'écouter, autrement.','sub'=>'Découvrez les écouteurs qui vous accompagnent partout. Profitez de votre musique, passez vos appels et vivez chaque instant sans contraintes.'],
];
$totalSlides = count($sliderImages);
?>

<div id="heroSlider" style="position:relative;width:100%;height:500px;overflow:hidden;background:#0f172a;">
    <?php foreach ($sliderImages as $i => $img):
        $d = $sliderData[$i % count($sliderData)];
    ?>
    <div class="hslide" id="hslide-<?= $i ?>"
         style="position:absolute;top:0;left:0;width:100%;height:100%;
                opacity:<?= $i===0?'1':'0' ?>;
                transition:opacity 1s ease-in-out;">

        <!-- Image de fond — cadrée à droite sur mobile -->
        <img src="<?= htmlspecialchars($img['src']) ?>" alt="Karim Telecom"
             loading="<?= $i===0?'eager':'lazy' ?>"
             class="hslide-bg">

        <!-- Overlay dégradé : fort à gauche, transparent à droite -->
        <div class="hslide-overlay"></div>

        <!-- Contenu aligné à gauche -->
        <div class="hslide-content">
            <h1 style="font-size:clamp(1.5rem,3.5vw,2.7rem);font-weight:800;line-height:1.2;margin-bottom:.75rem;text-shadow:0 2px 20px rgba(0,0,0,.6);">
                <?= htmlspecialchars($d['title']) ?>
            </h1>
            <p style="font-size:.95rem;color:rgba(255,255,255,.88);margin-bottom:1.75rem;text-shadow:0 1px 8px rgba(0,0,0,.4);line-height:1.6;max-width:480px;">
                <?= htmlspecialchars($d['sub']) ?>
            </p>
            <div style="display:flex;gap:.65rem;flex-wrap:wrap;">
                <a href="<?= $B ?>/catalogue.php" class="btn btn-sm"
                   style="background:#dc2626;border-color:#dc2626;color:#fff;font-size:.82rem;padding:.45rem 1.1rem;">
                    Découvrir
                </a>
                <a href="<?= waLink('Bonjour Karim Telecom, je voudrais des informations.') ?>"
                   target="_blank" rel="noopener" class="btn btn-sm"
                   style="background:rgba(255,255,255,.13);border:1.5px solid rgba(255,255,255,.32);color:#fff;font-size:.82rem;padding:.45rem 1.1rem;">
                    <?= svg('whatsapp',15) ?> WhatsApp
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <button onclick="hPrev()" aria-label="Précédent"
            style="position:absolute;top:50%;left:1rem;transform:translateY(-50%);z-index:10;
                   background:rgba(255,255,255,.18);backdrop-filter:blur(4px);
                   border:1px solid rgba(255,255,255,.28);color:#fff;
                   width:44px;height:44px;border-radius:50%;cursor:pointer;
                   display:flex;align-items:center;justify-content:center;transition:background .2s;"
            onmouseover="this.style.background='rgba(255,255,255,.35)'"
            onmouseout="this.style.background='rgba(255,255,255,.18)'">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button onclick="hNext()" aria-label="Suivant"
            style="position:absolute;top:50%;right:1rem;transform:translateY(-50%);z-index:10;
                   background:rgba(255,255,255,.18);backdrop-filter:blur(4px);
                   border:1px solid rgba(255,255,255,.28);color:#fff;
                   width:44px;height:44px;border-radius:50%;cursor:pointer;
                   display:flex;align-items:center;justify-content:center;transition:background .2s;"
            onmouseover="this.style.background='rgba(255,255,255,.35)'"
            onmouseout="this.style.background='rgba(255,255,255,.18)'">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </button>

    <div id="hDots" style="position:absolute;bottom:1.25rem;left:50%;transform:translateX(-50%);z-index:10;display:flex;gap:.5rem;">
        <?php for ($i = 0; $i < $totalSlides; $i++): ?>
        <button onclick="hGoTo(<?= $i ?>)" aria-label="Slide <?= $i+1 ?>"
                class="hdot <?= $i===0?'hdot-a':'' ?>"
                style="height:10px;border-radius:5px;border:none;cursor:pointer;padding:0;transition:all .3s;
                       background:<?= $i===0?'#fff':'rgba(255,255,255,.4)' ?>;
                       width:<?= $i===0?'28px':'10px' ?>;">
        </button>
        <?php endfor; ?>
    </div>
</div>
<style>/* hero responsive géré dans style.css */</style>
<script>
(function(){
    var cur=0,n=<?= $totalSlides ?>,timer;
    function show(idx){
        document.querySelectorAll('.hslide').forEach(function(s,i){s.style.opacity=i===idx?'1':'0';});
        document.querySelectorAll('.hdot').forEach(function(d,i){d.style.background=i===idx?'#fff':'rgba(255,255,255,.4)';d.style.width=i===idx?'28px':'10px';});
        cur=idx;
    }
    window.hGoTo=function(i){show(i);reset();};
    window.hNext=function(){show((cur+1)%n);reset();};
    window.hPrev=function(){show((cur-1+n)%n);reset();};
    function auto(){timer=setInterval(function(){hNext();},5000);}
    function reset(){clearInterval(timer);auto();}
    auto();
})();
</script>

<!-- TRUST BADGES -->
<div class="trust-badges">
    <div class="trust-badges-grid">
        <?php
        $badges = [
            ['store','Boutique physique','Venez tester avant'],
            ['truck','Livraison Douala','Rapide à domicile'],
            ['cash','Paiement livraison','Payez à réception'],
            ['shield','Garantie produit','Officielle incluse'],
            ['headset','Support WhatsApp','Réponse 7j/7'],
            ['receipt','Facture fournie','Document officiel'],
        ];
        foreach ($badges as $b): ?>
        <div class="trust-badge">
            <div class="trust-badge-icon"><?= svg($b[0], 20) ?></div>
            <strong><?= $b[1] ?></strong>
            <span><?= $b[2] ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- CARROUSEL CATÉGORIES -->
<?php
$categories = [];
try {
    $categories = $pdo->query("SELECT * FROM categories ORDER BY ordre ASC, nom ASC")->fetchAll();
} catch (PDOException $e) {}
?>
<?php if (!empty($categories)): ?>
<section style="padding:3rem 0 2rem;background:#fff;">
    <div style="text-align:center;margin-bottom:2rem;padding:0 1rem;">
        <h2 style="font-size:clamp(1.5rem,3vw,2rem);font-weight:800;color:#111827;margin-bottom:.35rem;">Nos Catégories</h2>
        <p style="font-size:.9rem;color:#6b7280;">Explorez notre sélection de téléphones et accessoires par marque</p>
    </div>
    <div style="position:relative;display:flex;align-items:center;padding:0 1rem;">
        <button id="catPrev" aria-label="Précédent"
            style="flex-shrink:0;width:38px;height:38px;border-radius:50%;background:#fff;border:1.5px solid #e5e7eb;box-shadow:0 2px 8px rgba(0,0,0,.12);display:flex;align-items:center;justify-content:center;cursor:pointer;color:#374151;z-index:2;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <div id="catTrack" style="display:flex;flex-direction:row;flex-wrap:nowrap;gap:1rem;overflow-x:auto;-webkit-overflow-scrolling:touch;flex:1;padding:.5rem 1rem 1rem;">
            <?php
            $catColors = ['#1a56db','#f97316','#16a34a','#7c3aed','#dc2626','#0891b2','#ca8a04'];
            foreach ($categories as $idx => $cat):
                $bg = $catColors[$idx % count($catColors)];
            ?>
            <a href="<?= $B ?>/catalogue.php?cat=<?= urlencode($cat['slug']) ?>"
               style="flex:0 0 160px;width:160px;min-width:160px;display:flex;flex-direction:column;align-items:center;text-decoration:none;">
                <div style="width:160px;height:140px;border-radius:12px;overflow:hidden;background:#f3f4f6;box-shadow:0 1px 3px rgba(0,0,0,.08);position:relative;flex-shrink:0;">
                    <?php if (!empty($cat['image'])): ?>
                        <img src="<?= $B ?>/uploads/categories/<?= htmlspecialchars($cat['image']) ?>"
                             alt="<?= htmlspecialchars($cat['nom']) ?>" loading="lazy"
                             style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;display:block;">
                    <?php else: ?>
                        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;color:#fff;font-size:2.5rem;font-weight:800;">
                            <?= mb_strtoupper(mb_substr($cat['nom'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <span style="margin-top:.65rem;font-size:.82rem;font-weight:600;color:#111827;text-align:center;line-height:1.3;max-width:155px;">
                    <?= htmlspecialchars($cat['nom']) ?>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
        <button id="catNext" aria-label="Suivant"
            style="flex-shrink:0;width:38px;height:38px;border-radius:50%;background:#fff;border:1.5px solid #e5e7eb;box-shadow:0 2px 8px rgba(0,0,0,.12);display:flex;align-items:center;justify-content:center;cursor:pointer;color:#374151;z-index:2;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
    <div style="display:flex;justify-content:center;gap:.4rem;margin-top:.75rem;">
        <?php foreach ($categories as $i => $c): ?>
        <button class="cdot" data-i="<?= $i ?>" style="width:<?= $i===0?'22px':'8px' ?>;height:8px;border-radius:<?= $i===0?'4px':'50%' ?>;background:<?= $i===0?'#1a56db':'#e5e7eb' ?>;border:none;padding:0;cursor:pointer;transition:all .2s;"></button>
        <?php endforeach; ?>
    </div>
</section>
<style>#catTrack::-webkit-scrollbar{display:none;}#catPrev:hover,#catNext:hover{background:#1a56db!important;color:#fff!important;border-color:#1a56db!important;}</style>
<script>
(function(){
    var track=document.getElementById('catTrack'),prev=document.getElementById('catPrev'),next=document.getElementById('catNext'),dots=document.querySelectorAll('.cdot');
    if(!track)return;
    var cur=0,TW=176;
    function goTo(i){i=Math.max(0,Math.min(i,track.children.length-1));cur=i;track.scrollTo({left:i*TW,behavior:'smooth'});dots.forEach(function(d,j){var a=j===cur;d.style.width=a?'22px':'8px';d.style.borderRadius=a?'4px':'50%';d.style.background=a?'#1a56db':'#e5e7eb';});}
    if(prev)prev.addEventListener('click',function(){goTo(cur-1);});
    if(next)next.addEventListener('click',function(){goTo(cur+1);});
    dots.forEach(function(d){d.addEventListener('click',function(){goTo(parseInt(d.dataset.i));});});
    track.addEventListener('scroll',function(){var i=Math.round(track.scrollLeft/TW);if(i!==cur){cur=i;dots.forEach(function(d,j){var a=j===cur;d.style.width=a?'22px':'8px';d.style.borderRadius=a?'4px':'50%';d.style.background=a?'#1a56db':'#e5e7eb';});}});
})();
</script>
<?php endif; ?>

<!-- PRODUITS VEDETTES -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">Nos téléphones populaires</h2>
                <p class="section-subtitle">Les modèles les plus demandés à Douala</p>
            </div>
            <a href="<?= $B ?>/catalogue.php">Voir tout <?= svg('arrow-r', 14) ?></a>
        </div>
        <?php if (empty($vedettes)): ?>
        <div style="text-align:center;padding:3rem;color:var(--gray-400);">
            <?= svg('phone', 48, '', 'margin:0 auto .75rem;opacity:.2;') ?>
            <p style="font-size:.9rem;">Aucun produit disponible pour le moment.</p>
            <a href="<?= $B ?>/admin/produit_form.php" class="btn btn-primary" style="margin-top:1rem;">Ajouter un produit</a>
        </div>
        <?php else: ?>
        <div class="product-grid">
            <?php foreach ($vedettes as $p):
                $imgUrl = $p['_main_image'] ?? ''; ?>
            <div class="product-card">
                <div class="product-img">
                    <a href="<?= $B ?>/produit.php?slug=<?= urlencode($p['slug']) ?>" class="product-img-link" tabindex="-1" aria-label="Voir <?= htmlspecialchars($p['nom']) ?>">
                    <?php if ($imgUrl): ?>
                        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($p['nom']) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="product-img-placeholder">
                            <?= svg('phone', 48) ?>
                            <span><?= htmlspecialchars($p['nom']) ?></span>
                        </div>
                    <?php endif; ?>
                    </a>
                    <span class="badge-stock <?= $p['en_stock']?'badge-instock':'badge-outstock' ?>">
                        <?= svg($p['en_stock']?'check':'x', 11) ?>
                        <?= $p['en_stock'] ? 'En stock' : 'Rupture' ?>
                    </span>
                    <span class="badge-new"><?= htmlspecialchars($p['etat']) ?></span>
                </div>
                <div class="product-body">
                    <p class="product-brand"><?= htmlspecialchars($p['marque']) ?></p>
                    <h3 class="product-name"><?= htmlspecialchars($p['nom']) ?></h3>
                    <p class="product-price"><?= formatPrix($p['prix']) ?></p>
                    <div class="product-installment">
                        <?= svg('receipt', 11) ?> Tranches dispo — 50% : <strong><?= formatPrix($p['prix'] / 2) ?></strong>
                    </div>
                    <div class="product-tags">
                        <?php if ($p['stockage']): ?>
                        <span class="product-tag"><?= svg('tag', 11) ?> <?= htmlspecialchars($p['stockage']) ?></span>
                        <?php endif; ?>
                        <span class="product-tag"><?= svg('shield', 11) ?> <?= htmlspecialchars($p['garantie']) ?></span>
                        <span class="product-tag"><?= svg('map-pin', 11) ?> <?= htmlspecialchars($p['disponibilite']) ?></span>
                    </div>
                    <div class="product-actions">
                        <a href="<?= $B ?>/produit.php?slug=<?= urlencode($p['slug']) ?>" class="btn btn-outline btn-sm btn-full">Voir les détails</a>
                        <a href="<?= waLink('Bonjour, je suis intéressé par le '.$p['nom'].' à '.formatPrix($p['prix']).'. Est-il disponible ?') ?>"
                           target="_blank" rel="noopener" class="btn btn-whatsapp btn-sm btn-full">
                            <?= svg('whatsapp', 15) ?> Commander sur WhatsApp
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- BOUTIQUE PHYSIQUE -->
<?php
$_sc        = include __DIR__ . '/config/site.php';
$_photoBout = $_sc['photo_boutique'] ?? '';
$_adresse   = $_sc['adresse']        ?? 'Carrefour Warda, Akwa, Douala, Cameroun';
$_mapLat    = $_sc['map_lat']        ?? '4.0511';
$_mapLng    = $_sc['map_lng']        ?? '9.7035';
$_mapSrc    = 'https://maps.google.com/maps?q=' . urlencode($_mapLat) . ',' . urlencode($_mapLng) . '&z=16&output=embed';
?>
<section class="store-section section-alt">
    <div class="container">
        <div class="store-grid">
            <!-- Photo ou placeholder -->
            <?php if ($_photoBout): ?>
            <div style="border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-lg);aspect-ratio:16/10;">
                <img src="<?= $B ?>/uploads/site/<?= htmlspecialchars($_photoBout) ?>"
                     alt="Boutique Karim Telecom"
                     style="width:100%;height:100%;object-fit:cover;display:block;">
            </div>
            <?php else: ?>
            <div class="store-img-placeholder">
                <?= svg('camera', 52) ?>
                <p>Photo de la boutique</p>
                <small><?= htmlspecialchars($_adresse) ?></small>
            </div>
            <?php endif; ?>

            <div class="store-info">
                <h2>Une boutique physique de confiance à Douala</h2>
                <p>Karim Telecom est installé au cœur d'Akwa, Douala. Venez tester les produits, les comparer et poser vos questions avant d'acheter. Pas d'achat en aveugle.</p>
                <div class="store-details">
                    <div class="store-detail"><?= svg('map-pin', 18) ?><span><?= htmlspecialchars($_adresse) ?></span></div>
                    <div class="store-detail"><?= svg('phone-call', 18) ?><a href="<?= telLink() ?>"><?= htmlspecialchars(getSitePhone()) ?></a></div>
                    <div class="store-detail"><?= svg('clock', 18) ?><span>Lun–Sam : 8h–20h | Dim : 10h–18h</span></div>
                    <div class="store-detail"><?= svg('parking', 18) ?><span>Parking disponible, accès facile</span></div>
                </div>
                <div class="map-embed">
                    <iframe src="<?= htmlspecialchars($_mapSrc) ?>"
                        allowfullscreen loading="lazy" title="Karim Telecom — <?= htmlspecialchars($_adresse) ?>"></iframe>
                </div>
                <a href="<?= waLink('Bonjour Karim Telecom, je voudrais passer en boutique.') ?>"
                   target="_blank" rel="noopener" class="btn btn-whatsapp">
                    <?= svg('whatsapp', 18) ?> Nous écrire sur WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<!-- AVIS CLIENTS -->
<section class="section">
    <div class="container">
        <div class="text-center mb-3">
            <h2 class="section-title">Ce que disent nos clients</h2>
            <p class="section-subtitle">Des centaines de Camerounais nous font confiance</p>
            <div style="display:flex;align-items:center;justify-content:center;gap:.4rem;margin-top:.5rem;">
                <?php for($i=0;$i<5;$i++): ?><?= svg('star-solid', 18, '', 'color:#fbbf24;') ?><?php endfor; ?>
                <strong style="margin-left:.3rem;">4.9/5</strong>
                <span style="color:var(--gray-400);font-size:.82rem;margin-left:.1rem;">— 127 avis vérifiés</span>
            </div>
        </div>
        <?php if (empty($avis)): ?>
        <p style="text-align:center;color:var(--gray-400);padding:2rem;">Aucun avis pour le moment.</p>
        <?php else: ?>
        <div class="reviews-grid">
            <?php foreach ($avis as $i => $a): ?>
            <div class="review-card">
                <div class="review-header">
                    <div class="review-avatar" style="<?= empty($a['photo']) ? 'background:'.$avatarColors[$i % count($avatarColors)] : 'background:#f3f4f6;padding:0;overflow:hidden;' ?>">
                        <?php if (!empty($a['photo'])): ?>
                            <img src="<?= $B ?>/uploads/avis/<?= htmlspecialchars($a['photo']) ?>"
                                 alt="<?= htmlspecialchars($a['nom']) ?>"
                                 style="width:100%;height:100%;object-fit:cover;display:block;border-radius:50%;">
                        <?php else: ?>
                            <?= strtoupper(mb_substr($a['nom'],0,1)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="review-meta">
                        <strong><?= htmlspecialchars($a['nom']) ?></strong>
                        <span class="review-city"><?= svg('map-pin',12) ?> <?= htmlspecialchars($a['ville']) ?></span>
                    </div>
                </div>
                <div class="review-stars">
                    <?php for($s=1;$s<=5;$s++): ?>
                    <?= svg('star-solid', 14, $s<=$a['note']?'':'empty', $s<=$a['note']?'color:#fbbf24;':'color:#e2e8f0;') ?>
                    <?php endfor; ?>
                </div>
                <p class="review-text">"<?= htmlspecialchars($a['texte']) ?>"</p>
                <?php if ($a['produit_achete']): ?>
                <p class="review-product"><?= svg('tag',12) ?> <?= htmlspecialchars($a['produit_achete']) ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- STATS -->
<div class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item"><strong>500+</strong><span>Clients satisfaits</span></div>
            <div class="stat-item"><strong>1 200+</strong><span>Téléphones vendus</span></div>
            <div class="stat-item"><strong>4.9/5</strong><span>Satisfaction client</span></div>
            <div class="stat-item"><strong>3 ans</strong><span>D'expérience</span></div>
        </div>
    </div>
</div>

<!-- VILLES -->
<section class="section section-alt">
    <div class="container">
        <div class="text-center mb-3">
            <h2 class="section-title">Livraison partout au Cameroun</h2>
            <p class="section-subtitle">Nous livrons dans les principales villes du pays</p>
        </div>
        <?php if (!empty($villes)): ?>
        <div class="cities-grid">
            <?php foreach ($villes as $v): ?>
            <a href="<?= $B ?>/ville.php?v=<?= urlencode($v['slug']) ?>" class="city-card">
                <div class="city-icon"><?= svg($villeIcons[$v['slug']] ?? 'map-pin', 22) ?></div>
                <h3 class="city-name"><?= htmlspecialchars($v['nom']) ?></h3>
                <p class="city-delivery"><?= svg('truck',13) ?> <?= htmlspecialchars($v['delai_livraison']) ?></p>
                <p class="city-desc"><?= htmlspecialchars(mb_substr($v['description'],0,90)) ?>...</p>
                <span class="city-link">Commander à <?= htmlspecialchars($v['nom']) ?> <?= svg('arrow-r',13) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- BLOG -->
<?php if (!empty($articles)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">Guides & Conseils</h2>
                <p class="section-subtitle">Tout ce qu'il faut savoir pour bien choisir</p>
            </div>
            <a href="<?= $B ?>/blog.php">Tous les articles <?= svg('arrow-r',14) ?></a>
        </div>
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
                    <h3 class="blog-title"><?= htmlspecialchars($art['titre']) ?></h3>
                    <p class="blog-excerpt"><?= htmlspecialchars(mb_substr($art['extrait'],0,100)) ?>...</p>
                    <span class="blog-read">Lire l'article <?= svg('arrow-r',13) ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
