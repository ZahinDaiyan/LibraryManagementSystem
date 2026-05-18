<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';

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
$sql = "UPDATE complaints 
        SET status = '$status', admin_response = '$admin_response', updated_at = NOW() 
        WHERE id = '$id'";

if (mysqli_query($conn, $sql)) {
    $_SESSION['msg'] = "Complaint updated successfully.";
} else {
    $_SESSION['error'] = "Failed to update complaint.";
}

Close($conn);
header('Location: AdminComplaintController.php');
exit();
