<?php

session_start();

if (!isset($_SESSION['role'])) {

    header('Location: /LibraryManagementSystem/Views/LoginView.php');
    exit();
}

if ($_SESSION['role'] != 'member') {

    header('Location: /LibraryManagementSystem/index.php');
    exit();
}

header('Location: /LibraryManagementSystem/Views/Member/dashboardView.php');

?>