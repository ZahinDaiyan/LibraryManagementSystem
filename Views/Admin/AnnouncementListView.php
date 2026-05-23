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
    <link rel="stylesheet" href="<?= Url::asset('Views/css/admin.css') ?>?v=<?= time() ?>">
    <title>Manage Announcements</title>
    <style>
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

<h2>Platform Announcements</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<a href="../../Controllers/AdminAnnouncementFormController.php" class="create-btn">Create New Announcement</a>
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
            <form method="POST" action="../../Controllers/AdminAnnouncementFormController.php" style="display:inline;"><input type="hidden" name="id" value="<?= $a['id'] ?>"><button type="submit" class="btn-link">Edit</button></form>
            <form data-admin-ajax="1" method="POST" action="../../Controllers/AdminAnnouncementActionController.php" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $a['id'] ?>"><button type="submit" onclick="return confirm('Delete this announcement?')" class="btn-link-danger">Delete</button></form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Removed admin_ajax.js to disable AJAX; using normal form submissions -->

</body>
</html>
