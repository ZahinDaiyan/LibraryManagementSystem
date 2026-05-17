<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../LoginView.php');
    exit();
}

$branches = $_SESSION['bm_branches'] ?? array();
$editBranch = $_SESSION['bm_edit_branch'] ?? null;
$errors = $_SESSION['form_errors'] ?? array();
$old = $_SESSION['old_data'] ?? array();
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['form_errors'], $_SESSION['old_data'], $_SESSION['msg'], $_SESSION['error']);

$title = $editBranch ? 'Edit Branch' : 'Add Branch';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Branches</title>
</head>
<body>

<h2>Manage Branch Profiles</h2>
<a href="../../Controllers/BranchManagerDashboardController.php">Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<h3><?= $title ?></h3>
<form novalidate action="../../Controllers/BranchManagerBranchActionController.php" method="POST" onsubmit="return validateBranchForm(this)">
    <input type="hidden" name="action" value="<?= $editBranch ? 'update' : 'create' ?>">
    <?php if ($editBranch): ?>
        <input type="hidden" name="id" value="<?= $editBranch['id'] ?>">
    <?php endif; ?>
    <p>
        <label>Name:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? $editBranch['name'] ?? '') ?>">
        <?php if (isset($errors['name'])) echo "<br><span style='color:red;'>{$errors['name']}</span>"; ?>
    </p>
    <p>
        <label>Address:</label><br>
        <textarea name="address" rows="3" cols="50"><?= htmlspecialchars($old['address'] ?? $editBranch['address'] ?? '') ?></textarea>
        <?php if (isset($errors['address'])) echo "<br><span style='color:red;'>{$errors['address']}</span>"; ?>
    </p>
    <p>
        <label>City:</label><br>
        <input type="text" name="city" value="<?= htmlspecialchars($old['city'] ?? $editBranch['city'] ?? '') ?>">
        <?php if (isset($errors['city'])) echo "<br><span style='color:red;'>{$errors['city']}</span>"; ?>
    </p>
    <p>
        <label>Phone:</label><br>
        <input type="text" name="phone" value="<?= htmlspecialchars($old['phone'] ?? $editBranch['phone'] ?? '') ?>">
        <?php if (isset($errors['phone'])) echo "<br><span style='color:red;'>{$errors['phone']}</span>"; ?>
    </p>
    <button type="submit"><?= $editBranch ? 'Update Branch' : 'Create Branch' ?></button>
    <?php if ($editBranch): ?>
        <a href="../../Controllers/BranchManagerBranchController.php">Cancel Edit</a>
    <?php endif; ?>
</form>

<hr>

<h3>Managed Branches</h3>
<table border="1" cellpadding="8" width="100%">
    <tr>
        <th>Name</th>
        <th>City</th>
        <th>Address</th>
        <th>Phone</th>
        <th>Librarians</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php if (empty($branches)): ?>
        <tr><td colspan="7">No managed branches found.</td></tr>
    <?php endif; ?>
    <?php foreach ($branches as $branch): ?>
        <tr>
            <td><?= htmlspecialchars($branch['name'] ?? '') ?></td>
            <td><?= htmlspecialchars($branch['city'] ?? '') ?></td>
            <td><?= htmlspecialchars($branch['address'] ?? '') ?></td>
            <td><?= htmlspecialchars($branch['phone'] ?? '') ?></td>
            <td><?= $branch['librarian_count'] ?? 0 ?></td>
            <td>
                <b style="color: <?= !empty($branch['is_active']) ? 'green' : 'red' ?>;">
                    <?= !empty($branch['is_active']) ? 'Active' : 'Inactive' ?>
                </b>
            </td>
            <td>
                <a href="../../Controllers/BranchManagerBranchController.php?id=<?= $branch['id'] ?>">Edit</a> |
                <a href="../../Controllers/BranchManagerBranchActionController.php?action=toggle_status&id=<?= $branch['id'] ?>" onclick="return confirm('Toggle this branch status?')">
                    <?= !empty($branch['is_active']) ? 'Deactivate' : 'Activate' ?>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<script src="../../js/branch_manager.js"></script>
</body>
</html>
