<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchManagerModel.php';

$branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 0;
$month = $_GET['month'] ?? date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
    $month = date('Y-m');
}

$conn = Connect();
$reports = array(
    'branches' => bmGetManagedBranches($conn, $_SESSION['id']),
    'inventory' => bmGetInventoryReport($conn, $_SESSION['id']),
    'borrowing_stats' => bmGetBranchBorrowingStats($conn, $_SESSION['id']),
    'most_borrowed' => bmGetMostBorrowedBooks($conn, $_SESSION['id']),
    'top_members' => bmGetTopBorrowingMembers($conn, $_SESSION['id']),
    'outstanding_fines' => bmGetMembersWithOutstandingFines($conn, $_SESSION['id']),
    'new_members' => bmGetNewMemberRegistrationsByBranch($conn, $_SESSION['id']),
    'monthly' => bmGetMonthlyReports($conn, $_SESSION['id'], $branchId, $month),
    'librarian_activity' => bmGetLibrarianActivity($conn, $_SESSION['id']),
    'overdue_alerts' => bmGetOverdueAlerts($conn, $_SESSION['id'], 7)
);
Close($conn);

$_SESSION['bm_reports'] = $reports;

header('Location: ../Views/BranchManager/ReportView.php?branch_id=' . urlencode($branchId) . '&month=' . urlencode($month));
exit();

?>
