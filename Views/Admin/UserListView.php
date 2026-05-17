<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$users = $_SESSION['admin_users'] ?? [];
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);

$search = $_GET['search'] ?? '';
$role_filter = $_GET['role_filter'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage All Users</title>
</head>
<body>

<h2>Manage All User Accounts</h2>
<a href="dashboardView.php">← Back to Dashboard</a> | 
<a href="../../Controllers/AdminUserFormController.php">Create Staff Account (Librarian/Manager)</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<form novalidate action="../../Controllers/AdminUserController.php" method="GET">
    <input type="text" name="search" placeholder="Search name, email, phone..." value="<?= htmlspecialchars($search) ?>">
    
    <select name="role_filter">
        <option value="">All Roles</option>
        <option value="member" <?= $role_filter === 'member' ? 'selected' : '' ?>>Member</option>
        <option value="librarian" <?= $role_filter === 'librarian' ? 'selected' : '' ?>>Librarian</option>
        <option value="branch_manager" <?= $role_filter === 'branch_manager' ? 'selected' : '' ?>>Branch Manager</option>
        <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>

    <button type="submit">Filter/Search</button>
    <a href="../../Controllers/AdminUserController.php">Clear</a>
</form>

<br>

<table border="1" cellpadding="10" width="100%">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Branch</th>
        <th>Status</th>
        <th>Joined</th>
        <th>Actions</th>
    </tr>

    <?php if (empty($users)): ?>
        <tr><td colspan="7">No users found matching your criteria.</td></tr>
    <?php endif; ?>

    <?php foreach ($users as $u): ?>
    <tr>
        <td><?= $u['name'] ?></td>
        <td><?= $u['email'] ?></td>
        <td>
            <form novalidate action="../../Controllers/AdminUserActionController.php" method="POST" style="display:inline;">
                <input type="hidden" name="action" value="change_role">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <select name="role" onchange="this.form.submit()">
                    <option value="member" <?= $u['role'] === 'member' ? 'selected' : '' ?>>Member</option>
                    <option value="librarian" <?= $u['role'] === 'librarian' ? 'selected' : '' ?>>Librarian</option>
                    <option value="branch_manager" <?= $u['role'] === 'branch_manager' ? 'selected' : '' ?>>Branch Manager</option>
                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </form>
        </td>
        <td><?= $u['branch_name'] ?? '<i>Global/None</i>' ?></td>
        <td>
            <b style="color: <?= $u['is_active'] ? 'green' : 'red' ?>;">
                <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
            </b>
        </td>
        <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
        <td>
            <a href="../../Controllers/AdminUserFormController.php?id=<?= $u['id'] ?>">Edit Info</a> | 
            <a href="../../Controllers/AdminUserActionController.php?action=toggle_status&id=<?= $u['id'] ?>" onclick="return confirm('Toggle status for this user?')">
                <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
