<?php
session_start();
require_once '../config/db.php';
require_once 'auth.php';
$pdo = getDB();
$adminTitle = 'Tableau de bord';

$nbProduits       = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$nbEnStock        = $pdo->query("SELECT COUNT(*) FROM produits WHERE en_stock=1")->fetchColumn();
$nbAvis           = $pdo->query("SELECT COUNT(*) FROM avis")->fetchColumn();
$nbArticles       = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$noteAvg          = $pdo->query("SELECT ROUND(AVG(note),1) FROM avis")->fetchColumn() ?: '—';
$derniersProduits = $pdo->query("SELECT * FROM produits ORDER BY date_ajout DESC LIMIT 6")->fetchAll();
$derniersAvis     = $pdo->query("SELECT * FROM avis ORDER BY date_avis DESC LIMIT 5")->fetchAll();

// ── Trafic ──
$visitesToday = 0; $visitesWeek = 0; $visitesMonth = 0; $clicsTotal = 0;
$topPages = []; $topProduits = []; $visitesByDay = [];
try {
    $visitesToday = $pdo->query("SELECT COUNT(*) FROM site_visits WHERE date_visite = CURDATE()")->fetchColumn();
    $visitesWeek  = $pdo->query("SELECT COUNT(*) FROM site_visits WHERE date_visite >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetchColumn();
    $visitesMonth = $pdo->query("SELECT COUNT(*) FROM site_visits WHERE date_visite >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();
    $clicsTotal   = $pdo->query("SELECT COUNT(*) FROM produit_clics WHERE date_clic >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();

    $topPages = $pdo->query("
        SELECT page, COUNT(*) as nb
        FROM site_visits
        WHERE date_visite >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY page ORDER BY nb DESC LIMIT 5
    ")->fetchAll();

    $topProduits = $pdo->query("
        SELECT p.nom, p.slug, COUNT(c.id) as clics
        FROM produit_clics c
        JOIN produits p ON p.id = c.produit_id
        WHERE c.date_clic >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY c.produit_id ORDER BY clics DESC LIMIT 5
    ")->fetchAll();

    // Visites par jour sur 14 jours
    $visitesByDay = $pdo->query("
        SELECT date_visite, COUNT(*) as nb
        FROM site_visits
        WHERE date_visite >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
        GROUP BY date_visite ORDER BY date_visite ASC
    ")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {}

include 'layout.php';
?>

<!-- Stats produits -->
<div class="stat-cards" style="margin-bottom:1rem;">
    <div class="stat-card">
        <div class="stat-card-icon blue"><?= icon('phone', 20) ?></div>
        <div class="s-label">Produits</div>
        <div class="s-value"><?= $nbProduits ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon green"><?= icon('check', 20) ?></div>
        <div class="s-label">En stock</div>
        <div class="s-value"><?= $nbEnStock ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon amber"><?= icon('star', 20) ?></div>
        <div class="s-label">Avis clients</div>
        <div class="s-value"><?= $nbAvis ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon purple"><?= icon('chart', 20) ?></div>
        <div class="s-label">Note moyenne</div>
        <div class="s-value"><?= $noteAvg ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon rose"><?= icon('article', 20) ?></div>
        <div class="s-label">Articles</div>
        <div class="s-value"><?= $nbArticles ?></div>
    </div>
</div>

<!-- Stats trafic -->
<div class="stat-cards" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-card-icon blue"><?= icon('globe', 20) ?></div>
        <div class="s-label">Visites aujourd'hui</div>
        <div class="s-value"><?= $visitesToday ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon green"><?= icon('globe', 20) ?></div>
        <div class="s-label">Visites 7 jours</div>
        <div class="s-value"><?= $visitesWeek ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon amber"><?= icon('globe', 20) ?></div>
        <div class="s-label">Visites 30 jours</div>
        <div class="s-value"><?= $visitesMonth ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon purple"><?= icon('tag', 20) ?></div>
        <div class="s-label">Clics produits (30j)</div>
        <div class="s-value"><?= $clicsTotal ?></div>
    </div>
</div>

<!-- Graphique visites 14 jours -->
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <h2><?= icon('chart', 16) ?> Visites des 14 derniers jours</h2>
    </div>
    <div class="card-body" style="padding:1.25rem;">
        <?php
        // Construire tableau 14 jours
        $labels = []; $values = []; $maxVal = 1;
        for ($d = 13; $d >= 0; $d--) {
            $day = date('Y-m-d', strtotime("-$d days"));
            $labels[] = date('d/m', strtotime($day));
            $val = (int)($visitesByDay[$day] ?? 0);
            $values[] = $val;
            if ($val > $maxVal) $maxVal = $val;
        }
        ?>
        <div style="display:flex;align-items:flex-end;gap:6px;height:120px;padding-bottom:.5rem;border-bottom:1px solid #f1f5f9;">
            <?php foreach ($values as $i => $v): ?>
            <?php $h = $maxVal > 0 ? max(4, round(($v / $maxVal) * 110)) : 4; ?>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;position:relative;">
                <span style="font-size:.6rem;color:#94a3b8;position:absolute;top:-16px;"><?= $v > 0 ? $v : '' ?></span>
                <div style="width:100%;height:<?= $h ?>px;background:<?= $v > 0 ? '#1a56db' : '#e2e8f0' ?>;border-radius:4px 4px 0 0;transition:height .3s;" title="<?= $labels[$i] ?> : <?= $v ?> visites"></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="display:flex;gap:6px;margin-top:.35rem;">
            <?php foreach ($labels as $l): ?>
            <div style="flex:1;text-align:center;font-size:.58rem;color:#94a3b8;"><?= $l ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Top pages + Top produits cliqués -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem;">
    <div class="card">
        <div class="card-header"><h2><?= icon('globe', 16) ?> Pages les plus visitées <span style="font-size:.72rem;color:#94a3b8;">(30 jours)</span></h2></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Page</th><th style="text-align:right;">Visites</th></tr></thead>
                <tbody>
                <?php if (empty($topPages)): ?>
                <tr><td colspan="2" style="text-align:center;color:#94a3b8;padding:1rem;font-size:.82rem;">Aucune donnée encore</td></tr>
                <?php else: foreach ($topPages as $tp): ?>
                <tr>
                    <td style="font-size:.82rem;color:#374151;"><?= htmlspecialchars($tp['page']) ?></td>
                    <td style="text-align:right;font-weight:700;color:#1a56db;"><?= $tp['nb'] ?></td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h2><?= icon('tag', 16) ?> Produits les plus cliqués <span style="font-size:.72rem;color:#94a3b8;">(30 jours)</span></h2></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Produit</th><th style="text-align:right;">Clics</th></tr></thead>
                <tbody>
                <?php if (empty($topProduits)): ?>
                <tr><td colspan="2" style="text-align:center;color:#94a3b8;padding:1rem;font-size:.82rem;">Aucune donnée encore</td></tr>
                <?php else: foreach ($topProduits as $tp): ?>
                <tr>
                    <td><a href="<?= BASE_URL ?>/produit.php?slug=<?= urlencode($tp['slug']) ?>" target="_blank" style="font-size:.82rem;color:#1a56db;"><?= htmlspecialchars($tp['nom']) ?></a></td>
                    <td style="text-align:right;font-weight:700;color:#1a56db;"><?= $tp['clics'] ?></td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="quick-actions" style="margin-bottom:1.5rem;">
    <a href="produit_form.php" class="btn btn-primary"><?= icon('plus', 16) ?> <span>Nouveau produit</span></a>
    <a href="article_form.php" class="btn btn-success"><?= icon('pencil', 16) ?> <span>Nouvel article</span></a>
    <a href="avis.php" class="btn btn-ghost"><?= icon('star', 16) ?> <span>Gérer les avis</span></a>
    <a href="<?= BASE_URL ?>/index.php" target="_blank" class="btn btn-ghost"><?= icon('globe', 16) ?> <span>Voir le site</span></a>
</div>

<!-- Derniers produits -->
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <h2><?= icon('phone', 16) ?> Derniers produits</h2>
        <a href="produits.php" class="btn btn-ghost btn-sm">Voir tout</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Produit</th><th>Prix</th><th>Marque</th><th>Stock</th><th>Vedette</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($derniersProduits as $p): ?>
            <tr>
                <td><span style="font-weight:600;color:#0f172a;"><?= htmlspecialchars($p['nom']) ?></span></td>
                <td style="font-weight:700;color:#1a56db;"><?= number_format($p['prix'],0,',',' ') ?> FCFA</td>
                <td><span class="badge badge-gray"><?= htmlspecialchars($p['marque']) ?></span></td>
                <td><span class="badge <?= $p['en_stock'] ? 'badge-green' : 'badge-red' ?>"><?= icon($p['en_stock']?'check':'x', 11) ?> <?= $p['en_stock'] ? 'En stock' : 'Rupture' ?></span></td>
                <td><?php if ($p['vedette']): ?><span class="badge badge-orange"><?= icon('star',11) ?> Vedette</span><?php else: ?><span style="color:#cbd5e1;">—</span><?php endif; ?></td>
                <td><a href="produit_form.php?id=<?= $p['id'] ?>" class="btn btn-ghost btn-sm"><?= icon('pencil',14) ?></a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Derniers avis -->
<div class="card">
    <div class="card-header">
        <h2><?= icon('star', 16) ?> Derniers avis</h2>
        <a href="avis.php" class="btn btn-ghost btn-sm">Voir tout</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Client</th><th>Ville</th><th>Note</th><th>Produit</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($derniersAvis as $a): ?>
            <tr>
                <td><strong><?= htmlspecialchars($a['nom']) ?></strong></td>
                <td><?= htmlspecialchars($a['ville']) ?></td>
                <td>
                    <div style="display:flex;gap:1px;">
                        <?php for($i=1;$i<=5;$i++): ?>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="<?= $i<=$a['note']?'#f59e0b':'#e2e8f0' ?>"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <?php endfor; ?>
                    </div>
                </td>
                <td style="font-size:.78rem;color:#64748b;"><?= htmlspecialchars($a['produit_achete']) ?></td>
                <td><a href="avis.php?delete=<?= $a['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')"><?= icon('trash',14) ?></a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout_end.php'; ?>
