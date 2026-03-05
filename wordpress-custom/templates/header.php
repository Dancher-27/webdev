<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc_html($pageTitle ?? SITE_NAME) ?></title>
    <link rel="stylesheet" href="<?= assets_url('css/style.css') ?>">
    <?php do_action('wp_head'); ?>
</head>
<body>

<header class="site-header">
    <div class="container">
        <a href="<?= site_url() ?>" class="logo">
            <span class="logo-icon">&#9881;</span>
            <?= esc_html(SITE_NAME) ?>
        </a>
        <nav class="main-nav">
            <ul>
                <li><a href="<?= site_url() ?>">Home</a></li>
                <li><a href="<?= site_url('projecten.php') ?>">Projecten</a></li>
                <li><a href="<?= site_url('offerte.php') ?>" class="btn-nav">Offerte aanvragen</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="site-main">
