<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!function_exists('memberFineActionRespond')) {
    function memberFineActionRespond($expectsJson, $success, $message, $statusCode = 200)
    {
        if ($expectsJson) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($statusCode);
            echo json_encode(array(
                'success' => (bool)$success,
                'message' => $message,
                'redirect' => 'FineController.php'
            ));
            exit();
        }

        if ($success) {
            $_SESSION['msg'] = $message;
        } else {
            $_SESSION['error'] = $message;
        }

        header("Location: FineController.php");
        exit();
    }
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        memberFineActionRespond($expectsJson, false, 'Unauthorized', 403);
    }

    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/FineModel.php';

$action = $_POST['action'] ?? '';
$member_id = $_SESSION['id'];
$conn = Connect();
$success = false;
$message = 'Invalid fine action';

if ($action === 'confirm_payment') {
    $fine_id = $_POST['fine_id'];
    $details = htmlspecialchars($_POST['payment_details']);
    
    if (submitPaymentConfirmation($conn, $fine_id, $member_id, $details)) {
        $success = true;
        $message = "Payment confirmation submitted for verification";
    } else {
        $success = false;
        $message = "Failed to submit confirmation";
    }
}

Close($conn);
memberFineActionRespond($expectsJson, $success, $message, $success ? 200 : 400);
