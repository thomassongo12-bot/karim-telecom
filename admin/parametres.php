<?php
session_start();
require_once '../config/db.php';
require_once 'auth.php';
$adminTitle = 'Paramètres';

$uploadDir = dirname(__DIR__) . '/uploads/site/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$siteConfigFile = dirname(__DIR__) . '/config/site.php';

// Lire config actuelle
$siteConfig = [];
if (file_exists($siteConfigFile)) $siteConfig = include $siteConfigFile;

$nomSite       = $siteConfig['nom_site']       ?? 'Karim Telecom';
$slogan        = $siteConfig['slogan']         ?? '';
$logo          = $siteConfig['logo']           ?? '';
$favicon       = $siteConfig['favicon']        ?? '';
$photoBoutique = $siteConfig['photo_boutique'] ?? '';
$adresse       = $siteConfig['adresse']        ?? 'Carrefour Warda, Akwa, Douala, Cameroun';
$mapLat        = $siteConfig['map_lat']        ?? '4.0511';
$mapLng        = $siteConfig['map_lng']        ?? '9.7035';

// Lire db.php
$dbFile    = dirname(__DIR__) . '/config/db.php';
$dbContent = file_get_contents($dbFile);
preg_match("/define\('WA_NUMBER',\s*'([^']*)'\)/", $dbContent, $waMatch);
preg_match("/define\('BASE_URL',\s*'([^']*)'\)/",  $dbContent, $baseMatch);
$waNumber = $waMatch[1]  ?? '237600000000';
$baseUrl  = $baseMatch[1] ?? '/Karim_Telecom';

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newNom    = trim($_POST['nom_site']  ?? 'Karim Telecom');
    $newSlogan = trim($_POST['slogan']    ?? '');
    $newWa     = preg_replace('/\D/', '', $_POST['wa_number'] ?? '237600000000');
    $newBase   = rtrim(trim($_POST['base_url'] ?? '/Karim_Telecom'), '/');
    $newUser   = trim($_POST['admin_user'] ?? '');
    $newPass   = trim($_POST['admin_pass'] ?? '');
    $newAdresse = trim($_POST['adresse']  ?? $adresse);
    $newLat     = trim($_POST['map_lat']  ?? $mapLat);
    $newLng     = trim($_POST['map_lng']  ?? $mapLng);

    // Upload logo
    $newLogo = $logo;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['png','jpg','jpeg','svg','webp'])) {
            $fn = 'logo_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $fn)) {
                if ($logo && file_exists($uploadDir . $logo)) unlink($uploadDir . $logo);
                $newLogo = $fn;
            }
        }
    }

    // Upload favicon
    $newFavicon = $favicon;
    if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['png','jpg','jpeg','svg','webp','ico'])) {
            $fn = 'favicon_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['favicon']['tmp_name'], $uploadDir . $fn)) {
                if ($favicon && file_exists($uploadDir . $favicon)) unlink($uploadDir . $favicon);
                $newFavicon = $fn;
            }
        }
    }

    // Upload photo boutique
    $newPhotoBoutique = $photoBoutique;
    if (isset($_FILES['photo_boutique']) && $_FILES['photo_boutique']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo_boutique']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['png','jpg','jpeg','webp'])) {
            $fn = 'boutique_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['photo_boutique']['tmp_name'], $uploadDir . $fn)) {
                if ($photoBoutique && file_exists($uploadDir . $photoBoutique)) unlink($uploadDir . $photoBoutique);
                $newPhotoBoutique = $fn;
            }
        }
    }

    // Sauvegarder config/site.php
    $configData  = "<?php\n";
    $configData .= "return [\n";
    $configData .= "    'nom_site'       => " . var_export($newNom, true) . ",\n";
    $configData .= "    'slogan'         => " . var_export($newSlogan, true) . ",\n";
    $configData .= "    'logo'           => " . var_export($newLogo, true) . ",\n";
    $configData .= "    'favicon'        => " . var_export($newFavicon, true) . ",\n";
    $configData .= "    'photo_boutique' => " . var_export($newPhotoBoutique, true) . ",\n";
    $configData .= "    'adresse'        => " . var_export($newAdresse, true) . ",\n";
    $configData .= "    'map_lat'        => " . var_export($newLat, true) . ",\n";
    $configData .= "    'map_lng'        => " . var_export($newLng, true) . ",\n";
    $configData .= "];\n";
    file_put_contents($siteConfigFile, $configData);

    // Mettre à jour db.php
    $dbContent = preg_replace("/define\('WA_NUMBER',\s*'[^']*'\);/", "define('WA_NUMBER', '$newWa');", $dbContent);
    $dbContent = preg_replace("/define\('BASE_URL',\s*'[^']*'\);/",  "define('BASE_URL', '$newBase');",  $dbContent);
    file_put_contents($dbFile, $dbContent);

    // Identifiants admin (hash bcrypt pour le mot de passe)
    $loginFile = __DIR__ . '/index.php';
    if ($newUser || $newPass) {
        $loginContent = file_get_contents($loginFile);
        if ($newUser) {
            $loginContent = preg_replace("/define\('ADMIN_USER',\s*'[^']*'\);/", "define('ADMIN_USER', '" . addslashes($newUser) . "');", $loginContent);
        }
        if ($newPass) {
            $hash = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
            $loginContent = preg_replace("/define\('ADMIN_PASS_HASH',\s*'[^']*'\);/", "define('ADMIN_PASS_HASH', '" . addslashes($hash) . "');", $loginContent);
        }
        file_put_contents($loginFile, $loginContent);
    }

    // Relire
    $nomSite = $newNom; $slogan = $newSlogan; $logo = $newLogo; $favicon = $newFavicon;
    $photoBoutique = $newPhotoBoutique; $adresse = $newAdresse;
    $mapLat = $newLat; $mapLng = $newLng;
    $waNumber = $newWa; $baseUrl = $newBase;
    $success = true;
}

