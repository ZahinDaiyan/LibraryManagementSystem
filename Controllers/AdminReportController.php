<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';

$conn = Connect();

// 1. Total Borrows per Month (Last 6 Months)
$sql_borrows = "SELECT DATE_FORMAT(borrow_date, '%Y-%m') AS month, COUNT(*) AS count 
                FROM borrow_records 
                WHERE borrow_date IS NOT NULL
                GROUP BY month 
                ORDER BY month DESC 
                LIMIT 6";
$res_borrows = mysqli_query($conn, $sql_borrows);
$borrows_data = [];
while($row = mysqli_fetch_assoc($res_borrows)) $borrows_data[] = $row;

// 2. Total Fines Collected per Month (Last 6 Months)
$sql_fines = "SELECT DATE_FORMAT(paid_at, '%Y-%m') AS month, SUM(amount) AS total 
              FROM fines 
              WHERE is_paid = 1 AND paid_at IS NOT NULL
              GROUP BY month 
              ORDER BY month DESC 
              LIMIT 6";
$res_fines = mysqli_query($conn, $sql_fines);
$fines_data = [];
while($row = mysqli_fetch_assoc($res_fines)) $fines_data[] = $row;

// 3. Most Active Branches (By Total Loans)
$sql_branches = "SELECT b.name, COUNT(br.id) AS loan_count 
                 FROM branches b 
                 LEFT JOIN borrow_records br ON b.id = br.branch_id 
                 GROUP BY b.id 
                 ORDER BY loan_count DESC 
                 LIMIT 5";
$res_branches = mysqli_query($conn, $sql_branches);
$branches_data = [];
while($row = mysqli_fetch_assoc($res_branches)) $branches_data[] = $row;

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
while($row = mysqli_fetch_assoc($res_genres)) $genres_data[] = $row;

// 5. Member Growth Trend (New Members per Month)
$sql_growth = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count 
               FROM users 
               WHERE role = 'member' 
               GROUP BY month 
               ORDER BY month DESC 
               LIMIT 6";
$res_growth = mysqli_query($conn, $sql_growth);
$growth_data = [];
while($row = mysqli_fetch_assoc($res_growth)) $growth_data[] = $row;

Close($conn);

$_SESSION['admin_reports'] = [
    'borrows' => $borrows_data,
    'fines' => $fines_data,
    'branches' => $branches_data,
    'genres' => $genres_data,
    'growth' => $growth_data
];

header('Location: ../Views/Admin/ReportView.php');
exit();
