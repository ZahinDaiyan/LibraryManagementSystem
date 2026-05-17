<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$settings = $_SESSION['system_settings'] ?? [];
$errors = $_SESSION['form_errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];
$msg = $_SESSION['msg'] ?? '';

unset($_SESSION['form_errors'], $_SESSION['old_data'], $_SESSION['msg']);

// Helper to get value
function getVal($key, $settings, $old_data) {
    return $old_data[$key] ?? $settings[$key]['setting_value'] ?? '';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Global System Settings</title>
</head>
<body>

<h2>Global System Settings & Defaults</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>

<form novalidate action="../../Controllers/AdminSettingsActionController.php" method="POST" onsubmit="return validateSettingsForm(this)">
    <input type="hidden" name="action" value="update_settings">

    <h3>Platform Configuration</h3>
    <p>
        <label>
            <input type="checkbox" name="allow_self_registration" value="1" <?= getVal('allow_self_registration', $settings, $old_data) == '1' ? 'checked' : '' ?>>
            Allow Member Self-Registration
        </label>
        <br><small>If disabled, members must be created or invited by an Admin/Staff.</small>
    </p>

    <hr>

    <h3>Global Policy Fallbacks</h3>
    <p><i>These values are used if a branch has no custom policy defined.</i></p>

    <p>
        <label>Default Fine Rate (per day):</label><br>
        <input type="text" name="default_fine_rate" value="<?= htmlspecialchars(getVal('default_fine_rate', $settings, $old_data)) ?>">
        <?php if (isset($errors['default_fine_rate'])): ?>
            <span style="color:red;"><br><?= $errors['default_fine_rate'] ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label>Default Max Borrow Days:</label><br>
        <input type="number" name="default_max_borrow_days" value="<?= htmlspecialchars(getVal('default_max_borrow_days', $settings, $old_data)) ?>">
        <?php if (isset($errors['default_max_borrow_days'])): ?>
            <span style="color:red;"><br><?= $errors['default_max_borrow_days'] ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label>Default Max Books per Member:</label><br>
        <input type="number" name="default_max_books_per_member" value="<?= htmlspecialchars(getVal('default_max_books_per_member', $settings, $old_data)) ?>">
        <?php if (isset($errors['default_max_books_per_member'])): ?>
            <span style="color:red;"><br><?= $errors['default_max_books_per_member'] ?></span>
        <?php endif; ?>
    </p>

    <hr>
    <button type="submit">Save Global Settings</button>
</form>

<script src="../js/admin_validation.js"></script>
</body>
</html>

