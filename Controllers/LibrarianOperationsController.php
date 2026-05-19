<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/LibrarianWorkflowModel.php';

$conn = Connect();
$branchInfo = getLibrarianBranchByUserId($conn, $_SESSION['id']);
$branchId = isset($branchInfo['branch_id']) ? (int)$branchInfo['branch_id'] : 0;

$data = array();
$data['branch'] = $branchInfo;
$data['genres'] = getGenresList($conn);
$data['inventory'] = $branchId ? getBranchInventoryRows($conn, $branchId) : array();
$data['unassigned_books'] = $branchId ? getBooksWithoutInventoryForBranch($conn, $branchId) : array();
$data['pending_requests'] = $branchId ? getPendingBorrowRequestsForBranch($conn, $branchId) : array();
$data['active_loans'] = $branchId ? getActiveLoansForBranch($conn, $branchId, '') : array();
$data['reservations'] = $branchId ? getReservationWaitlistForBranch($conn, $branchId) : array();
$data['members'] = $branchId && isset($_POST['member_query']) ? searchMembersByBranch($conn, $branchId, $_POST['member_query']) : array();
$data['stats'] = $branchId ? getBranchCatalogStats($conn, $branchId) : array('most_borrowed' => array(), 'never_borrowed' => array(), 'borrows_by_genre' => array());
$data['announcements'] = $branchId ? getAnnouncementsForBranch($conn, $branchId) : array();
$data['transfers'] = $branchId ? getInterBranchRequestsForBranch($conn, $branchId) : array();
$data['unpaid_fines'] = $branchId ? getUnpaidFinesForBranch($conn, $branchId) : array();

if (isset($_POST['member_id']) && $branchId) {
    $memberId = (int)$_POST['member_id'];
    $data['member_history'] = getMemberBorrowHistory($conn, $memberId);
    $data['member_fines'] = getMemberFineHistory($conn, $memberId);
} else {
    $data['member_history'] = array();
    $data['member_fines'] = array();
}

if (isset($_POST['return_query']) && $branchId) {
    $data['return_matches'] = getBorrowRecordForReturnSearch($conn, $branchId, $_POST['return_query']);
} else {
    $data['return_matches'] = array();
}

if (isset($_POST['loan_filter']) && $branchId) {
    $data['active_loans'] = getActiveLoansForBranch($conn, $branchId, $_POST['loan_filter']);
}

Close($conn);

$_SESSION['librarian_ops'] = $data;

require_once '../Views/Librariyan/OperationsView.php';

?>