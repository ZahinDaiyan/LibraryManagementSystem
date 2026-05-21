<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Unauthorized', array('logs' => array()), 403);
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/AuditModel.php';

$conn = Connect();
$logs = getAuditLogs($conn);
Close($conn);

$_SESSION['audit_logs'] = $logs;

if (adminWantsJson()) {
    adminJsonResponse(true, 'Audit logs loaded', array('logs' => $logs));
}

header('Location: ../Views/Admin/AuditLogView.php');
exit();
