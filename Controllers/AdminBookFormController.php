<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Unauthorized', array(), 403);
    }

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

if (adminWantsJson()) {
    adminJsonResponse(true, 'Book form loaded', array(
        'edit_book' => $_SESSION['edit_book'] ?? null,
        'genres' => $_SESSION['genres']
    ));
}

header('Location: ../Views/Admin/BookFormView.php');
exit();
