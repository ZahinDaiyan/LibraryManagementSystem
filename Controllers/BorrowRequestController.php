<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!function_exists('memberBorrowRequestRespond')) {
    function memberBorrowRequestRespond($expectsJson, $success, $message, $bookId, $redirect, $statusCode = 200)
    {
        if ($expectsJson) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($statusCode);
            echo json_encode(array(
                'success' => (bool)$success,
                'message' => $message,
                'book_id' => (int)$bookId,
                'redirect' => $redirect
            ));
            exit();
        }

        if ($success) {
            $_SESSION['msg'] = $message;
        } else {
            $_SESSION['error'] = $message;
        }

        if ($bookId > 0) {
            $_SESSION['book_details_id'] = $bookId;
        }

        header("Location: $redirect");
        exit();
    }
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        memberBorrowRequestRespond($expectsJson, false, 'Unauthorized', 0, '../Views/LoginView.php', 403);
    }

    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/LoanModel.php';

$member_id = $_SESSION['id'];
$book_id = isset($_POST['book_id']) && is_numeric($_POST['book_id']) ? intval($_POST['book_id']) : 0;
$branch_id = isset($_POST['branch_id']) && is_numeric($_POST['branch_id']) ? intval($_POST['branch_id']) : 0;

if ($book_id <= 0 || $branch_id <= 0) {
    memberBorrowRequestRespond($expectsJson, false, "Invalid borrow request.", $book_id, "/LibraryManagementSystem/Controllers/BookIndexController.php", 400);
}

$conn = Connect();

if (!checkBookAvailabilityInBranch($conn, $book_id, $branch_id)) {
    Close($conn);
    memberBorrowRequestRespond($expectsJson, false, "Book not available", $book_id, "BookDetailsController.php", 409);
}

if (hasPendingBorrowRequest($conn, $member_id, $book_id)) {
    Close($conn);
    memberBorrowRequestRespond($expectsJson, false, "Already requested", $book_id, "BookDetailsController.php", 409);
}

if (createBorrowRequest($conn, $member_id, $book_id, $branch_id)) {
    Close($conn);
    memberBorrowRequestRespond($expectsJson, true, "Borrow request submitted", $book_id, "MemberDashboardController.php", 200);
} else {
    Close($conn);
    memberBorrowRequestRespond($expectsJson, false, "Failed to submit borrow request", $book_id, "BookDetailsController.php", 500);
}


?>