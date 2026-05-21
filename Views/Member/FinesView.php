<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

$unpaid = $_SESSION['unpaid_fines'] ?? [];
$paid = $_SESSION['paid_fines'] ?? [];
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Fines</title>
    <link rel="stylesheet" href="../css/member.css">
</head>
<body>

<h2>My Fines</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<h3>Outstanding Fines</h3>
<table border="1" cellpadding="10">
    <tr>
        <th>Book</th>
        <th>Amount</th>
        <th>Reason</th>
        <th>Created At</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php if (empty($unpaid)): ?>
        <tr><td colspan="6">No outstanding fines. Yay!</td></tr>
    <?php endif; ?>

    <?php foreach ($unpaid as $f) { ?>
    <tr>
        <td><?= $f['book_title'] ?? 'N/A' ?></td>
        <td>$<?= number_format($f['amount'], 2) ?></td>
        <td><?= $f['reason'] ?></td>
        <td><?= $f['id'] ?></td>
        <td>Unpaid</td>
        <td>
            <form novalidate action="../../Controllers/FineActionController.php" method="POST">
                <input type="hidden" name="action" value="confirm_payment">
                <input type="hidden" name="fine_id" value="<?= $f['id'] ?>">
                <button type="submit">Mark as Paid (Demo)</button>
            </form>
        </td>
    </tr>
    <?php } ?>
</table>

<hr>

<h3>Paid Fine History</h3>
<table border="1" cellpadding="10">
    <tr>
        <th>Book</th>
        <th>Amount</th>
        <th>Reason</th>
        <th>Paid At</th>
    </tr>

    <?php if (empty($paid)): ?>
        <tr><td colspan="4">No paid fine history.</td></tr>
    <?php endif; ?>

    <?php foreach ($paid as $f) { ?>
    <tr>
        <td><?= $f['book_title'] ?? 'N/A' ?></td>
        <td>$<?= number_format($f['amount'], 2) ?></td>
        <td><?= $f['reason'] ?></td>
        <td><?= $f['paid_at'] ?></td>
    </tr>
    <?php } ?>
</table>

<script src="../js/member_ajax.js?v=<?= time() ?>"></script>
</body>
</html>

