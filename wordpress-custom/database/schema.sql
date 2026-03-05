-- WordPress Custom Project - Database Schema
-- Database: wordpress_custom

CREATE DATABASE IF NOT EXISTS wordpress_custom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wordpress_custom;

-- Custom Post Types tabel (vergelijkbaar met wp_posts)
CREATE TABLE IF NOT EXISTS posts (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    post_type   VARCHAR(50)  NOT NULL DEFAULT 'post',
    title       VARCHAR(255) NOT NULL,
    content     TEXT,
    excerpt     VARCHAR(500),
    status      ENUM('publish', 'draft', 'trash') NOT NULL DEFAULT 'draft',
    author      VARCHAR(100),
    slug        VARCHAR(255) UNIQUE,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     ON UPDATE CURRENT_TIMESTAMP
);

-- Meta data per post (vergelijkbaar met wp_postmeta)
CREATE TABLE IF NOT EXISTS post_meta (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    post_id     INT          NOT NULL,
    meta_key    VARCHAR(100) NOT NULL,
    meta_value  TEXT,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    INDEX idx_post_id (post_id),
    INDEX idx_meta_key (meta_key)
);

-- Offerte aanvragen
CREATE TABLE IF NOT EXISTS quotes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    phone       VARCHAR(20),
    service     VARCHAR(100),
    budget      VARCHAR(50),
    message     TEXT         NOT NULL,
    status      ENUM('nieuw', 'in_behandeling', 'afgerond', 'afgewezen') NOT NULL DEFAULT 'nieuw',
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Gesimuleerde mail log (Mailgun-achtig)
CREATE TABLE IF NOT EXISTS mail_log (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    to_email    VARCHAR(150) NOT NULL,
    from_email  VARCHAR(150) NOT NULL,
    subject     VARCHAR(255) NOT NULL,
    body        TEXT         NOT NULL,
    status      ENUM('sent', 'failed', 'queued') NOT NULL DEFAULT 'queued',
    api_key     VARCHAR(100),
    domain      VARCHAR(100),
    response    TEXT,
    sent_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Voorbeelddata: projecten als custom post type
INSERT INTO posts (post_type, title, content, excerpt, status, author, slug) VALUES
('project', 'Webshop voor lokale bakker', 'Een complete WooCommerce webshop gebouwd voor een lokale bakkerij. Met product beheer, betaalsysteem en bezorgopties.', 'WooCommerce webshop met betaalsysteem', 'publish', 'Admin', 'webshop-lokale-bakker'),
('project', 'Portfolio website freelancer', 'Een modern portfolio voor een freelance grafisch ontwerper. Responsive design, animaties en contactformulier.', 'Modern portfolio met animaties', 'publish', 'Admin', 'portfolio-freelancer'),
('project', 'Reserveringssysteem restaurant', 'Online reserveringssysteem voor een restaurant. Realtime beschikbaarheid en automatische bevestigingsmails.', 'Realtime reserveringssysteem', 'publish', 'Admin', 'reservering-restaurant');

-- Meta data voor projecten
INSERT INTO post_meta (post_id, meta_key, meta_value) VALUES
(1, 'technologie', 'WordPress, WooCommerce, PHP'),
(1, 'duur', '4 weken'),
(1, 'klant', 'Bakkerij De Vries'),
(2, 'technologie', 'HTML, CSS, JavaScript, PHP'),
(2, 'duur', '2 weken'),
(2, 'klant', 'Jan Pietersen Design'),
(3, 'technologie', 'PHP, MySQL, jQuery'),
(3, 'duur', '3 weken'),
(3, 'klant', 'Restaurant La Bella');
