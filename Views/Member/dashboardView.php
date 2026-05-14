<?php
session_start();

if (!isset($_SESSION['role'])) {
    header('Location: ../LoginView.php');
    exit();
}

if ($_SESSION['role'] != 'member') {
    header('Location: ../../index.php');
    exit();
}

$announcements = $_SESSION['announcements'] ?? [];
$notifications = $_SESSION['notifications'] ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Member Dashboard</title>
</head>
<body>

<h2>Member Dashboard</h2>
<p>Welcome, <?php echo $_SESSION['name']; ?></p>

<hr>

<?php if (!empty($notifications)): ?>
    <div style="background-color: #fff3cd; border: 1px solid #ffeeba; padding: 10px; margin-bottom: 20px;">
        <h3>Notifications</h3>
        <ul>
            <?php foreach ($notifications as $n): ?>
                <li>
                    <?= $n['message'] ?> 
                    <a href="../../Controllers/NotificationActionController.php?id=<?= $n['id'] ?>">[Mark as Read]</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div style="display: flex; gap: 40px;">

    <div style="flex: 1;">
        <h3>Features</h3>
        <ul>
            <li><a href="../../controllers/BookIndexController.php">Browse Books</a></li>
            <li><a href="../../controllers/MyLoansController.php">Active Loans</a></li>
            <li><a href="../../controllers/BorrowHistoryController.php">Borrow History</a></li>
            <li><a href="../../controllers/ReservationController.php">My Reservations</a></li>
            <li><a href="../../controllers/ReadingListController.php">Reading List</a></li>
            <li><a href="../../controllers/ProfileController.php">My Profile</a></li>
            <li><a href="../../controllers/FineController.php">My Fines</a></li>
        </ul>
        <br>
        <a href="../../controllers/LogoutController.php"><button>Logout</button></a>
    </div>

    <div style="flex: 2; border-left: 1px solid #ccc; padding-left: 20px;">
        <h3>Library Announcements</h3>
        <?php if (empty($announcements)): ?>
            <p>No announcements.</p>
        <?php endif; ?>
        <?php foreach ($announcements as $a): ?>
            <div style="border-bottom: 1px solid #eee; margin-bottom: 15px; padding-bottom: 10px;">
                <h4><?= $a['title'] ?></h4>
                <p><?= $a['body'] ?></p>
                <small>By <?= $a['author_name'] ?> on <?= $a['created_at'] ?></small>
            </div>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>
