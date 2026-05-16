<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch_manager') {
    header("Location: ../Views/login.php");
    exit;
}

require_once "../config/Database.php";
require_once "../Models/BranchModel.php";

$db = new Database();
$conn = $db->getConnection();

$branchModel = new BranchModel($conn);
$managerId = (int)$_SESSION['user_id'];

$success = "";
$error = "";
$editBranch = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle_status') {
        $branchId = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : 0;
        $newStatus = $branchModel->toggleBranchStatus($branchId, $managerId);

        header("Content-Type: application/json");

        if ($newStatus !== false) {
            echo json_encode([
                "success" => true,
                "new_status" => $newStatus,
                "status_text" => $newStatus == 1 ? "Active" : "Inactive"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Unable to update branch status."
            ]);
        }

        exit;
    }

    if ($action === 'add_branch') {
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($name === '' || $address === '') {
            $error = "Branch name and address are required.";
        } else {
            $created = $branchModel->addBranch($name, $address, $city, $phone, $managerId);

            if ($created) {
                header("Location: BranchController.php?success=Branch+added+successfully");
                exit;
            } else {
                $error = "Failed to add branch.";
            }
        }
    }

    if ($action === 'update_branch') {
        $branchId = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : 0;
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($branchId <= 0 || $name === '' || $address === '') {
            $error = "Valid branch ID, name, and address are required.";
        } else {
            $updated = $branchModel->updateBranch($branchId, $name, $address, $city, $phone, $managerId);

            if ($updated) {
                header("Location: BranchController.php?success=Branch+updated+successfully");
                exit;
            } else {
                $error = "Failed to update branch. You may not have permission for this branch.";
            }
        }
    }
}

if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    $editBranch = $branchModel->getBranchByIdForManager($editId, $managerId);

    if (!$editBranch) {
        $error = "Branch not found or permission denied.";
    }
}

if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

$branches = $branchModel->getManagedBranches($managerId);

require_once "../Views/branch_list.php";

?>