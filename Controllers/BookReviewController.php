<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BookModel.php';

$action = $_POST['action'] ?? '';
$book_id = isset($_POST['book_id']) && is_numeric($_POST['book_id']) ? intval($_POST['book_id']) : 0;
$member_id = $_SESSION['id'];
$conn = Connect();

if ($action === 'submit_review') {
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = htmlspecialchars(trim($_POST['comment'] ?? ''));

    if ($book_id <= 0 || $rating < 1 || $rating > 5) {
        $_SESSION['error'] = "Invalid review submission.";
    } elseif (addOrUpdateReview($conn, $book_id, $member_id, $rating, $comment)) {
        $_SESSION['msg'] = "Review submitted successfully";
    } else {
        $_SESSION['error'] = "Failed to submit review";
    }
} elseif ($action === 'delete_review') {
    $review_id = isset($_POST['review_id']) && is_numeric($_POST['review_id']) ? intval($_POST['review_id']) : 0;
    if ($review_id > 0 && deleteReview($conn, $review_id, $member_id)) {
        $_SESSION['msg'] = "Review deleted";
    } else {
        $_SESSION['error'] = "Failed to delete review";
    }
}

Close($conn);
$_SESSION['book_details_id'] = $book_id;
header("Location: BookDetailsController.php");
exit();
