<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$books = $_SESSION['admin_books'] ?? [];
$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

$search = $_SESSION['admin_book_search'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/admin.css">
    <title>Master Book Catalog</title>
</head>
<body>

<h2>Master Book Catalog</h2>
<p>Admin Override: Full management of the global library collection.</p>
<a href="dashboardView.php">← Back to Dashboard</a> | 
<a href="../../Controllers/AdminBookFormController.php">Add New Book to Catalog</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>

<form novalidate action="../../Controllers/AdminBookCatalogController.php" method="POST">
    <input type="text" name="search" placeholder="Search title, author, ISBN..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search Catalog</button>
    <a href="../../Controllers/AdminBookCatalogController.php">Clear</a>
</form>

<br>

<table border="1" cellpadding="10" width="100%">
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Genre</th>
        <th>ISBN</th>
        <th>Global Stock</th>
        <th>Available</th>
        <th>Actions</th>
    </tr>

    <?php if (empty($books)): ?>
        <tr><td colspan="7">No books found in the global catalog.</td></tr>
    <?php endif; ?>

    <?php foreach ($books as $b): ?>
    <tr>
        <td><?= $b['title'] ?></td>
        <td><?= $b['author'] ?></td>
        <td><?= $b['genre_name'] ?? '<i>None</i>' ?></td>
        <td><?= $b['isbn'] ?></td>
        <td><?= $b['total_stock'] ?? 0 ?></td>
        <td><?= $b['total_available'] ?? 0 ?></td>
        <td>
            <form method="POST" action="../../Controllers/AdminBookFormController.php" style="display:inline;"><input type="hidden" name="id" value="<?= $b['id'] ?>"><button type="submit"  style="background:none; border:none; color:blue; text-decoration:underline; cursor:pointer; padding:0; font:inherit; ">Edit Details</button></form> | 
            <form method="POST" action="../../Controllers/AdminBookActionController.php" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $b['id'] ?>"><button type="submit"  onclick="return confirm('Permanently delete this book from the global catalog? This action cannot be undone.')"  style="background:none; border:none; color:blue; text-decoration:underline; cursor:pointer; padding:0; font:inherit; color:red;">Delete</button></form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>

