<?php
session_start();
require_once '../config/db.php';
require_once 'auth.php';
$pdo = getDB();
$adminTitle = 'Produits';

if (isset($_GET['toggle_stock'])) {
    $pdo->prepare("UPDATE produits SET en_stock = 1 - en_stock WHERE id = ?")->execute([(int)$_GET['toggle_stock']]);
    header('Location: produits.php?msg=stock'); exit;
}
if (isset($_GET['toggle_vedette'])) {
    $pdo->prepare("UPDATE produits SET vedette = 1 - vedette WHERE id = ?")->execute([(int)$_GET['toggle_vedette']]);
    header('Location: produits.php?msg=vedette'); exit;
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM produit_villes WHERE produit_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM produits WHERE id = ?")->execute([$id]);
    header('Location: produits.php?msg=deleted'); exit;
}

$search = $_GET['q'] ?? '';
$cat    = $_GET['cat'] ?? '';
$sql    = "SELECT * FROM produits WHERE 1=1";
$params = [];
if ($search) { $sql .= " AND (nom LIKE ? OR marque LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($cat)    { $sql .= " AND categorie = ?"; $params[] = $cat; }
$sql .= " ORDER BY vedette DESC, date_ajout DESC";
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$produits = $stmt->fetchAll();

include 'layout.php';
?>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success">
    <?= icon('check', 16) ?>
    <?php
    $msgs = ['stock'=>'Stock mis à jour.','vedette'=>'Vedette mis à jour.','deleted'=>'Produit supprimé.','saved'=>'Produit enregistré.'];
    echo $msgs[$_GET['msg']] ?? 'Action effectuée.';
    ?>
</div>
<?php endif; ?>

<!-- Filtres -->
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:.9rem 1.25rem;">
        <form method="GET" style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="min-width:180px;">
                <label>Recherche</label>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Nom ou marque...">
            </div>
            <div class="form-group">
                <label>Catégorie</label>
                <select name="cat" style="min-width:140px;">
                    <option value="">Toutes</option>
                    <option value="telephone" <?= $cat==='telephone'?'selected':'' ?>>Téléphones</option>
                    <option value="accessoire" <?= $cat==='accessoire'?'selected':'' ?>>Accessoires</option>
                </select>
            </div>
            <div style="display:flex;gap:.5rem;align-items:flex-end;">
                <button type="submit" class="btn btn-primary btn-sm"><?= icon('chart',14) ?> Filtrer</button>
                <a href="produits.php" class="btn btn-ghost btn-sm">Réinitialiser</a>
            </div>
            <div style="margin-left:auto;align-self:flex-end;">
                <a href="produit_form.php" class="btn btn-primary"><?= icon('plus',16) ?> Nouveau produit</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><?= icon('phone',16) ?> Produits <span style="font-weight:400;color:#64748b;margin-left:.35rem;">(<?= count($produits) ?>)</span></h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Produit</th><th>Marque</th><th>Prix</th><th>Cat.</th>
                    <th>Stock</th><th>Vedette</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($produits as $p): ?>
            <tr>
                <td>
                    <div style="font-weight:600;color:#0f172a;font-size:.875rem;"><?= htmlspecialchars($p['nom']) ?></div>
                    <div style="font-size:.72rem;color:#94a3b8;margin-top:.1rem;">/<?= $p['slug'] ?></div>
                </td>
                <td><span class="badge badge-gray"><?= htmlspecialchars($p['marque']) ?></span></td>
                <td style="font-weight:700;color:#1a56db;"><?= number_format($p['prix'],0,',',' ') ?> FCFA</td>
                <td>
                    <span class="badge <?= $p['categorie']==='telephone'?'badge-blue':'badge-orange' ?>">
                        <?= $p['categorie'] ?>
                    </span>
                </td>
                <td>
                    <a href="produits.php?toggle_stock=<?= $p['id'] ?>" style="text-decoration:none;">
                        <label class="toggle" style="cursor:pointer;">
                            <input type="checkbox" <?= $p['en_stock']?'checked':'' ?> onclick="window.location='produits.php?toggle_stock=<?= $p['id'] ?>'">
                            <span class="toggle-s"></span>
                        </label>
                    </a>
                </td>
                <td>
                    <a href="produits.php?toggle_vedette=<?= $p['id'] ?>" class="btn btn-ghost btn-sm" title="<?= $p['vedette']?'Retirer vedette':'Mettre en vedette' ?>">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="<?= $p['vedette']?'#f59e0b':'none' ?>" stroke="<?= $p['vedette']?'#f59e0b':'#94a3b8' ?>" stroke-width="1.8" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </a>
                </td>
                <td>
                    <div style="display:flex;gap:.35rem;">
                        <a href="produit_form.php?id=<?= $p['id'] ?>" class="btn btn-ghost btn-sm" title="Modifier"><?= icon('pencil',14) ?></a>
                        <a href="<?= BASE_URL ?>/produit.php?slug=<?= $p['slug'] ?>" target="_blank" class="btn btn-ghost btn-sm" title="Voir"><?= icon('eye',14) ?></a>
                        <a href="produits.php?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" title="Supprimer"
                           onclick="return confirm('Supprimer «<?= addslashes($p['nom']) ?>» ?')"><?= icon('trash',14) ?></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout_end.php'; ?>
