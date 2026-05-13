<?php

function getAllBooks($conn)
{
    $sql = "SELECT * FROM books";
    $result = mysqli_query($conn, $sql);

    $books = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }

    return $books;
}
function getBookById($conn, $id)
{
    $sql = "SELECT * FROM books WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    return mysqli_fetch_assoc($result);
}

function getBookAvailabilityByBranches($conn, $book_id)
{
    $sql = "
       SELECT 
    b.id AS branch_id,
    b.name AS branch_name,
    bi.total_copies,
    bi.available_copies
FROM branch_inventory bi
JOIN branches b ON b.id = bi.branch_id
WHERE bi.book_id = ?
    ";

    $result = mysqli_query($conn, $sql);

    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

?>