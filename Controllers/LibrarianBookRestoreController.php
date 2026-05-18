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
$copies = isset($_POST['copies']) ? $_POST['copies'] : 1;

if ($bookId == '') {
    $_SESSION['error'] = 'Book ID is required';
    header('Location: ../Controllers/LibrarianBookCatalogController.php');
    exit();
}

$conn = Connect();
$branchInfo = getLibrarianBranchByUserId($conn, $_SESSION['id']);
$branchId = isset($branchInfo['branch_id']) ? $branchInfo['branch_id'] : null;
$result = makeBookAvailable($conn, $bookId, $copies, $branchId);
Close($conn);

if ($result) {
    $_SESSION['msg'] = 'Book marked as available';
} else {
    $_SESSION['error'] = 'Unable to mark book as available. Please add inventory for this book in branch inventory.';
}

header('Location: ../Controllers/LibrarianBookCatalogController.php');
exit();

?>
