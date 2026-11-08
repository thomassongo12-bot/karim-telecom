<?php
require_once 'config/db.php';
$B   = BASE_URL;
$pdo = getDB();
$pageTitle = 'Contact — Karim Telecom Douala';
$pageDesc  = 'Contactez Karim Telecom. Boutique Carrefour Warda Akwa Douala. ' . getSitePhone() . '. WhatsApp 7j/7.';

$_sc      = include __DIR__ . '/config/site.php';
$_adresse = $_sc['adresse'] ?? 'Carrefour Warda, Akwa, Douala, Cameroun';
$_mapLat  = $_sc['map_lat'] ?? '4.0511';
$_mapLng  = $_sc['map_lng'] ?? '9.7035';
$_mapSrc  = 'https://maps.google.com/maps?q=' . urlencode($_mapLat) . ',' . urlencode($_mapLng) . '&z=16&output=embed';

include 'includes/header.php';
require_once 'includes/icons.php';
?>

<div class="page-hero">
    <div class="container">
        <h1>Contactez Karim Telecom</h1>
        <p>WhatsApp, téléphone ou venez en boutique — nous répondons rapidement.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="contact-grid">
            <div>
                <h2 style="font-size:1.5rem;font-weight:800;margin-bottom:1rem;">Nos coordonnées</h2>
                <div class="contact-item">
                    <div class="contact-icon"><?= svg('map-pin',18) ?></div>
                    <div><strong>Adresse boutique</strong><span><?= htmlspecialchars($_adresse) ?></span></div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon"><?= svg('phone-call',18) ?></div>
                    <div><strong>Téléphone</strong><span><a href="<?= telLink() ?>" style="color:#1a56db;font-weight:600;"><?= htmlspecialchars(getSitePhone()) ?></a></span></div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon" style="background:#f0fdf4;color:#16a34a;"><?= svg('whatsapp',18) ?></div>
                    <div><strong>WhatsApp</strong><span><a href="<?= waLink() ?>" target="_blank" rel="noopener" style="color:#16a34a;font-weight:600;"><?= htmlspecialchars(getSitePhone()) ?></a></span></div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon"><?= svg('clock',18) ?></div>
                    <div>
                        <strong>Horaires</strong>
                        <table class="hours-table">
                            <tr><td>Lundi – Vendredi</td><td>8h00 – 20h00</td></tr>
                            <tr><td>Samedi</td><td>8h00 – 20h00</td></tr>
                            <tr><td>Dimanche</td><td>10h00 – 18h00</td></tr>
                        </table>
                    </div>
                </div>
                <div style="margin-top:1.5rem;">
                    <a href="<?= waLink('Bonjour Karim Telecom, je voudrais vous contacter.') ?>" target="_blank" rel="noopener" class="btn btn-whatsapp btn-lg"><?= svg('whatsapp',18) ?> Écrire sur WhatsApp maintenant</a>
                </div>
                <div class="map-embed" style="margin-top:1.5rem;">
                    <iframe src="<?= htmlspecialchars($_mapSrc) ?>" allowfullscreen loading="lazy" title="Karim Telecom — <?= htmlspecialchars($_adresse) ?>"></iframe>
                </div>
            </div>

            <div>
                <div style="background:#f9fafb;border-radius:12px;padding:1.5rem;">
                    <h2 style="margin-bottom:1rem;">Envoyer un message</h2>
                    <p style="font-size:.85rem;color:#64748b;margin-bottom:1.25rem;">Remplissez et cliquez "Envoyer sur WhatsApp" — nous répondons en quelques minutes.</p>
                    <form onsubmit="sendWhatsApp(event)">
                        <div style="display:flex;flex-direction:column;gap:1rem;">
                            <div>
                                <label style="font-size:.85rem;font-weight:600;display:block;margin-bottom:.4rem;">Votre nom</label>
                                <input type="text" id="c-nom" placeholder="Ex: Armand Tchamba" style="width:100%;padding:.65rem 1rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.9rem;">
                            </div>
                            <div>
                                <label style="font-size:.85rem;font-weight:600;display:block;margin-bottom:.4rem;">Téléphone recherché</label>
                                <input type="text" id="c-produit" placeholder="Ex: iPhone 17 Pro Max..." style="width:100%;padding:.65rem 1rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.9rem;">
                            </div>
                            <div>
                                <label style="font-size:.85rem;font-weight:600;display:block;margin-bottom:.4rem;">Message</label>
                                <textarea id="c-msg" rows="4" placeholder="Votre question ou besoin..." style="width:100%;padding:.65rem 1rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.9rem;resize:vertical;"></textarea>
                            </div>
                            <button type="submit" class="btn btn-whatsapp btn-full btn-lg"><?= svg('whatsapp',18) ?> Envoyer sur WhatsApp</button>
                        </div>
                    </form>
                </div>

                <div style="margin-top:1.5rem;background:#eff6ff;border-radius:12px;padding:1.25rem;">
                    <h3 style="font-size:1rem;font-weight:700;margin-bottom:.75rem;display:flex;align-items:center;gap:.4rem;"><?= svg('map-pin',18) ?> Livraison dans votre ville</h3>
                    <?php $vs = $pdo->query("SELECT * FROM villes")->fetchAll(); foreach ($vs as $v): ?>
                    <a href="<?= $B ?>/ville.php?v=<?= urlencode($v['slug']) ?>" style="display:flex;align-items:center;gap:.6rem;font-size:.85rem;color:#1a56db;font-weight:500;margin-bottom:.5rem;">
                        <?= svg('map-pin',13) ?> <?= htmlspecialchars($v['nom']) ?>
                        <span style="font-size:.78rem;color:#64748b;font-weight:400;">— <?= htmlspecialchars($v['delai_livraison']) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function sendWhatsApp(e) {
    e.preventDefault();
    var nom = document.getElementById('c-nom').value || 'Client';
    var produit = document.getElementById('c-produit').value || '';
    var msg = document.getElementById('c-msg').value || '';
    var text = 'Bonjour Karim Telecom,\n\nNom : ' + nom;
    if (produit) text += '\nTéléphone recherché : ' + produit;
    if (msg) text += '\n\nMessage : ' + msg;
    window.open('https://wa.me/<?= WA_NUMBER ?>?text=' + encodeURIComponent(text), '_blank');
}
</script>
<?php include 'includes/footer.php'; ?>
