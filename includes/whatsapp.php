<?php if (!function_exists('svg')) require_once __DIR__ . '/icons.php'; ?>
<div class="wa-float">
    <span class="wa-tooltip">Besoin d'aide ? Écrivez-nous !</span>
    <a href="<?= waLink('Bonjour Karim Telecom, j\'ai besoin d\'aide.') ?>"
       target="_blank" rel="noopener"
       class="wa-btn" aria-label="Contacter sur WhatsApp">
        <?= svg('whatsapp', 26) ?>
    </a>
</div>
