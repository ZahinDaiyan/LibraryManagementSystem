<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

$user = $_SESSION['user_data'] ?? null;
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';

unset($_SESSION['msg'], $_SESSION['error']);

if (!$user) {
    header("Location: dashboardView.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
</head>
<body>

<h2>My Profile</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<h3>Personal Information</h3>
<form novalidate action="../../Controllers/ProfileUpdateController.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="update_profile">
    
    <p>
        <?php if ($user['profile_pic']): ?>
            <img src="../../uploads/profiles/<?= $user['profile_pic'] ?>" alt="Profile Picture" width="100"><br>
        <?php endif; ?>
        <label>Profile Picture:</label><br>
        <input type="file" name="profile_pic">
    </p>

    <p>
        <label>Name:</label><br>
        <input type="text" name="name" value="<?= $user['name'] ?>" required>
    </p>
    <p>
        <label>Email:</label><br>
        <input type="email" name="email" value="<?= $user['email'] ?>" required>
    </p>
    <p>
        <label>Phone:</label><br>
        <input type="text" name="phone" value="<?= $user['phone'] ?>" required>
    </p>
    <p>
        <label>Primary Branch:</label><br>
        <input type="text" value="<?= $user['branch_name'] ?> (<?= $user['branch_city'] ?>)" disabled>
    </p>
    <p>
        <label>Membership Status:</label><br>
        <b style="color: <?= $user['is_active'] ? 'green' : 'red' ?>;">
            <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
        </b>
    </p>
    
    <button type="submit">Update Information</button>
</form>

<hr>

<h3>Change Password</h3>
<form novalidate action="../../Controllers/ProfileUpdateController.php" method="POST">
    <input type="hidden" name="action" value="change_password">
    <p>
        <label>Current Password:</label><br>
        <input type="password" name="current_password" required>
    </p>
    <p>
        <label>New Password:</label><br>
        <input type="password" name="new_password" required>
    </p>
    <p>
        <label>Confirm New Password:</label><br>
        <input type="password" name="confirm_password" required>
    </p>
    <button type="submit">Change Password</button>
</form>

</body>
</html>

