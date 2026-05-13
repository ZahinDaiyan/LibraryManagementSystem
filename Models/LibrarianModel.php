<?php

function getAllCatalogBooks($conn)
{
    $sql = "SELECT
                b.id,
                b.title,
                b.author,
                b.isbn,
                b.genre_id,
                g.name AS genre_name,
                b.publisher,
                b.published_year,
                b.description,
                b.cover_image_path,
                b.created_at,
                COALESCE(SUM(bi.total_copies), 0) AS total_copies,
                COALESCE(SUM(bi.available_copies), 0) AS available_copies,
                CASE
                    WHEN COALESCE(SUM(bi.total_copies), 0) > 0 THEN 'Active'
                    ELSE 'Retired/Unavailable'
                END AS status_label
            FROM books b
            LEFT JOIN genres g ON g.id = b.genre_id
            LEFT JOIN branch_inventory bi ON bi.book_id = b.id
            GROUP BY
                b.id, b.title, b.author, b.isbn, b.genre_id, g.name,
                b.publisher, b.published_year, b.description, b.cover_image_path, b.created_at
            ORDER BY b.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $books = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $books;
}

function getBookById($conn, $id)
{
    $sql = "SELECT * FROM books WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $book = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $book;
}

function getGenres($conn)
{
    $sql = "SELECT id, name FROM genres ORDER BY name ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $genres = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $genres[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $genres;
}

function createBook(
    $conn,
    $title,
    $author,
    $isbn,
    $genre_id,
    $publisher,
    $published_year,
    $description,
    $cover_image_path
)
{
    $sql = "INSERT INTO books
            (title, author, isbn, genre_id, publisher, published_year, description, cover_image_path, created_at)
            VALUES
            (?, ?, ?, NULLIF(?, ''), ?, NULLIF(?, ''), ?, ?, NOW())";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        'ssssssss',
        $title,
        $author,
        $isbn,
        $genre_id,
        $publisher,
        $published_year,
        $description,
        $cover_image_path
    );

    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function updateBook(
    $conn,
    $id,
    $title,
    $author,
    $isbn,
    $genre_id,
    $publisher,
    $published_year,
    $description,
    $cover_image_path
)
{
    $sql = "UPDATE books
            SET title = ?,
                author = ?,
                isbn = ?,
                genre_id = NULLIF(?, ''),
                publisher = ?,
                published_year = NULLIF(?, ''),
                description = ?,
                cover_image_path = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        'ssssssssi',
        $title,
        $author,
        $isbn,
        $genre_id,
        $publisher,
        $published_year,
        $description,
        $cover_image_path,
        $id
    );

    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function retireBook($conn, $id)
{
    $sql = "UPDATE branch_inventory
            SET total_copies = 0,
                available_copies = 0
            WHERE book_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

?>