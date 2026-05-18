<?php

/**
 * Models/AdminModel.php
 * Procedural Model for Administrative and Platform-Wide System Operations
 */

/**
 * Fetch overview metrics for the Admin Dashboard
 * 
 * @param mysqli $conn Database connection
 * @return array
 */
function getAdminDashboardStats($conn)
{
    // 1. Total members
    $res_members = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'member'");
    $total_members = $res_members ? mysqli_fetch_assoc($res_members)['count'] : 0;

    // 2. Total books in catalog
    $res_books = mysqli_query($conn, "SELECT COUNT(*) as count FROM books");
    $total_books = $res_books ? mysqli_fetch_assoc($res_books)['count'] : 0;

    // 3. Total active loans
    $res_active = mysqli_query($conn, "SELECT COUNT(*) as count FROM borrow_records WHERE status = 'active'");
    $total_active_loans = $res_active ? mysqli_fetch_assoc($res_active)['count'] : 0;

    // 4. Total overdue loans
    $res_overdue = mysqli_query($conn, "SELECT COUNT(*) as count FROM borrow_records WHERE status = 'active' AND due_date < CURDATE()");
    $total_overdue_loans = $res_overdue ? mysqli_fetch_assoc($res_overdue)['count'] : 0;

    // 5. Total fines outstanding
    $res_fines = mysqli_query($conn, "SELECT SUM(amount) as total FROM fines WHERE is_paid = 0");
    $fines_row = $res_fines ? mysqli_fetch_assoc($res_fines) : null;
    $total_fines_outstanding = $fines_row ? ($fines_row['total'] ?? 0) : 0;

    return [
        'total_members' => $total_members,
        'total_books' => $total_books,
        'total_active_loans' => $total_active_loans,
        'total_overdue_loans' => $total_overdue_loans,
        'total_fines_outstanding' => $total_fines_outstanding
    ];
}

/**
 * Fetch platform-wide reports and analytical trends
 * 
 * @param mysqli $conn Database connection
 * @return array
 */
function getAdminReportData($conn)
{
    // 1. Total Borrows per Month (Last 6 Months)
    $sql_borrows = "SELECT DATE_FORMAT(borrow_date, '%Y-%m') AS month, COUNT(*) AS count 
                    FROM borrow_records 
                    WHERE borrow_date IS NOT NULL
                    GROUP BY month 
                    ORDER BY month DESC 
                    LIMIT 6";
    $res_borrows = mysqli_query($conn, $sql_borrows);
    $borrows_data = [];
    if ($res_borrows) {
        while ($row = mysqli_fetch_assoc($res_borrows)) {
            $borrows_data[] = $row;
        }
    }

    // 2. Total Fines Collected per Month (Last 6 Months)
    $sql_fines = "SELECT DATE_FORMAT(paid_at, '%Y-%m') AS month, SUM(amount) AS total 
                  FROM fines 
                  WHERE is_paid = 1 AND paid_at IS NOT NULL
                  GROUP BY month 
                  ORDER BY month DESC 
                  LIMIT 6";
    $res_fines = mysqli_query($conn, $sql_fines);
    $fines_data = [];
    if ($res_fines) {
        while ($row = mysqli_fetch_assoc($res_fines)) {
            $fines_data[] = $row;
        }
    }

    // 3. Most Active Branches (By Total Loans)
    $sql_branches = "SELECT b.name, COUNT(br.id) AS loan_count 
                     FROM branches b 
                     LEFT JOIN borrow_records br ON b.id = br.branch_id 
                     GROUP BY b.id 
                     ORDER BY loan_count DESC 
                     LIMIT 5";
    $res_branches = mysqli_query($conn, $sql_branches);
    $branches_data = [];
    if ($res_branches) {
        while ($row = mysqli_fetch_assoc($res_branches)) {
            $branches_data[] = $row;
        }
    }

    // 4. Most Borrowed Genres
    $sql_genres = "SELECT g.name, COUNT(br.id) AS borrow_count 
                   FROM genres g 
                   JOIN books bk ON g.id = bk.genre_id 
                   JOIN borrow_records br ON bk.id = br.book_id 
                   GROUP BY g.id 
                   ORDER BY borrow_count DESC 
                   LIMIT 5";
    $res_genres = mysqli_query($conn, $sql_genres);
    $genres_data = [];
    if ($res_genres) {
        while ($row = mysqli_fetch_assoc($res_genres)) {
            $genres_data[] = $row;
        }
    }

    // 5. Member Growth Trend (New Members per Month)
    $sql_growth = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count 
                   FROM users 
                   WHERE role = 'member' 
                   GROUP BY month 
                   ORDER BY month DESC 
                   LIMIT 6";
    $res_growth = mysqli_query($conn, $sql_growth);
    $growth_data = [];
    if ($res_growth) {
        while ($row = mysqli_fetch_assoc($res_growth)) {
            $growth_data[] = $row;
        }
    }

    return [
        'borrows' => $borrows_data,
        'fines' => $fines_data,
        'branches' => $branches_data,
        'genres' => $genres_data,
        'growth' => $growth_data
    ];
}

/**
 * Fetch all system settings indexed by setting_key
 * 
 * @param mysqli $conn Database connection
 * @return array
 */
function getSystemSettings($conn)
{
    $sql = "SELECT * FROM system_settings";
    $result = mysqli_query($conn, $sql);
    $settings = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $settings[$row['setting_key']] = $row;
        }
    }
    return $settings;
}

/**
 * Update system settings key-value pairs
 * 
 * @param mysqli $conn Database connection
 * @param array $updates Associative array of setting_key => setting_value
 * @return bool
 */
function updateSystemSettings($conn, $updates)
{
    foreach ($updates as $key => $value) {
        $escaped_key = mysqli_real_escape_string($conn, $key);
        $escaped_value = mysqli_real_escape_string($conn, $value);
        $sql = "UPDATE system_settings 
                SET setting_value = '$escaped_value' 
                WHERE setting_key = '$escaped_key'";
        if (!mysqli_query($conn, $sql)) {
            return false;
        }
    }
    return true;
}
