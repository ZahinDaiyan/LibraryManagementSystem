<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!function_exists('memberComplaintRespond')) {
    function memberComplaintRespond($expectsJson, $success, $message, $extra = array(), $statusCode = 200)
    {
        if ($expectsJson) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($statusCode);
            echo json_encode(array_merge(array(
                'success' => (bool)$success,
                'message' => $message,
                'redirect' => $success ? 'MemberComplaintController.php' : '../Views/Member/ComplaintFormView.php'
            ), $extra));
            exit();
        }

        if ($success) {
            $_SESSION['msg'] = $message;
            header("Location: MemberComplaintController.php");
        } else {
            if (isset($extra['errors'])) {
                $_SESSION['form_errors'] = $extra['errors'];
                $_SESSION['old_data'] = $_POST;
            } else {
                $_SESSION['error'] = $message;
            }
            header("Location: ../Views/Member/ComplaintFormView.php");
        }
        exit();
    }
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        memberComplaintRespond($expectsJson, false, 'Unauthorized', array(), 403);
    }

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
    memberComplaintRespond($expectsJson, false, 'Validation failed', array('errors' => $errors), 422);
}

$conn = Connect();
if (createComplaint($conn, $_SESSION['id'], $title, $description)) {
    Close($conn);
    memberComplaintRespond($expectsJson, true, "Complaint submitted successfully", array(), 200);
} else {
    Close($conn);
    memberComplaintRespond($expectsJson, false, "Failed to submit complaint", array(), 500);
}
