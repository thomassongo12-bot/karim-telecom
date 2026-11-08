<?php
session_start();
require_once '../config/db.php';
require_once 'auth.php';
$pdo = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = null; $errors = [];

$uploadDir = dirname(__DIR__) . '/uploads/articles/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if ($id) {
    $s = $pdo->prepare("SELECT * FROM articles WHERE id = ?"); $s->execute([$id]);
    $article = $s->fetch();
    if (!$article) { header('Location: articles.php'); exit; }
}

$adminTitle = $id ? 'Modifier l\'article' : 'Nouvel article';

function makeSlug($str) {
    $str = mb_strtolower($str);
    foreach (['àáâã'=>'a','éèêë'=>'e','îï'=>'i','ôö'=>'o','ùûü'=>'u'] as $from=>$to)
        $str = preg_replace('/['.$from.']/u',$to,$str);
    $str = preg_replace('/[^a-z0-9\s-]/u','',$str);
    return trim(preg_replace('/[\s]+/','-',trim($str)),'-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre    = trim($_POST['titre'] ?? '');
    $slug     = trim($_POST['slug'] ?? '') ?: makeSlug($titre);
    $extrait  = trim($_POST['extrait'] ?? '');
    $contenu  = trim($_POST['contenu'] ?? '');
    $categorie= trim($_POST['categorie'] ?? 'Guide achat');
    $lecture  = max(1, (int)($_POST['temps_lecture'] ?? 5));
    $date     = $_POST['date_publication'] ?? date('Y-m-d');
    $imageActuelle = $article['image'] ?? '';

    // Upload image article
    $newImage = $imageActuelle;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $fn = 'article_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fn)) {
                if ($imageActuelle && file_exists($uploadDir . $imageActuelle)) unlink($uploadDir . $imageActuelle);
                $newImage = $fn;
            }
        }
    }

    if (!$titre)   $errors[] = 'Le titre est obligatoire.';
    if (!$contenu) $errors[] = 'Le contenu est obligatoire.';

    if (!$errors) {
        if ($id) {
            $pdo->prepare("UPDATE articles SET titre=?,slug=?,extrait=?,contenu=?,categorie=?,image=?,temps_lecture=?,date_publication=? WHERE id=?")
                ->execute([$titre,$slug,$extrait,$contenu,$categorie,$newImage,$lecture,$date,$id]);
        } else {
            $pdo->prepare("INSERT INTO articles (titre,slug,extrait,contenu,categorie,image,temps_lecture,date_publication) VALUES (?,?,?,?,?,?,?,?)")
                ->execute([$titre,$slug,$extrait,$contenu,$categorie,$newImage,$lecture,$date]);
        }
        header('Location: articles.php?msg=saved'); exit;
    }
}

include 'layout.php';
?>

<?php if ($errors): ?>
<div class="alert alert-error"><?= icon('x',16) ?> <?= implode(' — ', array_map('htmlspecialchars',$errors)) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
<div class="card">
    <div class="card-header">
        <h2><?= icon($id?'pencil':'plus',16) ?> <?= $id ? 'Modifier l\'article' : 'Nouvel article' ?></h2>
        <div style="display:flex;gap:.5rem;">
            <a href="articles.php" class="btn btn-ghost btn-sm">Retour</a>
            <button type="submit" class="btn btn-primary btn-sm"><?= icon('check',14) ?> Enregistrer</button>
        </div>
    </div>
    <div class="card-body">

        <!-- IMAGE DE COUVERTURE -->
        <p style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.85rem;">Image de couverture</p>
        <div style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid #f1f5f9;">
            <?php $imgActuelle = $article['image'] ?? ''; ?>
            <?php if ($imgActuelle): ?>
            <div style="display:flex;align-items:flex-start;gap:1rem;margin-bottom:.85rem;">
                <img src="<?= BASE_URL ?>/uploads/articles/<?= htmlspecialchars($imgActuelle) ?>"
                     alt="Couverture" style="width:200px;height:110px;border-radius:10px;object-fit:cover;border:1px solid #e2e8f0;flex-shrink:0;">
                <div>
                    <p style="font-size:.82rem;font-weight:600;color:#374151;margin-bottom:.25rem;">Image actuelle</p>
                    <p style="font-size:.75rem;color:#64748b;">Uploadez une nouvelle image pour la remplacer.</p>
                </div>
            </div>
            <?php else: ?>
            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;padding:1rem;background:#f8fafc;border-radius:10px;border:2px dashed #e2e8f0;">
                <?= icon('camera',28,'','color:#94a3b8;') ?>
                <div>
                    <p style="font-size:.82rem;font-weight:600;color:#94a3b8;">Aucune image de couverture</p>
                    <p style="font-size:.75rem;color:#94a3b8;">Ajoutez une photo qui illustre l'article (format 16/9 recommandé).</p>
                </div>
            </div>
            <?php endif; ?>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
                   style="padding:.5rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.85rem;width:100%;max-width:500px;">
            <span class="field-hint">JPG, PNG, WEBP — Format 16/9 recommandé (ex: 1200×675px). Max 5 MB.</span>
        </div>

        <!-- INFOS ARTICLE -->
        <div class="form-grid">
            <div class="form-group full">
                <label>Titre *</label>
                <input type="text" name="titre" value="<?= htmlspecialchars($article['titre'] ?? '') ?>"
                       placeholder="Les meilleurs iPhones au Cameroun en 2025" required>
            </div>
            <div class="form-group">
                <label>Slug URL</label>
                <input type="text" name="slug" value="<?= htmlspecialchars($article['slug'] ?? '') ?>" placeholder="auto-généré si vide">
            </div>
            <div class="form-group">
                <label>Catégorie</label>
                <select name="categorie">
                    <?php foreach (['Guide achat','Comparatif','Conseils','Actualité'] as $c): ?>
                    <option value="<?= $c ?>" <?= ($article['categorie']??'')===$c?'selected':'' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Temps de lecture (min)</label>
                <input type="number" name="temps_lecture" value="<?= $article['temps_lecture'] ?? 5 ?>" min="1" max="60">
            </div>
            <div class="form-group">
                <label>Date de publication</label>
                <input type="date" name="date_publication" value="<?= $article['date_publication'] ?? date('Y-m-d') ?>">
            </div>
            <div class="form-group full">
                <label>Extrait (affiché dans la liste)</label>
                <textarea name="extrait" rows="2" placeholder="Résumé de l'article..."><?= htmlspecialchars($article['extrait'] ?? '') ?></textarea>
            </div>
            <div class="form-group full">
                <label>Contenu (HTML)</label>
                <span class="field-hint" style="margin-bottom:.4rem;display:block;">Utilisez : &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;&lt;li&gt;, &lt;strong&gt;</span>
                <textarea name="contenu" rows="18" placeholder="<h2>Titre</h2>&#10;<p>Contenu...</p>"><?= htmlspecialchars($article['contenu'] ?? '') ?></textarea>
            </div>
        </div>
    </div>
</div>
<div style="display:flex;gap:.75rem;margin-top:.25rem;">
    <button type="submit" class="btn btn-primary btn-lg"><?= icon('check',16) ?> Enregistrer l'article</button>
    <a href="articles.php" class="btn btn-ghost btn-lg">Annuler</a>
</div>
</form>

<?php include 'layout_end.php'; ?>
