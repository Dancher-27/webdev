<?php
/**
 * Projecten overzichtspagina
 * Toont alle custom post types van het type 'project'
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/functions.php';
require_once __DIR__ . '/plugins/quote-request/quote-request.php';

$plugin = quote_request_init();
$postTypes = $plugin->getPostTypes();

$projects = $postTypes->getPosts('project', 'publish', 20);

$pageTitle = 'Projecten - ' . SITE_NAME;

include __DIR__ . '/templates/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Projecten</h1>
        <p>Opgeslagen als <strong>Custom Post Type</strong> in de database. Vergelijkbaar met hoe WordPress inhoud opslaat in <code>wp_posts</code>.</p>
    </div>
</section>

<section class="projects-section">
    <div class="container">
        <?php if (empty($projects)): ?>
            <div class="empty-state">
                <p>Geen projecten gevonden. Voeg projecten toe via het admin panel.</p>
                <a href="<?= site_url('admin/') ?>" class="btn btn-primary">Naar admin panel</a>
            </div>
        <?php else: ?>
            <div class="projects-grid projects-grid--large">
                <?php foreach ($projects as $project): ?>
                <article class="project-card">
                    <div class="project-card-body">
                        <span class="post-type-label">post_type: project &bull; id: <?= (int) $project['id'] ?></span>
                        <h2><?= esc_html($project['title']) ?></h2>
                        <p><?= esc_html(apply_filters('the_excerpt', $project['excerpt'] ?? '')) ?></p>
                        <div class="project-content">
                            <?= nl2br(esc_html($project['content'])) ?>
                        </div>
                        <?php if (!empty($project['meta'])): ?>
                        <div class="project-meta-box">
                            <h4>Post Meta (post_meta tabel)</h4>
                            <ul class="project-meta">
                                <?php foreach ($project['meta'] as $key => $value): ?>
                                    <li>
                                        <code><?= esc_html($key) ?></code>
                                        <span><?= esc_html($value) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="project-card-footer">
                        <span class="project-date"><?= format_date($project['created_at']) ?></span>
                        <span class="project-status status-<?= esc_attr($project['status']) ?>"><?= esc_html($project['status']) ?></span>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/templates/footer.php'; ?>
