<?php
require_once 'config/db.php';
$pdo = getDB();
$B   = BASE_URL;

$vslug = $_GET['v'] ?? '';
if (!$vslug) { header('Location: ' . $B . '/index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM villes WHERE slug = ?");
$stmt->execute([$vslug]);
$ville = $stmt->fetch();
if (!$ville) { http_response_code(404); die('<p style="padding:2rem;font-family:sans-serif;">Ville introuvable.</p>'); }

$prods = $pdo->prepare("SELECT p.* FROM produits p INNER JOIN produit_villes pv ON pv.produit_id = p.id WHERE pv.ville_slug = ? AND p.en_stock = 1 ORDER BY p.vedette DESC, p.prix DESC");
$prods->execute([$vslug]);
$produits = $prods->fetchAll();
foreach ($produits as &$pp) { $pp['_main_image'] = getMainImage($pdo, $pp['id']); } unset($pp);

$pageTitle = 'Acheter un téléphone à ' . $ville['nom'] . ' — Karim Telecom';
$pageDesc  = 'Achetez votre téléphone à ' . $ville['nom'] . '. ' . $ville['delai_livraison'] . '.';
$waMsg     = 'Bonjour Karim Telecom, je suis à ' . $ville['nom'] . ' et je voudrais commander un téléphone.';

include 'includes/header.php';
require_once 'includes/icons.php';
?>

<div class="ville-hero">
    <div class="container">
        <div class="ville-icon-big"><?= svg('building', 28) ?></div>
        <h1>Acheter un téléphone à <?= htmlspecialchars($ville['nom']) ?></h1>
        <p style="color:rgba(255,255,255,.85);margin-top:.5rem;"><?= htmlspecialchars($ville['description']) ?></p>
        <div class="ville-chips">
            <span class="ville-chip"><?= svg('truck',14) ?> <?= htmlspecialchars($ville['delai_livraison']) ?></span>
            <span class="ville-chip"><?= svg('cash',14) ?> Paiement à la livraison</span>
            <span class="ville-chip"><?= svg('shield',14) ?> Garantie officielle</span>
        </div>
        <div style="margin-top:1.5rem;">
            <a href="<?= waLink($waMsg) ?>" target="_blank" rel="noopener" class="btn btn-whatsapp btn-lg">
                <?= svg('whatsapp',18) ?> Commander pour <?= htmlspecialchars($ville['nom']) ?>
            </a>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div style="background:#f3f4f6;border-radius:12px;padding:1.5rem;margin-bottom:2.5rem;">
            <h2 style="font-size:1.2rem;font-weight:800;margin-bottom:1.25rem;">Comment commander à <?= htmlspecialchars($ville['nom']) ?> ?</h2>
            <div class="steps-grid">
                <div class="step-item"><div class="step-num">1</div><div><h4>Choisissez votre téléphone</h4><p>Parcourez le catalogue ci-dessous.</p></div></div>
                <div class="step-item"><div class="step-num">2</div><div><h4>Contactez-nous sur WhatsApp</h4><p>Envoyez votre commande avec votre adresse à <?= htmlspecialchars($ville['nom']) ?>.</p></div></div>
                <div class="step-item"><div class="step-num">3</div><div><h4>Recevez et payez</h4><p><?= htmlspecialchars($ville['delai_livraison']) ?>. Paiement à la réception.</p></div></div>
            </div>
        </div>

        <?php if (empty($produits)): ?>
        <div style="text-align:center;padding:3rem;color:var(--gray-400);">
            <?= svg('phone',48,'','opacity:.2;') ?>
            <p style="font-size:.9rem;">Aucun produit disponible pour <?= htmlspecialchars($ville['nom']) ?> pour le moment.</p>
            <a href="<?= $B ?>/catalogue.php" class="btn btn-primary" style="margin-top:1rem;">Voir tous les produits</a>
        </div>
        <?php else: ?>
        <h2 style="font-size:1.4rem;font-weight:800;margin-bottom:1.5rem;">Téléphones disponibles à <?= htmlspecialchars($ville['nom']) ?> <span style="font-size:.9rem;font-weight:400;color:#64748b;">(<?= count($produits) ?> produits)</span></h2>
        <div class="product-grid">
            <?php foreach ($produits as $p): $imgUrl = $p['_main_image'] ?? ''; ?>
            <div class="product-card">
                <div class="product-img">
                    <?php if ($imgUrl): ?>
                    <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($p['nom']) ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                    <div class="product-img-placeholder"><?= svg('phone',48) ?><span><?= htmlspecialchars($p['nom']) ?></span></div>
                    <?php endif; ?>
                    <span class="badge-stock badge-instock"><?= svg('check',11) ?> En stock</span>
                    <span class="badge-new"><?= htmlspecialchars($p['etat']) ?></span>
                </div>
                <div class="product-body">
                    <p class="product-brand"><?= htmlspecialchars($p['marque']) ?></p>
                    <h3 class="product-name"><?= htmlspecialchars($p['nom']) ?></h3>
                    <p class="product-price"><?= formatPrix($p['prix']) ?></p>
                    <div class="product-tags">
                        <?php if ($p['stockage']): ?><span class="product-tag"><?= svg('tag',11) ?> <?= htmlspecialchars($p['stockage']) ?></span><?php endif; ?>
                        <span class="product-tag"><?= svg('shield',11) ?> <?= htmlspecialchars($p['garantie']) ?></span>
                    </div>
                    <div class="product-actions">
                        <a href="<?= $B ?>/produit.php?slug=<?= urlencode($p['slug']) ?>" class="btn btn-outline btn-sm btn-full">Voir détails</a>
                        <a href="<?= waLink('Bonjour, je suis à '.$ville['nom'].' et je voudrais le '.$p['nom'].' à '.formatPrix($p['prix']).'.') ?>" target="_blank" rel="noopener" class="btn btn-whatsapp btn-sm btn-full"><?= svg('whatsapp',15) ?> Commander — <?= htmlspecialchars($ville['nom']) ?></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
