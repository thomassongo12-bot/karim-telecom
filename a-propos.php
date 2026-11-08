<?php
require_once 'config/db.php';
$B = BASE_URL;
$pageTitle = 'À propos — Karim Telecom Douala';
$pageDesc  = 'Karim Telecom, votre boutique de téléphones à Douala depuis 2022. Boutique physique Akwa, produits originaux, garantie officielle.';
include 'includes/header.php';
require_once 'includes/icons.php';
?>

<div class="page-hero">
    <div class="container">
        <h1>À propos de Karim Telecom</h1>
        <p>Votre boutique de confiance à Douala depuis 2022</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="about-grid">
            <div class="store-img-placeholder">
                <?= svg('store',52) ?>
                <p>La boutique Karim Telecom</p>
                <small>Carrefour Warda, Akwa, Douala</small>
            </div>
            <div>
                <h2 style="font-size:1.6rem;font-weight:800;margin-bottom:1rem;">Notre histoire</h2>
                <p style="color:#374151;margin-bottom:1rem;line-height:1.8;">Karim Telecom est né en 2022 d'une constatation simple : les Camerounais méritaient un accès à des téléphones 100% originaux avec un service digne de ce nom.</p>
                <p style="color:#374151;margin-bottom:1rem;line-height:1.8;">Nous avons ouvert notre boutique physique à Akwa, Douala avec une promesse : <strong>vendre uniquement des produits originaux, avec garantie, facture et un service WhatsApp disponible 7j/7.</strong></p>
                <p style="color:#374151;margin-bottom:1.5rem;line-height:1.8;">Aujourd'hui, plus de 500 clients nous font confiance à Douala et partout au Cameroun.</p>
                <a href="<?= waLink('Bonjour Karim Telecom, je voudrais en savoir plus.') ?>" target="_blank" rel="noopener" class="btn btn-whatsapp"><?= svg('whatsapp',18) ?> Nous contacter sur WhatsApp</a>
            </div>
        </div>
    </div>
</section>

<div class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item"><strong>500+</strong><span>Clients satisfaits</span></div>
            <div class="stat-item"><strong>1 200+</strong><span>Téléphones vendus</span></div>
            <div class="stat-item"><strong>4.9/5</strong><span>Note moyenne</span></div>
            <div class="stat-item"><strong>3 ans</strong><span>D'expérience</span></div>
        </div>
    </div>
</div>

<section class="section section-alt">
    <div class="container">
        <h2 class="section-title text-center">Nos valeurs</h2>
        <p class="section-subtitle text-center">Ce qui guide chacune de nos décisions</p>
        <div class="about-values">
            <div class="value-card">
                <div class="value-icon"><?= svg('lock',22) ?></div>
                <h3>Authenticité</h3>
                <p>Chaque produit est 100% original, en boîte scellée, avec numéro IMEI vérifiable sur le site du constructeur.</p>
            </div>
            <div class="value-card">
                <div class="value-icon"><?= svg('users',22) ?></div>
                <h3>Confiance</h3>
                <p>Paiement à la livraison, garantie officielle, facture fournie. Vous ne payez que si vous êtes satisfait.</p>
            </div>
            <div class="value-card">
                <div class="value-icon"><?= svg('headset',22) ?></div>
                <h3>Service humain</h3>
                <p>Un vrai humain répond sur WhatsApp 7j/7. Des réponses rapides et des conseils honnêtes.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:800px;">
        <h2 class="section-title">Pourquoi nous choisir ?</h2>
        <div style="display:flex;flex-direction:column;gap:.85rem;margin-top:1.5rem;">
            <?php
            $raisons = [
                ['store','Boutique physique à Akwa','Venez tester les produits avant d\'acheter.'],
                ['box','Produits 100% originaux','Boîte scellée originale. Numéro IMEI vérifiable.'],
                ['cash','Paiement à la livraison','Vous payez uniquement quand vous êtes satisfait.'],
                ['shield','Garantie officielle','Tous nos produits sont couverts par la garantie constructeur.'],
                ['receipt','Facture officielle','Fournie pour chaque achat.'],
                ['truck','Livraison nationale','Douala le jour même, toutes les grandes villes du Cameroun.'],
            ];
            foreach ($raisons as $r): ?>
            <div style="display:flex;gap:1rem;align-items:flex-start;padding:1rem;background:#f9fafb;border-radius:10px;">
                <span style="color:#1a56db;flex-shrink:0;margin-top:.1rem;"><?= svg($r[0],22) ?></span>
                <div>
                    <strong style="display:block;margin-bottom:.25rem;"><?= $r[1] ?></strong>
                    <span style="font-size:.85rem;color:#64748b;"><?= $r[2] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
