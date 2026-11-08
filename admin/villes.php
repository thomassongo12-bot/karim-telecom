<?php
session_start();
require_once '../config/db.php';
require_once 'auth.php';
$pdo = getDB();
$adminTitle = 'Villes de livraison';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $slug = $pdo->prepare("SELECT slug FROM villes WHERE id=?"); $slug->execute([$id]); $slug = $slug->fetchColumn();
    if ($slug) $pdo->prepare("DELETE FROM produit_villes WHERE ville_slug=?")->execute([$slug]);
    $pdo->prepare("DELETE FROM villes WHERE id=?")->execute([$id]);
    header('Location: villes.php?msg=deleted'); exit;
}

$editVille = null;
if (isset($_GET['edit'])) {
    $e = $pdo->prepare("SELECT * FROM villes WHERE id=?"); $e->execute([(int)$_GET['edit']]);
    $editVille = $e->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)($_POST['id'] ?? 0);
    $nom  = trim($_POST['nom'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: strtolower(preg_replace('/[^a-z0-9]/i','-',$nom));
    $slug = preg_replace('/[^a-z0-9-]/','',strtolower($slug));
    $desc = trim($_POST['description'] ?? '');
    $delai= trim($_POST['delai_livraison'] ?? '');
    $emoji= trim($_POST['emoji'] ?? '');
    if ($nom) {
        if ($id) {
            $pdo->prepare("UPDATE villes SET nom=?,slug=?,description=?,delai_livraison=?,emoji=? WHERE id=?")
                ->execute([$nom,$slug,$desc,$delai,$emoji,$id]);
        } else {
            $pdo->prepare("INSERT INTO villes (nom,slug,description,delai_livraison,emoji) VALUES (?,?,?,?,?)")
                ->execute([$nom,$slug,$desc,$delai,$emoji]);
        }
        header('Location: villes.php?msg=saved'); exit;
    }
}

$villes = $pdo->query("SELECT v.*, COUNT(pv.produit_id) as nb FROM villes v LEFT JOIN produit_villes pv ON pv.ville_slug=v.slug GROUP BY v.id ORDER BY v.nom")->fetchAll();
include 'layout.php';
?>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success">
    <?= icon('check',16) ?>
    <?= $_GET['msg']==='saved' ? 'Ville enregistrée.' : 'Ville supprimée.' ?>
</div>
<?php endif; ?>

<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <h2><?= icon($editVille?'pencil':'plus',16) ?> <?= $editVille ? 'Modifier la ville' : 'Ajouter une ville' ?></h2>
        <?php if ($editVille): ?><a href="villes.php" class="btn btn-ghost btn-sm"><?= icon('x',14) ?> Annuler</a><?php endif; ?>
    </div>
    <div class="card-body">
        <form method="POST">
            <?php if ($editVille): ?><input type="hidden" name="id" value="<?= $editVille['id'] ?>"><?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($editVille['nom'] ?? '') ?>" placeholder="Douala" required>
                </div>
                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" value="<?= htmlspecialchars($editVille['slug'] ?? '') ?>" placeholder="douala (auto si vide)">
                </div>
                <div class="form-group">
                    <label>Délai de livraison</label>
                    <input type="text" name="delai_livraison" value="<?= htmlspecialchars($editVille['delai_livraison'] ?? '') ?>" placeholder="Livraison le jour même">
                </div>
                <div class="form-group">
                    <label>Emoji / Icône</label>
                    <input type="text" name="emoji" value="<?= htmlspecialchars($editVille['emoji'] ?? '') ?>" placeholder="🏙️">
                </div>
                <div class="form-group full">
                    <label>Description</label>
                    <textarea name="description" rows="2"><?= htmlspecialchars($editVille['description'] ?? '') ?></textarea>
                </div>
            </div>
            <div style="margin-top:1rem;">
                <button type="submit" class="btn btn-primary"><?= icon('check',16) ?> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><?= icon('location',16) ?> Villes <span style="font-weight:400;color:#64748b;">(<?= count($villes) ?>)</span></h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Ville</th><th>Slug</th><th>Délai</th><th>Produits</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($villes as $v): ?>
            <tr>
                <td><strong><?= htmlspecialchars($v['nom']) ?></strong></td>
                <td style="font-size:.78rem;color:#94a3b8;"><?= $v['slug'] ?></td>
                <td style="font-size:.82rem;"><?= htmlspecialchars($v['delai_livraison']) ?></td>
                <td><span class="badge badge-blue"><?= $v['nb'] ?></span></td>
                <td>
                    <div style="display:flex;gap:.35rem;">
                        <a href="villes.php?edit=<?= $v['id'] ?>" class="btn btn-ghost btn-sm"><?= icon('pencil',14) ?></a>
                        <a href="<?= BASE_URL ?>/ville.php?v=<?= $v['slug'] ?>" target="_blank" class="btn btn-ghost btn-sm"><?= icon('eye',14) ?></a>
                        <a href="villes.php?delete=<?= $v['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Supprimer <?= addslashes($v['nom']) ?> ?')"><?= icon('trash',14) ?></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout_end.php'; ?>
