<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/LoanModel.php';

$action = $_POST['action'] ?? '';
$loan_id = $_POST['loan_id'] ?? '';
$member_id = $_SESSION['id'];

if ($action === 'renew') {
    $conn = Connect();
    $result = requestRenewal($conn, $loan_id, $member_id);
    Close($conn);
    
    if ($result['success']) {
        $_SESSION['msg'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }
}

header("Location: MyLoansController.php");
exit();
