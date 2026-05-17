<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../LoginView.php');
    exit();
}

$profile = $_SESSION['branch_manager_profile'] ?? array();
$dashboard = $_SESSION['bm_dashboard'] ?? array();
$branches = $dashboard['branches'] ?? array();
$stats = $dashboard['stats'] ?? array();
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Branch Manager Dashboard</title>
</head>
<body>

<h2>Branch Manager Dashboard</h2>
<p>Welcome, <?= htmlspecialchars($_SESSION['name'] ?? $profile['name'] ?? '') ?></p>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<h3>Managed Branch Summary</h3>
<div style="display:flex; gap:20px; flex-wrap:wrap;">
    <div style="border:2px solid #333; padding:15px; width:190px; text-align:center;">
        <h4>Branches</h4>
        <p style="font-size:24px; font-weight:bold;"><?= $stats['total_branches'] ?? 0 ?></p>
    </div>
    <div style="border:2px solid #333; padding:15px; width:190px; text-align:center;">
        <h4>Librarians</h4>
        <p style="font-size:24px; font-weight:bold;"><?= $stats['total_librarians'] ?? 0 ?></p>
    </div>
    <div style="border:2px solid #333; padding:15px; width:190px; text-align:center;">
        <h4>Active Loans</h4>
        <p style="font-size:24px; font-weight:bold; color:blue;"><?= $stats['total_active_loans'] ?? 0 ?></p>
    </div>
    <div style="border:2px solid #333; padding:15px; width:190px; text-align:center;">
        <h4>Overdue Loans</h4>
        <p style="font-size:24px; font-weight:bold; color:red;"><?= $stats['total_overdue_loans'] ?? 0 ?></p>
    </div>
    <div style="border:2px solid #333; padding:15px; width:190px; text-align:center;">
        <h4>Outstanding Fines</h4>
        <p style="font-size:24px; font-weight:bold; color:green;">$<?= number_format((float)($stats['total_outstanding_fines'] ?? 0), 2) ?></p>
    </div>
</div>

<hr>

<h3>Navigation</h3>
<ul>
    <li><a href="../../Controllers/BranchManagerProfileController.php">Manage Profile</a></li>
    <li><a href="../../Controllers/BranchManagerBranchController.php">Manage Branch Profiles</a></li>
    <li><a href="../../Controllers/BranchManagerStaffController.php">Assign Librarians</a></li>
    <li><a href="../../Controllers/BranchManagerPolicyController.php">Configure Branch Policies</a></li>
    <li><a href="../../Controllers/BranchManagerReportController.php">Cross-Branch Reports</a></li>
    <li><a href="../../Controllers/BranchManagerTransferController.php">Inter-Branch Transfers</a></li>
    <li><a href="../../Controllers/BranchManagerAnnouncementController.php">Platform Announcements</a></li>
</ul>

<hr>

<h3>Branches Under Oversight</h3>
<table border="1" cellpadding="8" width="100%">
    <tr>
        <th>Branch</th>
        <th>City</th>
        <th>Librarians</th>
        <th>Active Loans</th>
        <th>Overdue</th>
        <th>Outstanding Fines</th>
        <th>Status</th>
    </tr>
    <?php if (empty($branches)): ?>
        <tr><td colspan="7">No branches are assigned to your manager account yet.</td></tr>
    <?php endif; ?>
    <?php foreach ($branches as $b): ?>
        <tr>
            <td><?= htmlspecialchars($b['name'] ?? '') ?></td>
            <td><?= htmlspecialchars($b['city'] ?? '') ?></td>
            <td><?= $b['librarian_count'] ?? 0 ?></td>
            <td><?= $b['active_loans'] ?? 0 ?></td>
            <td><?= $b['overdue_loans'] ?? 0 ?></td>
            <td>$<?= number_format((float)($b['outstanding_fines'] ?? 0), 2) ?></td>
            <td>
                <b style="color: <?= !empty($b['is_active']) ? 'green' : 'red' ?>;">
                    <?= !empty($b['is_active']) ? 'Active' : 'Inactive' ?>
                </b>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<br>
<a href="../../Controllers/LogoutController.php"><button>Logout</button></a>

</body>
</html>
