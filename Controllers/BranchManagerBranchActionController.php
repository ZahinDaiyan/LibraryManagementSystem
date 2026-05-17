<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchManagerModel.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$errors = array();
$conn = Connect();

if ($action === 'create' || $action === 'update') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $address = htmlspecialchars(trim($_POST['address'] ?? ''));
    $city = htmlspecialchars(trim($_POST['city'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));

    if ($name == '') $errors['name'] = 'Branch name is required';
    if ($address == '') $errors['address'] = 'Address is required';
    if ($city == '') $errors['city'] = 'City is required';
    if ($phone == '') $errors['phone'] = 'Phone is required';

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        Close($conn);
        header('Location: BranchManagerBranchController.php' . ($id > 0 ? "?id=$id" : ''));
        exit();
    }

    if ($action === 'create') {
        $ok = bmCreateBranch($conn, $_SESSION['id'], $name, $address, $city, $phone);
        $_SESSION[$ok ? 'msg' : 'error'] = $ok ? 'Branch created successfully' : 'Unable to create branch';
    } else {
        $ok = bmUpdateBranch($conn, $_SESSION['id'], $id, $name, $address, $city, $phone);
        $_SESSION[$ok ? 'msg' : 'error'] = $ok ? 'Branch updated successfully' : 'Unable to update branch';
    }
} elseif ($action === 'toggle_status') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $ok = bmToggleBranchStatus($conn, $_SESSION['id'], $id);
    $_SESSION[$ok ? 'msg' : 'error'] = $ok ? 'Branch status updated successfully' : 'Unable to update branch status';
}

Close($conn);

header('Location: BranchManagerBranchController.php');
exit();

?>
