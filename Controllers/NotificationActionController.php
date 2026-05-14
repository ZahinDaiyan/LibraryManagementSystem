<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: /LibraryManagementSystem/Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/NotificationModel.php';

$id = $_GET['id'];
$member_id = $_SESSION['id'];

$conn = Connect();
markNotificationAsRead($conn, $id, $member_id);
Close($conn);

header("Location: MemberDashboardController.php");
exit();
