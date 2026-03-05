<?php
/**
 * AJAX endpoint voor het offerte formulier
 * Vergelijkbaar met WordPress admin-ajax.php
 */

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/core/functions.php';
require_once dirname(__DIR__) . '/plugins/quote-request/quote-request.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . SITE_URL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$plugin = quote_request_init();
$quoteForm = $plugin->getQuoteForm();

// Trigger de form handler via de hook
do_action('handle_quote_form');
