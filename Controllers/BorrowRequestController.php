<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: /LibraryManagementSystem/Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';

$member_id = $_SESSION['id'];
$book_id = $_POST['book_id'];
$branch_id = $_POST['branch_id'];

$conn = Connect();

/* 1. Check availability */
$sql = "SELECT available_copies 
        FROM branch_inventory 
        WHERE book_id='$book_id' AND branch_id='$branch_id'";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row || $row['available_copies'] <= 0) {
    $_SESSION['error'] = "Book not available";
    $_SESSION['book_details_id'] = $book_id;
    header("Location: /LibraryManagementSystem/Controllers/BookDetailsController.php");
    exit();
}

/* 2. Prevent duplicate pending request */
$sql2 = "SELECT id FROM borrow_records 
         WHERE member_id='$member_id' 
         AND book_id='$book_id' 
         AND status='pending'";

$check = mysqli_query($conn, $sql2);

if (mysqli_num_rows($check) > 0) {
    $_SESSION['error'] = "Already requested";
    $_SESSION['book_details_id'] = $book_id;
    header("Location: /LibraryManagementSystem/Controllers/BookDetailsController.php");
    exit();
}

/* 3. Insert request */
$sql3 = "INSERT INTO borrow_records
(member_id, book_id, branch_id, status, borrow_date)
VALUES
('$member_id', '$book_id', '$branch_id', 'pending', CURDATE())";

mysqli_query($conn, $sql3);

Close($conn);

$_SESSION['msg'] = "Borrow request submitted";

header("Location: /LibraryManagementSystem/Controllers/MemberDashboardController.php");

?>