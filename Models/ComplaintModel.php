<?php

function getMemberComplaints($conn, $member_id) {
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $sql = "SELECT * FROM complaints WHERE member_id = '$member_id' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    $complaints = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $complaints[] = $row;
    }
    return $complaints;
}

function createComplaint($conn, $member_id, $title, $description) {
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $title = mysqli_real_escape_string($conn, $title);
    $description = mysqli_real_escape_string($conn, $description);
    
    $sql = "INSERT INTO complaints (member_id, title, description, status, created_at, updated_at) 
            VALUES ('$member_id', '$title', '$description', 'pending', NOW(), NOW())";
    return mysqli_query($conn, $sql);
}

function getAllComplaints($conn, $status_filter = '')
{
    $where_sql = "";
    if (!empty($status_filter)) {
        $escaped_status = mysqli_real_escape_string($conn, $status_filter);
        $where_sql = "WHERE c.status = '$escaped_status'";
    }

    $sql = "SELECT c.*, u.name AS member_name, u.email AS member_email 
            FROM complaints c
            JOIN users u ON c.member_id = u.id
            $where_sql
            ORDER BY c.created_at DESC";

    $result = mysqli_query($conn, $sql);
    $complaints = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $complaints[] = $row;
        }
    }
    return $complaints;
}

function getComplaintById($conn, $id)
{
    $escaped_id = mysqli_real_escape_string($conn, $id);
    $sql = "SELECT c.*, u.name AS member_name, u.email AS member_email 
            FROM complaints c
            JOIN users u ON c.member_id = u.id
            WHERE c.id = '$escaped_id'
            LIMIT 1";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

function updateComplaintStatusAndResponse($conn, $id, $status, $admin_response)
{
    $escaped_id = mysqli_real_escape_string($conn, $id);
    $escaped_status = mysqli_real_escape_string($conn, $status);
    $escaped_response = mysqli_real_escape_string($conn, $admin_response);

    $sql = "UPDATE complaints 
            SET status = '$escaped_status', admin_response = '$escaped_response', updated_at = NOW() 
            WHERE id = '$escaped_id'";
    return mysqli_query($conn, $sql);
}

?>
