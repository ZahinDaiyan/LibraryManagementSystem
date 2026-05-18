<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../LoginView.php');
    exit();
}

$transfers = $_SESSION['bm_transfers'] ?? array();
$statusFilter = $_GET['status_filter'] ?? '';
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inter-Branch Transfers</title>
    <link rel="stylesheet" href="../css/manager.css">
</head>
<body>

<h2>Inter-Branch Transfer Requests</h2>
<a href="../../Controllers/BranchManagerDashboardController.php">Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<form novalidate action="../../Controllers/BranchManagerTransferController.php" method="GET">
    <label>Status:</label>
    <select name="status_filter">
        <option value="">All</option>
        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
        <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
    </select>
    <button type="submit">Filter</button>
    <a href="../../Controllers/BranchManagerTransferController.php">Clear</a>
</form>

<br>

<table border="1" cellpadding="8" width="100%">
    <tr>
        <th>Book</th>
        <th>From Branch</th>
        <th>To Branch</th>
        <th>Requested By</th>
        <th>Status</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php if (empty($transfers)): ?>
        <tr><td colspan="7">No transfer requests found.</td></tr>
    <?php endif; ?>
    <?php foreach ($transfers as $transfer): ?>
        <tr>
            <td><?= htmlspecialchars($transfer['book_title'] ?? '') ?></td>
            <td><?= htmlspecialchars($transfer['from_branch_name'] ?? '') ?></td>
            <td><?= htmlspecialchars($transfer['to_branch_name'] ?? '') ?></td>
            <td><?= htmlspecialchars($transfer['requested_by_name'] ?? '') ?></td>
            <td><?= ucfirst(htmlspecialchars($transfer['status'] ?? '')) ?></td>
            <td><?= htmlspecialchars($transfer['created_at'] ?? '') ?></td>
            <td>
                <?php if (($transfer['status'] ?? '') === 'pending'): ?>
                    <form novalidate action="../../Controllers/BranchManagerTransferActionController.php" method="POST" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?= $transfer['id'] ?>">
                        <input type="hidden" name="status" value="approved">
                        <button type="submit">Approve</button>
                    </form>
                    <form novalidate action="../../Controllers/BranchManagerTransferActionController.php" method="POST" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?= $transfer['id'] ?>">
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit">Reject</button>
                    </form>
                <?php elseif (($transfer['status'] ?? '') === 'approved'): ?>
                    <form novalidate action="../../Controllers/BranchManagerTransferActionController.php" method="POST" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?= $transfer['id'] ?>">
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" onclick="return confirm('Complete this transfer and move one available copy?')">Complete</button>
                    </form>
                <?php else: ?>
                    No action
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
