<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/ReadingListModel.php';

$action = $_POST['action'] ?? $_POST['action'] ?? '';
$book_id = $_POST['book_id'] ?? $_POST['book_id'] ?? '';
$member_id = $_SESSION['id'];

$conn = Connect();

if ($action === 'add') {
    if (addToReadingList($conn, $member_id, $book_id)) {
        $_SESSION['msg'] = "Added to reading list";
    } else {
        $_SESSION['error'] = "Failed to add";
    }
} elseif ($action === 'remove') {
    if (removeFromReadingList($conn, $member_id, $book_id)) {
        $_SESSION['msg'] = "Removed from reading list";
    } else {
        $_SESSION['error'] = "Failed to remove";
    }
}

Close($conn);

if (isset($_POST['redirect']) && $_POST['redirect'] === 'details') {
    $_SESSION['book_details_id'] = $book_id;
    header("Location: BookDetailsController.php");
} else {
    header("Location: ReadingListController.php");
}
exit();
