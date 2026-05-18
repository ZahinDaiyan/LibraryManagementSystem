<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchModel.php';

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? '';
$conn = Connect();

if ($action === 'toggle_status') {
    if (toggleBranchStatus($conn, $id)) {
        $_SESSION['msg'] = "Branch status updated successfully";
    } else {
        $_SESSION['error'] = "Failed to update branch status";
    }
}

Close($conn);
header('Location: AdminBranchController.php');
exit();
