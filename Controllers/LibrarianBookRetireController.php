<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/LibrarianModel.php';
require_once '../Models/LibrarianWorkflowModel.php';

$bookId = isset($_POST['book_id']) ? $_POST['book_id'] : '';

if ($bookId == '') {
    $_SESSION['error'] = 'Book ID is required';
    header('Location: ../Controllers/LibrarianBookCatalogController.php');
    exit();
}

$conn = Connect();
$branchInfo = getLibrarianBranchByUserId($conn, $_SESSION['id']);
$branchId = isset($branchInfo['branch_id']) ? $branchInfo['branch_id'] : null;
$result = retireBook($conn, $bookId, $branchId);
Close($conn);

$_SESSION['msg'] = $result ? 'Book marked as unavailable' : 'Unable to update book status';

header('Location: ../Controllers/LibrarianBookCatalogController.php');
exit();

?>