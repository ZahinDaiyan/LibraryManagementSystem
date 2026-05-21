<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Unauthorized', array('reports' => array()), 403);
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

$reports = $_SESSION['admin_reports'] ?? null;

if (!$reports) {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'No report data available', array('reports' => array()), 404);
    }

    header('Location: AdminReportController.php');
    exit();
}

if (adminWantsJson()) {
    adminJsonResponse(true, 'Report data loaded', array(
        'reports' => $reports,
        'generated_on' => date('M d, Y')
    ));
}

$date = date('M d, Y');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Library Platform Report Summary - <?= $date ?></title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.6; padding: 40px; }
        .header { text-align: center; border-bottom: 2px solid #333; margin-bottom: 30px; padding-bottom: 10px; }
        h1 { margin: 0; font-size: 24px; }
        h2 { border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-top: 30px; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; page-break-inside: avoid; }
        th, td { border: 1px solid #999; padding: 10px; text-align: left; }
        th { background-color: #f0f0f0; }
        .footer { margin-top: 50px; font-size: 12px; text-align: center; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 20px;">
    <button onclick="window.print()">Print Report / Save as PDF</button>
    <a href="AdminReportController.php">← Back to Dashboard</a>
</div>

<div class="header">
    <h1>Library Management System</h1>
    <p><b>Platform-Wide Operations Summary</b></p>
    <p>Generated on: <?= $date ?></p>
</div>

<div class="report-section">
    <h2>1. Monthly Loan Activity (Last 6 Months)</h2>
    <table>
        <tr><th>Report Month</th><th>Total Books Borrowed</th></tr>
        <?php foreach ($reports['borrows'] as $r): ?>
            <tr><td><?= $r['month'] ?></td><td><?= $r['count'] ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="report-section">
    <h2>2. Revenue Summary: Fines Collected</h2>
    <table>
        <tr><th>Collection Month</th><th>Amount Collected (USD)</th></tr>
        <?php foreach ($reports['fines'] as $r): ?>
            <tr><td><?= $r['month'] ?></td><td>$<?= number_format($r['total'], 2) ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="report-section">
    <h2>3. Top 5 Most Active Branches</h2>
    <table>
        <tr><th>Branch Name</th><th>Total Completed/Active Loans</th></tr>
        <?php foreach ($reports['branches'] as $r): ?>
            <tr><td><?= $r['name'] ?></td><td><?= $r['loan_count'] ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="report-section">
    <h2>4. Genre Popularity (Top 5)</h2>
    <table>
        <tr><th>Genre Name</th><th>Total Times Borrowed</th></tr>
        <?php foreach ($reports['genres'] as $r): ?>
            <tr><td><?= $r['name'] ?></td><td><?= $r['borrow_count'] ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="report-section">
    <h2>5. Member Growth Trend</h2>
    <table>
        <tr><th>Registration Month</th><th>New Member Accounts</th></tr>
        <?php foreach ($reports['growth'] as $r): ?>
            <tr><td><?= $r['month'] ?></td><td><?= $r['count'] ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="footer">
    <p>End of Platform Summary Report. Authorized by: <?= $_SESSION['name'] ?> (Administrator)</p>
</div>

</body>
</html>
