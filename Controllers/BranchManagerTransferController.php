<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchManagerModel.php';

$statusFilter = $_GET['status_filter'] ?? '';
$allowed = array('', 'pending', 'approved', 'rejected', 'completed');
if (!in_array($statusFilter, $allowed)) {
    $statusFilter = '';
}

$conn = Connect();
$_SESSION['bm_transfers'] = bmGetTransferRequests($conn, $_SESSION['id'], $statusFilter);
Close($conn);

header('Location: ../Views/BranchManager/TransferView.php?status_filter=' . urlencode($statusFilter));
exit();

?>
