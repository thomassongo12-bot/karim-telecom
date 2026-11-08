<?php
session_start();
require_once '../config/db.php';

// ── Identifiants admin (mot de passe en hash bcrypt) ──
// Pour changer le mot de passe, générez un nouveau hash :
// php -r "echo password_hash('votre_mot_de_passe', PASSWORD_BCRYPT);"
define('ADMIN_USER', 'karim');
define('ADMIN_PASS_HASH', '$2y$12$8cVksrfQ3MZDn1SDTKRFSujG.fisoBujcYLxIgsdZl7Sz1d9vm5KK');
// Mot de passe par défaut : karim2025
// IMPORTANT : changez-le via Paramètres → Mot de passe admin

// ── Protection anti-brute-force (session) ──
if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;
if (!isset($_SESSION['lockout_until']))  $_SESSION['lockout_until']  = 0;

$locked    = time() < $_SESSION['lockout_until'];
$remaining = max(0, $_SESSION['lockout_until'] - time());
$error     = '';
$success   = false;

// ── CSRF token ──
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Déjà connecté
if (!empty($_SESSION['admin_logged'])) {
    header('Location: dashboard.php'); exit;
}

// ── Traitement du formulaire ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

    // Vérif CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Requête invalide. Veuillez réessayer.';
    } elseif ($locked) {
        $error = 'Trop de tentatives. Réessayez dans ' . $remaining . ' seconde(s).';
    } else {
        $user = trim($_POST['username'] ?? '');
        $pass = $_POST['password'] ?? '';

        // Vérification sécurisée
        $userOk = hash_equals(ADMIN_USER, $user);
        // Essayer d'abord le hash bcrypt, sinon compatibilité mot de passe en clair (migration)
        $passOk = password_verify($pass, ADMIN_PASS_HASH);

        if ($userOk && $passOk) {
            // Succès — régénérer session pour éviter la fixation
            session_regenerate_id(true);
            $_SESSION['admin_logged']   = true;
            $_SESSION['admin_user']     = $user;
            $_SESSION['admin_login_at'] = time();
            $_SESSION['login_attempts'] = 0;
            $_SESSION['lockout_until']  = 0;
            // Nouveau CSRF pour la session admin
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('Location: dashboard.php'); exit;
        } else {
            $_SESSION['login_attempts']++;
            // Bloquer après 5 tentatives — 5 min
            if ($_SESSION['login_attempts'] >= 5) {
                $_SESSION['lockout_until'] = time() + 300;
                $error = 'Compte temporairement bloqué (5 tentatives). Réessayez dans 5 minutes.';
            } else {
                $remaining_attempts = 5 - $_SESSION['login_attempts'];
                $error = 'Identifiants incorrects. ' . $remaining_attempts . ' tentative(s) restante(s).';
            }
        }
    }
}

