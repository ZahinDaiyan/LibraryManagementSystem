<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Unauthorized', array(), 403);
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/ComplaintModel.php';

$id = $_POST['id'] ?? $_SESSION['admin_complaint_detail_id'] ?? '';
unset($_SESSION['admin_complaint_detail_id']);
$conn = Connect();

$complaint = getComplaintById($conn, $id);

Close($conn);

if (!$complaint) {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Complaint not found', array(), 404);
    }

    header('Location: AdminComplaintController.php');
    exit();
}

$_SESSION['current_complaint'] = $complaint;

if (adminWantsJson()) {
    adminJsonResponse(true, 'Complaint loaded', array('complaint' => $complaint));
}

header('Location: ../Views/Admin/ComplaintDetailView.php');
exit();
