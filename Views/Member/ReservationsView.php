<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

$reservations = $_SESSION['reservations'] ?? [];
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Reservations</title>
    <link rel="stylesheet" href="../css/member.css">
</head>
<body>

<h2>My Reservations</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<table border="1" cellpadding="10">
    <tr>
        <th>Book Title</th>
        <th>Branch</th>
        <th>Reserved On</th>
        <th>Status</th>
        <th>Queue Position</th>
        <th>Action</th>
    </tr>

    <?php if (empty($reservations)): ?>
        <tr><td colspan="6">No active reservations.</td></tr>
    <?php endif; ?>

    <?php foreach ($reservations as $r) { ?>
    <tr>
        <td><?= $r['book_title'] ?></td>
        <td><?= $r['branch_name'] ?></td>
        <td><?= $r['reserved_at'] ?></td>
        <td><?= ucfirst($r['status']) ?></td>
        <td>
            <?php if ($r['status'] === 'waiting'): ?>
                <b>#<?= $r['queue_position'] ?></b>
            <?php else: ?>
                -
            <?php endif; ?>
        </td>
        <td>
            <a href="../../Controllers/ReservationActionController.php?action=cancel&id=<?= $r['id'] ?>" onclick="return confirm('Cancel reservation?')">Cancel</a>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
