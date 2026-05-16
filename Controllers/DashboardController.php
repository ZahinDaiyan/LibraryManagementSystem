<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch_manager') {
    header("Location: ../views/login.php");
    exit;
}

require_once "../config/Database.php";
require_once "../models/DashboardModel.php";

$db = new Database();
$conn = $db->getConnection();

$dashboardModel = new DashboardModel($conn);

$managerId = (int)$_SESSION['user_id'];

$manager = $dashboardModel->getManagerInfo($managerId);

if (!$manager) {
    session_destroy();
    header("Location: ../views/login.php?error=Invalid+branch+manager+session");
    exit;
}

$branches = $dashboardModel->getManagedBranches($managerId);
$branchIds = $dashboardModel->getBranchIds($branches);

$summary = $dashboardModel->getDashboardSummary($branchIds);
$branchStats = $dashboardModel->getBranchWiseStats($branchIds);
$recentBorrowRecords = $dashboardModel->getRecentBorrowRecords($branchIds);
$recentTransferRequests = $dashboardModel->getRecentTransferRequests($branchIds);

require_once "../views/dashboard.php";

?>