<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Unauthorized', array('complaints' => array()), 403);
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/ComplaintModel.php';

$status_filter = $_POST['status_filter'] ?? '';
$conn = Connect();

$_SESSION['admin_complaints'] = getAllComplaints($conn, $status_filter);
Close($conn);

$_SESSION['admin_complaint_status_filter'] = $status_filter;

if (adminWantsJson()) {
    adminJsonResponse(true, 'Complaints loaded', array(
        'complaints' => $_SESSION['admin_complaints'],
        'status_filter' => $status_filter
    ));
}

header('Location: ../Views/Admin/ComplaintListView.php');
exit();
