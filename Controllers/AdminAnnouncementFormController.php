<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BookModel.php'; // For getBranches

$id = $_GET['id'] ?? null;
$conn = Connect();

if ($id) {
    $res = mysqli_query($conn, "SELECT * FROM announcements WHERE id = '$id' LIMIT 1");
    $_SESSION['edit_announcement'] = mysqli_fetch_assoc($res);
} else {
    unset($_SESSION['edit_announcement']);
}

$_SESSION['branches'] = getBranches($conn);
Close($conn);

header('Location: ../Views/Admin/AnnouncementFormView.php');
exit();
