<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!function_exists('memberBookReviewRespond')) {
    function memberBookReviewRespond($expectsJson, $success, $message, $bookId, $statusCode = 200)
    {
        if ($expectsJson) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($statusCode);
            echo json_encode(array(
                'success' => (bool)$success,
                'message' => $message,
                'book_id' => (int)$bookId,
                'redirect' => 'BookDetailsController.php'
            ));
            exit();
        }

        if ($success) {
            $_SESSION['msg'] = $message;
        } else {
            $_SESSION['error'] = $message;
        }

        $_SESSION['book_details_id'] = $bookId;
        header("Location: BookDetailsController.php");
        exit();
    }
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        memberBookReviewRespond($expectsJson, false, 'Unauthorized', 0, 403);
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BookModel.php';

$action = $_POST['action'] ?? '';
$book_id = isset($_POST['book_id']) && is_numeric($_POST['book_id']) ? intval($_POST['book_id']) : 0;
$member_id = $_SESSION['id'];
$conn = Connect();
$success = false;
$message = 'Invalid review action.';

if ($action === 'submit_review') {
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = htmlspecialchars(trim($_POST['comment'] ?? ''));

    if ($book_id <= 0 || $rating < 1 || $rating > 5) {
        $success = false;
        $message = "Invalid review submission.";
    } elseif (addOrUpdateReview($conn, $book_id, $member_id, $rating, $comment)) {
        $success = true;
        $message = "Review submitted successfully";
    } else {
        $success = false;
        $message = "Failed to submit review";
    }
} elseif ($action === 'delete_review') {
    $review_id = isset($_POST['review_id']) && is_numeric($_POST['review_id']) ? intval($_POST['review_id']) : 0;
    if ($review_id > 0 && deleteReview($conn, $review_id, $member_id)) {
        $success = true;
        $message = "Review deleted";
    } else {
        $success = false;
        $message = "Failed to delete review";
    }
}

Close($conn);
memberBookReviewRespond($expectsJson, $success, $message, $book_id, $success ? 200 : 400);
