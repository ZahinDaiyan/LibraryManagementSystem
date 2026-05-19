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
    <link rel="stylesheet" href="../css/member.css?v=<?= time() ?>">
    <style>
        /* Premium announcement cards with high contrast readability */
        .announcement-card {
            background: #1a1d2e !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-left: 4px solid #f59e0b !important;
            border-radius: 8px !important;
            padding: 16px !important;
            margin-bottom: 16px !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15) !important;
        }

        .announcement-card h4 {
            color: #f59e0b !important; /* Golden amber heading for hierarchy */
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            margin-bottom: 8px !important;
        }

        .announcement-card p {
            color: #e8eaf0 !important; /* Crisp high-contrast text */
            font-size: 0.95rem !important;
            margin-bottom: 12px !important;
            line-height: 1.5 !important;
        }

        .announcement-card small {
            color: #9a9fbf !important; /* Readable gray for metadata */
            font-size: 0.8rem !important;
            font-weight: 400 !important;
        }

        /* Golden outline button style for Mark as Read */
        .btn-link {
            background: transparent !important;
            border: 1px solid #f59e0b !important;
            color: #f59e0b !important;
            padding: 4px 10px !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            text-decoration: none !important;
            margin-left: 10px !important;
            display: inline-block !important;
        }

        .btn-link:hover {
            background: #f59e0b !important;
            color: #fff !important;
        }
    </style>
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
                    <?= htmlspecialchars($n['message']) ?> 
                    <form method="POST" action="/LibraryManagementSystem/Controllers/NotificationActionController.php" style="display:inline;"><input type="hidden" name="id" value="<?= $n['id'] ?>"><button type="submit" class="btn-link">Mark as Read</button></form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div style="display: flex; gap: 40px;">

    <div style="flex: 1;">
        <h3>Features</h3>
        <ul>
            <li><a href="/LibraryManagementSystem/Controllers/BookIndexController.php">Browse Books</a></li>
            <li><a href="../../controllers/MyLoansController.php">Active Loans</a></li>
            <li><a href="../../controllers/BorrowHistoryController.php">Borrow History</a></li>
            <li><a href="../../controllers/ReservationController.php">My Reservations</a></li>
            <li><a href="../../controllers/ReadingListController.php">Reading List</a></li>
            <li><a href="../../controllers/ProfileController.php">My Profile</a></li>
            <li><a href="../../controllers/FineController.php">My Fines</a></li>
            <li><a href="../../controllers/MemberComplaintController.php">Support & Complaints</a></li>
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
            <div class="announcement-card">
                <h4><?= htmlspecialchars($a['title'] ?? '') ?></h4>
                <p><?= htmlspecialchars($a['body'] ?? '') ?></p>
                <small>By <?= htmlspecialchars($a['author_name'] ?? '') ?> on <?= htmlspecialchars($a['published_at'] ?? '') ?></small>
            </div>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>
