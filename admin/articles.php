<?php
session_start();
require_once '../config/db.php';
require_once 'auth.php';
$pdo = getDB();
$adminTitle = 'Articles blog';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM articles WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: articles.php?msg=deleted'); exit;
}

$articles = $pdo->query("SELECT * FROM articles ORDER BY date_publication DESC")->fetchAll();
include 'layout.php';
?>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success">
    <?= icon('check',16) ?>
    <?= $_GET['msg']==='saved' ? 'Article enregistré.' : 'Article supprimé.' ?>
</div>
<?php endif; ?>

<div style="display:flex;justify-content:flex-end;margin-bottom:1rem;">
    <a href="article_form.php" class="btn btn-primary"><?= icon('plus',16) ?> Nouvel article</a>
</div>

<div class="card">
    <div class="card-header">
        <h2><?= icon('article',16) ?> Articles <span style="font-weight:400;color:#64748b;">(<?= count($articles) ?>)</span></h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Titre</th><th>Catégorie</th><th>Lecture</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($articles as $a): ?>
            <tr>
                <td>
                    <div style="font-weight:600;font-size:.875rem;"><?= htmlspecialchars($a['titre']) ?></div>
                    <div style="font-size:.72rem;color:#94a3b8;">/<?= $a['slug'] ?></div>
                </td>
                <td><span class="badge badge-blue"><?= htmlspecialchars($a['categorie']) ?></span></td>
                <td style="font-size:.82rem;color:#64748b;"><?= $a['temps_lecture'] ?> min</td>
                <td style="font-size:.82rem;white-space:nowrap;"><?= $a['date_publication'] ?></td>
                <td>
                    <div style="display:flex;gap:.35rem;">
                        <a href="article_form.php?id=<?= $a['id'] ?>" class="btn btn-ghost btn-sm"><?= icon('pencil',14) ?></a>
                        <a href="<?= BASE_URL ?>/article.php?slug=<?= $a['slug'] ?>" target="_blank" class="btn btn-ghost btn-sm"><?= icon('eye',14) ?></a>
                        <a href="articles.php?delete=<?= $a['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Supprimer «<?= addslashes($a['titre']) ?>» ?')"><?= icon('trash',14) ?></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout_end.php'; ?>
