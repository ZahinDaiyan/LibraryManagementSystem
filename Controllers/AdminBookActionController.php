<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';

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
    $isbn_check_sql = "SELECT id FROM books WHERE isbn = '$isbn'" . ($id ? " AND id != '$id'" : "");
    $res = mysqli_query($conn, $isbn_check_sql);
    if (mysqli_num_rows($res) > 0) $errors['isbn'] = "This ISBN is already assigned to another book";

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        if ($id) {
            $_SESSION['admin_book_form_id'] = $id;
        }
        header('Location: AdminBookFormController.php');
        exit();
    }

    $genre_val = $genre_id == '' ? "NULL" : "'$genre_id'";
    $year_val = $published_year == '' ? "NULL" : "'$published_year'";

    if ($action === 'create') {
        $sql = "INSERT INTO books (title, author, isbn, genre_id, publisher, published_year, description, created_at) 
                VALUES ('$title', '$author', '$isbn', $genre_val, '$publisher', $year_val, '$description', NOW())";
        mysqli_query($conn, $sql);
        $_SESSION['msg'] = "Book '$title' successfully added to the master catalog";
    } else {
        $sql = "UPDATE books 
                SET title = '$title', author = '$author', isbn = '$isbn', genre_id = $genre_val, 
                    publisher = '$publisher', published_year = $year_val, description = '$description' 
                WHERE id = '$id'";
        mysqli_query($conn, $sql);
        $_SESSION['msg'] = "Book details for '$title' updated successfully";
    }

} elseif ($action === 'delete') {
    $id = $_POST['id'];
    
    // Safety check: Don't delete if there are active loans
    $check_sql = "SELECT id FROM borrow_records WHERE book_id = '$id' AND status IN ('pending', 'active')";
    $res = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($res) > 0) {
        $_SESSION['msg'] = "Error: Cannot delete book. It is currently borrowed or has a pending request.";
    } else {
        // Clean up inventory first
        mysqli_query($conn, "DELETE FROM branch_inventory WHERE book_id = '$id'");
        mysqli_query($conn, "DELETE FROM books WHERE id = '$id'");
        $_SESSION['msg'] = "Book permanently removed from catalog and all branch inventories";
    }
}

Close($conn);
header('Location: AdminBookCatalogController.php');
exit();
