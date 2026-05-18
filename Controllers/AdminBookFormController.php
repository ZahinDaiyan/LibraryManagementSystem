<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BookModel.php';

$id = $_POST['id'] ?? $_SESSION['admin_book_form_id'] ?? null;
unset($_SESSION['admin_book_form_id']);
$conn = Connect();

if ($id) {
    $book = getBookById($conn, $id);
    $_SESSION['edit_book'] = $book;
} else {
    unset($_SESSION['edit_book']);
}

$_SESSION['genres'] = getGenres($conn);
Close($conn);

header('Location: ../Views/Admin/BookFormView.php');
exit();
