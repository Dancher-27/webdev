<?php
/**
 * Database configuratie
 * Pas deze gegevens aan naar je XAMPP setup
 */

define('DB_HOST',     'localhost');
define('DB_NAME',     'wordpress_custom');
define('DB_USER',     'root');
define('DB_PASSWORD', '');
define('DB_CHARSET',  'utf8mb4');

// Site configuratie
define('SITE_URL',    'http://localhost/Portofolio-opdrachten/wordpress-custom');
define('SITE_NAME',   'WebDev Studio');
define('ADMIN_EMAIL', 'admin@example.com');

// Mail API configuratie (Mailgun-stijl)
define('MAIL_API_KEY',    'mg-simulated-key-' . md5('demo'));
define('MAIL_API_DOMAIN', 'mg.webdevstudio.nl');
define('MAIL_FROM',       'noreply@webdevstudio.nl');
define('MAIL_FROM_NAME',  'WebDev Studio');
