<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    adminFinishResponse(adminWantsJson(), false, 'Unauthorized', '../Views/LoginView.php', array(), 403);
}

require_once '../Models/DB.php';
require_once '../Models/BranchModel.php';

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? '';
$conn = Connect();
$success = false;
$message = 'Unknown admin branch action';

if ($action === 'toggle_status') {
    if (toggleBranchStatus($conn, $id)) {
        $success = true;
        $message = "Branch status updated successfully";
    } else {
        $success = false;
        $message = "Failed to update branch status";
    }
}

Close($conn);
adminFinishResponse(adminWantsJson(), $success, $message, 'AdminBranchController.php', array(), $success ? 200 : 400);
