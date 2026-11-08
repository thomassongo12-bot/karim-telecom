<?php
if (!function_exists('svg')) require_once __DIR__ . '/icons.php';
$_B = defined('BASE_URL') ? BASE_URL : '';
$_phone = function_exists('getSitePhone') ? getSitePhone() : '+237 600 000 000';
$_tel   = function_exists('telLink') ? telLink() : 'tel:+237600000000';
?>
<section class="cta-whatsapp">
    <div class="container">
        <h2>Une question ? Écrivez-nous sur WhatsApp</h2>
        <p>Notre équipe répond en quelques minutes, 7j/7.</p>
        <a href="<?= waLink('Bonjour Karim Telecom, je voudrais des informations.') ?>"
           target="_blank" rel="noopener" class="btn btn-lg"
           style="background:#fff;color:#16a34a;font-weight:800;">
            <?= svg('whatsapp', 18) ?> Démarrer sur WhatsApp
        </a>
        <p class="phone"><?= htmlspecialchars($_phone) ?> — Réponse rapide garantie</p>
    </div>
</section>

<footer>
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-logo"><?= svg('phone', 18) ?> Karim Telecom</div>
                <p class="footer-tagline">Votre boutique de confiance pour acheter des téléphones à Douala. Produits 100% originaux, garantie officielle.</p>
                <div class="footer-social">
                    <a href="#" title="Facebook"><?= svg('facebook', 18) ?></a>
                    <a href="#" title="Instagram"><?= svg('instagram', 18) ?></a>
                    <a href="<?= waLink() ?>" target="_blank" rel="noopener" title="WhatsApp"><?= svg('whatsapp', 18) ?></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="<?= $_B ?>/catalogue.php"><?= svg('phone', 14) ?> Téléphones</a></li>
                    <li><a href="<?= $_B ?>/blog.php"><?= svg('article', 14) ?> Blog & Guides</a></li>
                    <li><a href="<?= $_B ?>/a-propos.php"><?= svg('info', 14) ?> À propos</a></li>
                    <li><a href="<?= $_B ?>/contact.php"><?= svg('phone-call', 14) ?> Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Nos villes</h4>
                <ul>
                    <li><a href="<?= $_B ?>/ville.php?v=douala"><?= svg('map-pin', 14) ?> Douala</a></li>
                    <li><a href="<?= $_B ?>/ville.php?v=yaounde"><?= svg('map-pin', 14) ?> Yaoundé</a></li>
                    <li><a href="<?= $_B ?>/ville.php?v=bafoussam"><?= svg('map-pin', 14) ?> Bafoussam</a></li>
                    <li><a href="<?= $_B ?>/ville.php?v=bamenda"><?= svg('map-pin', 14) ?> Bamenda</a></li>
                    <li><a href="<?= $_B ?>/ville.php?v=garoua"><?= svg('map-pin', 14) ?> Garoua</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Nous trouver</h4>
                <ul class="footer-contact">
                    <li><span class="footer-contact-icon"><?= svg('map-pin', 16) ?></span><span>Carrefour Warda, Akwa<br>Douala, Cameroun</span></li>
                    <li><span class="footer-contact-icon"><?= svg('phone-call', 16) ?></span><a href="<?= $_tel ?>" style="color:inherit;"><?= htmlspecialchars($_phone) ?></a></li>
                    <li><span class="footer-contact-icon"><?= svg('clock', 16) ?></span><span>Lun–Sam 8h–20h | Dim 10h–18h</span></li>
                    <li><span class="footer-contact-icon"><?= svg('whatsapp', 16) ?></span><a href="<?= waLink() ?>" target="_blank" rel="noopener" style="color:#4ade80;">WhatsApp 7j/7</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© <?= date('Y') ?> Karim Telecom. Tous droits réservés.</span>
            <span><?= svg('flag', 14) ?> Fièrement camerounais</span>
        </div>
    </div>
</footer>

<?php include __DIR__ . '/whatsapp.php'; ?>
<script src="<?= $_B ?>/assets/js/main.js"></script>
</body>
</html>
