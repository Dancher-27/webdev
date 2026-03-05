<?php
/**
 * Plugin Name:       Quote Request
 * Plugin URI:        https://webdevstudio.nl/plugins/quote-request
 * Description:       Offerte aanvraag systeem met custom post types en mail API integratie.
 * Version:           1.0.0
 * Author:            WebDev Studio
 * Author URI:        https://webdevstudio.nl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       quote-request
 */

// Direct toegang blokkeren (WordPress security patroon)
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__, 3) . '/');
}

define('QR_PLUGIN_VERSION', '1.0.0');
define('QR_PLUGIN_DIR', __DIR__ . '/');
define('QR_PLUGIN_URL', SITE_URL . '/plugins/quote-request/');

// Laad alle includes
require_once QR_PLUGIN_DIR . 'includes/class-database.php';
require_once QR_PLUGIN_DIR . 'includes/class-mail-api.php';
require_once QR_PLUGIN_DIR . 'includes/class-post-types.php';
require_once QR_PLUGIN_DIR . 'includes/class-quote-form.php';
require_once QR_PLUGIN_DIR . 'includes/class-loader.php';

/**
 * Initialiseer de plugin en geef de Loader terug
 * Vergelijkbaar met WordPress plugin main class instantiatie
 */
function quote_request_init(): Loader
{
    $loader = new Loader();
    $loader->init();

    do_action('quote_request_loaded', $loader);

    return $loader;
}
