<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$users = $_SESSION['admin_users'] ?? [];
$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
</head>
<body>

<h2>User Management</h2>
<a href="dashboardView.php">← Back to Dashboard</a> | 
<a href="UserFormView.php">Add New User</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Branch</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($users as $u): ?>
    <tr>
        <td><?= $u['id'] ?></td>
        <td><?= $u['name'] ?></td>
        <td><?= $u['email'] ?></td>
        <td><?= ucfirst($u['role']) ?></td>
        <td><?= $u['branch_id'] ?? 'Global' ?></td>
        <td>
            <b style="color: <?= $u['is_active'] ? 'green' : 'red' ?>;">
                <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
            </b>
        </td>
        <td>
            <a href="../../Controllers/AdminUserFormController.php?id=<?= $u['id'] ?>">Edit</a> | 
            <a href="../../Controllers/AdminUserActionController.php?action=toggle_status&id=<?= $u['id'] ?>">
                <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
