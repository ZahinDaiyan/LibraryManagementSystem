<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$branches = $_SESSION['admin_branches'] ?? [];
$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/admin.css?v=<?= time() ?>">
    <title>Manage Branches</title>
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
    </style>
</head>
<body>

<h2>Library Branch Management</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>

<table border="1" cellpadding="10" width="100%">
    <tr>
        <th>Branch Name</th>
        <th>City</th>
        <th>Address</th>
        <th>Assigned Manager</th>
        <th>Librarians</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php if (empty($branches)): ?>
        <tr><td colspan="7">No branches found.</td></tr>
    <?php endif; ?>

    <?php foreach ($branches as $b): ?>
    <tr>
        <td><?= $b['name'] ?></td>
        <td><?= $b['city'] ?></td>
        <td><?= $b['address'] ?></td>
        <td><?= $b['manager_name'] ?? '<i>No Manager Assigned</i>' ?></td>
        <td><?= $b['librarian_count'] ?></td>
        <td>
            <b style="color: <?= $b['is_active'] ? 'green' : 'red' ?>;">
                <?= $b['is_active'] ? 'Active' : 'Inactive' ?>
            </b>
        </td>
        <td>
            <form data-admin-ajax="1" method="POST" action="../../Controllers/AdminBranchActionController.php" style="display:inline;"><input type="hidden" name="action" value="toggle_status"><input type="hidden" name="id" value="<?= $b['id'] ?>"><button type="submit" onclick="return confirm('Toggle status for this branch?')" class="btn-link"><?= $b['is_active'] ? 'Deactivate' : 'Activate' ?></button></form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<script src="../js/admin_ajax.js?v=<?= time() ?>"></script>

</body>
</html>
