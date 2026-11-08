<?php
session_start();
require_once '../config/db.php';
require_once 'auth.php';
$pdo = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$produit = null; $selectedVilles = []; $images = [];

if ($id) {
    $s = $pdo->prepare("SELECT * FROM produits WHERE id = ?"); $s->execute([$id]);
    $produit = $s->fetch();
    if (!$produit) { header('Location: produits.php'); exit; }
    $sv = $pdo->prepare("SELECT ville_slug FROM produit_villes WHERE produit_id = ?"); $sv->execute([$id]);
    $selectedVilles = $sv->fetchAll(PDO::FETCH_COLUMN);
    try {
        $si = $pdo->prepare("SELECT * FROM produit_images WHERE produit_id = ? ORDER BY is_main DESC, ordre ASC");
        $si->execute([$id]); $images = $si->fetchAll();
    } catch (PDOException $e) { $images = []; }
}

$villes = $pdo->query("SELECT * FROM villes ORDER BY nom")->fetchAll();
$errors = [];
$adminTitle = $id ? 'Modifier le produit' : 'Nouveau produit';

$uploadDir = dirname(__DIR__) . '/uploads/produits/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

function makeSlug($str) {
    $str = mb_strtolower($str);
    foreach (['àáâã'=>'a','éèêë'=>'e','îï'=>'i','ôö'=>'o','ùûü'=>'u'] as $from=>$to)
        $str = preg_replace('/['.$from.']/u',$to,$str);
    $str = preg_replace('/[^a-z0-9\s-]/u','',$str);
    return trim(preg_replace('/[\s]+/','-',trim($str)),'-');
}

function uploadImage($file, $produitId, $uploadDir) {
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp'])) return null;
    if ($file['size'] > 5 * 1024 * 1024) return null;
    $filename = 'prod_' . $produitId . '_' . uniqid() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) return $filename;
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    $nom          = trim($_POST['nom'] ?? '');
    $slug         = trim($_POST['slug'] ?? '') ?: makeSlug($nom);
    $marque       = trim($_POST['marque'] ?? '');
    $prix         = (int)($_POST['prix'] ?? 0);
    $categorie    = $_POST['categorie'] ?? 'telephone';
    $stockage     = trim($_POST['stockage'] ?? '');
    $couleur      = trim($_POST['couleur'] ?? '');
    $etat         = trim($_POST['etat'] ?? 'Neuf');
    $garantie     = trim($_POST['garantie'] ?? '');
    $disponibilite= trim($_POST['disponibilite'] ?? 'Douala');
    $en_stock     = isset($_POST['en_stock']) ? 1 : 0;
    $vedette      = isset($_POST['vedette']) ? 1 : 0;
    $description  = trim($_POST['description'] ?? '');
    $postVilles   = $_POST['villes'] ?? [];

    if (!$nom)  $errors[] = 'Le nom est obligatoire.';
    if (!$marque) $errors[] = 'La marque est obligatoire.';
    if ($prix <= 0) $errors[] = 'Le prix doit être supérieur à 0.';

    if (!$errors) {
        if ($id) {
            $pdo->prepare("UPDATE produits SET nom=?,slug=?,marque=?,prix=?,categorie=?,stockage=?,couleur=?,etat=?,garantie=?,disponibilite=?,en_stock=?,vedette=?,description=? WHERE id=?")
                ->execute([$nom,$slug,$marque,$prix,$categorie,$stockage?:null,$couleur,$etat,$garantie,$disponibilite,$en_stock,$vedette,$description,$id]);
            $pdo->prepare("DELETE FROM produit_villes WHERE produit_id=?")->execute([$id]);
        } else {
            $pdo->prepare("INSERT INTO produits (nom,slug,marque,prix,categorie,stockage,couleur,etat,garantie,disponibilite,en_stock,vedette,description) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)")
                ->execute([$nom,$slug,$marque,$prix,$categorie,$stockage?:null,$couleur,$etat,$garantie,$disponibilite,$en_stock,$vedette,$description]);
            $id = $pdo->lastInsertId();
        }

        foreach ($postVilles as $vs) {
            $pdo->prepare("INSERT IGNORE INTO produit_villes (produit_id,ville_slug) VALUES (?,?)")->execute([$id,$vs]);
        }

        // --- Upload photo principale ---
        if (isset($_FILES['photo_main']) && $_FILES['photo_main']['error'] === UPLOAD_ERR_OK) {
            $fn = uploadImage($_FILES['photo_main'], $id, $uploadDir);
            if ($fn) {
                try {
                    // Désactiver ancienne principale
                    $pdo->prepare("UPDATE produit_images SET is_main=0 WHERE produit_id=?")->execute([$id]);
                    // Vérifier si existe déjà une principale
                    $existing = $pdo->prepare("SELECT id FROM produit_images WHERE produit_id=? AND is_main=1 LIMIT 1");
                    $existing->execute([$id]); $existing = $existing->fetchColumn();
                    if ($existing) {
                        // Supprimer l'ancienne
                        $old = $pdo->prepare("SELECT filename FROM produit_images WHERE id=?"); $old->execute([$existing]); $old = $old->fetchColumn();
                        if ($old && file_exists($uploadDir.$old)) unlink($uploadDir.$old);
                        $pdo->prepare("DELETE FROM produit_images WHERE id=?")->execute([$existing]);
                    }
                    $ordre = $pdo->prepare("SELECT COUNT(*) FROM produit_images WHERE produit_id=?"); $ordre->execute([$id]); $ordre = (int)$ordre->fetchColumn();
                    $pdo->prepare("INSERT INTO produit_images (produit_id,filename,is_main,ordre) VALUES (?,?,1,?)")->execute([$id,$fn,0]);
                } catch (PDOException $e) {}
            }
        }

        // --- Upload autres photos (max 5) ---
        if (isset($_FILES['photos_extra'])) {
            $extras = $_FILES['photos_extra'];
            $fileList = [];
            if (is_array($extras['name'])) {
                for ($k = 0; $k < count($extras['name']); $k++) {
                    $fileList[] = ['name'=>$extras['name'][$k],'type'=>$extras['type'][$k],'tmp_name'=>$extras['tmp_name'][$k],'error'=>$extras['error'][$k],'size'=>$extras['size'][$k]];
                }
            }
            try {
                $currentCount = (int)$pdo->prepare("SELECT COUNT(*) FROM produit_images WHERE produit_id=?")->execute([$id]) ? $pdo->query("SELECT COUNT(*) FROM produit_images WHERE produit_id=$id")->fetchColumn() : 0;
                foreach ($fileList as $f) {
                    if ($currentCount >= 6) break;
                    $fn = uploadImage($f, $id, $uploadDir);
                    if ($fn) {
                        $pdo->prepare("INSERT INTO produit_images (produit_id,filename,is_main,ordre) VALUES (?,?,0,?)")->execute([$id,$fn,$currentCount]);
                        $currentCount++;
                    }
                }
            } catch (PDOException $e) {}
        }

        header('Location: produit_form.php?id=' . $id . '&msg=saved'); exit;
    }
}

