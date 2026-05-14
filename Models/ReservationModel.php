<?php

function reserveBook($conn, $member_id, $book_id, $branch_id)
{
    // Prevent duplicate active reservation
    $sql_check = "SELECT id FROM reservations 
                  WHERE member_id = '$member_id' AND book_id = '$book_id' 
                  AND branch_id = '$branch_id' AND status = 'waiting'";
    $result_check = mysqli_query($conn, $sql_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        return ['success' => false, 'message' => 'Already on waitlist for this book/branch'];
    }

    $sql = "INSERT INTO reservations (member_id, book_id, branch_id, status, reserved_at) 
            VALUES ('$member_id', '$book_id', '$branch_id', 'waiting', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        return ['success' => true, 'message' => 'Added to waitlist'];
    }
    return ['success' => false, 'message' => 'Failed to reserve'];
}

function getMemberReservations($conn, $member_id)
{
    $sql = "SELECT r.*, b.title AS book_title, brn.name AS branch_name
            FROM reservations r
            JOIN books b ON r.book_id = b.id
            JOIN branches brn ON r.branch_id = brn.id
            WHERE r.member_id = '$member_id'
            ORDER BY r.reserved_at DESC";
    
    $result = mysqli_query($conn, $sql);
    $reservations = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Get queue position
        $pos_sql = "SELECT COUNT(*) as pos FROM reservations 
                    WHERE book_id = '{$row['book_id']}' AND branch_id = '{$row['branch_id']}' 
                    AND status = 'waiting' AND reserved_at <= '{$row['reserved_at']}'";
        $pos_res = mysqli_query($conn, $pos_sql);
        $pos_row = mysqli_fetch_assoc($pos_res);
        $row['queue_position'] = $pos_row['pos'];
        
        $reservations[] = $row;
    }
    return $reservations;
}

function cancelReservation($conn, $reservation_id, $member_id)
{
    $sql = "DELETE FROM reservations WHERE id = '$reservation_id' AND member_id = '$member_id'";
    return mysqli_query($conn, $sql);
}

?>
