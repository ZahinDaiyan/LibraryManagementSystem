<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$complaints = $_SESSION['admin_complaints'] ?? [];
$status_filter = $_SESSION['admin_complaint_status_filter'] ?? '';
$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Member Complaints</title>
</head>
<body>

<h2>Escalated Member Complaints</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>

<form novalidate action="../../Controllers/AdminComplaintController.php" method="POST">
    <label>Filter by Status:</label>
    <select name="status_filter">
        <option value="">All Complaints</option>
        <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="in_review" <?= $status_filter === 'in_review' ? 'selected' : '' ?>>In Review</option>
        <option value="resolved" <?= $status_filter === 'resolved' ? 'selected' : '' ?>>Resolved</option>
    </select>
    <button type="submit">Filter</button>
    <a href="../../Controllers/AdminComplaintController.php">Clear</a>
</form>

<br>

<table border="1" cellpadding="10" width="100%">
    <tr>
        <th>Member</th>
        <th>Title</th>
        <th>Status</th>
        <th>Submitted On</th>
        <th>Actions</th>
    </tr>

    <?php if (empty($complaints)): ?>
        <tr><td colspan="5">No complaints found.</td></tr>
    <?php endif; ?>

    <?php foreach ($complaints as $c): ?>
    <tr>
        <td><?= htmlspecialchars($c['member_name']) ?><br><small><?= htmlspecialchars($c['member_email']) ?></small></td>
        <td><?= htmlspecialchars($c['title']) ?></td>
        <td>
            <b style="color: <?= $c['status'] === 'resolved' ? 'green' : ($c['status'] === 'pending' ? 'red' : 'orange') ?>;">
                <?= ucfirst(str_replace('_', ' ', $c['status'])) ?>
            </b>
        </td>
        <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
        <td>
            <form method="POST" action="../../Controllers/AdminComplaintDetailController.php" style="display:inline;"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button type="submit"  style="background:none; border:none; color:blue; text-decoration:underline; cursor:pointer; padding:0; font:inherit; ">View & Respond</button></form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>

