<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$transfers = $_SESSION['admin_transfers'] ?? [];
$status_filter = $_GET['status_filter'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inter-Branch Transfers</title>
</head>
<body>

<h2>Inter-Branch Transfer Monitoring</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<hr>

<form action="../../Controllers/AdminTransferController.php" method="GET">
    <label>Filter by Status:</label>
    <select name="status_filter">
        <option value="">All Transfers</option>
        <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="approved" <?= $status_filter === 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Completed</option>
        <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
    </select>
    <button type="submit">Filter</button>
    <a href="../../Controllers/AdminTransferController.php">Clear</a>
</form>

<br>

<table border="1" cellpadding="10" width="100%">
    <tr>
        <th>Book Title</th>
        <th>From Branch</th>
        <th>To Branch</th>
        <th>Requested By</th>
        <th>Status</th>
        <th>Date Requested</th>
    </tr>

    <?php if (empty($transfers)): ?>
        <tr><td colspan="6">No transfer requests found.</td></tr>
    <?php endif; ?>

    <?php foreach ($transfers as $t): 
        $status_color = 'black';
        if ($t['status'] === 'pending') $status_color = 'orange';
        if ($t['status'] === 'approved') $status_color = 'blue';
        if ($t['status'] === 'completed') $status_color = 'green';
        if ($t['status'] === 'rejected') $status_color = 'red';
    ?>
    <tr>
        <td><?= htmlspecialchars($t['book_title']) ?></td>
        <td><?= htmlspecialchars($t['from_branch_name']) ?></td>
        <td><?= htmlspecialchars($t['to_branch_name']) ?></td>
        <td><?= htmlspecialchars($t['requester_name']) ?></td>
        <td>
            <b style="color: <?= $status_color ?>;"><?= ucfirst($t['status']) ?></b>
        </td>
        <td><?= date('M d, Y H:i', strtotime($t['created_at'])) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
