<?php

function getMemberComplaints($conn, $member_id) {
    $sql = "SELECT * FROM complaints WHERE member_id = '$member_id' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    $complaints = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $complaints[] = $row;
    }
    return $complaints;
}

function createComplaint($conn, $member_id, $title, $description) {
    $title = mysqli_real_escape_string($conn, $title);
    $description = mysqli_real_escape_string($conn, $description);
    
    $sql = "INSERT INTO complaints (member_id, title, description, status, created_at, updated_at) 
            VALUES ('$member_id', '$title', '$description', 'pending', NOW(), NOW())";
    return mysqli_query($conn, $sql);
}

?>
