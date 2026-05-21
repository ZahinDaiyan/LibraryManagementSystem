<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!function_exists('adminBookCatalogRespond')) {
    function adminBookCatalogRespond($expectsJson, $success, $books, $search, $message = '', $statusCode = 200)
    {
        if ($expectsJson) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($statusCode);
            echo json_encode(array(
                'success' => (bool)$success,
                'message' => $message,
                'search' => $search,
                'books' => $books
            ));
            exit();
        }
    }
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if ($expectsJson) {
        adminBookCatalogRespond($expectsJson, false, array(), '', 'Unauthorized', 403);
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BookModel.php';

$search = $_POST['search'] ?? '';

$conn = Connect();
$_SESSION['admin_books'] = getAdminBookCatalog($conn, $search);
Close($conn);

$_SESSION['admin_book_search'] = $search;

if ($expectsJson) {
    adminBookCatalogRespond($expectsJson, true, $_SESSION['admin_books'], $search, 'Catalog loaded');
}

header('Location: ../Views/Admin/BookCatalogView.php');
exit();
