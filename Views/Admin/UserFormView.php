<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$user = $_SESSION['edit_user'] ?? null;
$branches = $_SESSION['branches'] ?? [];
$errors = $_SESSION['form_errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];

unset($_SESSION['form_errors'], $_SESSION['old_data']);

$title = $user ? "Edit User" : "Add New User";
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
</head>
<body>

<h2><?= $title ?></h2>
<a href="UserListView.php">← Back to List</a>
<hr>

<form novalidate action="../../Controllers/AdminUserActionController.php" method="POST" onsubmit="return validateUserForm(this)">
    <input type="hidden" name="action" value="<?= $user ? 'update' : 'create' ?>">
    <?php if ($user): ?>
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
    <?php endif; ?>

    <p>
        <label>Full Name:</label><br>
        <input type="text" name="name" value="<?= $old_data['name'] ?? $user['name'] ?? '' ?>">
        <?php if (isset($errors['name'])): ?>
            <span style="color:red;"><br><?= $errors['name'] ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label>Email Address:</label><br>
        <input type="email" name="email" value="<?= $old_data['email'] ?? $user['email'] ?? '' ?>">
        <?php if (isset($errors['email'])): ?>
            <span style="color:red;"><br><?= $errors['email'] ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label>Phone Number:</label><br>
        <input type="text" name="phone" value="<?= $old_data['phone'] ?? $user['phone'] ?? '' ?>">
        <?php if (isset($errors['phone'])): ?>
            <span style="color:red;"><br><?= $errors['phone'] ?></span>
        <?php endif; ?>
    </p>

    <?php if (!$user): ?>
    <p>
        <label>Password:</label><br>
        <input type="password" name="password">
        <?php if (isset($errors['password'])): ?>
            <span style="color:red;"><br><?= $errors['password'] ?></span>
        <?php endif; ?>
    </p>
    <?php endif; ?>

    <p>
        <label>Role:</label><br>
        <select name="role">
            <option value="member" <?= ($old_data['role'] ?? $user['role'] ?? '') === 'member' ? 'selected' : '' ?>>Member</option>
            <option value="librarian" <?= ($old_data['role'] ?? $user['role'] ?? '') === 'librarian' ? 'selected' : '' ?>>Librarian</option>
            <option value="branch_manager" <?= ($old_data['role'] ?? $user['role'] ?? '') === 'branch_manager' ? 'selected' : '' ?>>Branch Manager</option>
            <option value="admin" <?= ($old_data['role'] ?? $user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
    </p>

    <p>
        <label>Assign Branch:</label><br>
        <select name="branch_id">
            <option value="">No Branch (Global)</option>
            <?php foreach ($branches as $b): ?>
                <option value="<?= $b['id'] ?>" <?= ($old_data['branch_id'] ?? $user['branch_id'] ?? '') == $b['id'] ? 'selected' : '' ?>>
                    <?= $b['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <button type="submit"><?= $user ? 'Update User' : 'Create User' ?></button>
</form>

<script src="../../js/admin_validation.js"></script>
</body>
</html>
