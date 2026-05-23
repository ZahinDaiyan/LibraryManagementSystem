<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

$complaints = $_SESSION['member_complaints'] ?? [];
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Complaints</title>
    <link rel="stylesheet" href="<?= Url::asset('Views/css/member.css') ?>">
</head>
<body>

<h2>My Complaints</h2>
<a href="dashboardView.php">← Back to Dashboard</a> | 
<a href="ComplaintFormView.php">Submit New Complaint</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<table border="1" cellpadding="10" width="100%">
    <tr>
        <th>Date</th>
        <th>Title</th>
        <th>Status</th>
        <th>Admin Response</th>
    </tr>

    <?php if (empty($complaints)): ?>
        <tr><td colspan="4">You haven't submitted any complaints yet.</td></tr>
    <?php endif; ?>

    <?php foreach ($complaints as $c): ?>
    <tr>
        <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
        <td>
            <b><?= htmlspecialchars($c['title']) ?></b><br>
            <small><?= nl2br(htmlspecialchars($c['description'])) ?></small>
        </td>
        <td>
            <b style="color: <?= $c['status'] === 'resolved' ? 'green' : ($c['status'] === 'in_review' ? 'orange' : 'gray') ?>;">
                <?= ucfirst(str_replace('_', ' ', $c['status'])) ?>
            </b>
        </td>
        <td>
            <?= $c['admin_response'] ? nl2br(htmlspecialchars($c['admin_response'])) : '<i>No response yet</i>' ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
