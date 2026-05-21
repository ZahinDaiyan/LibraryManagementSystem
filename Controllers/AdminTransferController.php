<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Unauthorized', array('transfers' => array()), 403);
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchModel.php';

$status_filter = $_POST['status_filter'] ?? '';
$conn = Connect();

$_SESSION['admin_transfers'] = getAllInterBranchRequests($conn, $status_filter);
Close($conn);

$_SESSION['admin_transfer_status_filter'] = $status_filter;

if (adminWantsJson()) {
    adminJsonResponse(true, 'Transfers loaded', array(
        'transfers' => $_SESSION['admin_transfers'],
        'status_filter' => $status_filter
    ));
}

header('Location: ../Views/Admin/TransferListView.php');
exit();
