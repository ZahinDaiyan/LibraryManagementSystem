<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$a = $_SESSION['edit_announcement'] ?? null;
$branches = $_SESSION['branches'] ?? [];
$errors = $_SESSION['form_errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];

unset($_SESSION['form_errors'], $_SESSION['old_data']);

$title_text = $a ? "Edit Announcement" : "Create New Announcement";
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/admin.css">
    <title><?= $title_text ?></title>
</head>
<body>

<h2><?= $title_text ?></h2>
<a href="AnnouncementListView.php">← Back to List</a>
<hr>

<div id="adminAjaxMessage"></div>

<form novalidate data-admin-ajax="1" action="/LibraryManagementSystem/Controllers/AdminAnnouncementActionController.php" method="POST" onsubmit="return validateAnnouncementForm(this)">
    <input type="hidden" name="action" value="<?= $a ? 'update' : 'create' ?>">
    <?php if ($a): ?>
        <input type="hidden" name="id" value="<?= $a['id'] ?>">
    <?php endif; ?>

    <p>
        <label>Announcement Title:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($old_data['title'] ?? $a['title'] ?? '') ?>" style="width: 400px;">
        <?php if (isset($errors['title'])): ?>
            <span style="color:red;"><br><?= $errors['title'] ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label>Target Audience (Branch):</label><br>
        <select name="branch_id">
            <option value="">Global (All Branches)</option>
            <?php foreach ($branches as $b): ?>
                <option value="<?= $b['id'] ?>" <?= ($old_data['branch_id'] ?? $a['branch_id'] ?? '') == $b['id'] ? 'selected' : '' ?>>
                    <?= $b['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label>Announcement Body:</label><br>
        <textarea name="body" rows="8" cols="60" placeholder="Type the announcement content here..."><?= htmlspecialchars($old_data['body'] ?? $a['body'] ?? '') ?></textarea>
        <?php if (isset($errors['body'])): ?>
            <span style="color:red;"><br><?= $errors['body'] ?></span>
        <?php endif; ?>
    </p>

    <button type="submit"><?= $a ? 'Update Announcement' : 'Post Announcement' ?></button>
</form>

<script src="../js/admin_validation.js"></script>
<script src="../js/admin_ajax.js?v=<?= time() ?>"></script>
</body>
</html>

