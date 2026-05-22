<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../LoginView.php');
    exit();
}

$profile = $_SESSION['branch_manager_profile'] ?? array();
$branches = $_SESSION['bm_profile_branches'] ?? array();
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Branch Manager Profile</title>
    <link rel="stylesheet" href="../css/manager.css">
</head>
<body>

<h2>Branch Manager Profile</h2>
<a href="/LibraryManagementSystem/Controllers/BranchManagerDashboardController.php">Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<h3>Branches Under Oversight</h3>
<ul>
    <?php if (empty($branches)): ?>
        <li>No managed branches assigned.</li>
    <?php endif; ?>
    <?php foreach ($branches as $branch): ?>
        <li><?= htmlspecialchars($branch['name'] ?? '') ?> - <?= htmlspecialchars($branch['city'] ?? '') ?></li>
    <?php endforeach; ?>
</ul>

<hr>

<h3>Update Profile</h3>
<form novalidate action="/LibraryManagementSystem/Controllers/BranchManagerProfileUpdateController.php" method="POST" onsubmit="return validateManagerProfile(this)">
    <p>
        <label>Name:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($profile['name'] ?? '') ?>">
    </p>
    <p>
        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>">
    </p>
    <p>
        <label>Phone:</label><br>
        <input type="text" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
    </p>
    <p>
        <label>Current Password:</label><br>
        <input type="password" name="current_password" required>
    </p>
    <p>
        <label>New Password:</label><br>
        <input type="password" name="new_password" minlength="8">
    </p>
    <p>
        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password">
    </p>
    <button type="submit">Update Profile</button>
</form>

<script src="../js/branch_manager.js"></script>
</body>
</html>
