<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/AuditModel.php';

$conn = Connect();
$logs = getAuditLogs($conn);
Close($conn);

$_SESSION['audit_logs'] = $logs;

header('Location: ../Views/Admin/AuditLogView.php');
exit();
