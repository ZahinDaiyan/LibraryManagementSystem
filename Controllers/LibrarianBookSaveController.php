<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/LibrarianModel.php';
require_once '../Models/LibrarianWorkflowModel.php';

$_SESSION['error'] = '';
$_SESSION['msg'] = '';

$mode = isset($_POST['mode']) ? $_POST['mode'] : 'add';
$bookId = isset($_POST['book_id']) ? $_POST['book_id'] : '';
$title = htmlspecialchars($_POST['title']);
$author = htmlspecialchars($_POST['author']);
$isbn = htmlspecialchars($_POST['isbn']);
$genreId = isset($_POST['genre_id']) ? $_POST['genre_id'] : '';
$publisher = htmlspecialchars($_POST['publisher']);
$publishedYear = isset($_POST['published_year']) ? $_POST['published_year'] : '';
$description = htmlspecialchars($_POST['description']);
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
$existingCover = isset($_POST['current_cover_image_path']) ? $_POST['current_cover_image_path'] : '';

if ($title == '' || $author == '') {
    $_SESSION['error'] = 'Please fill the required book fields';
    header('Location: ../Controllers/LibrarianBookFormController.php?mode=' . $mode . ($bookId != '' ? '&id=' . $bookId : ''));
    exit();
}

$coverImagePath = $existingCover;

if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
    $fileName = basename($_FILES['cover_image']['name']);
    $safeFileName = preg_replace('/[^A-Za-z0-9._-]/', '_', $fileName);
    $targetFileName = time() . '_' . $safeFileName;
    $targetPath = '../uploads/book_covers/' . $targetFileName;

    if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $targetPath)) {
        $coverImagePath = 'uploads/book_covers/' . $targetFileName;
    }
}

$conn = Connect();

if ($mode === 'edit' && $bookId != '') {
    $result = updateBook(
        $conn,
        $bookId,
        $title,
        $author,
        $isbn,
        $genreId,
        $publisher,
        $publishedYear,
        $description,
        $coverImagePath
    );

    if ($result) {
        // Update branch inventory quantity for librarian's branch if provided
        $branchInfo = getLibrarianBranchByUserId($conn, $_SESSION['id']);
        if ($branchInfo && isset($branchInfo['branch_id']) && $branchInfo['branch_id'] != '') {
            $branchId = $branchInfo['branch_id'];
            $copies = $quantity > 0 ? $quantity : 0;
            $existingInv = getBranchInventoryRow($conn, $branchId, $bookId);
            if ($existingInv) {
                $existingAvailable = intval($existingInv['available_copies']);
                $newAvailable = $copies;
                if ($existingAvailable > $copies) {
                    $newAvailable = $copies; // cannot have available > total
                } elseif ($existingAvailable <= $copies) {
                    // keep existing available if less than or equal to new total
                    $newAvailable = $existingAvailable;
                }
            } else {
                $newAvailable = $copies;
            }

            saveBranchInventory($conn, $branchId, $bookId, $copies, $newAvailable);
        }

        $_SESSION['msg'] = 'Book updated successfully';
    } else {
        $_SESSION['msg'] = 'Book update failed';
    }
} else {
    $result = createBook(
        $conn,
        $title,
        $author,
        $isbn,
        $genreId,
        $publisher,
        $publishedYear,
        $description,
        $coverImagePath
    );

    if ($result) {
        // Get new book id and add inventory for the librarian's branch if possible
        $newBookId = mysqli_insert_id($conn);
        $branchInfo = getLibrarianBranchByUserId($conn, $_SESSION['id']);
        if ($branchInfo && isset($branchInfo['branch_id']) && $branchInfo['branch_id'] != '') {
            $branchId = $branchInfo['branch_id'];
            $copies = $quantity > 0 ? $quantity : 1;
            saveBranchInventory($conn, $branchId, $newBookId, $copies, $copies);
            $_SESSION['msg'] = 'Book added successfully and inventory updated for your branch';
        } else {
            $_SESSION['msg'] = 'Book added successfully. Add inventory for branches from Operations.';
        }
    } else {
        $_SESSION['msg'] = 'Book add failed';
    }
}

Close($conn);

header('Location: ../Controllers/LibrarianBookCatalogController.php');
exit();

?>