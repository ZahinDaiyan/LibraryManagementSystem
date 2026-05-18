<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BookModel.php';

$action = $_POST['action'] ?? $_POST['action'] ?? '';
$conn = Connect();
$errors = [];

if ($action === 'create' || $action === 'update') {
    $title = htmlspecialchars($_POST['title']);
    $author = htmlspecialchars($_POST['author']);
    $isbn = htmlspecialchars($_POST['isbn']);
    $genre_id = $_POST['genre_id'];
    $publisher = htmlspecialchars($_POST['publisher']);
    $published_year = $_POST['published_year'];
    $description = htmlspecialchars($_POST['description']);
    $id = $_POST['id'] ?? null;

    // PHP Field-Level Validation
    if (empty($title)) $errors['title'] = "Book title is required";
    if (empty($author)) $errors['author'] = "Author name is required";
    if (empty($isbn)) $errors['isbn'] = "ISBN is required";
    if (empty($genre_id)) $errors['genre_id'] = "Please select a genre";
    if (empty($published_year)) $errors['published_year'] = "Publication year is required";
    
    // Check ISBN uniqueness on create or if changed
    if (isIsbnAssignedToOtherBook($conn, $isbn, $id)) {
        $errors['isbn'] = "This ISBN is already assigned to another book";
    }

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        if ($id) {
            $_SESSION['admin_book_form_id'] = $id;
        }
        Close($conn);
        header('Location: AdminBookFormController.php');
        exit();
    }

    if ($action === 'create') {
        if (createBook($conn, $title, $author, $isbn, $genre_id, $publisher, $published_year, $description)) {
            $_SESSION['msg'] = "Book '$title' successfully added to the master catalog";
        } else {
            $_SESSION['error'] = "Failed to add book to the catalog";
        }
    } else {
        if (updateBook($conn, $id, $title, $author, $isbn, $genre_id, $publisher, $published_year, $description)) {
            $_SESSION['msg'] = "Book details for '$title' updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update book details";
        }
    }

} elseif ($action === 'delete') {
    $id = $_POST['id'];
    
    // Safety check: Don't delete if there are active loans
    if (hasActiveLoansForBook($conn, $id)) {
        $_SESSION['msg'] = "Error: Cannot delete book. It is currently borrowed or has a pending request.";
    } else {
        if (deleteBookAndInventory($conn, $id)) {
            $_SESSION['msg'] = "Book permanently removed from catalog and all branch inventories";
        } else {
            $_SESSION['error'] = "Failed to remove book";
        }
    }
}

Close($conn);
header('Location: AdminBookCatalogController.php');
exit();
