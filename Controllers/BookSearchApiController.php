<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../Models/DB.php';
require_once '../Models/BookModel.php';

$query = $_POST['search'] ?? '';
$genre_id = $_POST['genre_id'] ?? '';
$branch_id = $_POST['branch_id'] ?? '';
$year = $_POST['year'] ?? '';

$conn = Connect();
$books = searchBooks($conn, $query, $genre_id, $branch_id, $year);
Close($conn);

$data = [];
foreach ($books as $book) {
    $data[] = [
        'id' => $book['id'],
        'title' => htmlspecialchars($book['title']),
        'author' => htmlspecialchars($book['author']),
        'cover_image_path' => htmlspecialchars($book['cover_image_path'] ?? ''),
        'genre_name' => htmlspecialchars($book['genre_name'] ?? 'N/A'),
        'isbn' => htmlspecialchars($book['isbn']),
        'published_year' => htmlspecialchars($book['published_year'])
    ];
}

echo json_encode($data);
exit();
?>
