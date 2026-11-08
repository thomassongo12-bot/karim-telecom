<?php
session_start();
require_once '../config/db.php';
require_once 'auth.php';
$pdo = getDB();
$adminTitle = 'Avis clients';

$uploadDir = dirname(__DIR__) . '/uploads/avis/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $p = $pdo->prepare("SELECT photo FROM avis WHERE id=?"); $p->execute([$id]);
        $photo = $p->fetchColumn();
        if ($photo && file_exists($uploadDir.$photo)) unlink($uploadDir.$photo);
        $pdo->prepare("DELETE FROM avis WHERE id=?")->execute([$id]);
    } catch (PDOException $e) {}
    header('Location: avis.php?msg=deleted'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = (int)($_POST['id'] ?? 0);
    $nom     = trim($_POST['nom'] ?? '');
    $ville   = trim($_POST['ville'] ?? '');
    $note    = min(5, max(1, (int)($_POST['note'] ?? 5)));
    $texte   = trim($_POST['texte'] ?? '');
    $produit = trim($_POST['produit_achete'] ?? '');
    $date    = $_POST['date_avis'] ?? date('Y-m-d');

    // Photo actuelle
    $photoActuelle = '';
    if ($id) {
        $pa = $pdo->prepare("SELECT photo FROM avis WHERE id=?"); $pa->execute([$id]);
        $photoActuelle = $pa->fetchColumn() ?: '';
    }

    // Upload photo de profil
    $newPhoto = $photoActuelle;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $fn = 'avis_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $fn)) {
                if ($photoActuelle && file_exists($uploadDir.$photoActuelle)) unlink($uploadDir.$photoActuelle);
                $newPhoto = $fn;
            }
        }
    }

    if ($nom && $texte) {
        if ($id) {
            $pdo->prepare("UPDATE avis SET nom=?,ville=?,note=?,texte=?,produit_achete=?,date_avis=?,photo=? WHERE id=?")
                ->execute([$nom,$ville,$note,$texte,$produit,$date,$newPhoto,$id]);
        } else {
            $pdo->prepare("INSERT INTO avis (nom,ville,note,texte,produit_achete,date_avis,photo) VALUES (?,?,?,?,?,?,?)")
                ->execute([$nom,$ville,$note,$texte,$produit,$date,$newPhoto]);
        }
        header('Location: avis.php?msg=saved'); exit;
    }
}

$editAvis = null;
if (isset($_GET['edit'])) {
    $e = $pdo->prepare("SELECT * FROM avis WHERE id=?"); $e->execute([(int)$_GET['edit']]);
    $editAvis = $e->fetch();
}

$tousAvis = $pdo->query("SELECT * FROM avis ORDER BY date_avis DESC")->fetchAll();
include 'layout.php';
?>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success">
    <?= icon('check',16) ?>
    <?= $_GET['msg']==='saved' ? 'Avis enregistré avec succès.' : 'Avis supprimé.' ?>
</div>
<?php endif; ?>

