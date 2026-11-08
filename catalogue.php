<?php
require_once 'config/db.php';
$pdo = getDB();
$B   = BASE_URL;
require_once 'includes/tracker.php';
trackVisit($pdo, '/catalogue');
$cat    = $_GET['cat'] ?? '';
$search = $_GET['q'] ?? '';

$pageTitle = 'Téléphones à vendre à Douala — Karim Telecom';
$pageDesc  = 'Catalogue complet des téléphones disponibles chez Karim Telecom à Douala. Paiement à la livraison.';

// Récupérer les produits avec filtre optionnel
if ($cat || $search) {
    $sql = "SELECT * FROM produits WHERE 1=1";
    $params = [];
    if ($cat) { $sql .= " AND (categorie = ? OR marque LIKE ?)"; $params[] = $cat; $params[] = "%$cat%"; }
    if ($search) { $sql .= " AND (nom LIKE ? OR marque LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
    $sql .= " ORDER BY vedette DESC, prix DESC";
    $stmt = $pdo->prepare($sql); $stmt->execute($params);
    $produits = $stmt->fetchAll();
} else {
    $produits = $pdo->query("SELECT * FROM produits ORDER BY vedette DESC, prix DESC")->fetchAll();
}

$marques = $pdo->query("SELECT DISTINCT marque FROM produits ORDER BY marque")->fetchAll(PDO::FETCH_COLUMN);

// Charger image principale
foreach ($produits as &$p) { $p['_main_image'] = getMainImage($pdo, $p['id']); } unset($p);

include 'includes/header.php';
require_once 'includes/icons.php';

// Nom de la catégorie si filtrée
$catName = '';
if ($cat) {
    try {
        $cs = $pdo->prepare("SELECT nom FROM categories WHERE slug = ?"); $cs->execute([$cat]);
        $catName = $cs->fetchColumn() ?: $cat;
    } catch (PDOException $e) { $catName = $cat; }
}
?>

<div class="page-hero">
    <div class="container">
        <h1><?= $catName ? htmlspecialchars($catName) : 'Téléphones & Accessoires' ?> à Douala</h1>
        <p>Tous nos produits — paiement à la livraison — garantie officielle</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- FILTRES -->
        <div class="filters">
            <div class="filter-group">
                <label>Catégorie</label>
                <select id="filter-categorie">
                    <option value="all">Tous les produits</option>
                    <option value="telephone">Téléphones</option>
                    <option value="accessoire">Accessoires</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Marque</label>
                <select id="filter-marque">
                    <option value="all">Toutes les marques</option>
                    <?php foreach ($marques as $m): ?>
                    <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>État</label>
                <select id="filter-etat">
                    <option value="all">Tous les états</option>
                    <?php
                    $etats = $pdo->query("SELECT DISTINCT etat FROM produits WHERE etat != '' ORDER BY etat")->fetchAll(PDO::FETCH_COLUMN);
                    foreach ($etats as $et):
                    ?>
                    <option value="<?= htmlspecialchars($et) ?>"><?= htmlspecialchars($et) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Budget</label>
                <select id="filter-budget">
                    <option value="all">Tous les budgets</option>
                    <option value="moins200">Moins de 200 000 FCFA</option>
                    <option value="200-500">200 000 - 500 000 FCFA</option>
                    <option value="plus500">Plus de 500 000 FCFA</option>
                </select>
            </div>
            <div class="filter-group" style="justify-content:flex-end;flex:1;align-items:flex-end;">
                <span style="font-size:.85rem;color:#6b7280;" id="products-count"><?= count($produits) ?> produit<?= count($produits)>1?'s':'' ?></span>
            </div>
        </div>

        <?php if (empty($produits)): ?>
        <div style="text-align:center;padding:3rem;color:var(--gray-400);">
            <?= svg('phone', 48, '', 'margin:0 auto .75rem;opacity:.2;') ?>
            <p style="font-size:.9rem;">Aucun produit dans cette catégorie pour le moment.</p>
            <?php if ($cat): ?>
            <a href="<?= $B ?>/catalogue.php" class="btn btn-primary" style="margin-top:1rem;">Voir tous les produits</a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="product-grid">
            <?php foreach ($produits as $p):
                $imgUrl = $p['_main_image'] ?? ''; ?>
            <div class="product-card"
                 data-marque="<?= htmlspecialchars($p['marque']) ?>"
                 data-prix="<?= $p['prix'] ?>"
                 data-categorie="<?= $p['categorie'] ?>"
                 data-etat="<?= htmlspecialchars($p['etat']) ?>">
                <div class="product-img">
                    <a href="<?= $B ?>/produit.php?slug=<?= urlencode($p['slug']) ?>" class="product-img-link" tabindex="-1" aria-label="Voir <?= htmlspecialchars($p['nom']) ?>">
                    <?php if ($imgUrl): ?>
                        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($p['nom']) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="product-img-placeholder">
                            <?= svg($p['categorie']==='accessoire'?'headset':'phone', 48) ?>
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

<?php include 'includes/footer.php'; ?>
