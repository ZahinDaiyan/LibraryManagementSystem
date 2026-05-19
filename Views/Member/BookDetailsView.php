<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

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
    </style>
</head>

<body>

<h2>Book Details</h2>

<a href="BookIndexView.php">← Back to Catalog</a>

<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<h3><?= $book['title'] ?></h3>

<?php if (isset($_SESSION['in_reading_list']) && $_SESSION['in_reading_list']): ?>
    <form method="POST" action="/LibraryManagementSystem/Controllers/ReadingListActionController.php" style="display:inline;"><input type="hidden" name="action" value="remove"><input type="hidden" name="book_id" value="<?= $book['id'] ?>"><input type="hidden" name="redirect" value="details"><button type="submit" class="btn-link">Remove from Reading List</button></form>
<?php else: ?>
    <form method="POST" action="/LibraryManagementSystem/Controllers/ReadingListActionController.php" style="display:inline;"><input type="hidden" name="action" value="add"><input type="hidden" name="book_id" value="<?= $book['id'] ?>"><input type="hidden" name="redirect" value="details"><button type="submit" class="btn-link">Add to Reading List</button></form>
<?php endif; ?>

<p><b>Average Rating:</b> <?= number_format($rating_info['avg_rating'], 1) ?> / 5 (<?= $rating_info['review_count'] ?> reviews)</p>

<p><b>Author:</b> <?= $book['author'] ?></p>
<p><b>Genre:</b> <?= $book['genre_name'] ?? 'N/A' ?></p>
<p><b>ISBN:</b> <?= $book['isbn'] ?></p>
<p><b>Publisher:</b> <?= $book['publisher'] ?></p>
<p><b>Year:</b> <?= $book['published_year'] ?></p>
<p><b>Description:</b> <?= $book['description'] ?></p>

<hr>

<h3>Availability by Branch</h3>

<table border="1" cellpadding="10">

<tr>
    <th>Branch</th>
    <th>Total Copies</th>
    <th>Available</th>
    <th>Action</th>
</tr>

<?php foreach ($availability as $a) { ?>

<tr>
    <td><?= $a['branch_name'] ?></td>
    <td><?= $a['total_copies'] ?></td>
    <td><?= $a['available_copies'] ?></td>
    <td>
        <?php if ($a['available_copies'] > 0) { ?>
            <form method="POST" action="/LibraryManagementSystem/Controllers/BorrowRequestController.php" style="display:inline;"><input type="hidden" name="book_id" value="<?= $book['id'] ?>"><input type="hidden" name="branch_id" value="<?= $a['branch_id'] ?>"><button type="submit" class="btn-link">Request Borrow</button></form>
        <?php } else { ?>
            <span style="color:red; margin-right: 8px;">Not Available</span> | 
            <form method="POST" action="/LibraryManagementSystem/Controllers/ReservationActionController.php" style="display:inline;"><input type="hidden" name="action" value="reserve"><input type="hidden" name="book_id" value="<?= $book['id'] ?>"><input type="hidden" name="branch_id" value="<?= $a['branch_id'] ?>"><button type="submit" class="btn-link">Join Waitlist</button></form>
        <?php } ?>
    </td>
</tr>

<?php } ?>

</table>

<hr>

<h3>Member Reviews</h3>

<?php foreach ($reviews as $r) { ?>
    <div style="border-bottom: 1px solid #ccc; padding: 10px;">
        <p><b><?= $r['member_name'] ?></b> rated it <b><?= $r['rating'] ?>/5</b></p>
        <p><?= $r['review_text'] ?></p>
        <small><?= $r['created_at'] ?></small>
        
        <?php if ($r['member_id'] == $_SESSION['id']) { ?>
            <form novalidate action="/LibraryManagementSystem/Controllers/BookReviewController.php" method="POST" style="display:inline;">
                <input type="hidden" name="action" value="delete_review">
                <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                <button type="submit" onclick="return confirm('Delete review?')">Delete</button>
            </form>
        <?php } ?>
    </div>
<?php } ?>

<?php if (empty($reviews)) echo "<p>No reviews yet.</p>"; ?>

<hr>

<h3>Write a Review</h3>
<form novalidate action="/LibraryManagementSystem/Controllers/BookReviewController.php" method="POST" onsubmit="return validateReviewForm(this)">
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

<script src="../js/member_validation.js"></script>
</body>
</html>

