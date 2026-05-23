<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Helpers\Url;

if (!class_exists(Url::class)) {
    require_once __DIR__ . '/../../app/Helpers/Url.php';
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

require_once '../../models/DB.php';
require_once '../../models/LoanModel.php';

$conn = Connect();
$history = getBorrowHistory($conn, $_SESSION['id']);
Close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Borrow History</title>
    <link rel="stylesheet" href="<?= Url::asset('Views/css/member.css') ?>">
</head>
<body>

<h2>Borrow History</h2>
<a href="dashboardView.php">← Back to Dashboard</a>
<hr>

<table border="1" cellpadding="10">
    <tr>
        <th>Book Title</th>
        <th>Branch</th>
        <th>Borrow Date</th>
        <th>Return Date</th>
        <th>Status</th>
    </tr>

    <?php if (empty($history)): ?>
        <tr><td colspan="5">No borrow history found.</td></tr>
    <?php endif; ?>

    <?php foreach ($history as $h) { ?>
    <tr>
        <td><?= $h['book_title'] ?></td>
        <td><?= $h['branch_name'] ?></td>
        <td><?= $h['borrow_date'] ?></td>
        <td><?= $h['return_date'] ?? 'N/A' ?></td>
        <td><?= ucfirst($h['status']) ?></td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
