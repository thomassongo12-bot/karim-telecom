<?php
session_start();
require_once '../config/db.php';
require_once 'auth.php';
$pdo = getDB();
$adminTitle = 'Catégories';

// Supprimer
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $img = $pdo->prepare("SELECT image FROM categories WHERE id=?");
        $img->execute([$id]);
        $img = $img->fetchColumn();
        if ($img && file_exists(dirname(__DIR__) . '/uploads/categories/' . $img)) {
            unlink(dirname(__DIR__) . '/uploads/categories/' . $img);
        }
        $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
        header('Location: categories.php?msg=deleted'); exit;
    } catch (PDOException $ex) {
        header('Location: categories.php?msg=error'); exit;
    }
}

// Charger catégorie à éditer
$editCat = null;
if (isset($_GET['edit'])) {
    try {
        $e = $pdo->prepare("SELECT * FROM categories WHERE id=?");
        $e->execute([(int)$_GET['edit']]);
        $editCat = $e->fetch();
    } catch (PDOException $ex) { $editCat = null; }
}

// Upload image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = dirname(__DIR__) . '/uploads/categories/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg','jpeg','png','webp'])) {
        $filename = 'cat_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
        $_POST['image'] = $filename;
    }
}

// Sauvegarder
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $id   = (int)($_POST['id'] ?? 0);
    $nom  = trim($_POST['nom'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    if (!$slug && $nom) $slug = strtolower(preg_replace('/[^a-z0-9]/i', '-', $nom));
    $image = $_POST['image'] ?? '';
    $ordre = (int)($_POST['ordre'] ?? 0);

    if ($nom) {
        try {
            if ($id) {
                if ($image) {
                    $pdo->prepare("UPDATE categories SET image=?, nom=?, slug=?, ordre=? WHERE id=?")
                        ->execute([$image, $nom, $slug, $ordre, $id]);
                } else {
                    $pdo->prepare("UPDATE categories SET nom=?, slug=?, ordre=? WHERE id=?")
                        ->execute([$nom, $slug, $ordre, $id]);
                }
            } else {
                $pdo->prepare("INSERT INTO categories (nom, slug, image, ordre) VALUES (?,?,?,?)")
                    ->execute([$nom, $slug, $image, $ordre]);
            }
            header('Location: categories.php?msg=saved'); exit;
        } catch (PDOException $ex) {}
    }
}

// Lister
$cats = [];
try {
    $cats = $pdo->query("SELECT * FROM categories ORDER BY ordre ASC, nom ASC")->fetchAll();
} catch (PDOException $e) { $cats = []; }

include 'layout.php';
?>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'error'): ?>
<div class="alert alert-error">Erreur lors de l'opération. Vérifiez que la table <code>categories</code> existe.</div>
<?php elseif (isset($_GET['msg'])): ?>
<div class="alert alert-success"><?= icon('check',16) ?> <?= $_GET['msg']==='saved' ? 'Catégorie enregistrée.' : 'Catégorie supprimée.' ?></div>
<?php endif; ?>

<!-- Formulaire -->
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <h2><?= icon($editCat?'pencil':'plus',16) ?> <?= $editCat ? 'Modifier la catégorie' : 'Ajouter une catégorie' ?></h2>
        <?php if ($editCat): ?><a href="categories.php" class="btn btn-ghost btn-sm"><?= icon('x',14) ?> Annuler</a><?php endif; ?>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?php if ($editCat): ?><input type="hidden" name="id" value="<?= $editCat['id'] ?>"><?php endif; ?>
            <?php if ($editCat && $editCat['image']): ?><input type="hidden" name="image" value="<?= htmlspecialchars($editCat['image']) ?>"><?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($editCat['nom'] ?? '') ?>" placeholder="Apple, Samsung, Accessoires..." required>
                </div>
                <div class="form-group">
                    <label>Slug (auto si vide)</label>
                    <input type="text" name="slug" value="<?= htmlspecialchars($editCat['slug'] ?? '') ?>" placeholder="apple, samsung...">
                </div>
                <div class="form-group">
                    <label>Ordre d'affichage</label>
                    <input type="number" name="ordre" value="<?= $editCat['ordre'] ?? 0 ?>" min="0">
                </div>
                <div class="form-group full">
                    <label>Photo de la catégorie</label>
                    <?php if ($editCat && $editCat['image']): ?>
                    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:.75rem;">
                        <img src="<?= BASE_URL ?>/uploads/categories/<?= htmlspecialchars($editCat['image']) ?>" alt="" style="width:80px;height:80px;border-radius:10px;object-fit:cover;border:1px solid #e2e8f0;">
                        <span style="font-size:.82rem;color:#64748b;">Image actuelle. Uploadez une nouvelle pour remplacer.</span>
                    </div>
                    <?php endif; ?>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" style="padding:.5rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.85rem;">
                    <span class="field-hint">JPG, PNG, WEBP — Format carré recommandé (400x400)</span>
                </div>
            </div>
            <div style="margin-top:1rem;">
                <button type="submit" name="save" value="1" class="btn btn-primary"><?= icon('check',16) ?> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Liste -->
<div class="card">
    <div class="card-header">
        <h2><?= icon('tag',16) ?> Catégories <span style="font-weight:400;color:#64748b;">(<?= count($cats) ?>)</span></h2>
    </div>
    <?php if (empty($cats)): ?>
    <div class="card-body" style="text-align:center;padding:2rem;color:#94a3b8;">
        <?= icon('tag',40,'','opacity:.3;') ?>
        <p style="font-size:.85rem;margin-top:.5rem;">Aucune catégorie pour le moment.<br>Ajoutez-en une ci-dessus.</p>
        <?php if (!isset($_GET['edit'])): ?>
        <p style="font-size:.78rem;color:#dc2626;margin-top:.5rem;">Si vous voyez cette erreur, exécutez le SQL de création de table dans phpMyAdmin.</p>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Image</th><th>Nom</th><th>Slug</th><th>Ordre</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($cats as $c): ?>
            <tr>
                <td>
                    <?php if ($c['image']): ?>
                    <img src="<?= BASE_URL ?>/uploads/categories/<?= htmlspecialchars($c['image']) ?>" alt="" style="width:48px;height:48px;border-radius:8px;object-fit:cover;">
                    <?php else: ?>
                    <div style="width:48px;height:48px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#cbd5e1;"><?= icon('tag',20) ?></div>
                    <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($c['nom']) ?></strong></td>
                <td style="font-size:.78rem;color:#94a3b8;"><?= $c['slug'] ?></td>
                <td><?= $c['ordre'] ?></td>
                <td>
                    <div style="display:flex;gap:.35rem;">
                        <a href="categories.php?edit=<?= $c['id'] ?>" class="btn btn-ghost btn-sm"><?= icon('pencil',14) ?></a>
                        <a href="categories.php?delete=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer «<?= addslashes($c['nom']) ?>» ?')"><?= icon('trash',14) ?></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include 'layout_end.php'; ?>
