<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch_manager') {
    header("Location: ../Views/login.php");
    exit;
}

require_once "../config/Database.php";
require_once "../Models/BorrowModel.php";

$db = new Database();
$conn = $db->getConnection();

$borrowModel = new BorrowModel($conn);
$managerId = (int)$_SESSION['user_id'];

$error = "";

$allowedStatuses = ["", "pending", "active", "returned", "rejected"];
$allowedDueFilters = ["", "overdue", "due_today", "due_week"];

$filters = [
    "branch_id" => isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 0,
    "status" => trim($_GET['status'] ?? ''),
    "due_filter" => trim($_GET['due_filter'] ?? ''),
    "date_from" => trim($_GET['date_from'] ?? ''),
    "date_to" => trim($_GET['date_to'] ?? ''),
    "keyword" => trim($_GET['keyword'] ?? '')
];

if (!in_array($filters["status"], $allowedStatuses, true)) {
    $filters["status"] = "";
}

if (!in_array($filters["due_filter"], $allowedDueFilters, true)) {
    $filters["due_filter"] = "";
}

if ($filters["branch_id"] > 0 && !$borrowModel->isBranchManagedByManager($filters["branch_id"], $managerId)) {
    $error = "Selected branch is not under your oversight.";
    $filters["branch_id"] = 0;
}

$thresholdDays = isset($_GET['threshold_days']) ? (int)$_GET['threshold_days'] : 7;

if ($thresholdDays < 0) {
    $thresholdDays = 7;
}

$branches = $borrowModel->getManagedBranches($managerId);
$summary = $borrowModel->getBorrowSummary($managerId);
$borrowRecords = $borrowModel->getBorrowRecords($managerId, $filters);
$branchStats = $borrowModel->getBranchWiseBorrowStats($managerId);
$overdueAlerts = $borrowModel->getOverdueAlerts($managerId, $thresholdDays);
$outstandingFines = $borrowModel->getOutstandingFines($managerId);

require_once "../Views/borrow_report.php";

?>