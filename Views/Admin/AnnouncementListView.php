<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$announcements = $_SESSION['admin_announcements'] ?? [];
$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Announcements</title>
</head>
<body>

<h2>Platform Announcements</h2>
<a href="dashboardView.php">← Back to Dashboard</a> | 
<a href="../../Controllers/AdminAnnouncementFormController.php">Create New Announcement</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>

<table border="1" cellpadding="10" width="100%">
    <tr>
        <th>Title</th>
        <th>Target Branch</th>
        <th>Author</th>
        <th>Published On</th>
        <th>Actions</th>
    </tr>

    <?php if (empty($announcements)): ?>
        <tr><td colspan="5">No announcements found.</td></tr>
    <?php endif; ?>

    <?php foreach ($announcements as $a): ?>
    <tr>
        <td>
            <b><?= htmlspecialchars($a['title']) ?></b><br>
            <small><?= substr(htmlspecialchars($a['body']), 0, 50) ?>...</small>
        </td>
        <td><?= $a['branch_name'] ?? '<i>Global (All Branches)</i>' ?></td>
        <td><?= htmlspecialchars($a['author_name']) ?></td>
        <td><?= date('M d, Y H:i', strtotime($a['published_at'])) ?></td>
        <td>
            <a href="../../Controllers/AdminAnnouncementFormController.php?id=<?= $a['id'] ?>">Edit</a> | 
            <a href="../../Controllers/AdminAnnouncementActionController.php?action=delete&id=<?= $a['id'] ?>" onclick="return confirm('Delete this announcement?')" style="color:red;">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
