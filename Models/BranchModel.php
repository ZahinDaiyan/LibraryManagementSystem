<?php

/**
 * Models/BranchModel.php
 * Procedural Model for Branch and Inter-Branch Transfer Operations
 */

/**
 * Fetch list of all branches with manager name and count of librarians
 * 
 * @param mysqli $conn Database connection
 * @return array
 */
function getAdminBranchesList($conn)
{
    $sql = "SELECT b.*, u.name AS manager_name, 
            (SELECT COUNT(*) FROM users WHERE branch_id = b.id AND role = 'librarian') AS librarian_count 
            FROM branches b 
            LEFT JOIN users u ON b.manager_id = u.id 
            ORDER BY b.name ASC";

    $result = mysqli_query($conn, $sql);
    $branches = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $branches[] = $row;
        }
    }
    return $branches;
}

/**
 * Toggle the active status of a branch
 * 
 * @param mysqli $conn Database connection
 * @param int|string $id Branch ID
 * @return bool
 */
function toggleBranchStatus($conn, $id)
{
    $escaped_id = mysqli_real_escape_string($conn, $id);
    
    $res = mysqli_query($conn, "SELECT is_active FROM branches WHERE id = '$escaped_id'");
    $branch = $res ? mysqli_fetch_assoc($res) : null;
    
    if (!$branch) {
        return false;
    }
    
    $new_status = $branch['is_active'] ? 0 : 1;
    $sql = "UPDATE branches SET is_active = '$new_status' WHERE id = '$escaped_id'";
    return mysqli_query($conn, $sql);
}

/**
 * Fetch all inter-branch transfer requests with details, optionally filtered by status
 * 
 * @param mysqli $conn Database connection
 * @param string $status_filter Status to filter by ('pending', 'approved', 'rejected', etc.)
 * @return array
 */
function getAllInterBranchRequests($conn, $status_filter = '')
{
    $where_sql = "";
    if (!empty($status_filter)) {
        $escaped_status = mysqli_real_escape_string($conn, $status_filter);
        $where_sql = "WHERE ibr.status = '$escaped_status'";
    }

    $sql = "SELECT ibr.*, b.title AS book_title, 
                   br_from.name AS from_branch_name, 
                   br_to.name AS to_branch_name,
                   u.name AS requester_name
            FROM inter_branch_requests ibr
            JOIN books b ON ibr.book_id = b.id
            JOIN branches br_from ON ibr.from_branch_id = br_from.id
            JOIN branches br_to ON ibr.to_branch_id = br_to.id
            JOIN users u ON ibr.requested_by = u.id
            $where_sql
            ORDER BY ibr.created_at DESC";

    $result = mysqli_query($conn, $sql);
    $transfers = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $transfers[] = $row;
        }
    }
    return $transfers;
}
