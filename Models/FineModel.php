<?php

function getMemberFines($conn, $member_id)
{
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $sql = "SELECT f.*, b.title AS book_title 
            FROM fines f
            LEFT JOIN borrow_records br ON f.borrow_record_id = br.id
            LEFT JOIN books b ON br.book_id = b.id
            WHERE f.member_id = '$member_id' AND f.is_paid = 0
            ORDER BY f.id DESC";
    
    $result = mysqli_query($conn, $sql);
    $fines = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $fines[] = $row;
    }
    return $fines;
}

function getPaidFineHistory($conn, $member_id)
{
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $sql = "SELECT f.*, b.title AS book_title 
            FROM fines f
            LEFT JOIN borrow_records br ON f.borrow_record_id = br.id
            LEFT JOIN books b ON br.book_id = b.id
            WHERE f.member_id = '$member_id' AND f.is_paid = 1
            ORDER BY f.paid_at DESC";
    
    $result = mysqli_query($conn, $sql);
    $fines = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $fines[] = $row;
    }
    return $fines;
}

function submitPaymentConfirmation($conn, $fine_id, $member_id, $details)
{
    $fine_id = mysqli_real_escape_string($conn, $fine_id);
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $sql = "UPDATE fines 
            SET is_paid = 1, paid_at = NOW() 
            WHERE id = '$fine_id' AND member_id = '$member_id' AND is_paid = 0";
    return mysqli_query($conn, $sql);
}

?>
