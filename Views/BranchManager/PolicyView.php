<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../LoginView.php');
    exit();
}

$policies = $_SESSION['bm_policies'] ?? array();
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Branch Policies</title>
</head>
<body>

<h2>Configure Branch Policies</h2>
<a href="../../Controllers/BranchManagerDashboardController.php">Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<?php if (empty($policies)): ?>
    <p>No managed branches found.</p>
<?php endif; ?>

<?php foreach ($policies as $policy): ?>
    <div style="border:1px solid #333; padding:12px; margin-bottom:15px;">
        <h3><?= htmlspecialchars($policy['branch_name'] ?? '') ?> <?= !empty($policy['branch_city']) ? '- ' . htmlspecialchars($policy['branch_city']) : '' ?></h3>
        <form novalidate action="../../Controllers/BranchManagerPolicyActionController.php" method="POST" onsubmit="return validatePolicyForm(this)">
            <input type="hidden" name="branch_id" value="<?= $policy['branch_id'] ?>">
            <p>
                <label>Maximum Borrow Duration (days):</label><br>
                <input type="number" name="max_borrow_days" value="<?= htmlspecialchars($policy['max_borrow_days'] ?? '7') ?>">
            </p>
            <p>
                <label>Maximum Books Per Member:</label><br>
                <input type="number" name="max_books_per_member" value="<?= htmlspecialchars($policy['max_books_per_member'] ?? '3') ?>">
            </p>
            <p>
                <label>Fine Rate Per Overdue Day:</label><br>
                <input type="text" name="fine_rate_per_day" value="<?= htmlspecialchars($policy['fine_rate_per_day'] ?? '0.00') ?>">
            </p>
            <p>
                <label>Maximum Allowed Renewals:</label><br>
                <input type="number" name="max_renewals" value="<?= htmlspecialchars($policy['max_renewals'] ?? '1') ?>">
            </p>
            <button type="submit">Save Policy</button>
        </form>
    </div>
<?php endforeach; ?>

<script src="../../js/branch_manager.js"></script>
</body>
</html>
