<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';
require_once '../Models/BookModel.php';

$id = $_POST['id'] ?? $_SESSION['admin_user_form_id'] ?? null;
unset($_SESSION['admin_user_form_id']);
$conn = Connect();

if ($id) {
    $user = getUserById($conn, $id);
    $_SESSION['edit_user'] = $user;
} else {
    unset($_SESSION['edit_user']);
}

$_SESSION['branches'] = getBranches($conn);
Close($conn);

header('Location: ../Views/Admin/UserFormView.php');
exit();
