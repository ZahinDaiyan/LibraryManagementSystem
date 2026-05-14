<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BookModel.php';

$search = $_GET['search'] ?? '';

$conn = Connect();

$where_sql = "";
if (!empty($search)) {
    $where_sql = "WHERE (b.title LIKE '%$search%' OR b.author LIKE '%$search%' OR b.isbn LIKE '%$search%')";
}

$sql = "SELECT b.*, g.name AS genre_name, 
        (SELECT SUM(total_copies) FROM branch_inventory WHERE book_id = b.id) AS total_stock,
        (SELECT SUM(available_copies) FROM branch_inventory WHERE book_id = b.id) AS total_available
        FROM books b 
        LEFT JOIN genres g ON b.genre_id = g.id 
        $where_sql 
        ORDER BY b.title ASC";

$result = mysqli_query($conn, $sql);
$books = [];
while ($row = mysqli_fetch_assoc($result)) {
    $books[] = $row;
}

Close($conn);

$_SESSION['admin_books'] = $books;

header('Location: ../Views/Admin/BookCatalogView.php?search=' . urlencode($search));
exit();
