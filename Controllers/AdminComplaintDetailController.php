<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/ComplaintModel.php';

$id = $_POST['id'] ?? $_SESSION['admin_complaint_detail_id'] ?? '';
unset($_SESSION['admin_complaint_detail_id']);
$conn = Connect();

$complaint = getComplaintById($conn, $id);

Close($conn);

if (!$complaint) {
    header('Location: AdminComplaintController.php');
    exit();
}

$_SESSION['current_complaint'] = $complaint;

header('Location: ../Views/Admin/ComplaintDetailView.php');
exit();
