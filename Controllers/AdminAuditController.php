<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

// 1. Enforce strict Admin permission access control
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Unauthorized', array('logs' => array()), 403);
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/AuditModel.php';

// 2. Open connection, query the logs, and close connection
$conn = Connect();
$logs = getAuditLogs($conn);
Close($conn);

// 3. Put logs array in session for the View to access
$_SESSION['audit_logs'] = $logs;

if (adminWantsJson()) {
    adminJsonResponse(true, 'Audit logs loaded', array('logs' => $logs));
}

// 4. Redirect to the UI View
header('Location: ../Views/Admin/AuditLogView.php');
exit();
