<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/LibrarianModel.php';
require_once '../Models/LibrarianWorkflowModel.php';

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'add';
$bookId = isset($_GET['id']) ? $_GET['id'] : '';

$conn = Connect();
$genres = getGenres($conn);
$book = array();

if ($mode === 'edit' && $bookId != '') {
    $book = getBookById($conn, $bookId);
    // Fetch branch-specific inventory quantity for this librarian
    $branchInfo = getLibrarianBranchByUserId($conn, $_SESSION['id']);
    if ($branchInfo && isset($branchInfo['branch_id']) && $branchInfo['branch_id'] != '') {
        $inv = getBranchInventoryRow($conn, $branchInfo['branch_id'], $bookId);
        $book['quantity'] = $inv ? (int)$inv['total_copies'] : 0;
    } else {
        $book['quantity'] = 0;
    }
}

Close($conn);

$_SESSION['book_form_mode'] = $mode;
$_SESSION['book_form_data'] = $book;
$_SESSION['book_form_genres'] = $genres;

header('Location: ../Views/Librariyan/BookFormView.php');
exit();

?>