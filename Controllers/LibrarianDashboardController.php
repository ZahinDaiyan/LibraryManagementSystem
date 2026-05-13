<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';

$conn = Connect();
$librarian = getUserWithBranchById($conn, $_SESSION['id']);
Close($conn);

if (!$librarian) {
    $_SESSION['error'] = 'Librarian profile not found';
    header('Location: ../Views/LoginView.php');
    exit();
}

$_SESSION['librarian'] = $librarian;

header('Location: ../Views/Librariyan/DemoView.php');
exit();

?>