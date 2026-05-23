<?php
session_start();

use App\Helpers\Url;

if (!class_exists(Url::class)) {
    require_once __DIR__ . '/../../app/Helpers/Url.php';
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$user = $user ?? $_SESSION['edit_user'] ?? null;
$branches = $branches ?? $_SESSION['branches'] ?? [];
$errors = $errors ?? $_SESSION['form_errors'] ?? [];
$old_data = $old_data ?? $_SESSION['old_data'] ?? [];

unset($_SESSION['form_errors'], $_SESSION['old_data']);

$title = $user ? "Edit User" : "Add New User";
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/admin.css">
    <title><?= $title ?></title>
</head>
<body>

<h2><?= $title ?></h2>
<a href="<?= Url::route('/admin/users') ?>">← Back to List</a>
<hr>
<?php if (!empty($errors['general'])): ?>
    <p style="color:red;"><?= htmlspecialchars($errors['general']) ?></p>
<?php endif; ?>
<div id="adminAjaxMessage"></div>

<form novalidate data-admin-ajax="1" action="<?= Url::route($user ? '/admin/users/' . $user['id'] : '/admin/users') ?>" method="POST" onsubmit="return validateUserForm(this)">
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

    <!-- Removed admin_ajax.js to disable AJAX; using normal form submissions -->
<script src="../js/admin_validation.js"></script>
</body>
</html>
