<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';
require_once '../Models/BookModel.php'; // For getBranches

$conn = Connect();
$users = getAllUsers($conn);
$branches = getBranches($conn);
Close($conn);

$_SESSION['admin_users'] = $users;
$_SESSION['branches'] = $branches;

header('Location: ../Views/Admin/UserListView.php');
exit();