include 'layout.php';
?>

<?php if ($success): ?>
<div class="alert alert-success"><?= icon('check',16) ?> Paramètres enregistrés.</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<!-- ── IDENTITÉ ── -->
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header"><h2><?= icon('store',16) ?> Identité du site</h2></div>
    <div class="card-body">

        <p class="section-label">Informations générales</p>
        <div class="form-grid" style="margin-bottom:1.5rem;">
            <div class="form-group">
                <label>Nom du site</label>
                <input type="text" name="nom_site" value="<?= htmlspecialchars($nomSite) ?>" placeholder="Karim Telecom">
            </div>
            <div class="form-group">
                <label>Slogan</label>
                <input type="text" name="slogan" value="<?= htmlspecialchars($slogan) ?>" placeholder="Téléphones & Accessoires">
            </div>
        </div>

        <p class="section-label" style="padding-top:1rem;border-top:1px solid #f1f5f9;">Logo & Favicon</p>
        <div class="form-grid" style="margin-bottom:1.5rem;">
            <div class="form-group">
                <label>Logo du site</label>
                <?php if ($logo): ?>
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.6rem;">
                    <img src="<?= BASE_URL ?>/uploads/site/<?= htmlspecialchars($logo) ?>" alt="Logo" style="width:56px;height:56px;border-radius:10px;object-fit:cover;border:1px solid #e2e8f0;">
                    <span style="font-size:.78rem;color:#64748b;">Logo actuel — uploadez pour remplacer</span>
                </div>
                <?php endif; ?>
                <input type="file" name="logo" accept=".jpg,.jpeg,.png,.svg,.webp" style="padding:.5rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.85rem;width:100%;">
            </div>
            <div class="form-group">
                <label>Favicon (onglet navigateur)</label>
                <?php if ($favicon): ?>
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.6rem;">
                    <img src="<?= BASE_URL ?>/uploads/site/<?= htmlspecialchars($favicon) ?>" alt="Favicon" style="width:32px;height:32px;border-radius:6px;object-fit:cover;border:1px solid #e2e8f0;">
                    <span style="font-size:.78rem;color:#64748b;">Favicon actuel — uploadez pour remplacer</span>
                </div>
                <?php endif; ?>
                <input type="file" name="favicon" accept=".jpg,.jpeg,.png,.svg,.webp,.ico" style="padding:.5rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.85rem;width:100%;">
            </div>
        </div>

        <p class="section-label" style="padding-top:1rem;border-top:1px solid #f1f5f9;">Contact & URL</p>
        <div class="form-grid" style="margin-bottom:1.5rem;">
            <div class="form-group">
                <label>Numéro WhatsApp</label>
                <input type="text" name="wa_number" value="<?= htmlspecialchars($waNumber) ?>" placeholder="237600000000">
                <span class="field-hint">Sans + ni espaces. Ex: 237692091345</span>
            </div>
            <div class="form-group">
                <label>BASE_URL</label>
                <input type="text" name="base_url" value="<?= htmlspecialchars($baseUrl) ?>" placeholder="/Karim_Telecom">
                <span class="field-hint">Local: /Karim_Telecom — En ligne: (vide)</span>
            </div>
        </div>

        <p class="section-label" style="padding-top:1rem;border-top:1px solid #f1f5f9;">Identifiants admin</p>
        <div class="form-grid">
            <div class="form-group">
                <label>Nom d'utilisateur</label>
                <input type="text" name="admin_user" placeholder="karim">
                <span class="field-hint">Laisser vide pour ne pas changer</span>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="text" name="admin_pass" placeholder="Nouveau mot de passe...">
                <span class="field-hint">Laisser vide pour ne pas changer</span>
            </div>
        </div>
    </div>
