<?php

function logAction($conn, $user_id, $action, $table_name = null, $record_id = null, $details = null)
{
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $action = mysqli_real_escape_string($conn, $action);
    $table_val = $table_name ? "'" . mysqli_real_escape_string($conn, $table_name) . "'" : "NULL";
    $record_val = $record_id ? "'" . mysqli_real_escape_string($conn, $record_id) . "'" : "NULL";
    $details_val = $details ? "'" . mysqli_real_escape_string($conn, $details) . "'" : "NULL";
    $ip = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);

    $sql = "INSERT INTO audit_log (user_id, action, table_name, record_id, details, ip_address, created_at) 
            VALUES ('$user_id', '$action', $table_val, $record_val, $details_val, '$ip', NOW())";
    
    return mysqli_query($conn, $sql);
}

function getAuditLogs($conn, $limit = 100)
{
    $limit = intval($limit);
    $sql = "SELECT al.*, u.name AS user_name, u.role AS user_role 
            FROM audit_log al
            JOIN users u ON al.user_id = u.id
            ORDER BY al.created_at DESC
            LIMIT $limit";
    
    $result = mysqli_query($conn, $sql);
    $logs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $logs[] = $row;
    }
    return $logs;
}

?>
