<?php
/**
 * Admin dashboard
 * Geeft statistieken over offertes, posts en mail log
 */

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/core/functions.php';
require_once dirname(__DIR__) . '/plugins/quote-request/quote-request.php';

$plugin     = quote_request_init();
$db         = $plugin->getDb();
$postTypes  = $plugin->getPostTypes();
$quoteForm  = $plugin->getQuoteForm();
$mailer     = $plugin->getMailer();

// Statistieken ophalen
$totalQuotes    = $db->queryOne('SELECT COUNT(*) as cnt FROM quotes')['cnt'];
$newQuotes      = $db->queryOne('SELECT COUNT(*) as cnt FROM quotes WHERE status = "nieuw"')['cnt'];
$totalProjects  = $db->queryOne('SELECT COUNT(*) as cnt FROM posts WHERE post_type = "project"')['cnt'];
$totalMails     = $db->queryOne('SELECT COUNT(*) as cnt FROM mail_log')['cnt'];

// Recente offertes
$recentQuotes   = $db->query('SELECT * FROM quotes ORDER BY created_at DESC LIMIT 5');
$registeredTypes = $postTypes->getRegisteredTypes();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?= esc_html(SITE_NAME) ?></title>
    <link rel="stylesheet" href="<?= assets_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= assets_url('css/admin.css') ?>">
</head>
<body class="admin-body">

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <span>&#9881;</span> Admin Panel
        </div>
        <nav class="admin-nav">
            <a href="<?= site_url('admin/') ?>" class="active">&#128200; Dashboard</a>
            <a href="<?= site_url('admin/offertes.php') ?>">&#128220; Offertes</a>
            <a href="<?= site_url('admin/mail-log.php') ?>">&#128231; Mail log</a>
            <a href="<?= site_url('admin/post-types.php') ?>">&#128196; Post types</a>
            <hr>
            <a href="<?= site_url() ?>">&#8592; Naar website</a>
        </nav>
    </aside>

    <div class="admin-content">
        <header class="admin-header">
            <h1>Dashboard</h1>
            <span class="admin-version">Plugin v<?= QR_PLUGIN_VERSION ?></span>
        </header>

        <div class="stats-grid">
            <div class="stat-card stat-new">
                <div class="stat-icon">&#9888;</div>
                <div class="stat-info">
                    <div class="stat-number"><?= (int) $newQuotes ?></div>
                    <div class="stat-label">Nieuwe offertes</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">&#128220;</div>
                <div class="stat-info">
                    <div class="stat-number"><?= (int) $totalQuotes ?></div>
                    <div class="stat-label">Totaal offertes</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">&#128196;</div>
                <div class="stat-info">
                    <div class="stat-number"><?= (int) $totalProjects ?></div>
                    <div class="stat-label">Projecten (custom post type)</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">&#128231;</div>
                <div class="stat-info">
                    <div class="stat-number"><?= (int) $totalMails ?></div>
                    <div class="stat-label">Mails verstuurd (API log)</div>
                </div>
            </div>
        </div>

        <div class="admin-grid">
            <section class="admin-card">
                <h2>Recente offerte aanvragen</h2>
                <?php if (empty($recentQuotes)): ?>
                    <p class="empty">Nog geen aanvragen ontvangen.</p>
                <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Naam</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Datum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentQuotes as $quote): ?>
                        <tr>
                            <td><?= (int) $quote['id'] ?></td>
                            <td><?= esc_html($quote['name']) ?></td>
                            <td><?= esc_html($quote['email']) ?></td>
                            <td><span class="badge-status badge-<?= esc_attr($quote['status']) ?>"><?= esc_html($quote['status']) ?></span></td>
                            <td><?= format_date($quote['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="<?= site_url('admin/offertes.php') ?>" class="btn btn-sm">Alle offertes &rarr;</a>
                <?php endif; ?>
            </section>

            <section class="admin-card">
                <h2>Geregistreerde Post Types</h2>
                <p class="hint">Geregistreerd via <code>do_action('init')</code> hook</p>
                <?php foreach ($registeredTypes as $type => $args): ?>
                <div class="post-type-item">
                    <code><?= esc_html($type) ?></code>
                    <span><?= esc_html($args['label']) ?></span>
                    <small><?= esc_html($args['description']) ?></small>
                </div>
                <?php endforeach; ?>

                <h3 style="margin-top: 1.5rem;">Hooks geregistreerd</h3>
                <p class="hint">Alle actieve hooks in het systeem:</p>
                <?php
                $actions = $GLOBALS['wp_hooks']->getActions();
                foreach ($actions as $hook => $priorities):
                    $count = array_sum(array_map('count', $priorities));
                ?>
                <div class="hook-item">
                    <code><?= esc_html($hook) ?></code>
                    <span><?= $count ?> callback<?= $count !== 1 ? 's' : '' ?></span>
                </div>
                <?php endforeach; ?>
            </section>
        </div>
    </div>
</div>

</body>
</html>
