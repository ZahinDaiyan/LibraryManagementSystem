<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/AdminModel.php';
require_once '../Models/LoanModel.php';

$conn = Connect();
$_SESSION['admin_stats'] = getAdminDashboardStats($conn);
$_SESSION['admin_pending_renewals'] = getPendingRenewalRequestsForAdmin($conn);
Close($conn);

header('Location: ../Views/Admin/dashboardView.php');
exit();
