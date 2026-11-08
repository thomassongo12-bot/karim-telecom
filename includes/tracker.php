<?php
/**
 * Karim Telecom — Tracker de visites (privacy-friendly, sans cookies)
 * Enregistre page, IP hashée, date et heure
 */
function trackVisit($pdo, $page = '') {
    try {
        $ip      = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ip      = trim(explode(',', $ip)[0]);
        $ipHash  = hash('sha256', $ip . date('Y-m-d')); // hash journalier = anonyme
        $ua      = mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200);
        $ref     = mb_substr($_SERVER['HTTP_REFERER'] ?? '', 0, 200);
        $page    = $page ?: (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
        $today   = date('Y-m-d');
        $heure   = (int)date('G');

        // Bots courants à ignorer
        $botPatterns = ['bot','crawl','spider','slurp','mediapartners','google','bing','yahoo','baidu','yandex','facebookexternalhit','curl','wget','python'];
        $uaLow = strtolower($ua);
        foreach ($botPatterns as $b) {
            if (strpos($uaLow, $b) !== false) return;
        }

        $pdo->prepare("INSERT INTO site_visits (page, ip_hash, user_agent, referrer, date_visite, heure)
                       VALUES (?, ?, ?, ?, ?, ?)")
            ->execute([$page, $ipHash, $ua, $ref, $today, $heure]);
    } catch (PDOException $e) { /* fail silently */ }
}

function trackClic($pdo, $produitId) {
    try {
        $ip     = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ip     = trim(explode(',', $ip)[0]);
        $ipHash = hash('sha256', $ip . date('Y-m-d'));
        $today  = date('Y-m-d');
        $pdo->prepare("INSERT INTO produit_clics (produit_id, ip_hash, date_clic) VALUES (?, ?, ?)")
            ->execute([$produitId, $ipHash, $today]);
    } catch (PDOException $e) { /* fail silently */ }
}
?>
