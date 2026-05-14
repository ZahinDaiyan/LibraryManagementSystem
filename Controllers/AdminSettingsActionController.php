<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';

$action = $_POST['action'] ?? '';
$conn = Connect();
$errors = [];

if ($action === 'update_settings') {
    $allow_self_reg = $_POST['allow_self_registration'] ?? '0';
    $fine_rate = $_POST['default_fine_rate'] ?? '';
    $max_days = $_POST['default_max_borrow_days'] ?? '';
    $max_books = $_POST['default_max_books_per_member'] ?? '';

    // PHP Field-Level Validation
    if (!is_numeric($fine_rate) || $fine_rate < 0) $errors['default_fine_rate'] = "Must be a valid positive number";
    if (!is_numeric($max_days) || $max_days < 1) $errors['default_max_borrow_days'] = "Must be at least 1 day";
    if (!is_numeric($max_books) || $max_books < 1) $errors['default_max_books_per_member'] = "Must be at least 1 book";

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        header('Location: AdminSettingsController.php');
        exit();
    }

    // Direct SQL Updates
    $updates = [
        'allow_self_registration' => $allow_self_reg,
        'default_fine_rate' => $fine_rate,
        'default_max_borrow_days' => $max_days,
        'default_max_books_per_member' => $max_books
    ];

    foreach ($updates as $key => $value) {
        $sql = "UPDATE system_settings SET setting_value = '$value' WHERE setting_key = '$key'";
        mysqli_query($conn, $sql);
    }

    $_SESSION['msg'] = "Global system settings updated successfully";
}

Close($conn);
header('Location: AdminSettingsController.php');
exit();
