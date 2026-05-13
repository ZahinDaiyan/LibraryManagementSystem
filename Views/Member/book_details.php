<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/LoginView.php");
    exit();
}

$book = $_SESSION['book'];
$availability = $_SESSION['availability'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Details</title>
</head>

<body>

<h2>Book Details</h2>

<a href="book_catalog.php">← Back</a>

<hr>

<h3><?= $book['title'] ?></h3>

<p><b>Author:</b> <?= $book['author'] ?></p>
<p><b>ISBN:</b> <?= $book['isbn'] ?></p>
<p><b>Publisher:</b> <?= $book['publisher'] ?></p>
<p><b>Year:</b> <?= $book['published_year'] ?></p>
<p><b>Description:</b> <?= $book['description'] ?></p>

<hr>

<h3>Availability by Branch</h3>

<table border="1">

<tr>
    <th>Branch</th>
    <th>Total Copies</th>
    <th>Available</th>
</tr>

<?php foreach ($availability as $a) { ?>

<tr>
    <td><?= $a['branch_name'] ?></td>
    <td><?= $a['total_copies'] ?></td>
    <td><?= $a['available_copies'] ?></td>
</tr>

<?php } ?>

</table>

</body>
</html>