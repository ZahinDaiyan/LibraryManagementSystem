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

$search = $_SESSION['admin_user_search'] ?? '';
$role_filter = $_SESSION['admin_user_role_filter'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/admin.css?v=<?= time() ?>">
    <title>Manage All Users</title>
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

        .search-form input[type="text"] {
            flex: 2 !important;
            min-width: 220px !important;
            max-width: none !important;
            margin-bottom: 0 !important;
        }

        .search-form select {
            flex: 1 !important;
            min-width: 150px !important;
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

        .create-btn {
            background: #f59e0b !important;
            color: #fff !important;
            padding: 8px 16px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
            display: inline-block !important;
            margin-left: 12px !important;
            text-decoration: none !important;
        }

        .create-btn:hover {
            background: #fbbf24 !important;
            color: #fff !important;
        }
    </style>
</head>
<body>

<h2>Manage All User Accounts</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<a href="/LibraryManagementSystem/Controllers/AdminUserFormController.php" class="create-btn">Create Staff Account</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<form novalidate class="search-form" action="/LibraryManagementSystem/Controllers/AdminUserController.php" method="POST" onsubmit="event.preventDefault(); ajaxSearchUsers();">
    <input type="text" id="userSearch" name="search" placeholder="Search name, email, phone..." value="<?= htmlspecialchars($search) ?>" onkeyup="ajaxSearchUsers()">
    
    <select id="userRole" name="role_filter" onchange="ajaxSearchUsers()">
        <option value="">All Roles</option>
        <option value="member" <?= $role_filter === 'member' ? 'selected' : '' ?>>Member</option>
        <option value="librarian" <?= $role_filter === 'librarian' ? 'selected' : '' ?>>Librarian</option>
        <option value="branch_manager" <?= $role_filter === 'branch_manager' ? 'selected' : '' ?>>Branch Manager</option>
        <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>

    <button type="submit">Filter/Search</button>
    <a href="/LibraryManagementSystem/Controllers/AdminUserController.php" class="btn-clear">Clear</a>
</form>

<br>

<table border="1" cellpadding="10" width="100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Branch</th>
            <th>Status</th>
            <th>Joined</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody id="userTableBody">
        <?php if (empty($users)): ?>
            <tr><td colspan="7">No users found matching your criteria.</td></tr>
        <?php endif; ?>

        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['name'] ?></td>
            <td><?= $u['email'] ?></td>
            <td>
                <form novalidate action="/LibraryManagementSystem/Controllers/AdminUserActionController.php" method="POST" style="display:inline;">
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
                <form method="POST" action="/LibraryManagementSystem/Controllers/AdminUserFormController.php" style="display:inline;"><input type="hidden" name="id" value="<?= $u['id'] ?>"><button type="submit" class="btn-link">Edit Info</button></form>
                <form method="POST" action="/LibraryManagementSystem/Controllers/AdminUserActionController.php" style="display:inline;"><input type="hidden" name="action" value="toggle_status"><input type="hidden" name="id" value="<?= $u['id'] ?>"><button type="submit" onclick="return confirm('Toggle status for this user?')" class="btn-link"><?= $u['is_active'] ? 'Deactivate' : 'Activate' ?></button></form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script src="../js/admin_user_search.js"></script>

</body>
</html>