// Supprimer une image existante
if (isset($_GET['del_img']) && $id) {
    $imgId = (int)$_GET['del_img'];
    try {
        $fn = $pdo->prepare("SELECT filename FROM produit_images WHERE id=? AND produit_id=?"); $fn->execute([$imgId,$id]); $fn = $fn->fetchColumn();
        if ($fn && file_exists($uploadDir.$fn)) unlink($uploadDir.$fn);
        $pdo->prepare("DELETE FROM produit_images WHERE id=? AND produit_id=?")->execute([$imgId,$id]);
    } catch (PDOException $e) {}
    header('Location: produit_form.php?id='.$id); exit;
}

// Définir image principale
if (isset($_GET['set_main']) && $id) {
    $imgId = (int)$_GET['set_main'];
    try {
        $pdo->prepare("UPDATE produit_images SET is_main=0 WHERE produit_id=?")->execute([$id]);
        $pdo->prepare("UPDATE produit_images SET is_main=1 WHERE id=? AND produit_id=?")->execute([$imgId,$id]);
    } catch (PDOException $e) {}
    header('Location: produit_form.php?id='.$id.'&msg=saved'); exit;
}

include 'layout.php';
?>

<?php if (isset($_GET['msg']) && $_GET['msg']==='saved'): ?>
<div class="alert alert-success"><?= icon('check',16) ?> Produit enregistré avec succès.</div>
<?php endif; ?>
<?php if ($errors): ?>
<div class="alert alert-error"><?= icon('x',16) ?> <?= implode(' — ', array_map('htmlspecialchars', $errors)) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="save_product" value="1">

