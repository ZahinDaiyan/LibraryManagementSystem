<?php

session_start();

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], array('librarian', 'branch_manager', 'admin'))) {
    $_SESSION['error'] = 'Unauthorized';
    header('Location: ../Views/LoginView.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method';
    if ($_SESSION['role'] === 'librarian') {
        header('Location: LibrarianOperationsController.php');
    } elseif ($_SESSION['role'] === 'branch_manager') {
        header('Location: BranchManagerDashboardController.php');
    } else {
        header('Location: AdminDashboardController.php');
    }
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/LoanModel.php';

$requestId = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;
$decision = isset($_POST['decision']) ? trim($_POST['decision']) : '';

if ($requestId <= 0 || ($decision !== 'approved' && $decision !== 'rejected')) {
    $_SESSION['error'] = 'Invalid renewal decision request';
    if ($_SESSION['role'] === 'librarian') {
        header('Location: LibrarianOperationsController.php');
    } elseif ($_SESSION['role'] === 'branch_manager') {
        header('Location: BranchManagerDashboardController.php');
    } else {
        header('Location: AdminDashboardController.php');
    }
    exit();
}

$conn = Connect();
$result = processRenewalApprovalDecision($conn, $requestId, $_SESSION['role'], $_SESSION['id'], $decision);
Close($conn);

if (is_array($result) && isset($result['success']) && $result['success']) {
    $_SESSION['msg'] = $result['message'];
} else {
    $_SESSION['error'] = is_array($result) && isset($result['message']) ? $result['message'] : 'Failed to process renewal decision';
}

if ($_SESSION['role'] === 'librarian') {
    header('Location: LibrarianOperationsController.php');
} elseif ($_SESSION['role'] === 'branch_manager') {
    header('Location: BranchManagerDashboardController.php');
} else {
    header('Location: AdminDashboardController.php');
}
exit();

?>
