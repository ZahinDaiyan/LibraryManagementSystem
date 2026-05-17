<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchManagerModel.php';

$title = htmlspecialchars(trim($_POST['title'] ?? ''));
$body = htmlspecialchars(trim($_POST['body'] ?? ''));

if ($title == '' || $body == '') {
    $_SESSION['error'] = 'Title and content are required';
    header('Location: BranchManagerAnnouncementController.php');
    exit();
}

$conn = Connect();
$ok = bmCreatePlatformAnnouncement($conn, $_SESSION['id'], $title, $body);
Close($conn);

$_SESSION[$ok ? 'msg' : 'error'] = $ok ? 'Platform-wide announcement posted' : 'Unable to post announcement';

header('Location: BranchManagerAnnouncementController.php');
exit();

?>
