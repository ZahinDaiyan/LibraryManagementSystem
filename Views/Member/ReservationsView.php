<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <link rel="stylesheet" href="<?= Url::asset('Views/css/member.css') ?>">
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
            <form method="POST" action="../../Controllers/ReservationActionController.php" style="display:inline;"><input type="hidden" name="action" value="cancel"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button type="submit"  onclick="return confirm('Cancel reservation?')" style="background:none; border:none; color:blue; text-decoration:underline; cursor:pointer; padding:0; font:inherit; ">Cancel</button></form>
        </td>
    </tr>
    <?php } ?>
</table>

<script src="../js/member_ajax.js?v=<?= time() ?>"></script>
</body>
</html>
