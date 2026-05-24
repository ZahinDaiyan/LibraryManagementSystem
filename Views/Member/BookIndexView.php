<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/i18n.php';

$lang = app_current_language();

$role = $_SESSION['role'] ?? '';
$isLoggedIn = $role !== '';
$isMember = $role === 'member';
$homeHref = $isLoggedIn && $isMember ? 'dashboardView.php' : '../../index.php';
$homeLabel = $isLoggedIn && $isMember ? app_translate('member.book.back_dashboard') : app_translate('member.book.back_home');

$books = $_SESSION['books'] ?? [];
$genres = $_SESSION['genres'] ?? [];
$branches = $_SESSION['branches'] ?? [];

$search = $_SESSION['book_search'] ?? '';
$selected_genre = $_SESSION['book_genre_id'] ?? '';
$selected_branch = $_SESSION['book_branch_id'] ?? '';
$selected_year = $_SESSION['book_year'] ?? '';
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= app_translate('member.book.title') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/member.css?v=<?= time() ?>">
    <style>
        :root {
            --catalog-bg: #0b1220;
            --catalog-surface: rgba(19, 28, 47, 0.94);
            --catalog-surface-soft: rgba(255, 255, 255, 0.04);
            --catalog-line: rgba(255, 255, 255, 0.1);
            --catalog-text: #eef2ff;
            --catalog-muted: #a8b3c7;
            --catalog-accent: #f59e0b;
            --catalog-accent-strong: #fbbf24;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(245, 158, 11, 0.16), transparent 22%),
                radial-gradient(circle at left top, rgba(59, 130, 246, 0.12), transparent 26%),
                linear-gradient(180deg, #07101d 0%, #0b1220 100%);
            color: var(--catalog-text);
            color-scheme: dark;
        }

        body.lang-bn {
            font-family: 'Noto Sans Bengali', var(--font-body, sans-serif);
        }

        .page-shell {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 24px 0 48px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 18px;
            padding: 18px 20px;
            border: 1px solid var(--catalog-line);
            border-radius: 20px;
            background: rgba(8, 15, 27, 0.78);
            backdrop-filter: blur(16px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.24);
        }

        .page-header h2 {
            margin: 0 0 6px;
            font-size: clamp(1.7rem, 2.6vw, 2.4rem);
        }

        .page-header p {
            margin: 0;
            color: var(--catalog-muted);
            line-height: 1.6;
        }

        .page-header a {
            color: var(--catalog-accent-strong);
            font-weight: 700;
            text-decoration: none;
        }

        .page-header a:hover {
            color: #fff;
        }

        .lang-switch {
            display: inline-flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .lang-switch a {
            color: var(--catalog-accent-strong);
            border: 1px solid rgba(245, 158, 11, 0.35);
            padding: 8px 12px;
            border-radius: 999px;
            text-decoration: none;
        }

        .lang-switch a.active {
            background: rgba(245, 158, 11, 0.14);
            color: #fff;
        }

        hr {
            border: 0;
            border-top: 1px solid var(--catalog-line);
            margin: 20px 0;
        }

        .search-form {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 16px !important;
            max-width: 100% !important;
            align-items: center !important;
            background: var(--catalog-surface) !important;
            border: 1px solid var(--catalog-line) !important;
            border-radius: 18px !important;
            padding: 20px !important;
            margin-bottom: 24px !important;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
        }

        .search-form input[type="text"] {
            flex: 2 !important;
            min-width: 220px !important;
            max-width: none !important;
            margin-bottom: 0 !important;
            background: rgba(255, 255, 255, 0.06) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: var(--catalog-text) !important;
            border-radius: 12px !important;
            padding: 12px 14px !important;
        }

        .search-form select {
            flex: 1 !important;
            min-width: 150px !important;
            max-width: none !important;
            margin-bottom: 0 !important;
            background: rgba(255, 255, 255, 0.06) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: var(--catalog-text) !important;
            border-radius: 12px !important;
            padding: 12px 14px !important;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: linear-gradient(45deg, transparent 50%, var(--catalog-accent-strong) 50%), linear-gradient(135deg, var(--catalog-accent-strong) 50%, transparent 50%);
            background-position: calc(100% - 18px) calc(50% + 2px), calc(100% - 12px) calc(50% + 2px);
            background-size: 6px 6px, 6px 6px;
            background-repeat: no-repeat;
            color-scheme: dark;
            font-family: inherit;
            font-size: 0.98rem;
            font-weight: 700;
        }

        .search-form select option {
            background: #0f172a !important;
            color: #f8fafc !important;
            font-family: inherit;
            font-size: 0.98rem;
            font-weight: 600;
        }

        .search-form select:focus,
        .search-form input[type="text"]:focus,
        .search-form input[type="number"]:focus {
            outline: none !important;
            border-color: rgba(251, 191, 36, 0.65) !important;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.12) !important;
        }

        .search-form input[type="number"] {
            flex: 0.5 !important;
            min-width: 80px !important;
            max-width: none !important;
            margin-bottom: 0 !important;
            background: rgba(255, 255, 255, 0.06) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: var(--catalog-text) !important;
            border-radius: 12px !important;
            padding: 12px 14px !important;
        }

        .search-form button {
            padding: 12px 22px !important;
            flex: initial !important;
            width: auto !important;
            margin-bottom: 0 !important;
            border: 0 !important;
            border-radius: 999px !important;
            background: linear-gradient(135deg, var(--catalog-accent), var(--catalog-accent-strong)) !important;
            color: #111827 !important;
            font-weight: 800 !important;
            box-shadow: 0 12px 24px rgba(245, 158, 11, 0.2);
        }

        .search-form .btn-clear {
            color: var(--catalog-accent-strong) !important;
            font-weight: 600 !important;
            margin-left: 8px !important;
            text-decoration: underline !important;
        }

        .search-form .btn-clear:hover {
            color: #fff !important;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid var(--catalog-line);
            background: rgba(8, 15, 27, 0.78);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.18);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 840px;
            color: var(--catalog-text);
        }

        thead th {
            text-align: left;
            font-size: 0.85rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--catalog-muted);
            background: rgba(255, 255, 255, 0.03);
        }

        th, td {
            border-color: rgba(255, 255, 255, 0.08) !important;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        td {
            color: #e8edf8;
        }

        td span[style*="No cover"] {
            color: var(--catalog-muted) !important;
        }

        .cover-thumb {
            width: 54px;
            height: 72px;
            object-fit: cover;
            border-radius: 10px;
            display: block;
            box-shadow: 0 10px 16px rgba(0, 0, 0, 0.22);
        }

        .btn-link {
            background: transparent !important;
            border: 1px solid var(--catalog-accent) !important;
            color: var(--catalog-accent-strong) !important;
            padding: 9px 15px !important;
            font-size: 0.85rem !important;
            font-weight: 700 !important;
            border-radius: 999px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        .btn-link:hover {
            background: linear-gradient(135deg, var(--catalog-accent), var(--catalog-accent-strong)) !important;
            color: #111827 !important;
            text-decoration: none !important;
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.18);
        }

        .empty-row td {
            text-align: center;
            color: var(--catalog-muted);
            padding: 24px !important;
        }

        .book-meta-note {
            margin-top: 10px;
            color: var(--catalog-muted);
        }

        .page-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-link {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        @media (max-width: 900px) {
            .page-header {
                padding: 16px;
            }

            .search-form {
                padding: 16px !important;
            }

            .search-form input[type="text"],
            .search-form select,
            .search-form input[type="number"] {
                min-width: 100% !important;
            }
        }

        @media (max-width: 640px) {
            .page-shell {
                width: min(100% - 20px, 1180px);
            }

            .page-header h2 {
                font-size: 1.55rem;
            }

            .page-header p {
                font-size: 0.95rem;
            }

            .search-form button,
            .search-form .btn-clear {
                width: 100% !important;
                margin-left: 0 !important;
                text-align: center;
            }

            .page-actions {
                width: 100%;
            }
        }
    </style>
</head>

<body class="<?= $lang === 'bn' ? 'lang-bn' : 'lang-en' ?>">

<div class="page-shell">
    <div class="page-header">
        <div>
            <h2><?= app_translate('member.book.title') ?></h2>
            <p><?= app_translate('home.panel.browse.body') ?></p>
            <div class="book-meta-note"><?= app_translate('home.panel.caption') ?></div>
        </div>
        <div class="page-actions">
            <div class="lang-switch" aria-label="<?= app_translate('language.switch') ?>">
                <a class="<?= $lang === 'en' ? 'active' : '' ?>" href="<?= app_language_url('BookIndexView.php', 'en') ?>"><?= app_translate('language.english') ?></a>
                <a class="<?= $lang === 'bn' ? 'active' : '' ?>" href="<?= app_language_url('BookIndexView.php', 'bn') ?>"><?= app_translate('language.bengali') ?></a>
            </div>
            <a href="<?= htmlspecialchars($homeHref) ?>"><?= htmlspecialchars($homeLabel) ?></a>
        </div>
    </div>

    <form novalidate class="search-form" action="../../Controllers/BookIndexController.php" method="POST" onsubmit="event.preventDefault(); ajaxSearchBooks();">
        <input type="text" id="bookSearch" name="search" placeholder="Search title, author, ISBN..." value="<?= htmlspecialchars($search) ?>" onkeyup="ajaxSearchBooks()">

        <select id="bookGenre" name="genre_id" onchange="ajaxSearchBooks()">
            <option value="">All Genres</option>
            <?php foreach ($genres as $genre) { ?>
                <option value="<?= $genre['id'] ?>" <?= $selected_genre == $genre['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($genre['name']) ?>
                </option>
            <?php } ?>
        </select>

        <select id="bookBranch" name="branch_id" onchange="ajaxSearchBooks()">
            <option value="">All Branches</option>
            <?php foreach ($branches as $branch) { ?>
                <option value="<?= $branch['id'] ?>" <?= $selected_branch == $branch['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($branch['name']) ?>
                </option>
            <?php } ?>
        </select>

        <input type="number" id="bookYear" name="year" placeholder="Year" value="<?= htmlspecialchars($selected_year) ?>" onkeyup="ajaxSearchBooks()" onchange="ajaxSearchBooks()">

        <button type="submit">Search</button>
        <a href="../../Controllers/BookIndexController.php" class="btn-clear">Clear</a>
    </form>

    <div class="table-wrap">
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>ISBN</th>
                    <th>Year</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="bookTableBody">
                <?php if (empty($books)): ?>
                    <tr class="empty-row"><td colspan="7">No books found.</td></tr>
                <?php endif; ?>

                <?php foreach ($books as $book) { ?>
                <tr>
                    <td>
                        <?php if (!empty($book['cover_image_path'])) { ?>
                            <img class="cover-thumb" src="../../<?php echo htmlspecialchars($book['cover_image_path']); ?>" alt="Book Cover">
                        <?php } else { ?>
                            <span style="font-size:12px;color:#9ca3af;">No cover</span>
                        <?php } ?>
                    </td>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['genre_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($book['isbn']) ?></td>
                    <td><?= htmlspecialchars((string)$book['published_year']) ?></td>
                    <td>
                        <form method="POST" action="../../Controllers/BookDetailsController.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $book['id'] ?>">
                            <button type="submit" class="btn-link">View Details</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="../js/book_search.js"></script>
</div>

</body>
</html>

