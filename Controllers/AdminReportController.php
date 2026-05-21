<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Unauthorized', array('reports' => array()), 403);
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/AdminModel.php';

$conn = Connect();
$_SESSION['admin_reports'] = getAdminReportData($conn);
Close($conn);

if (adminWantsJson()) {
    adminJsonResponse(true, 'Reports loaded', array('reports' => $_SESSION['admin_reports']));
}

header('Location: ../Views/Admin/ReportView.php');
exit();
