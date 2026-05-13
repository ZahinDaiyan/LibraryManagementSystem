<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/BookModel.php';

$conn = Connect();
$books = getAllBooks($conn);
Close($conn);

$_SESSION['books'] = $books;

header("Location: ../views/Member/book_catalog.php");
exit();

?>