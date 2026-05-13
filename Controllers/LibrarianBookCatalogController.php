<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/LibrarianModel.php';

$conn = Connect();
$books = getAllCatalogBooks($conn);
Close($conn);

$_SESSION['catalog_books'] = $books;

header('Location: ../Views/Librariyan/BookCatalogView.php');
exit();

?>