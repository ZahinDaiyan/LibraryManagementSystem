<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$c = $_SESSION['current_complaint'] ?? null;
$errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']);

if (!$c) {
    header('Location: ComplaintListView.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/admin.css">
    <title>Complaint Details</title>
</head>
<body>

<h2>Complaint Details</h2>
<a href="../../Controllers/AdminComplaintController.php">← Back to List</a>
<hr>

<div id="adminAjaxMessage"></div>

<div class="complaint-card">
    <p><b>From:</b> <?= htmlspecialchars($c['member_name']) ?> (<?= htmlspecialchars($c['member_email']) ?>)</p>
    <p><b>Date:</b> <?= date('M d, Y H:i', strtotime($c['created_at'])) ?></p>
    <p><b>Subject:</b> <?= htmlspecialchars($c['title']) ?></p>
    <p><b>Description:</b></p>
    <p style="white-space: pre-wrap;"><?= htmlspecialchars($c['description']) ?></p>
</div>

<hr>

<h3>Admin Action</h3>
<form novalidate data-admin-ajax="1" action="../../Controllers/AdminComplaintActionController.php" method="POST" onsubmit="return validateComplaintResponse(this)">
    <input type="hidden" name="id" value="<?= $c['id'] ?>">

    <p>
        <label>Update Status:</label><br>
        <select name="status">
            <option value="pending" <?= $c['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="in_review" <?= $c['status'] === 'in_review' ? 'selected' : '' ?>>In Review</option>
            <option value="resolved" <?= $c['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
        </select>
    </p>

    <p>
        <label>Response to Member:</label><br>
        <textarea name="admin_response" rows="6" cols="60" placeholder="Type your response here..."><?= htmlspecialchars($c['admin_response'] ?? '') ?></textarea>
        <?php if (isset($errors['admin_response'])): ?>
            <span style="color:red;"><br><?= $errors['admin_response'] ?></span>
        <?php endif; ?>
    </p>

    <button type="submit">Submit Response & Update Status</button>
</form>

<script src="../js/admin_validation.js"></script>
<!-- Removed admin_ajax.js to disable AJAX; using normal form submissions -->
</body>
</html>

