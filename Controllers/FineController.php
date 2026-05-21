<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Unauthorized', 'unpaid_fines' => array(), 'paid_fines' => array()));
        exit();
    }

    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/FineModel.php';

$conn = Connect();
$unpaid = getMemberFines($conn, $_SESSION['id']);
$paid = getPaidFineHistory($conn, $_SESSION['id']);
Close($conn);

$_SESSION['unpaid_fines'] = $unpaid;
$_SESSION['paid_fines'] = $paid;

if ($expectsJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'success' => true,
        'unpaid_fines' => $unpaid,
        'paid_fines' => $paid
    ));
    exit();
}

header("Location: ../Views/Member/FinesView.php");
exit();
