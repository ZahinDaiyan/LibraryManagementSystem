<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!function_exists('memberLoanActionRespond')) {
    function memberLoanActionRespond($expectsJson, $success, $message, $statusCode = 200)
    {
        if ($expectsJson) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($statusCode);
            echo json_encode(array(
                'success' => (bool)$success,
                'message' => $message,
                'redirect' => 'MyLoansController.php'
            ));
            exit();
        }

        if ($success) {
            $_SESSION['msg'] = $message;
        } else {
            $_SESSION['error'] = $message;
        }

        header("Location: MyLoansController.php");
        exit();
    }
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        memberLoanActionRespond($expectsJson, false, 'Unauthorized', 403);
    }

    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/LoanModel.php';

$action = $_POST['action'] ?? '';
$loan_id = $_POST['loan_id'] ?? '';
$member_id = $_SESSION['id'];
$success = false;
$message = 'Invalid loan action';

if ($action === 'renew') {
    $conn = Connect();
    $result = requestRenewal($conn, $loan_id, $member_id);
    Close($conn);
    
    if ($result['success']) {
        $success = true;
        $message = $result['message'];
    } else {
        $success = false;
        $message = $result['message'];
    }
}

memberLoanActionRespond($expectsJson, $success, $message, $success ? 200 : 400);
