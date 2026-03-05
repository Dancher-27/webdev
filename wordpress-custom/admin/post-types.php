<?php
/**
 * Admin - Custom Post Types beheer
 * Bekijk en voeg posts toe voor elk geregistreerd post type
 */

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/core/functions.php';
require_once dirname(__DIR__) . '/plugins/quote-request/quote-request.php';

$plugin    = quote_request_init();
$postTypes = $plugin->getPostTypes();

$message = '';
$error   = '';

// Nieuw post toevoegen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_post') {
    $postId = $postTypes->insertPost([
        'post_type' => sanitize_text($_POST['post_type'] ?? 'project'),
        'title'     => sanitize_text($_POST['title'] ?? ''),
        'content'   => sanitize_text($_POST['content'] ?? ''),
        'excerpt'   => sanitize_text($_POST['excerpt'] ?? ''),
        'status'    => 'publish',
        'meta'      => [
            'technologie' => sanitize_text($_POST['meta_technologie'] ?? ''),
            'duur'        => sanitize_text($_POST['meta_duur'] ?? ''),
            'klant'       => sanitize_text($_POST['meta_klant'] ?? ''),
        ],
    ]);

    if ($postId) {
        $message = "Post #{$postId} aangemaakt!";
    } else {
        $error = 'Aanmaken mislukt';
    }
}

$registeredTypes = $postTypes->getRegisteredTypes();
$selectedType    = sanitize_text($_GET['type'] ?? 'project');
$posts           = $postTypes->getPosts($selectedType, 'publish', 50);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Types - <?= esc_html(SITE_NAME) ?> Admin</title>
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
            <a href="<?= site_url('admin/mail-log.php') ?>">&#128231; Mail log</a>
            <a href="<?= site_url('admin/post-types.php') ?>" class="active">&#128196; Post types</a>
            <hr>
            <a href="<?= site_url() ?>">&#8592; Naar website</a>
        </nav>
    </aside>

    <div class="admin-content">
        <header class="admin-header">
            <h1>Custom Post Types</h1>
        </header>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= esc_html($message) ?></div>
        <?php endif; ?>

        <div class="admin-grid">
            <section class="admin-card">
                <h2>Nieuw post aanmaken</h2>
                <p class="hint">Vergelijkbaar met <code>wp_insert_post()</code> in WordPress</p>
                <form method="POST">
                    <input type="hidden" name="action" value="add_post">
                    <div class="form-group">
                        <label>Post type</label>
                        <select name="post_type">
                            <?php foreach ($registeredTypes as $type => $args): ?>
                            <option value="<?= esc_attr($type) ?>"><?= esc_html($args['label_single']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Titel *</label>
                        <input type="text" name="title" required placeholder="Post titel">
                    </div>
                    <div class="form-group">
                        <label>Excerpt</label>
                        <input type="text" name="excerpt" placeholder="Korte omschrijving">
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <textarea name="content" rows="4" placeholder="Volledige inhoud..."></textarea>
                    </div>
                    <h4>Post Meta</h4>
                    <div class="form-group">
                        <label>Technologie</label>
                        <input type="text" name="meta_technologie" placeholder="PHP, MySQL, CSS">
                    </div>
                    <div class="form-group">
                        <label>Duur</label>
                        <input type="text" name="meta_duur" placeholder="2 weken">
                    </div>
                    <div class="form-group">
                        <label>Klant</label>
                        <input type="text" name="meta_klant" placeholder="Bedrijfsnaam">
                    </div>
                    <button type="submit" class="btn btn-primary">Post aanmaken</button>
                </form>
            </section>

            <section class="admin-card">
                <h2>Bestaande posts</h2>
                <div class="type-tabs">
                    <?php foreach ($registeredTypes as $type => $args): ?>
                    <a href="?type=<?= esc_attr($type) ?>" class="filter-btn <?= $selectedType === $type ? 'active' : '' ?>">
                        <?= esc_html($args['label']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <?php if (empty($posts)): ?>
                    <p class="empty">Geen posts gevonden voor type '<?= esc_html($selectedType) ?>'.</p>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                    <div class="post-item">
                        <div class="post-item-header">
                            <strong><?= esc_html($post['title']) ?></strong>
                            <code>id: <?= (int) $post['id'] ?></code>
                        </div>
                        <?php if (!empty($post['meta'])): ?>
                        <div class="post-meta-inline">
                            <?php foreach ($post['meta'] as $k => $v): ?>
                                <span><em><?= esc_html($k) ?>:</em> <?= esc_html($v) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <small><?= format_date($post['created_at']) ?></small>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>

</body>
</html>
