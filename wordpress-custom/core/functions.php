<?php
/**
 * Global helper functies - WordPress-stijl API
 *
 * Deze functies zijn het publieke interface voor het hooks systeem,
 * net zoals WordPress globale functies heeft voor add_action() etc.
 */

require_once __DIR__ . '/class-hooks.php';

// Globale hooks instantie
$GLOBALS['wp_hooks'] = WP_Hooks::getInstance();

// -------------------------------------------------------
// Actions
// -------------------------------------------------------

function add_action(string $hook, callable $callback, int $priority = 10): void
{
    $GLOBALS['wp_hooks']->addAction($hook, $callback, $priority);
}

function do_action(string $hook, mixed ...$args): void
{
    $GLOBALS['wp_hooks']->doAction($hook, ...$args);
}

// -------------------------------------------------------
// Filters
// -------------------------------------------------------

function add_filter(string $hook, callable $callback, int $priority = 10): void
{
    $GLOBALS['wp_hooks']->addFilter($hook, $callback, $priority);
}

function apply_filters(string $hook, mixed $value, mixed ...$args): mixed
{
    return $GLOBALS['wp_hooks']->applyFilters($hook, $value, ...$args);
}

// -------------------------------------------------------
// Utility helpers
// -------------------------------------------------------

/**
 * Escaped output (XSS preventie), vergelijkbaar met WordPress esc_html()
 */
function esc_html(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Escaped attribuut output
 */
function esc_attr(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize tekstinvoer (strips tags + trim)
 */
function sanitize_text(string $str): string
{
    return trim(strip_tags($str));
}

/**
 * Sanitize email
 */
function sanitize_email(string $email): string
{
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

/**
 * Valideer email
 */
function is_valid_email(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Slug generator (titel naar URL-vriendelijke string)
 */
function sanitize_slug(string $str): string
{
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    return trim($str, '-');
}

/**
 * Geeft de volledige site URL terug
 */
function site_url(string $path = ''): string
{
    return SITE_URL . '/' . ltrim($path, '/');
}

/**
 * Geeft een assets URL terug
 */
function assets_url(string $path = ''): string
{
    return SITE_URL . '/assets/' . ltrim($path, '/');
}

/**
 * Redirect helper
 */
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/**
 * Datum formattering (NL-stijl)
 */
function format_date(string $dateStr, string $format = 'd-m-Y H:i'): string
{
    $dt = new DateTime($dateStr);
    return $dt->format($format);
}
