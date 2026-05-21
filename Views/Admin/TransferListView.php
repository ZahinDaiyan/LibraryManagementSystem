<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$transfers = $_SESSION['admin_transfers'] ?? [];
$status_filter = $_SESSION['admin_transfer_status_filter'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/admin.css?v=<?= time() ?>">
    <title>Inter-Branch Transfers</title>
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

        .search-form label {
            margin-bottom: 0 !important;
        }

        .search-form select {
            flex: 1 !important;
            min-width: 200px !important;
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
    </style>
</head>
<body>

<h2>Inter-Branch Transfer Monitoring</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<hr>

<form novalidate data-admin-ajax="1" class="search-form" action="../../Controllers/AdminTransferController.php" method="POST">
    <label>Filter by Status:</label>
    <select name="status_filter">
        <option value="">All Transfers</option>
        <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="approved" <?= $status_filter === 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Completed</option>
        <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
    </select>
    <button type="submit">Filter</button>
    <a href="../../Controllers/AdminTransferController.php" class="btn-clear">Clear</a>
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

<script src="../js/admin_ajax.js?v=<?= time() ?>"></script>

</body>
</html>