<div class="card" style="margin-bottom:1rem;">
    <div class="card-header">
        <h2><?= icon($id?'pencil':'plus',16) ?> <?= $id ? htmlspecialchars($produit['nom']) : 'Nouveau produit' ?></h2>
        <div style="display:flex;gap:.5rem;">
            <a href="produits.php" class="btn btn-ghost btn-sm">Retour</a>
            <button type="submit" class="btn btn-primary btn-sm"><?= icon('check',14) ?> Enregistrer</button>
        </div>
    </div>
    <div class="card-body">

        <!-- PHOTOS — en haut du formulaire -->
        <div style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid #f1f5f9;">
            <p style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:1rem;">Photos du produit</p>

            <?php if (!empty($images)): ?>
            <!-- Galerie existante -->
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.25rem;">
                <?php foreach ($images as $img): ?>
                <div style="position:relative;width:110px;height:110px;border-radius:10px;overflow:hidden;border:2.5px solid <?= $img['is_main']?'#1a56db':'#e2e8f0' ?>;">
                    <img src="<?= BASE_URL ?>/uploads/produits/<?= htmlspecialchars($img['filename']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                    <?php if ($img['is_main']): ?>
                    <div style="position:absolute;top:.3rem;left:.3rem;background:#1a56db;color:#fff;font-size:.6rem;font-weight:700;padding:.15rem .4rem;border-radius:4px;">PRINCIPALE</div>
                    <?php endif; ?>
                    <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,.6);display:flex;gap:.2rem;padding:.3rem;justify-content:center;">
                        <?php if (!$img['is_main']): ?>
                        <a href="?id=<?= $id ?>&set_main=<?= $img['id'] ?>" title="Définir principale"
                           style="background:#1a56db;color:#fff;border:none;border-radius:4px;padding:.2rem .4rem;cursor:pointer;font-size:.65rem;font-weight:700;text-decoration:none;">Principal</a>
                        <?php endif; ?>
                        <a href="?id=<?= $id ?>&del_img=<?= $img['id'] ?>" title="Supprimer"
                           onclick="return confirm('Supprimer cette photo ?')"
                           style="background:#dc2626;color:#fff;border:none;border-radius:4px;padding:.2rem .4rem;cursor:pointer;font-size:.65rem;text-decoration:none;"><?= icon('trash',12) ?></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (empty($images) || count($images) === 0): ?>
            <!-- Placeholder quand pas d'image -->
            <div style="display:flex;gap:.75rem;margin-bottom:1rem;">
                <div style="width:110px;height:110px;border-radius:10px;border:2px dashed #e2e8f0;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#d1d5db;font-size:.7rem;">
                    <?= icon('camera',28,'','opacity:.3;') ?>
                    <span style="margin-top:.3rem;">Aucune photo</span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Upload photo principale -->
            <div class="form-grid">
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.4rem;"><?= icon('star-solid',13,'','color:#f59e0b;') ?> Photo principale</label>
                    <input type="file" name="photo_main" accept=".jpg,.jpeg,.png,.webp"
                           style="padding:.5rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.85rem;width:100%;">
                    <span class="field-hint">JPG, PNG, WEBP — Max 5 MB. Remplace la photo principale actuelle.</span>
                </div>
                <div class="form-group">
                    <label>Autres photos (max 5 supplémentaires)</label>
                    <input type="file" name="photos_extra[]" accept=".jpg,.jpeg,.png,.webp" multiple
                           style="padding:.5rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.85rem;width:100%;">
                    <span class="field-hint">Sélectionnez plusieurs fichiers en maintenant Ctrl (ou Cmd sur Mac)</span>
                </div>
            </div>
        </div>

        <!-- INFOS PRINCIPALES -->
        <p style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.85rem;">Informations principales</p>
        <div class="form-grid" style="margin-bottom:1.5rem;">
            <div class="form-group">
                <label>Nom du produit *</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($produit['nom'] ?? '') ?>" placeholder="iPhone 17 Pro Max 256GB" required>
            </div>
            <div class="form-group">
                <label>Slug URL</label>
                <input type="text" name="slug" value="<?= htmlspecialchars($produit['slug'] ?? '') ?>" placeholder="iphone-17-pro-max (auto si vide)">
            </div>
            <div class="form-group">
                <label>Marque *</label>
                <?php
                $marques = ['Apple','Samsung','Xiaomi','Infinix','Tecno','Sony','Huawei','Google','Oppo','OnePlus','Motorola','Nokia','Realme','Itel','Autre'];
                $marqueActuelle = $produit['marque'] ?? '';
                ?>
                <select name="marque" required>
                    <option value="">-- Choisir une marque --</option>
                    <?php foreach ($marques as $m): ?>
                    <option value="<?= $m ?>" <?= $marqueActuelle === $m ? 'selected' : '' ?>><?= $m ?></option>
                    <?php endforeach; ?>
                    <?php if ($marqueActuelle && !in_array($marqueActuelle, $marques)): ?>
                    <option value="<?= htmlspecialchars($marqueActuelle) ?>" selected><?= htmlspecialchars($marqueActuelle) ?></option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Prix (FCFA) *</label>
                <input type="number" name="prix" value="<?= $produit['prix'] ?? '' ?>" placeholder="1250000" min="0" required>
            </div>
            <div class="form-group">
                <label>Catégorie</label>
                <select name="categorie">
                    <?php
                    $cats = [];
                    try { $cats = $pdo->query("SELECT * FROM categories ORDER BY ordre ASC, nom ASC")->fetchAll(); } catch (PDOException $e) {}
                    if (empty($cats)):
                    ?>
                    <option value="telephone" <?= ($produit['categorie']??'telephone')==='telephone'?'selected':'' ?>>Téléphone</option>
                    <option value="accessoire" <?= ($produit['categorie']??'')==='accessoire'?'selected':'' ?>>Accessoire</option>
                    <?php else: foreach ($cats as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['slug']) ?>"
                        <?= ($produit['categorie']??'') === $cat['slug'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Stockage</label>
                <?php $stockages = ['32 Go','64 Go','128 Go','256 Go','512 Go','1 To','2 To']; ?>
                <select name="stockage">
                    <option value="">-- Sans stockage (accessoire) --</option>
                    <?php foreach ($stockages as $s): ?>
                    <option value="<?= $s ?>" <?= ($produit['stockage'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Couleur</label>
                <input type="text" name="couleur" value="<?= htmlspecialchars($produit['couleur'] ?? '') ?>" placeholder="Noir, Blanc...">
            </div>
            <div class="form-group">
                <label>État</label>
                <select name="etat">
                    <?php foreach (['Neuf','Scellé','Reconditionné','FastDeal'] as $e): ?>
                    <option value="<?= $e ?>" <?= ($produit['etat'] ?? 'Neuf') === $e ? 'selected' : '' ?>><?= $e ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Garantie</label>
                <input type="text" name="garantie" value="<?= htmlspecialchars($produit['garantie'] ?? '') ?>" placeholder="1 an constructeur">
            </div>
            <div class="form-group">
                <label>Ville de disponibilité</label>
                <input type="text" name="disponibilite" value="<?= htmlspecialchars($produit['disponibilite'] ?? 'Douala') ?>">
            </div>
            <div class="form-group full">
                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Description complète du produit..."><?= htmlspecialchars($produit['description'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- VILLES -->
        <p style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.85rem;padding-top:1rem;border-top:1px solid #f1f5f9;">Villes de livraison</p>
        <div style="display:flex;flex-wrap:wrap;gap:.6rem;margin-bottom:1.5rem;">
            <?php foreach ($villes as $v): ?>
            <label style="display:flex;align-items:center;gap:.5rem;font-weight:500;font-size:.85rem;cursor:pointer;padding:.45rem .85rem;border-radius:8px;border:1.5px solid <?= in_array($v['slug'],$selectedVilles)?'#1a56db':'#e2e8f0' ?>;background:<?= in_array($v['slug'],$selectedVilles)?'#eff6ff':'#fff' ?>;">
                <input type="checkbox" name="villes[]" value="<?= $v['slug'] ?>" <?= in_array($v['slug'],$selectedVilles)?'checked':'' ?> style="width:auto;">
                <?= htmlspecialchars($v['nom']) ?>
            </label>
            <?php endforeach; ?>
        </div>

        <!-- OPTIONS -->
        <p style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.85rem;padding-top:1rem;border-top:1px solid #f1f5f9;">Options</p>
        <div style="display:flex;gap:2.5rem;flex-wrap:wrap;">
            <label style="display:flex;align-items:center;gap:.65rem;cursor:pointer;font-size:.875rem;font-weight:500;">
                <label class="toggle"><input type="checkbox" name="en_stock" <?= ($produit['en_stock']??1)?'checked':'' ?>><span class="toggle-s"></span></label>
                En stock
            </label>
            <label style="display:flex;align-items:center;gap:.65rem;cursor:pointer;font-size:.875rem;font-weight:500;">
                <label class="toggle"><input type="checkbox" name="vedette" <?= ($produit['vedette']??0)?'checked':'' ?>><span class="toggle-s"></span></label>
                Produit vedette (homepage)
            </label>
        </div>
    </div>
</div>

<div style="display:flex;gap:.75rem;margin-bottom:2rem;">
    <button type="submit" class="btn btn-primary btn-lg"><?= icon('check',16) ?> Enregistrer le produit</button>
    <a href="produits.php" class="btn btn-ghost btn-lg">Annuler</a>
</div>
</form>

<?php include 'layout_end.php'; ?>
