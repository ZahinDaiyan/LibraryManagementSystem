<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

$books = $_SESSION['books'] ?? [];
$genres = $_SESSION['genres'] ?? [];
$branches = $_SESSION['branches'] ?? [];

$search = $_GET['search'] ?? '';
$selected_genre = $_GET['genre_id'] ?? '';
$selected_branch = $_GET['branch_id'] ?? '';
$selected_year = $_GET['year'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Catalog</title>
</head>

<body>

<h2>Available Books</h2>

<a href="dashboardView.php">← Back to Dashboard</a>

<hr>

<form action="../../Controllers/BookIndexController.php" method="GET">
    <input type="text" name="search" placeholder="Search title, author, ISBN..." value="<?= htmlspecialchars($search) ?>">
    
    <select name="genre_id">
        <option value="">All Genres</option>
        <?php foreach ($genres as $genre) { ?>
            <option value="<?= $genre['id'] ?>" <?= $selected_genre == $genre['id'] ? 'selected' : '' ?>>
                <?= $genre['name'] ?>
            </option>
        <?php } ?>
    </select>

    <select name="branch_id">
        <option value="">All Branches</option>
        <?php foreach ($branches as $branch) { ?>
            <option value="<?= $branch['id'] ?>" <?= $selected_branch == $branch['id'] ? 'selected' : '' ?>>
                <?= $branch['name'] ?>
            </option>
        <?php } ?>
    </select>

    <input type="number" name="year" placeholder="Year" value="<?= htmlspecialchars($selected_year) ?>" style="width: 80px;">

    <button type="submit">Search</button>
    <a href="../../Controllers/BookIndexController.php">Clear</a>
</form>

<hr>

<table border="1" cellpadding="10">
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Genre</th>
        <th>ISBN</th>
        <th>Year</th>
        <th>Action</th>
    </tr>

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
            <a href="../../Controllers/BookDetailsController.php?id=<?= $book['id'] ?>">View Details</a>
        </td>
    </tr>
    <?php } ?>

</table>

</body>
</html>
