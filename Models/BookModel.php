<?php

function getAllBooks($conn)
{
    $sql = "SELECT b.*, g.name AS genre_name 
            FROM books b 
            LEFT JOIN genres g ON b.genre_id = g.id";
    $result = mysqli_query($conn, $sql);

    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }

    return $books;
}

function searchBooks($conn, $query = '', $genre_id = '', $branch_id = '', $year = '')
{
    $conditions = [];
    
    if ($query != '') {
        $conditions[] = "(b.title LIKE '%$query%' OR b.author LIKE '%$query%' OR b.isbn LIKE '%$query%')";
    }
    
    if ($genre_id != '') {
        $conditions[] = "b.genre_id = '$genre_id'";
    }
    
    if ($year != '') {
        $conditions[] = "b.published_year = '$year'";
    }

    $join_inventory = "";
    if ($branch_id != '') {
        $join_inventory = " JOIN branch_inventory bi ON bi.book_id = b.id AND bi.branch_id = '$branch_id' AND bi.available_copies > 0 ";
    }

    $where = "";
    if (count($conditions) > 0) {
        $where = "WHERE " . implode(' AND ', $conditions);
    }

    $sql = "SELECT DISTINCT b.*, g.name AS genre_name 
            FROM books b 
            LEFT JOIN genres g ON b.genre_id = g.id 
            $join_inventory
            $where";
            
    $result = mysqli_query($conn, $sql);
    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    return $books;
}

function getBookById($conn, $id)
{
    $sql = "SELECT b.*, g.name AS genre_name 
            FROM books b 
            LEFT JOIN genres g ON b.genre_id = g.id
            WHERE b.id = '$id'";
    $result = mysqli_query($conn, $sql);

    return mysqli_fetch_assoc($result);
}

function getBookAvailabilityByBranches($conn, $book_id)
{
    $sql = "SELECT b.id AS branch_id, b.name AS branch_name, bi.total_copies, bi.available_copies
            FROM branch_inventory bi
            JOIN branches b ON b.id = bi.branch_id
            WHERE bi.book_id = '$book_id'";

    $result = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

function getGenres($conn)
{
    $sql = "SELECT * FROM genres ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);
    $genres = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $genres[] = $row;
    }
    return $genres;
}

function getBranches($conn)
{
    $sql = "SELECT id, name FROM branches ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);
    $branches = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $branches[] = $row;
    }
    return $branches;
}

// Review Functions

function getBookReviews($conn, $book_id)
{
    $sql = "SELECT br.*, u.name AS member_name 
            FROM book_reviews br
            JOIN users u ON br.member_id = u.id
            WHERE br.book_id = '$book_id'
            ORDER BY br.created_at DESC";
    
    $result = mysqli_query($conn, $sql);
    $reviews = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $reviews[] = $row;
    }
    return $reviews;
}

function getBookAverageRating($conn, $book_id)
{
    $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
            FROM book_reviews 
            WHERE book_id = '$book_id'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

function addOrUpdateReview($conn, $book_id, $member_id, $rating, $comment)
{
    // Check if review exists
    $checkSql = "SELECT id FROM book_reviews WHERE book_id = '$book_id' AND member_id = '$member_id'";
    $checkResult = mysqli_query($conn, $checkSql);
    
    if (mysqli_num_rows($checkResult) > 0) {
        $row = mysqli_fetch_assoc($checkResult);
        $review_id = $row['id'];
        $sql = "UPDATE book_reviews 
                SET rating = '$rating', review_text = '$comment' 
                WHERE id = '$review_id'";
    } else {
        $sql = "INSERT INTO book_reviews (book_id, member_id, rating, review_text, created_at) 
                VALUES ('$book_id', '$member_id', '$rating', '$comment', NOW())";
    }
    
    return mysqli_query($conn, $sql);
}

function deleteReview($conn, $review_id, $member_id)
{
    $sql = "DELETE FROM book_reviews WHERE id = '$review_id' AND member_id = '$member_id'";
    return mysqli_query($conn, $sql);
}

function getAdminBookCatalog($conn, $search = '')
{
    $where_sql = "";
    if (!empty($search)) {
        $escaped_search = mysqli_real_escape_string($conn, $search);
        $where_sql = "WHERE (b.title LIKE '%$escaped_search%' OR b.author LIKE '%$escaped_search%' OR b.isbn LIKE '%$escaped_search%')";
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
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
        }
    }
    return $books;
}

function isIsbnAssignedToOtherBook($conn, $isbn, $current_id = null)
{
    $escaped_isbn = mysqli_real_escape_string($conn, $isbn);
    $sql = "SELECT id FROM books WHERE isbn = '$escaped_isbn'";
    if ($current_id !== null) {
        $escaped_id = mysqli_real_escape_string($conn, $current_id);
        $sql .= " AND id != '$escaped_id'";
    }
    $result = mysqli_query($conn, $sql);
    return ($result && mysqli_num_rows($result) > 0);
}

function createBook($conn, $title, $author, $isbn, $genre_id, $publisher, $published_year, $description)
{
    $title = mysqli_real_escape_string($conn, $title);
    $author = mysqli_real_escape_string($conn, $author);
    $isbn = mysqli_real_escape_string($conn, $isbn);
    $publisher = mysqli_real_escape_string($conn, $publisher);
    $description = mysqli_real_escape_string($conn, $description);

    $genre_val = empty($genre_id) ? "NULL" : "'" . mysqli_real_escape_string($conn, $genre_id) . "'";
    $year_val = empty($published_year) ? "NULL" : "'" . mysqli_real_escape_string($conn, $published_year) . "'";

    $sql = "INSERT INTO books (title, author, isbn, genre_id, publisher, published_year, description, created_at) 
            VALUES ('$title', '$author', '$isbn', $genre_val, '$publisher', $year_val, NOW())";
    return mysqli_query($conn, $sql);
}

function updateBook($conn, $id, $title, $author, $isbn, $genre_id, $publisher, $published_year, $description)
{
    $id = mysqli_real_escape_string($conn, $id);
    $title = mysqli_real_escape_string($conn, $title);
    $author = mysqli_real_escape_string($conn, $author);
    $isbn = mysqli_real_escape_string($conn, $isbn);
    $publisher = mysqli_real_escape_string($conn, $publisher);
    $description = mysqli_real_escape_string($conn, $description);

    $genre_val = empty($genre_id) ? "NULL" : "'" . mysqli_real_escape_string($conn, $genre_id) . "'";
    $year_val = empty($published_year) ? "NULL" : "'" . mysqli_real_escape_string($conn, $published_year) . "'";

    $sql = "UPDATE books 
            SET title = '$title', author = '$author', isbn = '$isbn', genre_id = $genre_val, 
                publisher = '$publisher', published_year = $year_val, description = '$description' 
            WHERE id = '$id'";
    return mysqli_query($conn, $sql);
}

function hasActiveLoansForBook($conn, $book_id)
{
    $escaped_id = mysqli_real_escape_string($conn, $book_id);
    $sql = "SELECT id FROM borrow_records WHERE book_id = '$escaped_id' AND status IN ('pending', 'active')";
    $result = mysqli_query($conn, $sql);
    return ($result && mysqli_num_rows($result) > 0);
}

function deleteBookAndInventory($conn, $book_id)
{
    $escaped_id = mysqli_real_escape_string($conn, $book_id);
    
    // Clean up inventory first
    if (!mysqli_query($conn, "DELETE FROM branch_inventory WHERE book_id = '$escaped_id'")) {
        return false;
    }
    
    // Delete book record
    return mysqli_query($conn, "DELETE FROM books WHERE id = '$escaped_id'");
}

?>
