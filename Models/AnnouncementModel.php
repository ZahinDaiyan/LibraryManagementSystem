<?php

function getAnnouncements($conn, $branch_id)
{
    // Fetch global (branch_id IS NULL) and branch-specific announcements
    // Schema uses author_id and published_at
    $sql = "SELECT a.*, u.name AS author_name 
            FROM announcements a
            JOIN users u ON a.author_id = u.id
            WHERE a.branch_id IS NULL OR a.branch_id = '$branch_id'
            ORDER BY a.published_at DESC";
    
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }
    
    $announcements = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $announcements[] = $row;
    }
    return $announcements;
}

function getAllAnnouncements($conn)
{
    $sql = "SELECT a.*, b.name AS branch_name, u.name AS author_name 
            FROM announcements a
            LEFT JOIN branches b ON a.branch_id = b.id
            JOIN users u ON a.author_id = u.id
            ORDER BY a.published_at DESC";

    $result = mysqli_query($conn, $sql);
    $announcements = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $announcements[] = $row;
        }
    }
    return $announcements;
}

function createAnnouncement($conn, $title, $body, $branch_id, $admin_id)
{
    $title = mysqli_real_escape_string($conn, $title);
    $body = mysqli_real_escape_string($conn, $body);
    $branch_val = empty($branch_id) ? "NULL" : "'" . mysqli_real_escape_string($conn, $branch_id) . "'";
    $admin_id = mysqli_real_escape_string($conn, $admin_id);

    $sql = "INSERT INTO announcements (title, body, branch_id, author_id, published_at) 
            VALUES ('$title', '$body', $branch_val, '$admin_id', NOW())";
    return mysqli_query($conn, $sql);
}

function updateAnnouncement($conn, $id, $title, $body, $branch_id)
{
    $id = mysqli_real_escape_string($conn, $id);
    $title = mysqli_real_escape_string($conn, $title);
    $body = mysqli_real_escape_string($conn, $body);
    $branch_val = empty($branch_id) ? "NULL" : "'" . mysqli_real_escape_string($conn, $branch_id) . "'";

    $sql = "UPDATE announcements 
            SET title = '$title', body = '$body', branch_id = $branch_val 
            WHERE id = '$id'";
    return mysqli_query($conn, $sql);
}

function deleteAnnouncement($conn, $id)
{
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "DELETE FROM announcements WHERE id = '$id'";
    return mysqli_query($conn, $sql);
}

?>
