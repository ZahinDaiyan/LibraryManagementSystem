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
    <title>Manage Branches</title>
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
            <a href="../../Controllers/AdminBranchActionController.php?action=toggle_status&id=<?= $b['id'] ?>" onclick="return confirm('Toggle status for this branch?')">
                <?= $b['is_active'] ? 'Deactivate' : 'Activate' ?>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
