<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: /LibraryManagementSystem/Views/LoginView.php");
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

header("Location: /LibraryManagementSystem/Views/Member/dashboardView.php");
exit();