</div>

<!-- ── BOUTIQUE ── -->
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header"><h2><?= icon('building',16) ?> Boutique physique</h2></div>
    <div class="card-body">

        <p class="section-label">Photo de la boutique</p>
        <div class="form-grid" style="margin-bottom:1.5rem;">
            <div class="form-group full">
                <?php if ($photoBoutique): ?>
                <div style="display:flex;align-items:flex-start;gap:1rem;margin-bottom:.85rem;">
                    <img src="<?= BASE_URL ?>/uploads/site/<?= htmlspecialchars($photoBoutique) ?>"
                         alt="Photo boutique"
                         style="width:180px;height:110px;border-radius:10px;object-fit:cover;border:1px solid #e2e8f0;flex-shrink:0;">
                    <div>
                        <p style="font-size:.82rem;font-weight:600;color:#374151;margin-bottom:.25rem;">Photo actuelle</p>
                        <p style="font-size:.75rem;color:#64748b;">Uploadez une nouvelle photo pour la remplacer.</p>
                    </div>
                </div>
                <?php else: ?>
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.85rem;padding:1rem;background:#f8fafc;border-radius:10px;border:2px dashed #e2e8f0;">
                    <?= icon('camera',32,'','color:#94a3b8;') ?>
                    <div>
                        <p style="font-size:.82rem;font-weight:600;color:#94a3b8;">Aucune photo de boutique</p>
                        <p style="font-size:.75rem;color:#94a3b8;">Ajoutez une photo qui apparaîtra sur la page d'accueil.</p>
                    </div>
                </div>
                <?php endif; ?>
                <input type="file" name="photo_boutique" accept=".jpg,.jpeg,.png,.webp"
                       style="padding:.5rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.85rem;width:100%;">
                <span class="field-hint">JPG, PNG, WEBP — Format paysage recommandé (ex: 800×500px). Max 5 MB.</span>
            </div>
        </div>

        <p class="section-label" style="padding-top:1rem;border-top:1px solid #f1f5f9;">Adresse & Localisation</p>
        <div class="form-grid" style="margin-bottom:1.5rem;">
            <div class="form-group full">
                <label>Adresse de la boutique</label>
                <input type="text" name="adresse" value="<?= htmlspecialchars($adresse) ?>" placeholder="Carrefour Warda, Akwa, Douala, Cameroun">
                <span class="field-hint">Affichée dans la section boutique et le footer du site.</span>
            </div>
            <div class="form-group">
                <label>Latitude GPS</label>
                <input type="text" name="map_lat" value="<?= htmlspecialchars($mapLat) ?>" placeholder="4.0511">
                <span class="field-hint">Ex: 4.0511 (Akwa Palace Douala)</span>
            </div>
            <div class="form-group">
                <label>Longitude GPS</label>
                <input type="text" name="map_lng" value="<?= htmlspecialchars($mapLng) ?>" placeholder="9.7035">
                <span class="field-hint">Ex: 9.7035 (Akwa Palace Douala)</span>
            </div>
        </div>

        <!-- Aperçu carte -->
        <p class="section-label" style="padding-top:1rem;border-top:1px solid #f1f5f9;">Aperçu de la carte</p>
        <div style="border-radius:10px;overflow:hidden;border:1px solid #e2e8f0;height:220px;">
            <iframe
                src="https://maps.google.com/maps?q=<?= urlencode($mapLat) ?>,<?= urlencode($mapLng) ?>&z=16&output=embed"
                width="100%" height="220" style="border:0;display:block;" allowfullscreen loading="lazy"
                title="Localisation boutique">
            </iframe>
        </div>
        <p style="font-size:.75rem;color:#94a3b8;margin-top:.5rem;">
            Coordonnées actuelles : <?= htmlspecialchars($mapLat) ?>, <?= htmlspecialchars($mapLng) ?> —
            <a href="https://www.google.com/maps?q=<?= urlencode($mapLat) ?>,<?= urlencode($mapLng) ?>" target="_blank" style="color:#1a56db;">Voir sur Google Maps</a>
        </p>
    </div>
