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

    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/UserModel.php';
require_once '../models/AnnouncementModel.php';
require_once '../models/NotificationModel.php';

$conn = Connect();
$user = getUserById($conn, $_SESSION['id']);
$branch_id = $user['branch_id'];

$announcements = getAnnouncements($conn, $branch_id);
$notifications = getNotifications($conn, $_SESSION['id']);
Close($conn);

$_SESSION['announcements'] = $announcements;
$_SESSION['notifications'] = $notifications;

if ($expectsJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'success' => true,
        'announcements' => $announcements,
        'notifications' => $notifications
    ));
    exit();
}

header("Location: ../Views/Member/dashboardView.php");
exit();
