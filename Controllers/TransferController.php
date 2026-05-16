<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch_manager') {
    header("Location: ../Views/login.php");
    exit;
}

require_once "../config/Database.php";
require_once "../Models/TransferRequestModel.php";

$db = new Database();
$conn = $db->getConnection();

$transferModel = new TransferRequestModel($conn);
$managerId = (int)$_SESSION['user_id'];

$error = "";
$success = "";

$allowedStatuses = ["", "pending", "approved", "rejected", "completed"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $requestId = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;

    header("Content-Type: application/json");

    if ($requestId <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid transfer request selected."
        ]);
        exit;
    }

    if ($action === 'approve') {
        echo json_encode($transferModel->approveRequest($requestId, $managerId));
        exit;
    }

    if ($action === 'reject') {
        echo json_encode($transferModel->rejectRequest($requestId, $managerId));
        exit;
    }

    if ($action === 'complete') {
        echo json_encode($transferModel->completeTransfer($requestId, $managerId));
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Invalid action."
    ]);
    exit;
}

$filters = [
    "status" => trim($_GET['status'] ?? ''),
    "branch_id" => isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 0,
    "keyword" => trim($_GET['keyword'] ?? '')
];

if (!in_array($filters["status"], $allowedStatuses, true)) {
    $filters["status"] = "";
}

$branches = $transferModel->getManagedBranches($managerId);

$managedBranchIds = [];

foreach ($branches as $branch) {
    $managedBranchIds[] = (int)$branch['id'];
}

if ($filters["branch_id"] > 0 && !in_array($filters["branch_id"], $managedBranchIds, true)) {
    $error = "Selected branch is not under your oversight.";
    $filters["branch_id"] = 0;
}

$summary = $transferModel->getTransferSummary($managerId);
$transferRequests = $transferModel->getTransferRequests($managerId, $filters);

require_once "../Views/transfer_requests.php";

?>