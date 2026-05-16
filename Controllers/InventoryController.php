<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch_manager') {
    header("Location: ../Views/login.php");
    exit;
}

require_once "../config/Database.php";
require_once "../Models/InventoryModel.php";

$db = new Database();
$conn = $db->getConnection();

$inventoryModel = new InventoryModel($conn);
$managerId = (int)$_SESSION['user_id'];

$error = "";

$filters = [
    "branch_id" => isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 0,
    "genre_id" => isset($_GET['genre_id']) ? (int)$_GET['genre_id'] : 0,
    "keyword" => trim($_GET['keyword'] ?? ''),
    "availability" => trim($_GET['availability'] ?? '')
];

if ($filters["branch_id"] > 0 && !$inventoryModel->isBranchManagedByManager($filters["branch_id"], $managerId)) {
    $error = "Selected branch is not under your oversight.";
    $filters["branch_id"] = 0;
}

$branches = $inventoryModel->getManagedBranches($managerId);
$genres = $inventoryModel->getGenres();
$summary = $inventoryModel->getInventorySummary($managerId);
$inventoryRecords = $inventoryModel->getInventoryReport($managerId, $filters);
$lowAvailabilityBooks = $inventoryModel->getLowAvailabilityBooks($managerId);

require_once "../Views/inventory_report.php";

?>