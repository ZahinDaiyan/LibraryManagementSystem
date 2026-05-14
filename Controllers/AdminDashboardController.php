<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';

$conn = Connect();

// 1. Total members
$res_members = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'member'");
$total_members = mysqli_fetch_assoc($res_members)['count'];

// 2. Total books in catalog
$res_books = mysqli_query($conn, "SELECT COUNT(*) as count FROM books");
$total_books = mysqli_fetch_assoc($res_books)['count'];

// 3. Total active loans
$res_active = mysqli_query($conn, "SELECT COUNT(*) as count FROM borrow_records WHERE status = 'active'");
$total_active_loans = mysqli_fetch_assoc($res_active)['count'];

// 4. Total overdue loans
$res_overdue = mysqli_query($conn, "SELECT COUNT(*) as count FROM borrow_records WHERE status = 'active' AND due_date < CURDATE()");
$total_overdue_loans = mysqli_fetch_assoc($res_overdue)['count'];

// 5. Total fines outstanding
$res_fines = mysqli_query($conn, "SELECT SUM(amount) as total FROM fines WHERE is_paid = 0");
$fines_row = mysqli_fetch_assoc($res_fines);
$total_fines_outstanding = $fines_row['total'] ?? 0;

Close($conn);

$_SESSION['admin_stats'] = [
    'total_members' => $total_members,
    'total_books' => $total_books,
    'total_active_loans' => $total_active_loans,
    'total_overdue_loans' => $total_overdue_loans,
    'total_fines_outstanding' => $total_fines_outstanding
];

header('Location: ../Views/Admin/dashboardView.php');
exit();
