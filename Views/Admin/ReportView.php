<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$reports = $_SESSION['admin_reports'] ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/admin.css">
    <title>Platform Reports</title>
    <style>
        .report-section { margin-bottom: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        .bar { height: 20px; background-color: #4CAF50; display: inline-block; }
    </style>
</head>
<body>

<h2>Platform-Wide Data Reports</h2>
<a href="dashboardView.php">← Back to Dashboard</a> | 
<a href="../../Controllers/AdminReportExportController.php">Export Data (Printable)</a>
<hr>

<div class="report-section">
    <h3>1. Total Borrows per Month</h3>
    <table>
        <tr><th>Month</th><th>Loan Count</th></tr>
        <?php foreach ($reports['borrows'] as $r): ?>
            <tr><td><?= $r['month'] ?></td><td><?= $r['count'] ?></td></tr>
        <?php endforeach; ?>
        <?php if(empty($reports['borrows'])) echo "<tr><td colspan='2'>No data available.</td></tr>"; ?>
    </table>
</div>

<div class="report-section">
    <h3>2. Total Fines Collected per Month</h3>
    <table>
        <tr><th>Month</th><th>Amount Collected</th></tr>
        <?php foreach ($reports['fines'] as $r): ?>
            <tr><td><?= $r['month'] ?></td><td>$<?= number_format($r['total'], 2) ?></td></tr>
        <?php endforeach; ?>
        <?php if(empty($reports['fines'])) echo "<tr><td colspan='2'>No data available.</td></tr>"; ?>
    </table>
</div>

<div style="display: flex; gap: 40px;">
    <div class="report-section" style="flex: 1;">
        <h3>3. Most Active Branches</h3>
        <table>
            <tr><th>Branch</th><th>Total Loans</th></tr>
            <?php foreach ($reports['branches'] as $r): ?>
                <tr><td><?= $r['name'] ?></td><td><?= $r['loan_count'] ?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="report-section" style="flex: 1;">
        <h3>4. Most Borrowed Genres</h3>
        <table>
            <tr><th>Genre</th><th>Borrow Count</th></tr>
            <?php foreach ($reports['genres'] as $r): ?>
                <tr><td><?= $r['name'] ?></td><td><?= $r['borrow_count'] ?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<div class="report-section">
    <h3>5. Member Growth Trend (New Members)</h3>
    <table>
        <tr><th>Month</th><th>New Registrations</th></tr>
        <?php foreach ($reports['growth'] as $r): ?>
            <tr><td><?= $r['month'] ?></td><td><?= $r['count'] ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