</div>

<div style="margin-bottom:2rem;">
    <button type="submit" class="btn btn-primary btn-lg"><?= icon('check',16) ?> Enregistrer tous les paramètres</button>
</div>
</form>

<!-- Infos système -->
<div class="card">
    <div class="card-header"><h2><?= icon('chart',16) ?> Informations système</h2></div>
    <div class="table-wrap">
        <table>
            <tbody>
                <tr><td style="font-weight:600;width:180px;background:#f8fafc;padding:.75rem 1.25rem;">PHP Version</td><td style="padding:.75rem 1.25rem;"><?= phpversion() ?></td></tr>
                <tr><td style="font-weight:600;background:#f8fafc;padding:.75rem 1.25rem;">URL du site</td><td style="padding:.75rem 1.25rem;"><a href="http://<?= $_SERVER['HTTP_HOST'].$baseUrl ?>/" target="_blank" style="color:#1a56db;">http://<?= $_SERVER['HTTP_HOST'].$baseUrl ?>/</a></td></tr>
                <tr><td style="font-weight:600;background:#f8fafc;padding:.75rem 1.25rem;">Dossier racine</td><td style="padding:.75rem 1.25rem;font-size:.82rem;color:#64748b;"><?= dirname(__DIR__) ?></td></tr>
                <tr><td style="font-weight:600;background:#f8fafc;padding:.75rem 1.25rem;">Config site</td><td style="padding:.75rem 1.25rem;font-size:.82rem;color:#64748b;"><?= $siteConfigFile ?></td></tr>
            </tbody>
        </table>
    </div>
</div>

<style>
.section-label {
    font-size:.72rem;font-weight:700;color:#94a3b8;
    text-transform:uppercase;letter-spacing:.08em;
    margin-bottom:.85rem;
}
</style>

<?php include 'layout_end.php'; ?>
