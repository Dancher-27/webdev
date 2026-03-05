</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <h3><?= esc_html(SITE_NAME) ?></h3>
                <p>Professionele webontwikkeling met custom PHP, WordPress en moderne technieken.</p>
            </div>
            <div>
                <h4>Navigatie</h4>
                <ul>
                    <li><a href="<?= site_url() ?>">Home</a></li>
                    <li><a href="<?= site_url('projecten.php') ?>">Projecten</a></li>
                    <li><a href="<?= site_url('offerte.php') ?>">Offerte aanvragen</a></li>
                </ul>
            </div>
            <div>
                <h4>Admin</h4>
                <ul>
                    <li><a href="<?= site_url('admin/') ?>">Admin panel</a></li>
                    <li><a href="<?= site_url('admin/offertes.php') ?>">Offertes beheren</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= esc_html(SITE_NAME) ?> &mdash; Portfolio project WordPress Custom PHP</p>
        </div>
    </div>
</footer>

<script src="<?= assets_url('js/main.js') ?>"></script>
<?php do_action('wp_footer'); ?>
</body>
</html>
