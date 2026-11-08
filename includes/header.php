<?php
require_once __DIR__ . '/icons.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
function activeClass($page) {
    global $currentPage;
    return $currentPage === $page ? ' active' : '';
}
$_B = defined('BASE_URL') ? BASE_URL : '';

// Logo, favicon, nom depuis config/site.php
$_siteConfig = [];
$_siteFile   = __DIR__ . '/../config/site.php';
if (file_exists($_siteFile)) $_siteConfig = include $_siteFile;
$_logo    = $_siteConfig['logo']    ?? '';
$_favicon = $_siteConfig['favicon'] ?? '';
$_nomSite = $_siteConfig['nom_site'] ?? 'Karim Telecom';
$_slogan  = $_siteConfig['slogan']  ?? 'Téléphones & Accessoires';

// Valeur de recherche courante
$_searchQ = htmlspecialchars($_GET['q'] ?? '');

// Numéro de téléphone dynamique depuis db.php
$_phone = function_exists('getSitePhone') ? getSitePhone() : '+237 600 000 000';
$_tel   = function_exists('telLink') ? telLink() : 'tel:+237600000000';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $_nomSite . ' — Téléphones & Accessoires à Douala') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc ?? 'Achetez vos téléphones iPhone, Samsung à Douala. Paiement à la livraison, garantie officielle. ' . getSitePhone()) ?>">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Karim Telecom">
    <meta name="geo.region" content="CM-LT">
    <meta name="geo.placename" content="Douala, Cameroun">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= htmlspecialchars($_nomSite) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? $_nomSite) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDesc ?? 'Téléphones & Accessoires à Douala') ?>">
    <meta property="og:locale" content="fr_CM">
    <?php if (!empty($pageOgImage)): ?>
    <meta property="og:image" content="<?= htmlspecialchars($pageOgImage) ?>">
    <?php endif; ?>
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle ?? $_nomSite) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDesc ?? '') ?>">
    <!-- Canonical -->
    <link rel="canonical" href="https://<?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'karimtelecom.cm') ?><?= htmlspecialchars(strtok($_SERVER['REQUEST_URI'] ?? '/', '?')) ?>">
    <?php if (!empty($pageJsonLd)): ?>
    <script type="application/ld+json"><?= $pageJsonLd ?></script>
    <?php endif; ?>
    <?php if ($_favicon): ?>
    <link rel="icon" href="<?= $_B ?>/uploads/site/<?= htmlspecialchars($_favicon) ?>">
    <?php else: ?>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%231a56db'><path d='M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'/></svg>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= $_B ?>/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
    <div class="topbar-inner">
        <span class="topbar-item">
            <?= svg('phone-call', 14) ?>
            <a href="<?= $_tel ?>"><?= htmlspecialchars($_phone) ?></a>
        </span>
        <span class="topbar-sep">|</span>
        <span class="topbar-item"><?= svg('truck', 14) ?> Livraison Douala le jour même</span>
        <span class="topbar-sep">|</span>
        <span class="topbar-item"><?= svg('cash', 14) ?> Paiement à la livraison</span>
    </div>
</div>

<!-- HEADER -->
<header>
    <div class="header-inner">

        <!-- LOGO -->
        <a href="<?= $_B ?>/index.php" class="logo" style="flex-shrink:0;">
            <?php if ($_logo): ?>
            <img src="<?= $_B ?>/uploads/site/<?= htmlspecialchars($_logo) ?>"
                 alt="<?= htmlspecialchars($_nomSite) ?>"
                 class="logo-img-custom">
            <?php else: ?>
            <div class="logo-icon"><?= svg('phone', 20) ?></div>
            <?php endif; ?>
            <div class="logo-text">
                <span class="logo-name"><?= htmlspecialchars($_nomSite) ?></span>
                <span class="logo-sub"><?= htmlspecialchars($_slogan) ?></span>
            </div>
        </a>

        <!-- NAV DESKTOP -->
        <nav>
            <a href="<?= $_B ?>/index.php"     class="nav-item<?= activeClass('index') ?>">Accueil</a>
            <a href="<?= $_B ?>/catalogue.php" class="nav-item<?= activeClass('catalogue') ?>">Shop</a>
            <a href="<?= $_B ?>/blog.php"      class="nav-item<?= activeClass('blog') ?>">Blog</a>
            <a href="<?= $_B ?>/a-propos.php"  class="nav-item<?= activeClass('a-propos') ?>">À propos</a>
            <a href="<?= $_B ?>/contact.php"   class="nav-item<?= activeClass('contact') ?>">Contact</a>
        </nav>

        <!-- SEARCH DESKTOP -->
        <form class="search-form" action="<?= $_B ?>/catalogue.php" method="GET" role="search">
            <input type="text" name="q" value="<?= $_searchQ ?>" placeholder="Rechercher un iPhone, Samsung..." aria-label="Recherche">
            <button type="submit" aria-label="Lancer la recherche"><?= svg('search', 18) ?></button>
        </form>

        <!-- CTA DESKTOP -->
        <div class="header-cta">
            <a href="<?= $_tel ?>" class="header-phone">
                <?= svg('phone-call', 16) ?> <?= htmlspecialchars($_phone) ?>
            </a>
            <a href="<?= waLink('Bonjour Karim Telecom, je voudrais des informations.') ?>"
               target="_blank" rel="noopener" class="btn btn-whatsapp btn-sm">
                <?= svg('whatsapp', 16) ?> WhatsApp
            </a>
        </div>

        <!-- HAMBURGER -->
        <button class="hamburger" id="hamburger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- SEARCH MOBILE -->
    <div class="search-mobile" id="searchMobile" style="display:none;">
        <form class="search-form" action="<?= $_B ?>/catalogue.php" method="GET" role="search">
            <input type="text" name="q" value="<?= $_searchQ ?>" placeholder="Rechercher un iPhone, Samsung..." aria-label="Recherche">
            <button type="submit" aria-label="Rechercher"><?= svg('search', 18) ?></button>
        </form>
    </div>

    <!-- MOBILE NAV -->
    <div class="mobile-nav" id="mobile-nav">
        <a href="<?= $_B ?>/index.php">Accueil</a>
        <a href="<?= $_B ?>/catalogue.php">Shop</a>
        <a href="<?= $_B ?>/blog.php">Blog</a>
        <a href="<?= $_B ?>/a-propos.php">À propos</a>
        <a href="<?= $_B ?>/contact.php">Contact</a>
        <a href="<?= waLink() ?>" target="_blank" rel="noopener"
           style="color:#16a34a;font-weight:700;display:flex;align-items:center;gap:.5rem;">
            <?= svg('whatsapp', 18) ?> WhatsApp — <?= htmlspecialchars($_phone) ?>
        </a>
    </div>
</header>

<style>
/* Contraindre le logo pour ne jamais casser la navbar */
.logo { max-width: 220px; min-width: 0; }
.logo-img-custom { height: 38px; width: auto; max-width: 140px; object-fit: contain; }
.logo-name { font-size: clamp(.85rem, 1.5vw, 1.1rem); white-space: nowrap; }
.logo-sub  { display: none; }
@media (min-width: 1100px) { .logo-sub { display: block; } }

/* Masquer la search desktop sur mobile, afficher la mobile */
@media (max-width: 767px) {
    .header-inner .search-form { display: none; }
}
@media (min-width: 768px) {
    #searchMobile { display: none !important; }
    .header-inner .search-form { display: flex; }
}
</style>
