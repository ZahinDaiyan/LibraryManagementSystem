<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: /LibraryManagementSystem/Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/ReservationModel.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$member_id = $_SESSION['id'];
$conn = Connect();

if ($action === 'reserve') {
    $book_id = $_GET['book_id'];
    $branch_id = $_GET['branch_id'];
    $result = reserveBook($conn, $member_id, $book_id, $branch_id);
    
    if ($result['success']) {
        $_SESSION['msg'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }
} elseif ($action === 'cancel') {
    $reservation_id = $_GET['id'];
    if (cancelReservation($conn, $reservation_id, $member_id)) {
        $_SESSION['msg'] = "Reservation cancelled";
    } else {
        $_SESSION['error'] = "Failed to cancel";
    }
}

Close($conn);
header("Location: ReservationController.php");
exit();
