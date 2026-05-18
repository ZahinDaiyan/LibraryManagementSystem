<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/AdminModel.php';

$conn = Connect();
$_SESSION['admin_reports'] = getAdminReportData($conn);
Close($conn);

header('Location: ../Views/Admin/ReportView.php');
exit();
