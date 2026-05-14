<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';

$conn = Connect();
$user = getUserWithBranchById($conn, $_SESSION['id']);
Close($conn);

if (!$user) {
    $_SESSION['error'] = 'User profile not found';
    header('Location: ../Views/LoginView.php');
    exit();
}

$_SESSION['user_data'] = $user;

header('Location: ../Views/Member/ProfileView.php');
exit();
