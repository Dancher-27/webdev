<?php
/**
 * Admin - Mail API log
 * Toont alle gesimuleerde mail verzendingen (Mailgun-stijl)
 */

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/core/functions.php';
require_once dirname(__DIR__) . '/plugins/quote-request/quote-request.php';

$plugin  = quote_request_init();
$mailer  = $plugin->getMailer();
$mailLog = $mailer->getLog(100);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mail log - <?= esc_html(SITE_NAME) ?> Admin</title>
    <link rel="stylesheet" href="<?= assets_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= assets_url('css/admin.css') ?>">
</head>
<body class="admin-body">

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-logo"><span>&#9881;</span> Admin Panel</div>
        <nav class="admin-nav">
            <a href="<?= site_url('admin/') ?>">&#128200; Dashboard</a>
            <a href="<?= site_url('admin/offertes.php') ?>">&#128220; Offertes</a>
            <a href="<?= site_url('admin/mail-log.php') ?>" class="active">&#128231; Mail log</a>
            <a href="<?= site_url('admin/post-types.php') ?>">&#128196; Post types</a>
            <hr>
            <a href="<?= site_url() ?>">&#8592; Naar website</a>
        </nav>
    </aside>

    <div class="admin-content">
        <header class="admin-header">
            <h1>Mail API log</h1>
            <span class="hint">Mailgun-stijl simulatie &mdash; domain: <code><?= esc_html(MAIL_API_DOMAIN) ?></code></span>
        </header>

        <div class="admin-card info-card">
            <h3>Hoe werkt de Mail API?</h3>
            <p>De <code>MailAPI</code> klasse simuleert de Mailgun REST API interface. In productie zou <code>MailAPI::send()</code> een HTTP POST doen naar <code>https://api.mailgun.net/v3/{domain}/messages</code>. Nu worden alle mails gelogd in de database zodat je ze hier kunt inzien.</p>
            <pre><code>$mailer = new MailAPI($db);
$result = $mailer->send(
    'klant@email.nl',
    'Bevestiging offerte aanvraag',
    $htmlBody
);
// $result = ['success' => true, 'id' => 42, 'message' => 'Queued.']</code></pre>
        </div>

        <?php if (empty($mailLog)): ?>
            <div class="admin-card">
                <p class="empty">Nog geen mails verstuurd. Dien een offerte in om de mail API te testen.</p>
                <a href="<?= site_url('offerte.php') ?>" class="btn btn-primary">Offerte aanvragen</a>
            </div>
        <?php else: ?>
        <div class="admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Aan</th>
                        <th>Van</th>
                        <th>Onderwerp</th>
                        <th>Status</th>
                        <th>Domain</th>
                        <th>Tijdstip</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mailLog as $mail): ?>
                    <tr>
                        <td><?= (int) $mail['id'] ?></td>
                        <td><?= esc_html($mail['to_email']) ?></td>
                        <td><?= esc_html($mail['from_email']) ?></td>
                        <td><?= esc_html($mail['subject']) ?></td>
                        <td><span class="badge-status badge-<?= esc_attr($mail['status']) ?>"><?= esc_html($mail['status']) ?></span></td>
                        <td><code><?= esc_html($mail['domain']) ?></code></td>
                        <td><?= format_date($mail['sent_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
