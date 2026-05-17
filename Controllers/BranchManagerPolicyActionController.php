<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchManagerModel.php';

$branchId = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : 0;
$maxBorrowDays = isset($_POST['max_borrow_days']) ? (int)$_POST['max_borrow_days'] : 0;
$maxBooks = isset($_POST['max_books_per_member']) ? (int)$_POST['max_books_per_member'] : 0;
$fineRate = isset($_POST['fine_rate_per_day']) ? (float)$_POST['fine_rate_per_day'] : -1;
$maxRenewals = isset($_POST['max_renewals']) ? (int)$_POST['max_renewals'] : -1;

if ($branchId <= 0 || $maxBorrowDays < 1 || $maxBooks < 1 || $fineRate < 0 || $maxRenewals < 0) {
    $_SESSION['error'] = 'Please provide valid policy values';
    header('Location: BranchManagerPolicyController.php');
    exit();
}

$conn = Connect();
$ok = bmSaveBranchPolicy($conn, $_SESSION['id'], $branchId, $maxBorrowDays, $maxBooks, $fineRate, $maxRenewals);
Close($conn);

$_SESSION[$ok ? 'msg' : 'error'] = $ok ? 'Branch policy saved successfully' : 'Unable to save branch policy';

header('Location: BranchManagerPolicyController.php');
exit();

?>
