<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

$books = $_SESSION['books'] ?? [];
$genres = $_SESSION['genres'] ?? [];
$branches = $_SESSION['branches'] ?? [];

$search = $_SESSION['book_search'] ?? '';
$selected_genre = $_SESSION['book_genre_id'] ?? '';
$selected_branch = $_SESSION['book_branch_id'] ?? '';
$selected_year = $_SESSION['book_year'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Catalog</title>
    <link rel="stylesheet" href="<?= Url::asset('Views/css/member.css') ?>?v=<?= time() ?>">
    <style>
        /* Force spacing and inline horizontal display */
        .search-form {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 16px !important;
            max-width: 100% !important;
            align-items: center !important;
            background: #1e2235 !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            border-radius: 12px !important;
            padding: 24px !important;
            margin-bottom: 24px !important;
        }

        .search-form input[type="text"] {
            flex: 2 !important;
            min-width: 220px !important;
            max-width: none !important;
            margin-bottom: 0 !important;
        }

        .search-form select {
            flex: 1 !important;
            min-width: 150px !important;
            max-width: none !important;
            margin-bottom: 0 !important;
        }

        .search-form input[type="number"] {
            flex: 0.5 !important;
            min-width: 80px !important;
            max-width: none !important;
            margin-bottom: 0 !important;
        }

        .search-form button {
            padding: 10px 24px !important;
            flex: initial !important;
            width: auto !important;
            margin-bottom: 0 !important;
        }

        .search-form .btn-clear {
            color: #f59e0b !important;
            font-weight: 600 !important;
            margin-left: 8px !important;
            text-decoration: underline !important;
        }

        .search-form .btn-clear:hover {
            color: #fbbf24 !important;
        }

        /* Styled button link for View Details */
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
        }

        .btn-link:hover {
            background: #f59e0b !important;
            color: #fff !important;
            text-decoration: none !important;
        }
    </style>
</head>

<body>

<h2>Available Books</h2>

<a href="dashboardView.php">← Back to Dashboard</a>

<hr>

<form novalidate class="search-form" action="/LibraryManagementSystem/Controllers/BookIndexController.php" method="POST" onsubmit="event.preventDefault(); ajaxSearchBooks();">
    <input type="text" id="bookSearch" name="search" placeholder="Search title, author, ISBN..." value="<?= htmlspecialchars($search) ?>" onkeyup="ajaxSearchBooks()">
    
    <select id="bookGenre" name="genre_id" onchange="ajaxSearchBooks()">
        <option value="">All Genres</option>
        <?php foreach ($genres as $genre) { ?>
            <option value="<?= $genre['id'] ?>" <?= $selected_genre == $genre['id'] ? 'selected' : '' ?>>
                <?= $genre['name'] ?>
            </option>
        <?php } ?>
    </select>

    <select id="bookBranch" name="branch_id" onchange="ajaxSearchBooks()">
        <option value="">All Branches</option>
        <?php foreach ($branches as $branch) { ?>
            <option value="<?= $branch['id'] ?>" <?= $selected_branch == $branch['id'] ? 'selected' : '' ?>>
                <?= $branch['name'] ?>
            </option>
        <?php } ?>
    </select>

    <input type="number" id="bookYear" name="year" placeholder="Year" value="<?= htmlspecialchars($selected_year) ?>" onkeyup="ajaxSearchBooks()" onchange="ajaxSearchBooks()">

    <button type="submit">Search</button>
    <a href="/LibraryManagementSystem/Controllers/BookIndexController.php" class="btn-clear">Clear</a>
</form>

<hr>

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
            <tr><td colspan="7">No books found.</td></tr>
        <?php endif; ?>

        <?php foreach ($books as $book) { ?>
        <tr>
            <td>
                <?php if (isset($book['cover_image_path']) && $book['cover_image_path'] != '') { ?>
                    <img src="/LibraryManagementSystem/<?php echo htmlspecialchars($book['cover_image_path']); ?>" alt="Book Cover" width="52" style="border-radius:6px; object-fit:cover;">
                <?php } else { ?>
                    <span style="font-size:12px;color:#9ca3af;">No cover</span>
                <?php } ?>
            </td>
            <td><?= $book['title'] ?></td>
            <td><?= $book['author'] ?></td>
            <td><?= $book['genre_name'] ?? 'N/A' ?></td>
            <td><?= $book['isbn'] ?></td>
            <td><?= $book['published_year'] ?></td>
            <td>
                <form method="POST" action="/LibraryManagementSystem/Controllers/BookDetailsController.php" style="display:inline;"><input type="hidden" name="id" value="<?= $book['id'] ?>"><button type="submit" class="btn-link">View Details</button></form>
            </td>
        </tr>
        <?php } ?>
    </tbody>

</table>

<script src="../js/book_search.js"></script>

</body>
</html>

