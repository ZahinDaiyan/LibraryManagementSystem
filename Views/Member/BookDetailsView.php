<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? '';
$isMember = $role === 'member';
$isLoggedIn = $role !== '';
$homeHref = $isLoggedIn && $isMember ? 'BookIndexView.php' : '../../Controllers/BookIndexController.php';
$homeLabel = $isLoggedIn && $isMember ? '← Back to Catalog' : '← Back to Books';

$book = $_SESSION['book'] ?? null;
$availability = $_SESSION['availability'] ?? [];
$reviews = $_SESSION['reviews'] ?? [];
$rating_info = $_SESSION['rating_info'] ?? ['avg_rating' => 0, 'review_count' => 0];

$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);

if (!$book) {
    header("Location: BookIndexView.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Details</title>
    <link rel="stylesheet" href="../css/member.css?v=<?= time() ?>">
    <style>
        :root {
            --details-bg: #0b1220;
            --details-surface: rgba(19, 28, 47, 0.94);
            --details-surface-soft: rgba(255, 255, 255, 0.04);
            --details-line: rgba(255, 255, 255, 0.1);
            --details-text: #eef2ff;
            --details-muted: #a8b3c7;
            --details-accent: #f59e0b;
            --details-accent-strong: #fbbf24;
            --details-danger: #ef4444;
            --details-success: #22c55e;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(245, 158, 11, 0.16), transparent 22%),
                radial-gradient(circle at left top, rgba(59, 130, 246, 0.12), transparent 26%),
                linear-gradient(180deg, #07101d 0%, #0b1220 100%);
            color: var(--details-text);
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
            border: 1px solid var(--details-line);
            border-radius: 20px;
            background: rgba(8, 15, 27, 0.78);
            backdrop-filter: blur(16px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.24);
        }

        .page-header h2 {
            margin: 0 0 6px;
            font-size: clamp(1.7rem, 2.6vw, 2.4rem);
        }

        .page-header a {
            color: var(--details-accent-strong);
            font-weight: 700;
            text-decoration: none;
        }

        .page-header a:hover {
            color: #fff;
        }

        hr {
            border: 0;
            border-top: 1px solid var(--details-line);
            margin: 20px 0;
        }

        .alert-msg {
            padding: 12px 14px;
            border-radius: 14px;
            margin-bottom: 14px;
            border: 1px solid transparent;
            background: rgba(255, 255, 255, 0.04);
        }

        .alert-msg.success {
            border-color: rgba(34, 197, 94, 0.35);
            color: #bbf7d0;
        }

        .alert-msg.error {
            border-color: rgba(239, 68, 68, 0.35);
            color: #fecaca;
        }

        .book-top-section {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 24px;
            align-items: start;
            padding: 22px;
            border-radius: 24px;
            background: var(--details-surface);
            border: 1px solid var(--details-line);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.18);
        }

        .book-cover-card {
            border: 1px solid var(--details-line);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.04);
            padding: 12px;
            text-align: center;
            min-height: 100%;
        }

        .book-cover-card img {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            border-radius: 14px;
            display: block;
        }

        .book-cover-empty {
            font-size: 0.95rem;
            color: var(--details-muted);
            padding: 48px 12px;
            min-height: 260px;
            display: grid;
            place-items: center;
        }

        .book-title {
            font-size: clamp(2rem, 3vw, 3rem);
            margin-bottom: 12px;
        }

        .login-hint {
            margin-top: 0;
            color: var(--details-muted);
        }

        .book-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 18px 0 22px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #edf2ff;
            font-size: 0.9rem;
        }

        .meta-list {
            display: grid;
            gap: 10px;
            margin-top: 18px;
        }

        .meta-item {
            padding: 14px 16px;
            border-radius: 16px;
            background: var(--details-surface-soft);
            border: 1px solid rgba(255, 255, 255, 0.06);
            color: var(--details-text);
            line-height: 1.7;
        }

        .meta-item b {
            color: #fff;
        }

        .section-card {
            margin-top: 22px;
            padding: 22px;
            border-radius: 24px;
            background: var(--details-surface);
            border: 1px solid var(--details-line);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.14);
        }

        .section-card h3 {
            margin-top: 0;
            margin-bottom: 16px;
            font-size: 1.5rem;
        }

        .availability-table,
        .reviews-list {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
            color: var(--details-text);
        }

        thead th {
            text-align: left;
            font-size: 0.85rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--details-muted);
            background: rgba(255, 255, 255, 0.03);
        }

        th, td {
            border-color: rgba(255, 255, 255, 0.08) !important;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .btn-link {
            background: transparent !important;
            border: 1px solid var(--details-accent) !important;
            color: var(--details-accent-strong) !important;
            padding: 9px 15px !important;
            font-size: 0.85rem !important;
            font-weight: 700 !important;
            border-radius: 999px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .btn-link:hover {
            background: linear-gradient(135deg, var(--details-accent), var(--details-accent-strong)) !important;
            color: #111827 !important;
            text-decoration: none !important;
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.18);
        }

        .availability-note {
            color: var(--details-muted);
        }

        .availability-note strong {
            color: #fff;
        }

        .availability-empty {
            padding: 18px;
            border-radius: 16px;
            border: 1px dashed rgba(255, 255, 255, 0.16);
            color: var(--details-muted);
            background: rgba(255, 255, 255, 0.03);
        }

        .review-card {
            padding: 16px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 14px;
        }

        .review-card p {
            margin: 10px 0;
            color: #dfe7f6;
            line-height: 1.7;
        }

        .review-card small {
            color: var(--details-muted);
        }

        .review-form {
            display: grid;
            gap: 14px;
        }

        .review-form label {
            display: inline-block;
            margin-bottom: 8px;
            color: #fff;
            font-weight: 600;
        }

        .review-form select,
        .review-form textarea {
            width: 100%;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.06);
            color: var(--details-text);
            padding: 12px 14px;
            outline: none;
        }

        .review-form textarea {
            min-height: 120px;
            resize: vertical;
        }

        .review-form button {
            width: fit-content;
            border: 0;
            border-radius: 999px;
            padding: 12px 20px;
            background: linear-gradient(135deg, var(--details-accent), var(--details-accent-strong));
            color: #111827;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 12px 24px rgba(245, 158, 11, 0.2);
        }

        .review-form button:hover {
            transform: translateY(-1px);
        }

        .empty-state {
            padding: 18px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px dashed rgba(255, 255, 255, 0.18);
            color: var(--details-muted);
        }

        @media (max-width: 900px) {
            .book-top-section {
                grid-template-columns: 1fr;
            }

            .book-cover-card {
                order: -1;
            }
        }

        @media (max-width: 640px) {
            .page-shell {
                width: min(100% - 20px, 1180px);
            }

            .page-header,
            .book-top-section,
            .section-card {
                padding: 16px;
                border-radius: 18px;
            }

            .book-title {
                font-size: 1.8rem;
            }

            table {
                min-width: 640px;
            }
        }

        /* Golden outline button style for links */
        .btn-link {
            background: transparent !important;
            border: 1px solid #f59e0b !important;
            color: #f59e0b !important;
            padding: 6px 14px !important;
            font-size: 0.8rem !important;
            font-weight: 500 !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: inline-block !important;
        }

        .btn-link:hover {
            background: #f59e0b !important;
            color: #fff !important;
            text-decoration: none !important;
        }

        .book-top-section {
            display: grid;
            grid-template-columns: minmax(320px, 1fr) 280px;
            gap: 28px;
            align-items: start;
        }

        .book-cover-card {
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
            padding: 12px;
            text-align: center;
        }

        .book-cover-card img {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            border-radius: 8px;
            display: block;
        }

        .book-cover-empty {
            font-size: 0.9rem;
            color: #9ca3af;
            padding: 40px 12px;
        }

        @media (max-width: 900px) {
            .book-top-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
<div class="page-shell">
    <div class="page-header">
        <div>
            <h2>Book Details</h2>
            <div class="availability-note">Explore the title, see branch stock, and borrow after signing in.</div>
        </div>
        <a href="<?= $homeHref ?>"><?= $homeLabel ?></a>
    </div>

    <?php if ($msg): ?>
        <div class="alert-msg success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert-msg error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="book-top-section">
        <div>
            <h3 class="book-title"><?= htmlspecialchars($book['title']) ?></h3>

            <?php if ($isMember): ?>
                <?php if (isset($_SESSION['in_reading_list']) && $_SESSION['in_reading_list']): ?>
                    <form method="POST" action="../../Controllers/ReadingListActionController.php" style="display:inline;"><input type="hidden" name="action" value="remove"><input type="hidden" name="book_id" value="<?= $book['id'] ?>"><input type="hidden" name="redirect" value="details"><button type="submit" class="btn-link">Remove from Reading List</button></form>
                <?php else: ?>
                    <form method="POST" action="../../Controllers/ReadingListActionController.php" style="display:inline;"><input type="hidden" name="action" value="add"><input type="hidden" name="book_id" value="<?= $book['id'] ?>"><input type="hidden" name="redirect" value="details"><button type="submit" class="btn-link">Add to Reading List</button></form>
                <?php endif; ?>
            <?php else: ?>
                <p class="login-hint">Login to save this book, borrow it, or leave a review.</p>
            <?php endif; ?>

            <div class="book-stats">
                <span class="pill"><b>Average Rating:</b>&nbsp;<?= number_format((float)$rating_info['avg_rating'], 1) ?> / 5</span>
                <span class="pill"><?= (int)$rating_info['review_count'] ?> reviews</span>
            </div>

            <div class="meta-list">
                <div class="meta-item"><b>Author:</b> <?= htmlspecialchars($book['author']) ?></div>
                <div class="meta-item"><b>Genre:</b> <?= htmlspecialchars($book['genre_name'] ?? 'N/A') ?></div>
                <div class="meta-item"><b>ISBN:</b> <?= htmlspecialchars($book['isbn']) ?></div>
                <div class="meta-item"><b>Publisher:</b> <?= htmlspecialchars($book['publisher']) ?></div>
                <div class="meta-item"><b>Year:</b> <?= htmlspecialchars((string)$book['published_year']) ?></div>
                <div class="meta-item"><b>Description:</b> <?= htmlspecialchars($book['description']) ?></div>
            </div>
        </div>

        <div class="book-cover-card">
            <?php if (isset($book['cover_image_path']) && $book['cover_image_path'] != '') { ?>
                <img src="../../<?= htmlspecialchars($book['cover_image_path']) ?>" alt="<?= htmlspecialchars($book['title']) ?> Cover">
            <?php } else { ?>
                <div class="book-cover-empty">No cover image uploaded</div>
            <?php } ?>
        </div>
    </div>

    <div class="section-card">
        <h3>Availability by Branch</h3>
        <?php if (empty($availability)): ?>
            <div class="availability-empty">No branch inventory has been assigned to this book yet.</div>
        <?php else: ?>
            <div class="availability-table">
                <table border="1" cellpadding="10">
                    <tr>
                        <th>Branch</th>
                        <th>Total Copies</th>
                        <th>Available</th>
                        <th>Action</th>
                    </tr>

                    <?php foreach ($availability as $a) { ?>

                    <tr>
                        <td><?= htmlspecialchars($a['branch_name']) ?></td>
                        <td><?= htmlspecialchars((string)$a['total_copies']) ?></td>
                        <td><?= htmlspecialchars((string)$a['available_copies']) ?></td>
                        <td>
                            <?php if ($a['available_copies'] > 0) { ?>
                                <form method="POST" action="../../Controllers/BorrowRequestController.php" style="display:inline;"><input type="hidden" name="book_id" value="<?= $book['id'] ?>"><input type="hidden" name="branch_id" value="<?= $a['branch_id'] ?>"><button type="submit" class="btn-link">Borrow Book</button></form>
                            <?php } else { ?>
                                <span style="color: var(--details-danger); margin-right: 8px;">Not Available</span> |
                                <?php if ($isMember): ?>
                                    <form method="POST" action="../../Controllers/ReservationActionController.php" style="display:inline;"><input type="hidden" name="action" value="reserve"><input type="hidden" name="book_id" value="<?= $book['id'] ?>"><input type="hidden" name="branch_id" value="<?= $a['branch_id'] ?>"><button type="submit" class="btn-link">Join Waitlist</button></form>
                                <?php else: ?>
                                    <a href="../../Views/LoginView.php">Login to reserve</a>
                                <?php endif; ?>
                            <?php } ?>
                        </td>
                    </tr>

                    <?php } ?>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="section-card">
        <h3>Member Reviews</h3>

        <?php if (empty($reviews)): ?>
            <div class="empty-state">No reviews yet.</div>
        <?php else: ?>
            <div class="reviews-list">
                <?php foreach ($reviews as $r) { ?>
                    <div class="review-card">
                        <p><b><?= htmlspecialchars($r['member_name']) ?></b> rated it <b><?= htmlspecialchars((string)$r['rating']) ?>/5</b></p>
                        <p><?= htmlspecialchars($r['review_text']) ?></p>
                        <small><?= htmlspecialchars($r['created_at']) ?></small>
                        
                        <?php if ($isMember && $r['member_id'] == $_SESSION['id']) { ?>
                            <form novalidate action="../../Controllers/BookReviewController.php" method="POST" style="display:inline; margin-top: 12px;">
                                <input type="hidden" name="action" value="delete_review">
                                <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <button type="submit" class="btn-link" onclick="return confirm('Delete review?')">Delete</button>
                            </form>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($isMember): ?>
        <div class="section-card">
            <h3>Write a Review</h3>
            <form class="review-form" novalidate action="../../Controllers/BookReviewController.php" method="POST" onsubmit="return validateReviewForm(this)">
                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                <input type="hidden" name="action" value="submit_review">
                
                <p>
                    <label>Rating:</label><br>
                    <select name="rating" required>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Very Good</option>
                        <option value="3">3 - Good</option>
                        <option value="2">2 - Fair</option>
                        <option value="1">1 - Poor</option>
                    </select>
                </p>
                
                <p>
                    <label>Comment:</label><br>
                    <textarea name="comment" rows="4" cols="50" required></textarea>
                </p>
                
                <button type="submit">Submit Review</button>
            </form>
        </div>
    <?php else: ?>
        <div class="section-card">
            <div class="empty-state">Sign in to add your own review and join the reading list.</div>
        </div>
    <?php endif; ?>
</div>

<script src="../js/member_validation.js"></script>
<script src="../js/member_ajax.js?v=<?= time() ?>"></script>
</body>
</html>

