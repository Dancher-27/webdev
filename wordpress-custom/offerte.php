<?php
/**
 * Offerte aanvraag pagina
 * Verwerkt het formulier via AJAX (POST naar api/quote.php)
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/functions.php';
require_once __DIR__ . '/plugins/quote-request/quote-request.php';

$plugin = quote_request_init();

$pageTitle = 'Offerte aanvragen - ' . SITE_NAME;

// Voeg AJAX URL toe via wp_head hook (WordPress-stijl)
add_action('wp_head', function () {
    echo '<script>const WP = { ajaxUrl: "' . site_url('api/quote.php') . '" };</script>';
});

include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/quote-form.php';
include __DIR__ . '/templates/footer.php';
