<?php

function getReadingList($conn, $member_id)
{
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $sql = "SELECT rl.*, b.title, b.author, b.isbn 
            FROM reading_lists rl
            JOIN books b ON rl.book_id = b.id
            WHERE rl.member_id = '$member_id'
            ORDER BY rl.added_at DESC";
    
    $result = mysqli_query($conn, $sql);
    $list = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}

function addToReadingList($conn, $member_id, $book_id)
{
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $book_id = mysqli_real_escape_string($conn, $book_id);
    $sql = "INSERT INTO reading_lists (member_id, book_id, added_at) 
            VALUES ('$member_id', '$book_id', NOW())";
    return mysqli_query($conn, $sql);
}

function removeFromReadingList($conn, $member_id, $book_id)
{
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $book_id = mysqli_real_escape_string($conn, $book_id);
    $sql = "DELETE FROM reading_lists WHERE member_id = '$member_id' AND book_id = '$book_id'";
    return mysqli_query($conn, $sql);
}

function isInReadingList($conn, $member_id, $book_id)
{
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $book_id = mysqli_real_escape_string($conn, $book_id);
    $sql = "SELECT id FROM reading_lists WHERE member_id = '$member_id' AND book_id = '$book_id'";
    $result = mysqli_query($conn, $sql);
    return ($result && mysqli_num_rows($result) > 0);
}

?>
