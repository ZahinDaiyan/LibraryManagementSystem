<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <link rel="stylesheet" href="<?= Url::asset('Views/css/admin.css') ?>?v=<?= time() ?>">
    <title>Member Complaints</title>
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
    </style>
</head>
<body>

<h2>Escalated Member Complaints</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>

<form novalidate class="search-form" action="../../Controllers/AdminComplaintController.php" method="POST">
    <label>Filter by Status:</label>
    <select name="status_filter">
        <option value="">All Complaints</option>
        <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="in_review" <?= $status_filter === 'in_review' ? 'selected' : '' ?>>In Review</option>
        <option value="resolved" <?= $status_filter === 'resolved' ? 'selected' : '' ?>>Resolved</option>
    </select>
    <button type="submit">Filter</button>
    <a href="../../Controllers/AdminComplaintController.php" class="btn-clear">Clear</a>
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
            <form data-admin-ajax="1" method="POST" action="../../Controllers/AdminComplaintDetailController.php" style="display:inline;"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button type="submit" class="btn-link">View & Respond</button></form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Removed admin_ajax.js to disable AJAX; using normal form submissions -->

</body>
</html>

