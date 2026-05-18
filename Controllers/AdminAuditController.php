<?php

session_start();

// 1. Enforce strict Admin permission access control
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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

// 4. Redirect to the UI View
header('Location: ../Views/Admin/AuditLogView.php');
exit();
