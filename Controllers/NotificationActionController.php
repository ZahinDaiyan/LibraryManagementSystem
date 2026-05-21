<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!function_exists('memberNotificationRespond')) {
    function memberNotificationRespond($expectsJson, $success, $message, $statusCode = 200)
    {
        if ($expectsJson) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($statusCode);
            echo json_encode(array(
                'success' => (bool)$success,
                'message' => $message,
                'redirect' => 'MemberDashboardController.php'
            ));
            exit();
        }

        if ($success) {
            $_SESSION['msg'] = $message;
        } else {
            $_SESSION['error'] = $message;
        }

        header("Location: MemberDashboardController.php");
        exit();
    }
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        memberNotificationRespond($expectsJson, false, 'Unauthorized', 403);
    }

    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/NotificationModel.php';

$id = $_POST['id'];
$member_id = $_SESSION['id'];

$conn = Connect();
$ok = markNotificationAsRead($conn, $id, $member_id);
Close($conn);

memberNotificationRespond($expectsJson, $ok ? true : false, $ok ? 'Notification marked as read' : 'Failed to mark notification', $ok ? 200 : 400);
