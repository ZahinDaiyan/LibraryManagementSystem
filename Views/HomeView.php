<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/i18n.php';

$lang = app_current_language();

require_once '../Models/DB.php';
require_once '../Models/BookModel.php';
require_once '../Models/AnnouncementModel.php';

$conn = Connect();
$featuredBooks = getAllBooks($conn);
// Take only first 6 as featured
$featuredBooks = array_slice($featuredBooks, 0, 6);
$announcements = getAllAnnouncements($conn);
// Take only first 3
$announcements = array_slice($announcements, 0, 3);
Close($conn);

$basePath = '..';
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= app_translate('home.brand_title') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #08111f;
            --bg-soft: #111a2c;
            --surface: rgba(12, 21, 38, 0.9);
            --surface-2: rgba(21, 31, 52, 0.9);
            --text: #edf2ff;
            --muted: #a9b7d0;
            --accent: #f6b73c;
            --accent-2: #76d2ff;
            --line: rgba(255, 255, 255, 0.12);
            --shadow: 0 24px 70px rgba(0, 0, 0, 0.35);
        }

        * { box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(118, 210, 255, 0.16), transparent 28%),
                radial-gradient(circle at 85% 15%, rgba(246, 183, 60, 0.18), transparent 24%),
                linear-gradient(180deg, #050b14 0%, var(--bg) 42%, #0a1321 100%);
            min-height: 100vh;
        }

        body.lang-bn {
            font-family: 'Noto Sans Bengali', 'Manrope', sans-serif;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .page-shell {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 20px 0 44px;
        }

        .topbar {
            position: sticky;
            top: 12px;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 20px;
            margin-bottom: 28px;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: rgba(8, 17, 31, 0.72);
            backdrop-filter: blur(18px);
            box-shadow: var(--shadow);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--accent), #ff8f5a);
            color: #111827;
            font-family: 'Fraunces', serif;
            font-size: 1.15rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .nav-links a:hover {
            color: var(--text);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            border-radius: 999px;
            font-weight: 700;
            border: 1px solid transparent;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--accent), #ffd15d);
            color: #101826;
            box-shadow: 0 14px 28px rgba(246, 183, 60, 0.22);
            white-space: nowrap;
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(300px, 0.85fr);
            gap: 24px;
            align-items: stretch;
            margin-bottom: 28px;
        }

        .hero-copy,
        .hero-panel,
        .section-card {
            border: 1px solid var(--line);
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(15, 24, 40, 0.92), rgba(11, 18, 31, 0.9));
            box-shadow: var(--shadow);
        }

        .hero-copy {
            padding: 34px;
            position: relative;
            overflow: hidden;
        }

        .hero-copy::after {
            content: '';
            position: absolute;
            inset: auto -40px -72px auto;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(118, 210, 255, 0.2), transparent 68%);
            pointer-events: none;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(246, 183, 60, 0.12);
            color: #ffd98a;
            font-size: 0.86rem;
            font-weight: 700;
            margin-bottom: 18px;
        }

        h1, h2, h3 {
            font-family: 'Fraunces', serif;
            margin: 0;
            line-height: 1.05;
        }

        h1 {
            font-size: clamp(2.5rem, 5vw, 4.8rem);
            max-width: 10ch;
            margin-bottom: 16px;
        }

        .hero-copy p {
            margin: 0;
            max-width: 58ch;
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 24px;
        }

        .btn-outline {
            border-color: rgba(118, 210, 255, 0.35);
            background: rgba(118, 210, 255, 0.08);
            color: #dff6ff;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 28px;
        }

        .stat {
            padding: 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .stat strong {
            display: block;
            font-size: 1.3rem;
            margin-bottom: 6px;
        }

        .stat span {
            color: var(--muted);
            font-size: 0.92rem;
        }

        .hero-panel {
            padding: 20px;
            display: grid;
            gap: 16px;
        }

        .panel-image {
            min-height: 260px;
            border-radius: 22px;
            background:
                linear-gradient(180deg, rgba(10, 18, 30, 0.2), rgba(10, 18, 30, 0.68)),
                url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=1200&q=80') center/cover;
            position: relative;
            overflow: hidden;
        }

        .panel-image::before {
            content: 'Open shelves, calm spaces, and modern services.';
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 18px;
            padding: 16px;
            border-radius: 18px;
            background: rgba(8, 17, 31, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.12);
            font-weight: 600;
            color: #f0f6ff;
        }

        .mini-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .mini-card {
            padding: 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--muted);
            line-height: 1.7;
        }

        .mini-card strong {
            display: block;
            color: var(--text);
            margin-bottom: 6px;
        }

        .section {
            margin-top: 28px;
        }

        .section-header {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .section-header p {
            margin: 0;
            color: var(--muted);
            max-width: 58ch;
            line-height: 1.7;
        }

        .section-card {
            padding: 20px;
        }

        .grid-books {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .book-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 22px;
            padding: 16px;
            display: grid;
            gap: 14px;
        }

        .book-cover {
            height: 220px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(118, 210, 255, 0.2), rgba(246, 183, 60, 0.18));
            overflow: hidden;
            position: relative;
        }

        .book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .book-cover span {
            position: absolute;
            inset: auto 16px 16px 16px;
            padding: 10px 12px;
            border-radius: 999px;
            background: rgba(8, 17, 31, 0.78);
            font-size: 0.85rem;
            color: #e8f4ff;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .book-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .pill {
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .book-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .book-card .btn {
            width: fit-content;
        }

        .inline-form {
            margin: 0;
        }

        .announcements {
            display: grid;
            gap: 14px;
        }

        .announcement-card {
            padding: 18px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .announcement-card small {
            color: var(--muted);
            display: block;
            margin-top: 8px;
        }

        .two-col {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .about-box,
        .contact-box {
            padding: 24px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            line-height: 1.8;
            color: var(--muted);
        }

        .contact-box strong {
            color: var(--text);
        }

        footer {
            padding: 26px 0 8px;
            color: var(--muted);
            text-align: center;
            font-size: 0.95rem;
        }

        .empty-state {
            padding: 18px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px dashed rgba(255, 255, 255, 0.18);
            color: var(--muted);
        }

        @media (max-width: 980px) {
            .hero,
            .grid-books,
            .two-col {
                grid-template-columns: 1fr;
            }

            .hero-stats,
            .mini-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .page-shell {
                width: min(100% - 20px, 1180px);
            }

            .topbar,
            .hero-copy,
            .hero-panel,
            .section-card {
                border-radius: 22px;
            }

            .hero-copy {
                padding: 24px;
            }
        }
    </style>
</head>
<body class="<?= $lang === 'bn' ? 'lang-bn' : 'lang-en' ?>">
    <div class="page-shell">
        <header class="topbar">
            <div class="brand">
                <div class="brand-mark">L</div>
                <div>
                    <div><?= app_translate('home.brand_title') ?></div>
                    <div style="font-size: 0.88rem; color: var(--muted); font-weight: 600;"><?= app_translate('home.brand_tagline') ?></div>
                </div>
            </div>

            <nav class="nav-links" aria-label="Primary navigation">
                <a href="#featured-books"><?= app_translate('home.nav.featured') ?></a>
                <a href="#announcements"><?= app_translate('home.nav.announcements') ?></a>
                <a href="#about"><?= app_translate('home.nav.about') ?></a>
                <a href="#contact"><?= app_translate('home.nav.contact') ?></a>
            </nav>

            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <a class="btn btn-login" href="<?= app_language_url('LoginView.php') ?>"><?= app_translate('home.login') ?></a>
                <div class="lang-switch" aria-label="<?= app_translate('language.switch') ?>" style="display:inline-flex; gap:8px;">
                    <a class="btn btn-outline" style="padding:10px 14px;" href="<?= app_language_url('HomeView.php', 'en') ?>"><?= app_translate('language.english') ?></a>
                    <a class="btn btn-outline" style="padding:10px 14px;" href="<?= app_language_url('HomeView.php', 'bn') ?>"><?= app_translate('language.bengali') ?></a>
                </div>
            </div>
        </header>

        <section class="hero">
            <div class="hero-copy">
                <div class="eyebrow"><?= app_translate('home.hero.badge') ?></div>
                <h1><?= app_translate('home.hero.title') ?></h1>
                <p>
                    <?= app_translate('home.hero.description') ?>
                </p>

                <div class="hero-actions">
                    <a class="btn btn-login" href="<?= $basePath ?>/Controllers/BookIndexController.php"><?= app_translate('home.hero.browse') ?></a>
                    <a class="btn btn-outline" href="#announcements"><?= app_translate('home.hero.news') ?></a>
                </div>

                <div class="hero-stats">
                    <div class="stat">
                        <strong><?= count($featuredBooks) ?></strong>
                        <span><?= app_translate('home.stat.featured') ?></span>
                    </div>
                    <div class="stat">
                        <strong><?= count($announcements) ?></strong>
                        <span><?= app_translate('home.stat.announcements') ?></span>
                    </div>
                    <div class="stat">
                        <strong>24/7</strong>
                        <span><?= app_translate('home.stat.access') ?></span>
                    </div>
                </div>
            </div>

            <aside class="hero-panel" aria-label="Library highlights">
                <div class="panel-image"></div>
                <div class="mini-grid">
                    <div class="mini-card">
                        <strong><?= app_translate('home.panel.browse.title') ?></strong>
                        <?= app_translate('home.panel.browse.body') ?>
                    </div>
                    <div class="mini-card">
                        <strong><?= app_translate('home.panel.borrow.title') ?></strong>
                        <?= app_translate('home.panel.borrow.body') ?>
                    </div>
                    <div class="mini-card">
                        <strong><?= app_translate('home.panel.news.title') ?></strong>
                        <?= app_translate('home.panel.news.body') ?>
                    </div>
                    <div class="mini-card">
                        <strong><?= app_translate('home.panel.roles.title') ?></strong>
                        <?= app_translate('home.panel.roles.body') ?>
                    </div>
                    <div class="mini-card">
                        <strong><?= app_translate('home.panel.language.title') ?></strong>
                        <?= app_translate('home.panel.language.body') ?>
                    </div>
                </div>
            </aside>
        </section>

        <section class="section" id="featured-books">
            <div class="section-header">
                <div>
                    <h2><?= app_translate('home.featured.title') ?></h2>
                    <p><?= app_translate('home.featured.body') ?></p>
                </div>
                <a href="<?= $basePath ?>/Controllers/BookIndexController.php"><?= app_translate('home.featured.catalog') ?></a>
            </div>

            <div class="section-card">
                <?php if (empty($featuredBooks)): ?>
                    <div class="empty-state"><?= app_translate('home.featured.empty') ?></div>
                <?php else: ?>
                    <div class="grid-books">
                        <?php foreach ($featuredBooks as $book): ?>
                            <article class="book-card">
                                <div class="book-cover">
                                    <?php if (!empty($book['cover_image_path'])): ?>
                                        <img src="<?= $basePath . '/' . htmlspecialchars($book['cover_image_path']) ?>" alt="<?= htmlspecialchars($book['title']) ?> cover">
                                    <?php else: ?>
                                        <span>No cover image</span>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <h3 style="font-size: 1.45rem; margin-bottom: 10px;"><?= htmlspecialchars($book['title'] ?? '') ?></h3>
                                    <div class="book-meta">
                                        <span class="pill"><?= htmlspecialchars($book['genre_name'] ?? 'Uncategorized') ?></span>
                                        <span class="pill">By <?= htmlspecialchars($book['author'] ?? 'Unknown') ?></span>
                                    </div>
                                </div>

                                <p><?= htmlspecialchars($book['description'] ?? 'Discover this title from the library catalog.') ?></p>

                                <div class="book-meta">
                                    <span class="pill">Available: <?= htmlspecialchars((string)($book['total_available'] ?? 0)) ?></span>
                                    <span class="pill">Total: <?= htmlspecialchars((string)($book['total_stock'] ?? 0)) ?></span>
                                    <span class="pill">Year: <?= htmlspecialchars((string)($book['published_year'] ?? 'N/A')) ?></span>
                                </div>

                                <form class="inline-form" method="POST" action="<?= $basePath ?>/Controllers/BookDetailsController.php">
                                    <input type="hidden" name="id" value="<?= (int)($book['id'] ?? 0) ?>">
                                    <button type="submit" class="btn btn-outline"><?= app_translate('home.featured.details') ?></button>
                                </form>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="section" id="announcements">
            <div class="section-header">
                <div>
                    <h2><?= app_translate('home.announcements.title') ?></h2>
                    <p><?= app_translate('home.announcements.body') ?></p>
                </div>
            </div>

            <div class="section-card announcements">
                <?php if (empty($announcements)): ?>
                    <div class="empty-state"><?= app_translate('home.announcements.empty') ?></div>
                <?php else: ?>
                    <?php foreach ($announcements as $announcement): ?>
                        <article class="announcement-card">
                            <h3 style="font-size: 1.35rem; margin-bottom: 10px;"><?= htmlspecialchars($announcement['title'] ?? '') ?></h3>
                            <p style="margin: 0; color: var(--muted); line-height: 1.8;"><?= htmlspecialchars($announcement['body'] ?? '') ?></p>
                            <small>
                                <?= htmlspecialchars($announcement['author_name'] ?? 'Library Staff') ?>
                                <?php if (!empty($announcement['published_at'])): ?>
                                    · <?= htmlspecialchars($announcement['published_at']) ?>
                                <?php endif; ?>
                            </small>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="section two-col">
            <div class="about-box" id="about">
                <h2 style="margin-bottom: 12px;"><?= app_translate('home.about.title') ?></h2>
                <p>
                    <?= app_translate('home.about.body1') ?>
                </p>
                <p>
                    <?= app_translate('home.about.body2') ?>
                </p>
            </div>

            <div class="contact-box" id="contact">
                <h2 style="margin-bottom: 12px;"><?= app_translate('home.contact.title') ?></h2>
                <p><strong><?= app_translate('home.contact.desk') ?></strong> support@library.local</p>
                <p><strong><?= app_translate('home.contact.phone') ?></strong> +1 (555) 010-2048</p>
                <p><strong><?= app_translate('home.contact.hours') ?></strong> Mon - Sat, 8:00 AM to 8:00 PM</p>
                <p style="margin-bottom: 0;">
                    <?= app_translate('home.contact.body') ?>
                </p>
            </div>
        </section>

        <footer>
            <div><?= app_translate('home.footer.line1') ?></div>
            <div style="margin-top: 8px;"><?= app_translate('home.footer.line2') ?></div>
        </footer>
    </div>
</body>
</html>