<!-- Formulaire -->
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <h2><?= icon($editAvis?'pencil':'plus',16) ?> <?= $editAvis ? 'Modifier l\'avis' : 'Ajouter un avis' ?></h2>
        <?php if ($editAvis): ?>
        <a href="avis.php" class="btn btn-ghost btn-sm"><?= icon('x',14) ?> Annuler</a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?php if ($editAvis): ?><input type="hidden" name="id" value="<?= $editAvis['id'] ?>"><?php endif; ?>

            <!-- Photo de profil -->
            <p style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.85rem;">Photo de profil du client</p>
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;padding-bottom:1.25rem;border-bottom:1px solid #f1f5f9;">
                <?php $photoEdit = $editAvis['photo'] ?? ''; ?>
                <?php if ($photoEdit): ?>
                    <img src="<?= BASE_URL ?>/uploads/avis/<?= htmlspecialchars($photoEdit) ?>"
                         alt="" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;flex-shrink:0;">
                <?php else: ?>
                    <div style="width:64px;height:64px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#94a3b8;flex-shrink:0;">
                        <?= icon('users',24) ?>
                    </div>
                <?php endif; ?>
                <div style="flex:1;">
                    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp"
                           style="padding:.5rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.85rem;width:100%;max-width:400px;">
                    <span class="field-hint">Photo carrée recommandée (ex: 200×200px). JPG, PNG, WEBP.</span>
                </div>
            </div>

            <!-- Infos avis -->
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom du client *</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($editAvis['nom'] ?? '') ?>" placeholder="Armand Tchamba" required>
                </div>
                <div class="form-group">
                    <label>Ville</label>
                    <input type="text" name="ville" value="<?= htmlspecialchars($editAvis['ville'] ?? '') ?>" placeholder="Douala">
                </div>
                <div class="form-group">
                    <label>Note</label>
                    <select name="note">
                        <?php for($i=5;$i>=1;$i--): ?>
                        <option value="<?= $i ?>" <?= ($editAvis['note']??5)==$i?'selected':'' ?>>
                            <?= $i ?>/5 — <?= str_repeat('★',$i).str_repeat('☆',5-$i) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Produit acheté</label>
                    <input type="text" name="produit_achete" value="<?= htmlspecialchars($editAvis['produit_achete'] ?? '') ?>" placeholder="iPhone 17 Pro Max...">
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date_avis" value="<?= $editAvis['date_avis'] ?? date('Y-m-d') ?>">
                </div>
                <div class="form-group full">
                    <label>Texte de l'avis *</label>
                    <textarea name="texte" rows="3" placeholder="Témoignage du client..." required><?= htmlspecialchars($editAvis['texte'] ?? '') ?></textarea>
                </div>
            </div>
            <div style="margin-top:1rem;">
                <button type="submit" class="btn btn-primary"><?= icon('check',16) ?> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Liste -->
<div class="card">
    <div class="card-header">
        <h2><?= icon('star',16) ?> Avis clients <span style="font-weight:400;color:#64748b;">(<?= count($tousAvis) ?>)</span></h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Client</th><th>Ville</th><th>Note</th><th>Avis</th><th>Produit</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($tousAvis as $a): ?>
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:.65rem;">
                        <?php if (!empty($a['photo'])): ?>
                        <img src="<?= BASE_URL ?>/uploads/avis/<?= htmlspecialchars($a['photo']) ?>"
                             alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;border:1.5px solid #e2e8f0;">
                        <?php else: ?>
                        <div style="width:36px;height:36px;border-radius:50%;background:#eff6ff;color:#1a56db;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0;">
                            <?= mb_strtoupper(mb_substr($a['nom'],0,1)) ?>
                        </div>
                        <?php endif; ?>
                        <strong><?= htmlspecialchars($a['nom']) ?></strong>
                    </div>
                </td>
                <td><?= htmlspecialchars($a['ville']) ?></td>
                <td>
                    <div style="display:flex;gap:1px;">
                        <?php for($i=1;$i<=5;$i++): ?>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="<?= $i<=$a['note']?'#f59e0b':'#e2e8f0' ?>" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <?php endfor; ?>
                    </div>
                </td>
                <td style="max-width:220px;font-size:.8rem;color:#475569;">"<?= htmlspecialchars(mb_substr($a['texte'],0,70)) ?>..."</td>
                <td style="font-size:.78rem;color:#94a3b8;"><?= htmlspecialchars($a['produit_achete']) ?></td>
                <td style="font-size:.78rem;white-space:nowrap;"><?= $a['date_avis'] ?></td>
                <td>
                    <div style="display:flex;gap:.35rem;">
                        <a href="avis.php?edit=<?= $a['id'] ?>" class="btn btn-ghost btn-sm"><?= icon('pencil',14) ?></a>
                        <a href="avis.php?delete=<?= $a['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Supprimer cet avis ?')"><?= icon('trash',14) ?></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout_end.php'; ?>
