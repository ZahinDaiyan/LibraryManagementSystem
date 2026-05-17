<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchManagerModel.php';

$requestId = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;
$status = $_POST['status'] ?? '';
$allowed = array('approved', 'rejected', 'completed');

if (!in_array($status, $allowed)) {
    $_SESSION['error'] = 'Invalid transfer action';
    header('Location: BranchManagerTransferController.php');
    exit();
}

$conn = Connect();
$ok = bmUpdateTransferStatus($conn, $_SESSION['id'], $requestId, $status);
Close($conn);

if ($ok) {
    $_SESSION['msg'] = 'Transfer request updated successfully';
} else {
    $_SESSION['error'] = 'Unable to update transfer request. Check status and available source copies.';
}

header('Location: BranchManagerTransferController.php');
exit();

?>
