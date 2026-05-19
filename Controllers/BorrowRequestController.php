<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/LoanModel.php';

$member_id = $_SESSION['id'];
$book_id = isset($_POST['book_id']) && is_numeric($_POST['book_id']) ? intval($_POST['book_id']) : 0;
$branch_id = isset($_POST['branch_id']) && is_numeric($_POST['branch_id']) ? intval($_POST['branch_id']) : 0;

if ($book_id <= 0 || $branch_id <= 0) {
    $_SESSION['error'] = "Invalid borrow request.";
    header("Location: /LibraryManagementSystem/Controllers/BookIndexController.php");
    exit();
}

$conn = Connect();

if (!checkBookAvailabilityInBranch($conn, $book_id, $branch_id)) {
    $_SESSION['error'] = "Book not available";
    $_SESSION['book_details_id'] = $book_id;
    Close($conn);
    header("Location: BookDetailsController.php");
    exit();
}

if (hasPendingBorrowRequest($conn, $member_id, $book_id)) {
    $_SESSION['error'] = "Already requested";
    $_SESSION['book_details_id'] = $book_id;
    Close($conn);
    header("Location: BookDetailsController.php");
    exit();
}

if (createBorrowRequest($conn, $member_id, $book_id, $branch_id)) {
    $_SESSION['msg'] = "Borrow request submitted";
} else {
    $_SESSION['error'] = "Failed to submit borrow request";
}

Close($conn);

header("Location: MemberDashboardController.php");

?>