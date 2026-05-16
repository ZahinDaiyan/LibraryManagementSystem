<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch_manager') {
    header("Location: ../Views/login.php");
    exit;
}

require_once "../config/Database.php";
require_once "../Models/BranchPolicyModel.php";

$db = new Database();
$conn = $db->getConnection();

$policyModel = new BranchPolicyModel($conn);
$managerId = (int)$_SESSION['user_id'];

$success = "";
$error = "";
$selectedBranchId = 0;
$selectedPolicy = null;

$branches = $policyModel->getManagedBranches($managerId);

if (!empty($branches)) {
    $selectedBranchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : (int)$branches[0]['id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_policy') {
        $branchId = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : 0;
        $maxBorrowDays = isset($_POST['max_borrow_days']) ? (int)$_POST['max_borrow_days'] : 0;
        $maxBooksPerMember = isset($_POST['max_books_per_member']) ? (int)$_POST['max_books_per_member'] : 0;
        $fineRatePerDay = isset($_POST['fine_rate_per_day']) ? (float)$_POST['fine_rate_per_day'] : 0;
        $maxRenewals = isset($_POST['max_renewals']) ? (int)$_POST['max_renewals'] : 0;

        if ($branchId <= 0) {
            $error = "Please select a valid branch.";
        } elseif ($maxBorrowDays <= 0) {
            $error = "Maximum borrow days must be greater than 0.";
        } elseif ($maxBooksPerMember <= 0) {
            $error = "Maximum books per member must be greater than 0.";
        } elseif ($fineRatePerDay < 0) {
            $error = "Fine rate cannot be negative.";
        } elseif ($maxRenewals < 0) {
            $error = "Maximum renewals cannot be negative.";
        } else {
            $saved = $policyModel->savePolicy(
                $branchId,
                $maxBorrowDays,
                $maxBooksPerMember,
                $fineRatePerDay,
                $maxRenewals,
                $managerId
            );

            if ($saved) {
                header("Location: PolicyController.php?branch_id=" . $branchId . "&success=Policy+saved+successfully");
                exit;
            } else {
                $error = "Failed to save policy. Permission may be denied for this branch.";
            }
        }

        $selectedBranchId = $branchId;
    }
}

if (isset($_GET['branch_id'])) {
    $selectedBranchId = (int)$_GET['branch_id'];
}

if ($selectedBranchId > 0) {
    $selectedPolicy = $policyModel->getPolicyByBranch($selectedBranchId, $managerId);
}

if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

$allPolicies = $policyModel->getAllPoliciesForManager($managerId);

require_once "../Views/branch_policy.php";

?>