<?php
$pdo = getDB();
$adminPage = basename($_SERVER['PHP_SELF'], '.php');

// SVG icons helper
function icon($name, $size = 18) {
    $icons = [
        'dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        'phone'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>',
        'plus'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>',
        'star'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
        'article'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'pencil'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
        'location'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'settings'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'globe'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'logout'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>',
        'trash'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>',
        'eye'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
        'check'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        'x'         => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        'menu'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>',
        'chevron'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>',
        'users'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
        'tag'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>',
        'chart'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
    ];
    $d = $icons[$name] ?? $icons['check'];
    return '<svg xmlns="http://www.w3.org/2000/svg" width="'.$size.'" height="'.$size.'" fill="none" viewBox="0 0 24 24" stroke="currentColor">'.$d.'</svg>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($adminTitle ?? 'Admin') ?> — Karim Telecom</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { font-size: 15px; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f1f5f9; color: #0f172a; display: flex; min-height: 100vh; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            width: 256px; background: #0f172a; color: #94a3b8;
            display: flex; flex-direction: column;
            position: fixed; inset: 0 auto 0 0; z-index: 200;
            transition: transform .3s cubic-bezier(.4,0,.2,1);
        }
        .sidebar-logo {
            padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,.06);
            display: flex; align-items: center; gap: .75rem;
        }
        .sidebar-logo-icon { width: 36px; height: 36px; background: #1a56db; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; }
        .sidebar-logo-text .name { font-size: .95rem; font-weight: 700; color: #fff; }
        .sidebar-logo-text .sub  { font-size: .72rem; color: #475569; }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: .75rem 0; }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }

        .nav-section { font-size: .68rem; text-transform: uppercase; letter-spacing: .1em; color: #475569; font-weight: 600; padding: 1rem 1.5rem .35rem; }
        .nav-link {
            display: flex; align-items: center; gap: .75rem;
            padding: .6rem 1.5rem; font-size: .875rem; color: #94a3b8;
            text-decoration: none; border-left: 3px solid transparent;
            transition: all .15s;
        }
        .nav-link:hover { background: rgba(255,255,255,.04); color: #e2e8f0; }
        .nav-link.active { background: rgba(26,86,219,.15); color: #fff; border-left-color: #1a56db; }
        .nav-link svg { flex-shrink: 0; opacity: .7; }
        .nav-link.active svg { opacity: 1; }

        .sidebar-footer { padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,.06); display: flex; flex-direction: column; gap: .5rem; }
        .sidebar-footer a { display: flex; align-items: center; gap: .6rem; font-size: .82rem; color: #64748b; text-decoration: none; padding: .4rem 0; transition: color .15s; }
        .sidebar-footer a:hover { color: #e2e8f0; }

        /* ─── OVERLAY mobile ─── */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.55); z-index: 199; }
        .sidebar-overlay.open { display: block; }

        /* ─── MAIN ─── */
        .main { margin-left: 256px; flex: 1; display: flex; flex-direction: column; min-width: 0; }

        /* ─── TOP BAR ─── */
        .topbar {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: .85rem 1.5rem; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 100;
        }
        .topbar-left { display: flex; align-items: center; gap: .85rem; }
        .hamburger-btn { display: none; background: none; border: none; cursor: pointer; padding: .3rem; color: #64748b; border-radius: 6px; }
        .hamburger-btn:hover { background: #f1f5f9; color: #0f172a; }
        .topbar h1 { font-size: 1rem; font-weight: 700; color: #0f172a; }
        .topbar-right { display: flex; align-items: center; gap: .75rem; }
        .admin-badge { background: #eff6ff; color: #1a56db; font-size: .75rem; font-weight: 600; padding: .3rem .75rem; border-radius: 20px; display: flex; align-items: center; gap: .4rem; }

        /* ─── CONTENT ─── */
        .content { padding: 1.5rem; flex: 1; }

        /* ─── STAT CARDS ─── */
        .stat-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .stat-card { background: #fff; border-radius: 12px; padding: 1.25rem; border: 1px solid #e2e8f0; }
        .stat-card-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: .85rem; }
        .stat-card-icon.blue   { background: #eff6ff; color: #1a56db; }
        .stat-card-icon.green  { background: #f0fdf4; color: #16a34a; }
        .stat-card-icon.amber  { background: #fffbeb; color: #d97706; }
        .stat-card-icon.purple { background: #faf5ff; color: #7c3aed; }
        .stat-card-icon.rose   { background: #fff1f2; color: #e11d48; }
        .stat-card .s-label { font-size: .75rem; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: .04em; }
        .stat-card .s-value { font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-top: .2rem; line-height: 1; }

        /* ─── CARD ─── */
        .card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 1.5rem; }
        .card-header { padding: .9rem 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; gap: .75rem; }
        .card-header h2 { font-size: .9rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: .5rem; }
        .card-body { padding: 1.25rem; }

        /* ─── TABLE ─── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .85rem; }
        thead th { text-align: left; padding: .6rem 1rem; background: #f8fafc; font-size: .72rem; text-transform: uppercase; letter-spacing: .05em; color: #64748b; font-weight: 600; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
        tbody td { padding: .65rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #fafbfc; }

        /* ─── FORMS ─── */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-group { display: flex; flex-direction: column; gap: .4rem; }
        .form-group.full { grid-column: 1 / -1; }
        .form-group.span3 { grid-column: 1 / -1; }
        label { font-size: .78rem; font-weight: 600; color: #374151; }
        input[type=text], input[type=number], input[type=url], input[type=date],
        input[type=password], select, textarea {
            padding: .6rem .85rem; border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: .88rem; font-family: inherit; width: 100%; outline: none;
            background: #fff; transition: border-color .15s, box-shadow .15s;
            color: #0f172a;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #1a56db;
            box-shadow: 0 0 0 3px rgba(26,86,219,.1);
        }
        textarea { resize: vertical; min-height: 90px; }
        .field-hint { font-size: .72rem; color: #94a3b8; margin-top: .15rem; }

        /* ─── BUTTONS ─── */
        .btn { display: inline-flex; align-items: center; gap: .45rem; padding: .5rem 1rem; border-radius: 8px; font-size: .85rem; font-weight: 600; cursor: pointer; border: 1.5px solid transparent; text-decoration: none; white-space: nowrap; transition: all .15s; }
        .btn svg { flex-shrink: 0; }
        .btn-primary   { background: #1a56db; color: #fff; border-color: #1a56db; }
        .btn-primary:hover { background: #1e40af; border-color: #1e40af; }
        .btn-success   { background: #16a34a; color: #fff; border-color: #16a34a; }
        .btn-success:hover { background: #15803d; }
        .btn-danger    { background: #fff; color: #dc2626; border-color: #fecaca; }
        .btn-danger:hover { background: #fee2e2; }
        .btn-ghost     { background: transparent; color: #64748b; border-color: #e2e8f0; }
        .btn-ghost:hover { background: #f8fafc; color: #0f172a; }
        .btn-sm        { padding: .3rem .65rem; font-size: .78rem; border-radius: 6px; }
        .btn-lg        { padding: .7rem 1.4rem; font-size: .95rem; }
        .btn-full      { width: 100%; justify-content: center; }

        /* ─── ALERTS ─── */
        .alert { padding: .85rem 1.1rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: .85rem; display: flex; align-items: center; gap: .65rem; }
        .alert svg { flex-shrink: 0; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        /* ─── BADGES ─── */
        .badge { display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .6rem; border-radius: 20px; font-size: .72rem; font-weight: 600; }
        .badge-green  { background: #f0fdf4; color: #166534; }
        .badge-red    { background: #fef2f2; color: #991b1b; }
        .badge-blue   { background: #eff6ff; color: #1e40af; }
        .badge-orange { background: #fff7ed; color: #c2410c; }
        .badge-gray   { background: #f8fafc; color: #475569; }

        /* ─── TOGGLE ─── */
        .toggle { position: relative; display: inline-block; width: 38px; height: 21px; flex-shrink: 0; }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-s { position: absolute; cursor: pointer; inset: 0; background: #e2e8f0; border-radius: 21px; transition: .2s; }
        .toggle-s:before { content: ''; position: absolute; width: 15px; height: 15px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: .2s; box-shadow: 0 1px 3px rgba(0,0,0,.15); }
        input:checked + .toggle-s { background: #16a34a; }
        input:checked + .toggle-s:before { transform: translateX(17px); }

        /* ─── QUICK ACTIONS ─── */
        .quick-actions { display: flex; gap: .75rem; flex-wrap: wrap; margin-bottom: 1.5rem; }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .hamburger-btn { display: flex; }
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full, .form-group.span3 { grid-column: 1; }
        }
        @media (max-width: 640px) {
            .content { padding: 1rem; }
            .topbar { padding: .75rem 1rem; }
            .stat-cards { grid-template-columns: 1fr 1fr; }
            .quick-actions .btn span { display: none; }
        }
    </style>
</head>
<body>

<!-- Overlay mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <?= icon('phone', 20) ?>
        </div>
        <div class="sidebar-logo-text">
            <div class="name">Karim Telecom</div>
            <div class="sub">Administration</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Général</div>
        <a href="dashboard.php" class="nav-link <?= $adminPage==='dashboard'?'active':'' ?>">
            <?= icon('dashboard') ?> <span>Tableau de bord</span>
        </a>

        <div class="nav-section">Catalogue</div>
        <a href="produits.php" class="nav-link <?= $adminPage==='produits'?'active':'' ?>">
            <?= icon('phone') ?> <span>Produits</span>
        </a>
        <a href="produit_form.php" class="nav-link <?= $adminPage==='produit_form'?'active':'' ?>">
            <?= icon('plus') ?> <span>Ajouter un produit</span>
        </a>
        <a href="categories.php" class="nav-link <?= $adminPage==='categories'?'active':'' ?>">
            <?= icon('tag') ?> <span>Catégories</span>
        </a>

        <div class="nav-section">Contenu</div>
        <a href="avis.php" class="nav-link <?= $adminPage==='avis'?'active':'' ?>">
            <?= icon('star') ?> <span>Avis clients</span>
        </a>
        <a href="articles.php" class="nav-link <?= $adminPage==='articles'?'active':'' ?>">
            <?= icon('article') ?> <span>Articles blog</span>
        </a>
        <a href="article_form.php" class="nav-link <?= $adminPage==='article_form'?'active':'' ?>">
            <?= icon('pencil') ?> <span>Nouvel article</span>
        </a>

        <div class="nav-section">Configuration</div>
        <a href="villes.php" class="nav-link <?= $adminPage==='villes'?'active':'' ?>">
            <?= icon('location') ?> <span>Villes livraison</span>
        </a>
        <a href="parametres.php" class="nav-link <?= $adminPage==='parametres'?'active':'' ?>">
            <?= icon('settings') ?> <span>Paramètres</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/index.php" target="_blank">
            <?= icon('globe', 16) ?> <span>Voir le site</span>
        </a>
        <a href="logout.php">
            <?= icon('logout', 16) ?> <span>Déconnexion</span>
        </a>
    </div>
</aside>

<!-- MAIN -->
<div class="main">
    <div class="topbar">
        <div class="topbar-left">
            <button class="hamburger-btn" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Menu">
                <?= icon('menu', 22) ?>
            </button>
            <h1><?= htmlspecialchars($adminTitle ?? 'Dashboard') ?></h1>
        </div>
        <div class="topbar-right">
            <span class="admin-badge">
                <?= icon('users', 14) ?> Admin
            </span>
        </div>
    </div>
    <div class="content">

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
}
// Fermer sidebar si on clique un lien en mobile
document.querySelectorAll && document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.nav-link').forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 1024) closeSidebar();
        });
    });
});
</script>
