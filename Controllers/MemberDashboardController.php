<?php

session_start();

if (!isset($_SESSION['role'])) {

    header('Location: ../views/auth/LoginView.php');
    exit();
}

if ($_SESSION['role'] != 'member') {

    header('Location: ../index.php');
    exit();
}

header('Location: ../views/member/dashboard.php');

?>