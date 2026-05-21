<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Unauthorized', 'books' => array()));
        exit();
    }

    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/BookModel.php';

$query = trim($_POST['search'] ?? '');
$genre_id = isset($_POST['genre_id']) && is_numeric($_POST['genre_id']) ? $_POST['genre_id'] : '';
$branch_id = isset($_POST['branch_id']) && is_numeric($_POST['branch_id']) ? $_POST['branch_id'] : '';
$year = isset($_POST['year']) && is_numeric($_POST['year']) ? $_POST['year'] : '';

$conn = Connect();
$books = searchBooks($conn, $query, $genre_id, $branch_id, $year);
$genres = getGenres($conn);
$branches = getBranches($conn);
Close($conn);

$_SESSION['books'] = $books;
$_SESSION['genres'] = $genres;
$_SESSION['branches'] = $branches;

$_SESSION['book_search'] = $query;
$_SESSION['book_genre_id'] = $genre_id;
$_SESSION['book_branch_id'] = $branch_id;
$_SESSION['book_year'] = $year;

if ($expectsJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'success' => true,
        'message' => 'Books loaded',
        'books' => $books,
        'genres' => $genres,
        'branches' => $branches,
        'filters' => array(
            'search' => $query,
            'genre_id' => $genre_id,
            'branch_id' => $branch_id,
            'year' => $year
        )
    ));
    exit();
}

header("Location: ../Views/Member/BookIndexView.php");
exit();

?>
