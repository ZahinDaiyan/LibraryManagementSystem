<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BookModel.php'; // For getBranches

$id = intval($_POST['id'] ?? $_SESSION['admin_announcement_form_id'] ?? 0);
unset($_SESSION['admin_announcement_form_id']);
$conn = Connect();

if ($id > 0) {
    $escaped_id = mysqli_real_escape_string($conn, $id);
    $res = mysqli_query($conn, "SELECT * FROM announcements WHERE id = '$escaped_id' LIMIT 1");
    $_SESSION['edit_announcement'] = mysqli_fetch_assoc($res);
} else {
    unset($_SESSION['edit_announcement']);
}

$_SESSION['branches'] = getBranches($conn);
Close($conn);

header('Location: ../Views/Admin/AnnouncementFormView.php');
exit();
