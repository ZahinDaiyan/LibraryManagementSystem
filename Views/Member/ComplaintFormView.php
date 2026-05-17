<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

$errors = $_SESSION['form_errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['old_data']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submit Complaint</title>
    <link rel="stylesheet" href="../css/member.css">
</head>
<body>

<h2>Submit a New Complaint</h2>
<a href="../../Controllers/MemberComplaintController.php">← Back to List</a>
<hr>

<form novalidate action="../../Controllers/MemberComplaintActionController.php" method="POST" onsubmit="return validateComplaintForm(this)">
    <p>
        <label>Complaint Title:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($old_data['title'] ?? '') ?>" style="width: 400px;">
        <?php if (isset($errors['title'])): ?>
            <span style="color:red;"><br><?= $errors['title'] ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label>Detailed Description:</label><br>
        <textarea name="description" rows="10" cols="50"><?= htmlspecialchars($old_data['description'] ?? '') ?></textarea>
        <?php if (isset($errors['description'])): ?>
            <span style="color:red;"><br><?= $errors['description'] ?></span>
        <?php endif; ?>
    </p>

    <button type="submit">Submit Complaint</button>
</form>

<script src="../js/member_validation.js"></script>
</body>
</html>
