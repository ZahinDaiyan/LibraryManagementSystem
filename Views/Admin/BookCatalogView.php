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
    <link rel="stylesheet" href="../css/admin.css?v=<?= time() ?>">
    <title>Master Book Catalog</title>
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
            min-width: 250px !important;
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

        /* Styled button link for table actions */
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
            margin-right: 8px !important;
        }

        .btn-link:hover {
            background: #f59e0b !important;
            color: #fff !important;
            text-decoration: none !important;
        }

        /* Styled button link for danger actions */
        .btn-link-danger {
            background: transparent !important;
            border: 1px solid #ef4444 !important;
            color: #ef4444 !important;
            padding: 6px 14px !important;
            font-size: 0.8rem !important;
            font-weight: 500 !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: inline-block !important;
        }

        .btn-link-danger:hover {
            background: #ef4444 !important;
            color: #fff !important;
            text-decoration: none !important;
        }

        .create-btn {
            background: #f59e0b !important;
            color: #fff !important;
            padding: 8px 16px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
            display: inline-block !important;
            margin-left: 12px !important;
            text-decoration: none !important;
        }

        .create-btn:hover {
            background: #fbbf24 !important;
            color: #fff !important;
        }
    </style>
</head>
<body>

<h2>Master Book Catalog</h2>
<p>Admin Override: Full management of the global library collection.</p>
<a href="dashboardView.php">← Back to Dashboard</a>
<a href="../../Controllers/AdminBookFormController.php" class="create-btn">Add New Book to Catalog</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>

<div id="catalogMessage" style="margin: 12px 0;"></div>

<form novalidate class="search-form" id="catalogSearchForm" action="../../Controllers/AdminBookCatalogController.php" method="POST">
    <input type="text" name="search" placeholder="Search title, author, ISBN..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search Catalog</button>
    <a href="../../Controllers/AdminBookCatalogController.php" class="btn-clear">Clear</a>
</form>

<br>

<table border="1" cellpadding="10" width="100%">
    <thead>
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Genre</th>
        <th>ISBN</th>
        <th>Global Stock</th>
        <th>Available</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody id="catalogTableBody">

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
            <form method="POST" action="../../Controllers/AdminBookFormController.php" style="display:inline;"><input type="hidden" name="id" value="<?= $b['id'] ?>"><button type="submit" class="btn-link">Edit Details</button></form>
            <form data-admin-ajax="1" method="POST" action="../../Controllers/AdminBookActionController.php" class="book-delete-form" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $b['id'] ?>"><button type="submit" class="btn-link-danger">Delete</button></form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
    window.adminCatalogInitialBooks = <?= json_encode($books) ?>;
    window.adminCatalogInitialSearch = <?= json_encode($search) ?>;
</script>
<script src="../js/admin_book_catalog.js?v=<?= time() ?>"></script>

</body>
</html>

