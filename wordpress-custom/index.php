<?php
/**
 * Homepage - WebDev Studio
 * Toont featured projecten (custom post type) en een call-to-action
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/functions.php';
require_once __DIR__ . '/plugins/quote-request/quote-request.php';

// Initialiseer de plugin
$plugin = quote_request_init();
$postTypes = $plugin->getPostTypes();

// Haal projecten op (custom post type)
$projects = $postTypes->getPosts('project', 'publish', 6);

// Demonstreer filter: voeg 'Uitgelicht' label toe aan eerste project
add_filter('the_posts', function (array $posts): array {
    if (!empty($posts)) {
        $posts[0]['featured'] = true;
    }
    return $posts;
}, 20);

$pageTitle = 'Home - ' . SITE_NAME;

include __DIR__ . '/templates/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <span class="hero-badge">WordPress Custom Plugin Demo</span>
            <h1>Professionele webontwikkeling<br>met Custom PHP</h1>
            <p>Dit project demonstreert een WordPress-stijl plugin architectuur met custom post types, hooks &amp; filters systeem en een Mailgun-achtige mail API &mdash; volledig gebouwd in PHP.</p>
            <div class="hero-actions">
                <a href="<?= site_url('offerte.php') ?>" class="btn btn-primary">Offerte aanvragen</a>
                <a href="<?= site_url('projecten.php') ?>" class="btn btn-outline">Bekijk projecten</a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="code-preview">
                <div class="code-header">
                    <span></span><span></span><span></span>
                    <small>quote-request.php</small>
                </div>
                <pre><code><span class="c">/**
 * Plugin Name: Quote Request
 * Version:     1.0.0
 */</span>

<span class="kw">add_action</span>(<span class="str">'init'</span>, function() {
    <span class="kw">register_post_type</span>(<span class="str">'project'</span>);
    <span class="kw">register_post_type</span>(<span class="str">'service'</span>);
});

<span class="kw">add_filter</span>(<span class="str">'the_posts'</span>, function($posts) {
    <span class="kw">return</span> $posts;
});</code></pre>
            </div>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">&#128268;</div>
                <h3>Custom Plugin</h3>
                <p>WordPress-stijl plugin met plugin header, autoloading en singleton patroon.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#128196;</div>
                <h3>Custom Post Types</h3>
                <p>Eigen content types (Projecten, Diensten) met meta data — opgeslagen in MySQL.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#128040;</div>
                <h3>Hooks &amp; Filters</h3>
                <p>Volledig zelfgebouwd hooks systeem identiek aan WordPress add_action / apply_filters.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#128231;</div>
                <h3>Mail API</h3>
                <p>Mailgun-achtige mail klasse met logging naar database. Geen SMTP vereist.</p>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($projects)): ?>
<section class="projects-section">
    <div class="container">
        <div class="section-header">
            <h2>Recente projecten</h2>
            <p>Opgeslagen als <code>custom post type</code> in de database</p>
        </div>
        <div class="projects-grid">
            <?php foreach ($projects as $project): ?>
            <article class="project-card <?= isset($project['featured']) ? 'featured' : '' ?>">
                <?php if (isset($project['featured'])): ?>
                    <span class="badge">Uitgelicht</span>
                <?php endif; ?>
                <div class="project-card-body">
                    <span class="post-type-label">post_type: project</span>
                    <h3><?= esc_html($project['title']) ?></h3>
                    <p><?= esc_html(apply_filters('the_excerpt', $project['excerpt'] ?? '')) ?></p>
                    <?php if (!empty($project['meta'])): ?>
                    <ul class="project-meta">
                        <?php foreach ($project['meta'] as $key => $value): ?>
                            <li><strong><?= esc_html(ucfirst($key)) ?>:</strong> <?= esc_html($value) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
                <div class="project-card-footer">
                    <span class="project-date"><?= format_date($project['created_at'], 'd M Y') ?></span>
                    <span class="project-status status-<?= esc_attr($project['status']) ?>"><?= esc_html($project['status']) ?></span>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <div class="section-cta">
            <a href="<?= site_url('projecten.php') ?>" class="btn btn-outline">Alle projecten bekijken</a>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-section">
    <div class="container">
        <div class="cta-card">
            <h2>Klaar om te starten?</h2>
            <p>Vraag vandaag nog een vrijblijvende offerte aan. We reageren binnen 24 uur.</p>
            <a href="<?= site_url('offerte.php') ?>" class="btn btn-white">Offerte aanvragen &rarr;</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/templates/footer.php'; ?>
