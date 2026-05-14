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

?>