// Forcer HTTPS en production
// if (!isset($_SERVER['HTTPS'])) { header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — Karim Telecom</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1a56db 100%);
            padding: 1rem;
        }

        /* Fond animé */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background: radial-gradient(ellipse at 20% 50%, rgba(26,86,219,.25) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 20%, rgba(99,102,241,.2) 0%, transparent 50%);
            pointer-events: none;
        }

        .login-wrap {
            width: 100%; max-width: 420px;
            position: relative; z-index: 1;
        }

        .login-box {
            background: rgba(255,255,255,.97);
            border-radius: 20px;
            padding: 2.5rem 2.25rem;
            box-shadow: 0 25px 60px rgba(0,0,0,.35), 0 0 0 1px rgba(255,255,255,.1);
        }

        /* Logo */
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #1a56db, #1e3a8a);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            margin-bottom: .85rem;
            box-shadow: 0 8px 24px rgba(26,86,219,.4);
        }
        .login-logo h1 {
            font-size: 1.35rem; font-weight: 800; color: #0f172a; margin-bottom: .25rem;
        }
        .login-logo p {
            font-size: .82rem; color: #64748b; font-weight: 500;
        }
        .login-logo .admin-badge {
            display: inline-flex; align-items: center; gap: .3rem;
            background: #eff6ff; color: #1a56db;
            font-size: .72rem; font-weight: 600;
            padding: .2rem .65rem; border-radius: 50px;
            margin-top: .5rem;
            border: 1px solid #bfdbfe;
        }

        /* Alerte erreur */
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: .75rem 1rem;
            border-radius: 10px;
            font-size: .83rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: .5rem;
        }
        .alert-error svg { flex-shrink: 0; margin-top: .05rem; color: #dc2626; }

        /* Alerte blocage */
        .alert-lock {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            padding: .75rem 1rem;
            border-radius: 10px;
            font-size: .83rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: .5rem;
        }
        .alert-lock svg { flex-shrink: 0; color: #d97706; }

        /* Champs */
        .field {
            margin-bottom: 1.1rem;
        }
        .field label {
            display: flex; align-items: center; gap: .4rem;
            font-size: .8rem; font-weight: 600; color: #374151;
            margin-bottom: .45rem;
        }
        .field label svg { color: #94a3b8; }
        .field-input {
            position: relative;
        }
        .field-input input {
            width: 100%;
            padding: .72rem 1rem .72rem 2.6rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: .9rem;
            font-family: inherit;
            color: #0f172a;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            background: #f9fafb;
        }
        .field-input input:focus {
            border-color: #1a56db;
            box-shadow: 0 0 0 3px rgba(26,86,219,.1);
            background: #fff;
        }
        .field-input .field-icon {
            position: absolute; left: .75rem; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8; pointer-events: none;
            display: flex; align-items: center;
        }
        .field-input input:focus ~ .field-icon { color: #1a56db; }

        /* Toggle password */
        .toggle-pass {
            position: absolute; right: .75rem; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #94a3b8; padding: 0; display: flex; align-items: center;
        }
        .toggle-pass:hover { color: #1a56db; }

        /* Barre de tentatives */
        .attempts-bar {
            height: 3px; background: #f1f5f9; border-radius: 2px;
            margin-bottom: 1.25rem; overflow: hidden;
        }
        .attempts-bar-fill {
            height: 100%; border-radius: 2px;
            transition: width .3s, background .3s;
        }

        /* Bouton */
        .btn-login {
            width: 100%;
            padding: .85rem 1.5rem;
            background: linear-gradient(135deg, #1a56db, #1e3a8a);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: .55rem;
            transition: opacity .15s, transform .1s, box-shadow .15s;
            box-shadow: 0 4px 14px rgba(26,86,219,.35);
            margin-top: .5rem;
        }
        .btn-login:hover  { opacity: .92; box-shadow: 0 6px 20px rgba(26,86,219,.45); }
        .btn-login:active { transform: scale(.98); }
        .btn-login:disabled { opacity: .5; cursor: not-allowed; }

        /* Back link */
        .login-back {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .8rem;
            color: rgba(255,255,255,.7);
        }
        .login-back a {
            color: rgba(255,255,255,.9);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex; align-items: center; gap: .3rem;
        }
        .login-back a:hover { color: #fff; text-decoration: underline; }

        /* Sécurité info */
        .security-note {
            display: flex; align-items: center; gap: .4rem;
            font-size: .72rem; color: #94a3b8;
            justify-content: center;
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f1f5f9;
        }
        .security-note svg { color: #10b981; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-box">

        <!-- Logo -->
        <div class="login-logo">
            <div class="login-logo-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <h1>Karim Telecom</h1>
            <p>Connexion à l'espace administration</p>
            <div class="admin-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Accès restreint
            </div>
        </div>

        <!-- Erreur / blocage -->
        <?php if ($locked): ?>
        <div class="alert-lock">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Compte bloqué <?= $remaining > 0 ? '— réessayez dans <strong style="margin-left:.25rem;">' . $remaining . 's</strong>' : '' ?>
        </div>
        <?php elseif ($error): ?>
        <div class="alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Barre de tentatives -->
        <?php if ($_SESSION['login_attempts'] > 0 && !$locked): ?>
        <?php $pct = ($_SESSION['login_attempts'] / 5) * 100; ?>
        <div class="attempts-bar">
            <div class="attempts-bar-fill" style="width:<?= $pct ?>%;background:<?= $pct >= 80 ? '#dc2626' : ($pct >= 60 ? '#f59e0b' : '#1a56db') ?>;"></div>
        </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <form method="POST" autocomplete="off" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="field">
                <label>
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Nom d'utilisateur
                </label>
                <div class="field-input">
                    <input type="text" name="username" id="username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           placeholder="karim"
                           required
                           <?= $locked ? 'disabled' : 'autofocus' ?>
                           autocomplete="username">
                    <span class="field-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                </div>
            </div>

            <div class="field">
                <label>
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Mot de passe
                </label>
                <div class="field-input">
                    <input type="password" name="password" id="password"
                           placeholder="••••••••"
                           required
                           <?= $locked ? 'disabled' : '' ?>
                           autocomplete="current-password">
                    <span class="field-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    </span>
                    <button type="button" class="toggle-pass" onclick="togglePass()" aria-label="Afficher le mot de passe" id="toggleBtn">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" name="login" class="btn-login" <?= $locked ? 'disabled' : '' ?>>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                <?= $locked ? 'Accès bloqué' : 'Se connecter' ?>
            </button>

            <!-- Note sécurité -->
            <div class="security-note">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Connexion sécurisée — Protection CSRF activée
            </div>
        </form>
    </div>

    <!-- Lien retour -->
    <div class="login-back">
        <a href="<?= BASE_URL ?>/index.php">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour au site
        </a>
    </div>
</div>

<?php if ($locked): ?>
<!-- Auto-refresh toutes les 10s si bloqué -->
<script>
setTimeout(function(){ location.reload(); }, 10000);
</script>
<?php endif; ?>

<script>
function togglePass() {
    var input = document.getElementById('password');
    var icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}
</script>
</body>
</html>
