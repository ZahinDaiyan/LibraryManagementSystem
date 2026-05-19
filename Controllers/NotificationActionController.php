<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/NotificationModel.php';

$id = $_POST['id'];
$member_id = $_SESSION['id'];

$conn = Connect();
markNotificationAsRead($conn, $id, $member_id);
Close($conn);

header("Location: MemberDashboardController.php");
exit();
