<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: /LibraryManagementSystem/Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/FineModel.php';

$action = $_POST['action'] ?? '';
$member_id = $_SESSION['id'];
$conn = Connect();

if ($action === 'confirm_payment') {
    $fine_id = $_POST['fine_id'];
    $details = htmlspecialchars($_POST['payment_details']);
    
    if (submitPaymentConfirmation($conn, $fine_id, $member_id, $details)) {
        $_SESSION['msg'] = "Payment confirmation submitted for verification";
    } else {
        $_SESSION['error'] = "Failed to submit confirmation";
    }
}

Close($conn);
header("Location: FineController.php");
exit();
