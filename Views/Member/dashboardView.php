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

<p>
    Welcome,
    <?php echo $_SESSION['name']; ?>
</p>

<hr>

<h3>Features</h3>

<ul>

    <li>
        <a href="../../controllers/BookIndexController.php">
            Browse Books
        </a>
    </li>

    <li>
        <a href="../../controllers/MyLoansController.php">
            Active Loans
        </a>
    </li>

    <li>
        <a href="../../controllers/BorrowHistoryController.php">
            Borrow History
        </a>
    </li>

    <li>
        <a href="../../controllers/ReadingListController.php">
            Reading List
        </a>
    </li>

    <li>
        <a href="../../controllers/ProfileController.php">
            My Profile
        </a>
    </li>

</ul>

<br>

<a href="../../controllers/LogoutController.php">
    <button>Logout</button>
</a>

</body>
</html>