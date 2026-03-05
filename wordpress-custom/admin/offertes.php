<?php
/**
 * Admin - Offertes beheren
 * CRUD voor offerte aanvragen
 */

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/core/functions.php';
require_once dirname(__DIR__) . '/plugins/quote-request/quote-request.php';

$plugin    = quote_request_init();
$quoteForm = $plugin->getQuoteForm();

$message = '';
$error   = '';

// Status update verwerken
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $id     = (int) ($_POST['id'] ?? 0);
        $status = sanitize_text($_POST['status'] ?? '');

        if ($quoteForm->updateStatus($id, $status)) {
            $message = "Offerte #{$id} bijgewerkt naar '{$status}'";
        } else {
            $error = 'Status bijwerken mislukt';
        }
    }
}

// Filter op status
$filterStatus = sanitize_text($_GET['status'] ?? '');
$quotes = $quoteForm->getAllQuotes($filterStatus);

$statusOptions = ['nieuw', 'in_behandeling', 'afgerond', 'afgewezen'];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offertes - <?= esc_html(SITE_NAME) ?> Admin</title>
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
            <a href="<?= site_url('admin/') ?>">&#128200; Dashboard</a>
            <a href="<?= site_url('admin/offertes.php') ?>" class="active">&#128220; Offertes</a>
            <a href="<?= site_url('admin/mail-log.php') ?>">&#128231; Mail log</a>
            <a href="<?= site_url('admin/post-types.php') ?>">&#128196; Post types</a>
            <hr>
            <a href="<?= site_url() ?>">&#8592; Naar website</a>
        </nav>
    </aside>

    <div class="admin-content">
        <header class="admin-header">
            <h1>Offertes beheren</h1>
            <a href="<?= site_url('offerte.php') ?>" class="btn btn-sm" target="_blank">Formulier bekijken &rarr;</a>
        </header>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= esc_html($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= esc_html($error) ?></div>
        <?php endif; ?>

        <div class="filter-bar">
            <span>Filter op status:</span>
            <a href="?" class="filter-btn <?= $filterStatus === '' ? 'active' : '' ?>">Alle</a>
            <?php foreach ($statusOptions as $s): ?>
            <a href="?status=<?= esc_attr($s) ?>" class="filter-btn <?= $filterStatus === $s ? 'active' : '' ?>">
                <?= esc_html(ucfirst(str_replace('_', ' ', $s))) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($quotes)): ?>
            <div class="admin-card">
                <p class="empty">Geen offertes gevonden<?= $filterStatus ? " met status '{$filterStatus}'" : '' ?>.</p>
            </div>
        <?php else: ?>
        <div class="admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Naam</th>
                        <th>Email</th>
                        <th>Telefoon</th>
                        <th>Dienst</th>
                        <th>Budget</th>
                        <th>Status</th>
                        <th>Datum</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotes as $quote): ?>
                    <tr>
                        <td><?= (int) $quote['id'] ?></td>
                        <td><?= esc_html($quote['name']) ?></td>
                        <td><a href="mailto:<?= esc_attr($quote['email']) ?>"><?= esc_html($quote['email']) ?></a></td>
                        <td><?= esc_html($quote['phone'] ?: '-') ?></td>
                        <td><?= esc_html($quote['service'] ?: '-') ?></td>
                        <td><?= esc_html($quote['budget'] ?: '-') ?></td>
                        <td><span class="badge-status badge-<?= esc_attr($quote['status']) ?>"><?= esc_html($quote['status']) ?></span></td>
                        <td><?= format_date($quote['created_at']) ?></td>
                        <td>
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="id" value="<?= (int) $quote['id'] ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <?php foreach ($statusOptions as $s): ?>
                                    <option value="<?= esc_attr($s) ?>" <?= $quote['status'] === $s ? 'selected' : '' ?>>
                                        <?= esc_html(ucfirst(str_replace('_', ' ', $s))) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php if (!empty($quote['message'])): ?>
                    <tr class="message-row">
                        <td colspan="9">
                            <strong>Omschrijving:</strong>
                            <?= esc_html($quote['message']) ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
