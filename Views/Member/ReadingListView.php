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
            <a href="../../Controllers/BookDetailsController.php?id=<?= $item['book_id'] ?>">View</a> |
            <a href="../../Controllers/ReadingListActionController.php?action=remove&book_id=<?= $item['book_id'] ?>" onclick="return confirm('Remove from list?')">Remove</a>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
