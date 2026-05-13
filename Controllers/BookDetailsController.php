<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/BookModel.php';

$id = $_GET['id'];

$conn = Connect();

$book = getBookById($conn, $id);
$availability = getBookAvailabilityByBranches($conn, $id);

Close($conn);

$_SESSION['book'] = $book;
$_SESSION['availability'] = $availability;

header("Location: ../views/member/book_details.php");
exit();

?>