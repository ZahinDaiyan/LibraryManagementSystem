<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../LoginView.php');
    exit();
}

$announcements = $_SESSION['bm_announcements'] ?? array();
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Platform Announcements</title>
    <link rel="stylesheet" href="../css/manager.css">
</head>
<body>

<h2>Platform-Wide Announcements</h2>
<a href="/LibraryManagementSystem/Controllers/BranchManagerDashboardController.php">Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<h3>Post Announcement</h3>
<form novalidate action="/LibraryManagementSystem/Controllers/BranchManagerAnnouncementActionController.php" method="POST" onsubmit="return validateManagerAnnouncementForm(this)">
    <p>
        <label>Title:</label><br>
        <input type="text" name="title" size="60">
    </p>
    <p>
        <label>Content:</label><br>
        <textarea name="body" rows="5" cols="70"></textarea>
    </p>
    <button type="submit">Post Platform Announcement</button>
</form>

<hr>

<h3>Recent Platform Announcements</h3>
<table border="1" cellpadding="8" width="100%">
    <tr><th>Title</th><th>Content</th><th>Author</th><th>Published</th></tr>
    <?php if (empty($announcements)): ?>
        <tr><td colspan="4">No announcements found.</td></tr>
    <?php endif; ?>
    <?php foreach ($announcements as $announcement): ?>
        <tr>
            <td><?= htmlspecialchars($announcement['title'] ?? '') ?></td>
            <td><?= htmlspecialchars($announcement['body'] ?? '') ?></td>
            <td><?= htmlspecialchars($announcement['author_name'] ?? '') ?></td>
            <td><?= htmlspecialchars($announcement['published_at'] ?? '') ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<script src="../js/branch_manager.js"></script>
</body>
</html>
