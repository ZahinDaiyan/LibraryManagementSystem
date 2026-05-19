<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../LoginView.php');
    exit();
}

$books = isset($_SESSION['catalog_books']) ? $_SESSION['catalog_books'] : array();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Book Catalog</title>
    <link rel="stylesheet" href="../css/librarian.css">
</head>

<body>

<h2>Master Book Catalog</h2>

<a href="/LibraryManagementSystem/Controllers/LibrarianDashboardController.php">Back to Dashboard</a>

<br><br>

<a href="/LibraryManagementSystem/Controllers/LibrarianBookFormController.php?mode=add">Add New Book</a>

<?php if (isset($_SESSION['error']) && $_SESSION['error'] != '') { ?>
    <p><?php echo $_SESSION['error']; ?></p>
<?php } ?>

<?php if (isset($_SESSION['msg']) && $_SESSION['msg'] != '') { ?>
    <p><?php echo $_SESSION['msg']; ?></p>
<?php } ?>

<hr>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Cover</th>
        <th>Title</th>
        <th>Author</th>
        <th>ISBN</th>
        <th>Genre</th>
        <th>Publisher</th>
        <th>Year</th>
        <th>Total Copies</th>
        <th>Available</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php foreach ($books as $book) { ?>
        <tr>
            <td>
                <?php if (isset($book['cover_image_path']) && $book['cover_image_path'] != '') { ?>
                    <img src="../../<?php echo $book['cover_image_path']; ?>" width="60" alt="Cover">
                <?php } ?>
            </td>
            <td><?php echo $book['title']; ?></td>
            <td><?php echo $book['author']; ?></td>
            <td><?php echo $book['isbn']; ?></td>
            <td><?php echo isset($book['genre_name']) ? $book['genre_name'] : ''; ?></td>
            <td><?php echo $book['publisher']; ?></td>
            <td><?php echo $book['published_year']; ?></td>
            <td><?php echo $book['total_copies']; ?></td>
            <td><?php echo $book['available_copies']; ?></td>
            <td><?php echo $book['status_label']; ?></td>
            <td>
                <a href="/LibraryManagementSystem/Controllers/LibrarianBookFormController.php?mode=edit&id=<?php echo $book['id']; ?>">Edit</a>

                <?php if ($book['status_label'] === 'Retired/Unavailable') { ?>
                    <form novalidate action="/LibraryManagementSystem/Controllers/LibrarianBookRestoreController.php" method="POST" style="display:inline;">
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <label for="copies_<?php echo $book['id']; ?>">Copies:</label>
                        <input type="number" id="copies_<?php echo $book['id']; ?>" name="copies" value="1" min="1" style="width:60px;">
                        <button type="submit">Make Available</button>
                    </form>
                <?php } else { ?>
                    <form novalidate action="/LibraryManagementSystem/Controllers/LibrarianBookRetireController.php" method="POST" style="display:inline;">
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <button type="submit">Mark Unavailable</button>
                    </form>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
