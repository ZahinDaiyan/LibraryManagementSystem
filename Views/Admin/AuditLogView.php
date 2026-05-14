<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$logs = $_SESSION['audit_logs'] ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Platform Audit Logs</title>
</head>
<body>

<h2>Platform-Wide Audit Logs</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<hr>

<p>Showing the last 100 significant actions performed by users.</p>

<table border="1" cellpadding="8" width="100%">
    <tr style="background-color: #eee;">
        <th>Timestamp</th>
        <th>User</th>
        <th>Action</th>
        <th>Table</th>
        <th>ID</th>
        <th>Details</th>
        <th>IP Address</th>
    </tr>

    <?php if (empty($logs)): ?>
        <tr><td colspan="7">No audit logs found.</td></tr>
    <?php endif; ?>

    <?php foreach ($logs as $l): ?>
    <tr>
        <td><?= date('M d, Y H:i:s', strtotime($l['created_at'])) ?></td>
        <td>
            <b><?= htmlspecialchars($l['user_name']) ?></b><br>
            <small>(<?= ucfirst($l['user_role']) ?>)</small>
        </td>
        <td><?= htmlspecialchars($l['action']) ?></td>
        <td><?= htmlspecialchars($l['table_name'] ?? '-') ?></td>
        <td><?= htmlspecialchars($l['record_id'] ?? '-') ?></td>
        <td><small><?= htmlspecialchars($l['details'] ?? '-') ?></small></td>
        <td><small><?= $l['ip_address'] ?></small></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
