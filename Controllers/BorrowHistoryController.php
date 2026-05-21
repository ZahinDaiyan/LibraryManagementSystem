<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Unauthorized', 'history' => array()));
        exit();
    }

    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/LoanModel.php';

$conn = Connect();
$history = getBorrowHistory($conn, $_SESSION['id']);
Close($conn);

$_SESSION['borrow_history'] = $history;

if ($expectsJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('success' => true, 'history' => $history));
    exit();
}

header("Location: ../Views/Member/BorrowHistoryView.php");
exit();
