<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchManagerModel.php';
require_once '../Models/LoanModel.php';

$conn = Connect();
$profile = bmGetManagerProfile($conn, $_SESSION['id']);
$branches = bmGetManagedBranches($conn, $_SESSION['id']);
$stats = bmGetDashboardStats($conn, $_SESSION['id']);
$pendingRenewals = getPendingRenewalRequestsForManager($conn, $_SESSION['id']);
Close($conn);

if (!$profile) {
    $_SESSION['error'] = 'Branch manager profile not found';
    header('Location: ../Views/LoginView.php');
    exit();
}

$_SESSION['branch_manager_profile'] = $profile;
$_SESSION['bm_dashboard'] = array(
    'branches' => $branches,
    'stats' => $stats,
    'pending_renewals' => $pendingRenewals
);

header('Location: ../Views/BranchManager/dashboardView.php');
exit();

?>
