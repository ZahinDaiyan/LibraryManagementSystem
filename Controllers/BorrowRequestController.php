<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: /LibraryManagementSystem/Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/LoanModel.php';

$member_id = $_SESSION['id'];
$book_id = $_POST['book_id'];
$branch_id = $_POST['branch_id'];

$conn = Connect();

/* 1. Check availability */
if (!checkBookAvailabilityInBranch($conn, $book_id, $branch_id)) {
    $_SESSION['error'] = "Book not available";
    $_SESSION['book_details_id'] = $book_id;
    Close($conn);
    header("Location: /LibraryManagementSystem/Controllers/BookDetailsController.php");
    exit();
}

/* 2. Prevent duplicate pending request */
if (hasPendingBorrowRequest($conn, $member_id, $book_id)) {
    $_SESSION['error'] = "Already requested";
    $_SESSION['book_details_id'] = $book_id;
    Close($conn);
    header("Location: /LibraryManagementSystem/Controllers/BookDetailsController.php");
    exit();
}

/* 3. Insert request */
if (createBorrowRequest($conn, $member_id, $book_id, $branch_id)) {
    $_SESSION['msg'] = "Borrow request submitted";
} else {
    $_SESSION['error'] = "Failed to submit borrow request";
}

Close($conn);

header("Location: /LibraryManagementSystem/Controllers/MemberDashboardController.php");

?>