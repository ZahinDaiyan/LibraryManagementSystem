<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

$loans = [];
require_once '../../models/DB.php';
require_once '../../models/LoanModel.php';

$conn = Connect();
$loans = getActiveLoans($conn, $_SESSION['id']);
Close($conn);

$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Active Loans</title>
    <link rel="stylesheet" href="<?= Url::asset('Views/css/member.css') ?>">
</head>
<body>

<h2>My Active Loans</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<table border="1" cellpadding="10">
    <tr>
        <th>Book Title</th>
        <th>Branch</th>
        <th>Borrow Date</th>
        <th>Due Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php if (empty($loans)): ?>
        <tr><td colspan="6">No active loans.</td></tr>
    <?php endif; ?>

    <?php foreach ($loans as $loan) { 
        $overdue = $loan['days_remaining'] < 0;
        $color = $overdue ? 'red' : 'black';
        $renewalPending = isset($loan['renewal_request_status']) && $loan['renewal_request_status'] === 'pending';
    ?>
    <tr style="color: <?= $color ?>;">
        <td><?= $loan['book_title'] ?></td>
        <td><?= $loan['branch_name'] ?></td>
        <td><?= $loan['borrow_date'] ?></td>
        <td><?= $loan['due_date'] ?></td>
        <td>
            <?= $overdue ? "Overdue by " . abs($loan['days_remaining']) . " days" : $loan['days_remaining'] . " days remaining" ?>
        </td>
        <td>
            <?php if ($renewalPending): ?>
                <span style="color:#f59e0b; font-weight:600;">Pending approvals</span>
            <?php else: ?>
                <form novalidate action="../../Controllers/LoanActionController.php" method="POST">
                    <input type="hidden" name="action" value="renew">
                    <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                    <button type="submit" <?= $overdue ? 'disabled' : '' ?>>Request Renewal</button>
                </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php } ?>
</table>

<script src="../js/member_ajax.js?v=<?= time() ?>"></script>
</body>
</html>

