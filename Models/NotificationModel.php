<?php

function getNotifications($conn, $member_id)
{
    $sql = "SELECT * FROM notifications 
            WHERE member_id = '$member_id' AND is_read = 0 
            ORDER BY created_at DESC";
    
    $result = mysqli_query($conn, $sql);
    
    $notifications = [];
    
    // Safety check: if table doesn't exist, mysqli_query returns false
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $notifications[] = $row;
        }
    }
    
    return $notifications;
}

function markNotificationAsRead($conn, $notification_id, $member_id)
{
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = '$notification_id' AND member_id = '$member_id'";
    return mysqli_query($conn, $sql);
}

?>
