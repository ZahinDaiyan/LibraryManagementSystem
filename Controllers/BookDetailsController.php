<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: /LibraryManagementSystem/Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/BookModel.php';
require_once '../models/ReadingListModel.php';

$id = intval($_POST['id'] ?? $_SESSION['book_details_id'] ?? 0);
unset($_SESSION['book_details_id']);

if ($id <= 0) {
    $_SESSION['error'] = "Invalid book selection.";
    header("Location: /LibraryManagementSystem/Controllers/BookIndexController.php");
    exit();
}

$conn = Connect();

$book = getBookById($conn, $id);
$availability = getBookAvailabilityByBranches($conn, $id);
$reviews = getBookReviews($conn, $id);
$rating_info = getBookAverageRating($conn, $id);
$in_reading_list = isInReadingList($conn, $_SESSION['id'], $id);

Close($conn);

$_SESSION['book'] = $book;
$_SESSION['availability'] = $availability;
$_SESSION['reviews'] = $reviews;
$_SESSION['rating_info'] = $rating_info;
$_SESSION['in_reading_list'] = $in_reading_list;

header("Location: /LibraryManagementSystem/Views/Member/BookDetailsView.php");
exit();

?>
