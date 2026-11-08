<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Vérifier connexion
if (empty($_SESSION['admin_logged'])) {
    header('Location: index.php'); exit;
}

// Timeout de session : 2h d'inactivité
$timeout = 7200;
if (isset($_SESSION['admin_last_activity']) && (time() - $_SESSION['admin_last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
    header('Location: index.php?msg=timeout'); exit;
}
$_SESSION['admin_last_activity'] = time();

// Fixation de session : vérifier que l'IP n'a pas changé brutalement
if (!isset($_SESSION['admin_ip'])) {
    $_SESSION['admin_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
} elseif ($_SESSION['admin_ip'] !== ($_SERVER['REMOTE_ADDR'] ?? '')) {
    // IP changée — déconnecter par précaution
    session_unset(); session_destroy();
    session_start(); session_regenerate_id(true);
    header('Location: index.php?msg=security'); exit;
}
?>
