<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
        exit();
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';

$conn = Connect();
$user = getUserWithBranchById($conn, $_SESSION['id']);
Close($conn);

if (!$user) {
    if ($expectsJson) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'User profile not found'));
        exit();
    }

    $_SESSION['error'] = 'User profile not found';
    header('Location: ../Views/LoginView.php');
    exit();
}

$_SESSION['user_data'] = $user;

if ($expectsJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('success' => true, 'user_data' => $user));
    exit();
}

header('Location: ../Views/Member/ProfileView.php');
exit();
