<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Unauthorized', array('stats' => array(), 'pending_renewals' => array()), 403);
    }

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

if (adminWantsJson()) {
    adminJsonResponse(true, 'Dashboard loaded', array(
        'stats' => $_SESSION['admin_stats'],
        'pending_renewals' => $_SESSION['admin_pending_renewals']
    ));
}

header('Location: ../Views/Admin/dashboardView.php');
exit();
