<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!function_exists('memberReadingListRespond')) {
    function memberReadingListRespond($expectsJson, $success, $message, $bookId, $redirect, $statusCode = 200)
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

        if ($redirect === 'BookDetailsController.php' && $bookId > 0) {
            $_SESSION['book_details_id'] = $bookId;
        }

        header("Location: $redirect");
        exit();
    }
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        memberReadingListRespond($expectsJson, false, 'Unauthorized', 0, '../Views/LoginView.php', 403);
    }

    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/ReadingListModel.php';

$action = $_POST['action'] ?? $_POST['action'] ?? '';
$book_id = $_POST['book_id'] ?? $_POST['book_id'] ?? '';
$member_id = $_SESSION['id'];
$success = false;
$message = 'Invalid reading list action';

$conn = Connect();

if ($action === 'add') {
    if (addToReadingList($conn, $member_id, $book_id)) {
        $success = true;
        $message = "Added to reading list";
    } else {
        $success = false;
        $message = "Failed to add";
    }
} elseif ($action === 'remove') {
    if (removeFromReadingList($conn, $member_id, $book_id)) {
        $success = true;
        $message = "Removed from reading list";
    } else {
        $success = false;
        $message = "Failed to remove";
    }
}

Close($conn);

$redirect = 'ReadingListController.php';
if (isset($_POST['redirect']) && $_POST['redirect'] === 'details') {
    $redirect = 'BookDetailsController.php';
}

memberReadingListRespond($expectsJson, $success, $message, (int)$book_id, $redirect, $success ? 200 : 400);
