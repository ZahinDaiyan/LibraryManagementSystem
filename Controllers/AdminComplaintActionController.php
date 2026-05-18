<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/ComplaintModel.php';

$id = $_POST['id'] ?? '';
$status = $_POST['status'] ?? '';
$admin_response = htmlspecialchars($_POST['admin_response'] ?? '');
$errors = [];

if (empty($admin_response) && $status === 'resolved') {
    $errors['admin_response'] = "Please provide a response before resolving the complaint.";
}

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['admin_complaint_detail_id'] = $id;
    header("Location: AdminComplaintDetailController.php");
    exit();
}

$conn = Connect();

if (updateComplaintStatusAndResponse($conn, $id, $status, $admin_response)) {
    $_SESSION['msg'] = "Complaint updated successfully.";
} else {
    $_SESSION['error'] = "Failed to update complaint.";
}

Close($conn);
header('Location: AdminComplaintController.php');
exit();
