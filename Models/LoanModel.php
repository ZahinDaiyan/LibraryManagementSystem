<?php

function getActiveLoans($conn, $member_id)
{
    $sql = "SELECT br.*, b.title AS book_title, brn.name AS branch_name,
            DATEDIFF(br.due_date, CURDATE()) AS days_remaining
            FROM borrow_records br
            JOIN books b ON br.book_id = b.id
            JOIN branches brn ON br.branch_id = brn.id
            WHERE br.member_id = '$member_id' AND br.status = 'active'
            ORDER BY br.due_date ASC";
    
    $result = mysqli_query($conn, $sql);
    $loans = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $loans[] = $row;
    }
    return $loans;
}

function getBorrowHistory($conn, $member_id)
{
    $sql = "SELECT br.*, b.title AS book_title, brn.name AS branch_name
            FROM borrow_records br
            JOIN books b ON br.book_id = b.id
            JOIN branches brn ON br.branch_id = brn.id
            WHERE br.member_id = '$member_id'
            ORDER BY br.borrow_date DESC";
    
    $result = mysqli_query($conn, $sql);
    $history = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $history[] = $row;
    }
    return $history;
}

function requestRenewal($conn, $loan_id, $member_id)
{
    // Check if renewals are allowed (can be complex, but let's keep it simple for now)
    // Check if book is reserved by others
    $sql_check = "SELECT book_id FROM reservations WHERE book_id = (SELECT book_id FROM borrow_records WHERE id = '$loan_id') AND status = 'pending'";
    $res_check = mysqli_query($conn, $sql_check);
    
    if (mysqli_num_rows($res_check) > 0) {
        return ['success' => false, 'message' => 'Book is reserved by another member'];
    }

    // Extend due date by 7 days
    $sql = "UPDATE borrow_records 
            SET due_date = DATE_ADD(due_date, INTERVAL 7 DAY) 
            WHERE id = '$loan_id' AND member_id = '$member_id' AND status = 'active'";
    
    if (mysqli_query($conn, $sql)) {
        return ['success' => true, 'message' => 'Loan renewed for 7 days'];
    }
    return ['success' => false, 'message' => 'Failed to renew loan'];
}

function checkBookAvailabilityInBranch($conn, $book_id, $branch_id)
{
    $book_id = mysqli_real_escape_string($conn, $book_id);
    $branch_id = mysqli_real_escape_string($conn, $branch_id);
    $sql = "SELECT available_copies 
            FROM branch_inventory 
            WHERE book_id='$book_id' AND branch_id='$branch_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return ($row && $row['available_copies'] > 0);
}

function hasPendingBorrowRequest($conn, $member_id, $book_id)
{
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $book_id = mysqli_real_escape_string($conn, $book_id);
    $sql = "SELECT id FROM borrow_records 
            WHERE member_id='$member_id' 
            AND book_id='$book_id' 
            AND status='pending'";
    $result = mysqli_query($conn, $sql);
    return (mysqli_num_rows($result) > 0);
}

function createBorrowRequest($conn, $member_id, $book_id, $branch_id)
{
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $book_id = mysqli_real_escape_string($conn, $book_id);
    $branch_id = mysqli_real_escape_string($conn, $branch_id);
    $sql = "INSERT INTO borrow_records
            (member_id, book_id, branch_id, status, borrow_date)
            VALUES
            ('$member_id', '$book_id', '$branch_id', 'pending', CURDATE())";
    return mysqli_query($conn, $sql);
}

?>
