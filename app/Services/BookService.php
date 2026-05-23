<?php

namespace App\Services;

class BookService
{
    public function __construct()
    {
        require_once __DIR__ . '/../../Models/DB.php';
        require_once __DIR__ . '/../../Models/BookModel.php';
    }

    public function searchCatalog(string $search = ''): array
    {
        $conn = Connect();
        $books = getAdminBookCatalog($conn, $search);
        Close($conn);
        return $books;
    }

    public function findBook(int $id): ?array
    {
        $conn = Connect();
        $book = getBookById($conn, $id);
        Close($conn);
        return $book ?: null;
    }

    public function getGenres(): array
    {
        $conn = Connect();
        $genres = getGenres($conn);
        Close($conn);
        return $genres;
    }

    public function createBook(array $data): bool
    {
        $conn = Connect();
        $result = createBook(
            $conn,
            $data['title'] ?? '',
            $data['author'] ?? '',
            $data['isbn'] ?? '',
            $data['genre_id'] ?? null,
            $data['publisher'] ?? '',
            $data['published_year'] ?? null,
            $data['description'] ?? ''
        );
        Close($conn);
        return $result;
    }

    public function updateBook(int $id, array $data): bool
    {
        $conn = Connect();
        $result = updateBook(
            $conn,
            $id,
            $data['title'] ?? '',
            $data['author'] ?? '',
            $data['isbn'] ?? '',
            $data['genre_id'] ?? null,
            $data['publisher'] ?? '',
            $data['published_year'] ?? null,
            $data['description'] ?? ''
        );
        Close($conn);
        return $result;
    }

    public function deleteBook(int $id): bool
    {
        $conn = Connect();
        $result = deleteBookAndInventory($conn, $id);
        Close($conn);
        return $result;
    }
}
