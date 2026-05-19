<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/ComplaintModel.php';

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$errors = [];

if (empty($title)) $errors['title'] = "Title is required";
if (empty($description)) $errors['description'] = "Description is required";

if (count($errors) > 0) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_data'] = $_POST;
    header("Location: ../Views/Member/ComplaintFormView.php");
    exit();
}

$conn = Connect();
if (createComplaint($conn, $_SESSION['id'], $title, $description)) {
    $_SESSION['msg'] = "Complaint submitted successfully";
    Close($conn);
    header("Location: MemberComplaintController.php");
} else {
    $_SESSION['error'] = "Failed to submit complaint";
    Close($conn);
    header("Location: ../Views/Member/ComplaintFormView.php");
}
exit();
