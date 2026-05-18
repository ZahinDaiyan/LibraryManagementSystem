<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BookModel.php';

$action = $_POST['action'] ?? '';
$book_id = $_POST['book_id'] ?? '';
$member_id = $_SESSION['id'];
$conn = Connect();

if ($action === 'submit_review') {
    $rating = (int)$_POST['rating'];
    $comment = htmlspecialchars($_POST['comment']);
    
    if (addOrUpdateReview($conn, $book_id, $member_id, $rating, $comment)) {
        $_SESSION['msg'] = "Review submitted successfully";
    } else {
        $_SESSION['error'] = "Failed to submit review";
    }
} elseif ($action === 'delete_review') {
    $review_id = $_POST['review_id'];
    if (deleteReview($conn, $review_id, $member_id)) {
        $_SESSION['msg'] = "Review deleted";
    } else {
        $_SESSION['error'] = "Failed to delete review";
    }
}

Close($conn);
$_SESSION['book_details_id'] = $book_id;
header("Location: BookDetailsController.php");
exit();
