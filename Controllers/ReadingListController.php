<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Unauthorized', 'reading_list' => array()));
        exit();
    }

    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/ReadingListModel.php';

$conn = Connect();
$list = getReadingList($conn, $_SESSION['id']);
Close($conn);

$_SESSION['reading_list'] = $list;

if ($expectsJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('success' => true, 'reading_list' => $list));
    exit();
}

header("Location: ../Views/Member/ReadingListView.php");
exit();
