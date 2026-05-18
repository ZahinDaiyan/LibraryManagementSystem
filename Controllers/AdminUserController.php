<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';
require_once '../Models/BookModel.php'; // For getBranches

$search = $_POST['search'] ?? '';
$role_filter = $_POST['role_filter'] ?? '';

$conn = Connect();
$_SESSION['admin_users'] = searchUsersWithBranch($conn, $search, $role_filter);
Close($conn);

$_SESSION['admin_user_search'] = $search;
$_SESSION['admin_user_role_filter'] = $role_filter;

header('Location: ../Views/Admin/UserListView.php');
exit();
