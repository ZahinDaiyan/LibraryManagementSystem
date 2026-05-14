<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: /LibraryManagementSystem/Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/BookModel.php';

$query = $_GET['search'] ?? '';
$genre_id = $_GET['genre_id'] ?? '';
$branch_id = $_GET['branch_id'] ?? '';
$year = $_GET['year'] ?? '';

$conn = Connect();
$books = searchBooks($conn, $query, $genre_id, $branch_id, $year);
$genres = getGenres($conn);
$branches = getBranches($conn);
Close($conn);

$_SESSION['books'] = $books;
$_SESSION['genres'] = $genres;
$_SESSION['branches'] = $branches;

header("Location: /LibraryManagementSystem/Views/Member/BookIndexView.php");
exit();

?>
