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

    $result = mysqli_query($conn, $sql);
    $books = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }

    return $books;
}

function getBookById($conn, $id)
{
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "SELECT * FROM books WHERE id = '$id' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    return $result ? mysqli_fetch_assoc($result) : null;
}

function getGenres($conn)
{
    $sql = "SELECT id, name FROM genres ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);
    $genres = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $genres[] = $row;
    }

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
    $title = mysqli_real_escape_string($conn, $title);
    $author = mysqli_real_escape_string($conn, $author);
    $isbn = mysqli_real_escape_string($conn, $isbn);
    $publisher = mysqli_real_escape_string($conn, $publisher);
    $description = mysqli_real_escape_string($conn, $description);
    $cover_image_path = mysqli_real_escape_string($conn, $cover_image_path);

    $genre_val = $genre_id == '' ? "NULL" : "'" . mysqli_real_escape_string($conn, $genre_id) . "'";
    $year_val = $published_year == '' ? "NULL" : "'" . mysqli_real_escape_string($conn, $published_year) . "'";

    $sql = "INSERT INTO books
            (title, author, isbn, genre_id, publisher, published_year, description, cover_image_path, created_at)
            VALUES
            ('$title', '$author', '$isbn', $genre_val, '$publisher', $year_val, '$description', '$cover_image_path', NOW())";

    return mysqli_query($conn, $sql);
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
    $id = mysqli_real_escape_string($conn, $id);
    $title = mysqli_real_escape_string($conn, $title);
    $author = mysqli_real_escape_string($conn, $author);
    $isbn = mysqli_real_escape_string($conn, $isbn);
    $publisher = mysqli_real_escape_string($conn, $publisher);
    $description = mysqli_real_escape_string($conn, $description);
    $cover_image_path = mysqli_real_escape_string($conn, $cover_image_path);

    $genre_val = $genre_id == '' ? "NULL" : "'" . mysqli_real_escape_string($conn, $genre_id) . "'";
    $year_val = $published_year == '' ? "NULL" : "'" . mysqli_real_escape_string($conn, $published_year) . "'";

    $sql = "UPDATE books
            SET title = '$title',
                author = '$author',
                isbn = '$isbn',
                genre_id = $genre_val,
                publisher = '$publisher',
                published_year = $year_val,
                description = '$description',
                cover_image_path = '$cover_image_path'
            WHERE id = '$id'";

    return mysqli_query($conn, $sql);
}

function retireBook($conn, $id, $branchId = null)
{
    $id = mysqli_real_escape_string($conn, $id);
    $branchFilter = $branchId === null || $branchId === '' ? '' : " AND branch_id = '" . mysqli_real_escape_string($conn, $branchId) . "'";

    $sql = "UPDATE branch_inventory
            SET total_copies = 0,
                available_copies = 0
            WHERE book_id = '$id'" . $branchFilter;

    return mysqli_query($conn, $sql);
}

function makeBookAvailable($conn, $id, $copies = 1, $branchId = null)
{
    $id = mysqli_real_escape_string($conn, $id);
    $copies_int = intval($copies) > 0 ? intval($copies) : 1;

    if (empty($branchId)) {
        $branchSql = "SELECT id FROM branches ORDER BY id ASC LIMIT 1";
        $branchResult = mysqli_query($conn, $branchSql);
        $branchRow = mysqli_fetch_assoc($branchResult);
        $branchId = $branchRow && isset($branchRow['id']) ? $branchRow['id'] : null;
    }

    if (empty($branchId)) {
        $sql = "UPDATE branch_inventory
                SET total_copies = $copies_int,
                    available_copies = $copies_int
                WHERE book_id = '$id'";

        return mysqli_query($conn, $sql);
    }

    $branchId = mysqli_real_escape_string($conn, $branchId);
    $checkSql = "SELECT id FROM branch_inventory WHERE book_id = '$id' AND branch_id = '$branchId' LIMIT 1";
    $checkResult = mysqli_query($conn, $checkSql);
    $existing = mysqli_fetch_assoc($checkResult);

    if ($existing) {
        $existingId = mysqli_real_escape_string($conn, $existing['id']);
        $sql = "UPDATE branch_inventory
                SET total_copies = $copies_int,
                    available_copies = $copies_int
                WHERE id = '$existingId'";
        return mysqli_query($conn, $sql);
    }

    $sql = "INSERT INTO branch_inventory (book_id, branch_id, total_copies, available_copies)
            VALUES ('$id', '$branchId', $copies_int, $copies_int)";
    return mysqli_query($conn, $sql);
}

?>