<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

require_once '../models/DB.php';
require_once '../models/BookModel.php';
require_once '../models/ReadingListModel.php';

$id = intval($_POST['id'] ?? $_SESSION['book_details_id'] ?? 0);
unset($_SESSION['book_details_id']);

if ($id <= 0) {
    if ($expectsJson) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(400);
        echo json_encode(array('success' => false, 'message' => 'Invalid book selection.'));
        exit();
    }

    $_SESSION['error'] = "Invalid book selection.";
    header("Location: /LibraryManagementSystem/Controllers/BookIndexController.php");
    exit();
}

$conn = Connect();

$book = getBookById($conn, $id);
$availability = getBookAvailabilityByBranches($conn, $id);
$reviews = getBookReviews($conn, $id);
$rating_info = getBookAverageRating($conn, $id);
$memberId = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0;
$in_reading_list = $memberId > 0 ? isInReadingList($conn, $memberId, $id) : false;

Close($conn);

$_SESSION['book'] = $book;
$_SESSION['availability'] = $availability;
$_SESSION['reviews'] = $reviews;
$_SESSION['rating_info'] = $rating_info;
$_SESSION['in_reading_list'] = $in_reading_list;

if ($expectsJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'success' => true,
        'message' => 'Book details loaded',
        'book' => $book,
        'availability' => $availability,
        'reviews' => $reviews,
        'rating_info' => $rating_info,
        'in_reading_list' => $in_reading_list
    ));
    exit();
}

header("Location: ../Views/Member/BookDetailsView.php");
exit();

?>
