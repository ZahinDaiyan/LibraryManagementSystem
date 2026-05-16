<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch_manager') {
    header("Location: ../Views/login.php");
    exit;
}

require_once "../config/Database.php";
require_once "../Models/ReportModel.php";

$db = new Database();
$conn = $db->getConnection();

$reportModel = new ReportModel($conn);
$managerId = (int)$_SESSION['user_id'];

$error = "";

$currentMonth = (int)date("m");
$currentYear = (int)date("Y");

$month = isset($_GET['month']) ? (int)$_GET['month'] : $currentMonth;
$year = isset($_GET['year']) ? (int)$_GET['year'] : $currentYear;
$branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 0;

if ($month < 1 || $month > 12) {
    $month = $currentMonth;
}

if ($year < 2000 || $year > 2100) {
    $year = $currentYear;
}

if ($branchId > 0 && !$reportModel->isBranchManagedByManager($branchId, $managerId)) {
    $error = "Selected branch is not under your oversight.";
    $branchId = 0;
}

$branches = $reportModel->getManagedBranches($managerId);

$summary = $reportModel->getMonthlySummary($managerId, $month, $year, $branchId);
$branchReports = $reportModel->getBranchMonthlyReports($managerId, $month, $year, $branchId);
$mostActiveMembers = $reportModel->getMostActiveMembers($managerId, $month, $year, $branchId);
$membersWithFines = $reportModel->getMembersWithOutstandingFines($managerId, $branchId);
$newMembers = $reportModel->getNewMemberRegistrations($managerId, $month, $year, $branchId);
$librarianActivity = $reportModel->getLibrarianActivity($managerId, $month, $year, $branchId);

require_once "../Views/monthly_report.php";

?>