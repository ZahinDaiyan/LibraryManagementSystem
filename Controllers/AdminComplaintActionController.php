<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    adminFinishResponse(adminWantsJson(), false, 'Unauthorized', '../Views/LoginView.php', array(), 403);
}

require_once '../Models/DB.php';
require_once '../Models/ComplaintModel.php';

$id = $_POST['id'] ?? '';
$status = $_POST['status'] ?? '';
$admin_response = htmlspecialchars($_POST['admin_response'] ?? '');
$errors = [];
$success = false;
$message = 'Failed to update complaint.';

$allowed_statuses = array('pending', 'in_review', 'resolved');
if (!in_array($status, $allowed_statuses, true)) {
    $status = 'in_review';
}

// A complaint should not stay pending after an admin response is submitted.
if (!empty(trim($admin_response)) && $status === 'pending') {
    $status = 'in_review';
}

if (empty($admin_response) && $status === 'resolved') {
    $errors['admin_response'] = "Please provide a response before resolving the complaint.";
}

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['admin_complaint_detail_id'] = $id;
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Validation failed', array('errors' => $errors), 422);
    }
    header("Location: AdminComplaintDetailController.php");
    exit();
}

$conn = Connect();

if (updateComplaintStatusAndResponse($conn, $id, $status, $admin_response)) {
    $success = true;
    $message = "Complaint updated successfully.";
} else {
    $success = false;
    $message = "Failed to update complaint.";
}

Close($conn);
adminFinishResponse(adminWantsJson(), $success, $message, 'AdminComplaintController.php', array(), $success ? 200 : 400);
