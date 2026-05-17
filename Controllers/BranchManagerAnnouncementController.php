<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchManagerModel.php';

$conn = Connect();
$_SESSION['bm_announcements'] = bmGetManagerAnnouncements($conn, $_SESSION['id']);
Close($conn);

header('Location: ../Views/BranchManager/AnnouncementView.php');
exit();

?>
