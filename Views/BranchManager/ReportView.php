<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../LoginView.php');
    exit();
}

$reports = $_SESSION['bm_reports'] ?? array();
$branches = $reports['branches'] ?? array();
$inventory = $reports['inventory'] ?? array();
$borrowingStats = $reports['borrowing_stats'] ?? array();
$mostBorrowed = $reports['most_borrowed'] ?? array();
$topMembers = $reports['top_members'] ?? array();
$outstandingFines = $reports['outstanding_fines'] ?? array();
$newMembers = $reports['new_members'] ?? array();
$monthly = $reports['monthly'] ?? array();
$librarianActivity = $reports['librarian_activity'] ?? array();
$overdueAlerts = $reports['overdue_alerts'] ?? array();
$selectedBranch = $_GET['branch_id'] ?? '0';
$selectedMonth = $_GET['month'] ?? date('Y-m');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Branch Manager Reports</title>
    <link rel="stylesheet" href="../css/manager.css">
</head>
<body>

<h2>Cross-Branch Reports</h2>
<a href="../../Controllers/BranchManagerDashboardController.php">Back to Dashboard</a>
<hr>

<h3>Monthly Report Filter</h3>
<form novalidate action="../../Controllers/BranchManagerReportController.php" method="GET">
    <label>Branch:</label>
    <select name="branch_id">
        <option value="0">All Managed Branches</option>
        <?php foreach ($branches as $branch): ?>
            <option value="<?= $branch['id'] ?>" <?= (string)$selectedBranch === (string)$branch['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($branch['name'] ?? '') ?>
            </option>
        <?php endforeach; ?>
    </select>
    <label>Month:</label>
    <input type="month" name="month" value="<?= htmlspecialchars($selectedMonth) ?>">
    <button type="submit">View</button>
</form>

<hr>

<h3>Branch Borrowing Statistics</h3>
<table border="1" cellpadding="8" width="100%">
    <tr><th>Branch</th><th>Active Loans</th><th>Overdue Loans</th><th>Outstanding Fines</th></tr>
    <?php if (empty($borrowingStats)): ?>
        <tr><td colspan="4">No branch statistics found.</td></tr>
    <?php endif; ?>
    <?php foreach ($borrowingStats as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['branch_name'] ?? '') ?></td>
            <td><?= $row['active_loans'] ?? 0 ?></td>
            <td><?= $row['overdue_loans'] ?? 0 ?></td>
            <td>$<?= number_format((float)($row['outstanding_fines'] ?? 0), 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<hr>

<h3>Cross-Branch Inventory</h3>
<table border="1" cellpadding="8" width="100%">
    <tr><th>Branch</th><th>Book</th><th>Author</th><th>ISBN</th><th>Total</th><th>Available</th></tr>
    <?php if (empty($inventory)): ?>
        <tr><td colspan="6">No inventory records found.</td></tr>
    <?php endif; ?>
    <?php foreach ($inventory as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['branch_name'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['title'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['author'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['isbn'] ?? '') ?></td>
            <td><?= $row['total_copies'] ?? 0 ?></td>
            <td><?= $row['available_copies'] ?? 0 ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<hr>

<h3>Most Borrowed Books Across Managed Branches</h3>
<table border="1" cellpadding="8" width="100%">
    <tr><th>Book</th><th>Author</th><th>Borrows</th></tr>
    <?php if (empty($mostBorrowed)): ?>
        <tr><td colspan="3">No borrow data found.</td></tr>
    <?php endif; ?>
    <?php foreach ($mostBorrowed as $book): ?>
        <tr>
            <td><?= htmlspecialchars($book['title'] ?? '') ?></td>
            <td><?= htmlspecialchars($book['author'] ?? '') ?></td>
            <td><?= $book['borrow_count'] ?? 0 ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<hr>

<h3>Member Activity Reports</h3>
<h4>Members With Most Borrows</h4>
<table border="1" cellpadding="8" width="100%">
    <tr><th>Member</th><th>Email</th><th>Borrows</th></tr>
    <?php if (empty($topMembers)): ?>
        <tr><td colspan="3">No member borrow data found.</td></tr>
    <?php endif; ?>
    <?php foreach ($topMembers as $member): ?>
        <tr>
            <td><?= htmlspecialchars($member['name'] ?? '') ?></td>
            <td><?= htmlspecialchars($member['email'] ?? '') ?></td>
            <td><?= $member['borrow_count'] ?? 0 ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h4>Members With Outstanding Fines</h4>
<table border="1" cellpadding="8" width="100%">
    <tr><th>Member</th><th>Email</th><th>Branch</th><th>Fines</th><th>Outstanding</th></tr>
    <?php if (empty($outstandingFines)): ?>
        <tr><td colspan="5">No outstanding member fines found.</td></tr>
    <?php endif; ?>
    <?php foreach ($outstandingFines as $fine): ?>
        <tr>
            <td><?= htmlspecialchars($fine['name'] ?? '') ?></td>
            <td><?= htmlspecialchars($fine['email'] ?? '') ?></td>
            <td><?= htmlspecialchars($fine['branch_name'] ?? '') ?></td>
            <td><?= $fine['fine_count'] ?? 0 ?></td>
            <td>$<?= number_format((float)($fine['outstanding_amount'] ?? 0), 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h4>New Member Registrations By Branch</h4>
<table border="1" cellpadding="8" width="100%">
    <tr><th>Branch</th><th>Members</th></tr>
    <?php if (empty($newMembers)): ?>
        <tr><td colspan="2">No registration data found.</td></tr>
    <?php endif; ?>
    <?php foreach ($newMembers as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['branch_name'] ?? '') ?></td>
            <td><?= $row['new_members'] ?? 0 ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<hr>

<h3>Monthly Branch Report</h3>
<table border="1" cellpadding="8" width="100%">
    <tr><th>Branch</th><th>Borrows</th><th>Returns</th><th>Fines Collected</th><th>New Members</th></tr>
    <?php if (empty($monthly)): ?>
        <tr><td colspan="5">No monthly data found for this filter.</td></tr>
    <?php endif; ?>
    <?php foreach ($monthly as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['branch_name'] ?? '') ?></td>
            <td><?= $row['borrows'] ?? 0 ?></td>
            <td><?= $row['returns_count'] ?? 0 ?></td>
            <td>$<?= number_format((float)($row['fines_collected'] ?? 0), 2) ?></td>
            <td><?= $row['new_members'] ?? 0 ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<hr>

<h3>Librarian Activity</h3>
<table border="1" cellpadding="8" width="100%">
    <tr><th>Librarian</th><th>Email</th><th>Branch</th><th>Borrows Processed</th><th>Returns</th><th>Fines Issued</th></tr>
    <?php if (empty($librarianActivity)): ?>
        <tr><td colspan="6">No librarian activity found.</td></tr>
    <?php endif; ?>
    <?php foreach ($librarianActivity as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['branch_name'] ?? '') ?></td>
            <td><?= $row['borrows_processed'] ?? 0 ?></td>
            <td><?= $row['returns_processed'] ?? 0 ?></td>
            <td><?= $row['fines_issued'] ?? 0 ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<hr>

<h3>Overdue Loan Alerts</h3>
<p>Show active loans overdue by more than this many days:</p>
<input type="number" id="threshold_days" value="7" min="0" max="365">
<button type="button" onclick="loadOverdueAlerts()">Load Alerts</button>
<p id="overdue_alert_message"></p>

<table border="1" cellpadding="8" width="100%">
    <thead>
        <tr><th>Record</th><th>Branch</th><th>Member</th><th>Email</th><th>Book</th><th>Due Date</th><th>Overdue Days</th></tr>
    </thead>
    <tbody id="overdue_alert_rows">
        <?php if (empty($overdueAlerts)): ?>
            <tr><td colspan="7">No overdue alerts above the default threshold.</td></tr>
        <?php endif; ?>
        <?php foreach ($overdueAlerts as $alert): ?>
            <tr>
                <td><?= $alert['borrow_record_id'] ?? '' ?></td>
                <td><?= htmlspecialchars($alert['branch_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($alert['member_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($alert['member_email'] ?? '') ?></td>
                <td><?= htmlspecialchars($alert['book_title'] ?? '') ?></td>
                <td><?= htmlspecialchars($alert['due_date'] ?? '') ?></td>
                <td><?= $alert['overdue_days'] ?? 0 ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script src="../js/branch_manager.js"></script>
</body>
</html>
