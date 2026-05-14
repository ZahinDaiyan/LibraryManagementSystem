<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';
$conn = Connect();

if ($action === 'toggle_status') {
    $res = mysqli_query($conn, "SELECT is_active FROM branches WHERE id = '$id'");
    $branch = mysqli_fetch_assoc($res);
    $new_status = $branch['is_active'] ? 0 : 1;
    mysqli_query($conn, "UPDATE branches SET is_active = '$new_status' WHERE id = '$id'");
    $_SESSION['msg'] = "Branch status updated successfully";
}

Close($conn);
header('Location: AdminBranchController.php');
exit();
