<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: /LibraryManagementSystem/Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/BookModel.php';

$conn = Connect();
$books = getAllBooks($conn);
Close($conn);

$_SESSION['books'] = $books;

header("Location: /LibraryManagementSystem/Views/Member/BookIndexView.php");
exit();

?>