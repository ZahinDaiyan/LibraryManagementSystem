<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!function_exists('memberReservationRespond')) {
    function memberReservationRespond($expectsJson, $success, $message, $statusCode = 200)
    {
        if ($expectsJson) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($statusCode);
            echo json_encode(array(
                'success' => (bool)$success,
                'message' => $message,
                'redirect' => 'ReservationController.php'
            ));
            exit();
        }

        if ($success) {
            $_SESSION['msg'] = $message;
        } else {
            $_SESSION['error'] = $message;
        }

        header("Location: ReservationController.php");
        exit();
    }
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        memberReservationRespond($expectsJson, false, 'Unauthorized', 403);
    }

    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/ReservationModel.php';

$action = $_POST['action'] ?? $_POST['action'] ?? '';
$member_id = $_SESSION['id'];
$conn = Connect();
$success = false;
$message = 'Invalid reservation action';

if ($action === 'reserve') {
    $book_id = $_POST['book_id'];
    $branch_id = $_POST['branch_id'];
    $result = reserveBook($conn, $member_id, $book_id, $branch_id);
    
    $success = isset($result['success']) && $result['success'] ? true : false;
    $message = isset($result['message']) ? $result['message'] : ($success ? 'Reservation created' : 'Reservation failed');
} elseif ($action === 'cancel') {
    $reservation_id = $_POST['id'];
    if (cancelReservation($conn, $reservation_id, $member_id)) {
        $success = true;
        $message = "Reservation cancelled";
    } else {
        $success = false;
        $message = "Failed to cancel";
    }
}

Close($conn);
memberReservationRespond($expectsJson, $success, $message, $success ? 200 : 400);
