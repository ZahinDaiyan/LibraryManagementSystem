<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$stats = $_SESSION['admin_stats'] ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>

<h2>Admin Dashboard</h2>
<p>Welcome, <?= $_SESSION['name'] ?> (Platform Admin)</p>
<hr>

<h3>Platform-Wide Statistics</h3>

<div style="display: flex; gap: 20px; flex-wrap: wrap;">
    
    <div style="border: 2px solid #333; padding: 15px; width: 200px; text-align: center;">
        <h4>Total Members</h4>
        <p style="font-size: 24px; font-weight: bold;"><?= $stats['total_members'] ?? 0 ?></p>
    </div>

    <div style="border: 2px solid #333; padding: 15px; width: 200px; text-align: center;">
        <h4>Books in Catalog</h4>
        <p style="font-size: 24px; font-weight: bold;"><?= $stats['total_books'] ?? 0 ?></p>
    </div>

    <div style="border: 2px solid #333; padding: 15px; width: 200px; text-align: center; color: blue;">
        <h4>Active Loans</h4>
        <p style="font-size: 24px; font-weight: bold;"><?= $stats['total_active_loans'] ?? 0 ?></p>
    </div>

    <div style="border: 2px solid #333; padding: 15px; width: 200px; text-align: center; color: red;">
        <h4>Overdue Loans</h4>
        <p style="font-size: 24px; font-weight: bold;"><?= $stats['total_overdue_loans'] ?? 0 ?></p>
    </div>

    <div style="border: 2px solid #333; padding: 15px; width: 200px; text-align: center; color: green;">
        <h4>Outstanding Fines</h4>
        <p style="font-size: 24px; font-weight: bold;">$<?= number_format($stats['total_fines_outstanding'] ?? 0, 2) ?></p>
    </div>

</div>

<hr>

<h3>Navigation</h3>
<ul>
    <li><a href="../../Controllers/AdminUserController.php">Manage All Users</a></li>
    <li><a href="../../Controllers/AdminBookCatalogController.php">Master Book Catalog</a></li>
    <li><a href="../../Controllers/AdminBranchController.php">Manage Branches</a></li>
    <li><a href="../../Controllers/AdminTransferController.php">Inter-Branch Transfers</a></li>
    <li><a href="../../Controllers/AdminComplaintController.php">Member Complaints</a></li>
    <li><a href="../../Controllers/AdminAuditLogController.php">Platform Audit Logs</a></li>
    <li><a href="../../Controllers/AdminAnnouncementController.php">Platform Announcements</a></li>
    <li><a href="../../Controllers/AdminSettingsController.php">Global System Settings</a></li>
    <li><a href="../../Controllers/AdminReportController.php">Platform Reports</a></li>
</ul>

<br>
<a href="../../Controllers/LogoutController.php"><button>Logout</button></a>

</body>
</html>
