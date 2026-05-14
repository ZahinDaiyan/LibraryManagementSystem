<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

$books = isset($_SESSION['books']) ? $_SESSION['books'] : [];
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

<table border="1" cellpadding="10">
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>ISBN</th>
        <th>Year</th>
        <th>Action</th>
    </tr>

    <?php foreach ($books as $book) { ?>
    <tr>
        <td><?= $book['title'] ?></td>
        <td><?= $book['author'] ?></td>
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
