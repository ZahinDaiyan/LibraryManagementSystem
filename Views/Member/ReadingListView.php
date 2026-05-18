<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

$list = $_SESSION['reading_list'] ?? [];
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Reading List</title>
    <link rel="stylesheet" href="../css/member.css">
</head>
<body>

<h2>My Reading List</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<table border="1" cellpadding="10">
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>ISBN</th>
        <th>Action</th>
    </tr>

    <?php if (empty($list)): ?>
        <tr><td colspan="4">Your reading list is empty.</td></tr>
    <?php endif; ?>

    <?php foreach ($list as $item) { ?>
    <tr>
        <td><?= $item['title'] ?></td>
        <td><?= $item['author'] ?></td>
        <td><?= $item['isbn'] ?></td>
        <td>
            <form method="POST" action="../../Controllers/BookDetailsController.php" style="display:inline;"><input type="hidden" name="id" value="<?= $item['book_id'] ?>"><button type="submit"  style="background:none; border:none; color:blue; text-decoration:underline; cursor:pointer; padding:0; font:inherit; ">View</button></form> |
            <form method="POST" action="../../Controllers/ReadingListActionController.php" style="display:inline;"><input type="hidden" name="action" value="remove"><input type="hidden" name="book_id" value="<?= $item['book_id'] ?>"><button type="submit"  onclick="return confirm('Remove from list?')" style="background:none; border:none; color:blue; text-decoration:underline; cursor:pointer; padding:0; font:inherit; ">Remove</button></form>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
