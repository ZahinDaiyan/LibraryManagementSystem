<?php
session_start();

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
    <link rel="stylesheet" href="../css/member.css">
</head>

<body>

<h2>Available Books</h2>

<a href="dashboardView.php">← Back to Dashboard</a>

<hr>

<form novalidate action="../../Controllers/BookIndexController.php" method="POST" onsubmit="event.preventDefault(); ajaxSearchBooks();">
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

    <input type="number" id="bookYear" name="year" placeholder="Year" value="<?= htmlspecialchars($selected_year) ?>" style="width: 80px;" onkeyup="ajaxSearchBooks()" onchange="ajaxSearchBooks()">

    <button type="submit">Search</button>
    <a href="../../Controllers/BookIndexController.php">Clear</a>
</form>

<hr>

<table border="1" cellpadding="10">
    <thead>
        <tr>
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
            <tr><td colspan="6">No books found.</td></tr>
        <?php endif; ?>

        <?php foreach ($books as $book) { ?>
        <tr>
            <td><?= $book['title'] ?></td>
            <td><?= $book['author'] ?></td>
            <td><?= $book['genre_name'] ?? 'N/A' ?></td>
            <td><?= $book['isbn'] ?></td>
            <td><?= $book['published_year'] ?></td>
            <td>
                <form method="POST" action="../../Controllers/BookDetailsController.php" style="display:inline;"><input type="hidden" name="id" value="<?= $book['id'] ?>"><button type="submit"  style="background:none; border:none; color:blue; text-decoration:underline; cursor:pointer; padding:0; font:inherit; ">View Details</button></form>
            </td>
        </tr>
        <?php } ?>
    </tbody>

</table>

<script src="../js/book_search.js"></script>

</body>
</html>

