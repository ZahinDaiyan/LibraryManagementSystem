<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/LibrarianModel.php';

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'add';
$bookId = isset($_GET['id']) ? $_GET['id'] : '';

$conn = Connect();
$genres = getGenres($conn);
$book = array();

if ($mode === 'edit' && $bookId != '') {
    $book = getBookById($conn, $bookId);
}

Close($conn);

$_SESSION['book_form_mode'] = $mode;
$_SESSION['book_form_data'] = $book;
$_SESSION['book_form_genres'] = $genres;

header('Location: ../Views/Librariyan/BookFormView.php');
exit();

?>