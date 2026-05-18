<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BookModel.php';

$search = $_POST['search'] ?? '';

$conn = Connect();
$_SESSION['admin_books'] = getAdminBookCatalog($conn, $search);
Close($conn);

$_SESSION['admin_book_search'] = $search;

header('Location: ../Views/Admin/BookCatalogView.php');
exit();
