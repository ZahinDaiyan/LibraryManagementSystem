<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

$book = isset($_SESSION['book']) ? $_SESSION['book'] : null;
$availability = isset($_SESSION['availability']) ? $_SESSION['availability'] : [];

if (!$book) {
    header("Location: BookIndexView.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Details</title>
</head>

<body>

<h2>Book Details</h2>

<a href="BookIndexView.php">← Back to Catalog</a>

<hr>

<h3><?= $book['title'] ?></h3>

<p><b>Author:</b> <?= $book['author'] ?></p>
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
            <a href="../../Controllers/BorrowRequestController.php?book_id=<?= $book['id'] ?>&branch_id=<?= $a['branch_id'] ?>">
                Request Borrow
            </a>
        <?php } else { ?>
            <span style="color:red;">Not Available</span>
        <?php } ?>
    </td>
</tr>

<?php } ?>

</table>

</body>
</html>
