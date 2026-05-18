<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';

$id = $_POST['id'] ?? $_SESSION['admin_complaint_detail_id'] ?? '';
unset($_SESSION['admin_complaint_detail_id']);
$conn = Connect();

$sql = "SELECT c.*, u.name AS member_name, u.email AS member_email 
        FROM complaints c
        JOIN users u ON c.member_id = u.id
        WHERE c.id = '$id'
        LIMIT 1";

$result = mysqli_query($conn, $sql);
$complaint = mysqli_fetch_assoc($result);

Close($conn);

if (!$complaint) {
    header('Location: AdminComplaintController.php');
    exit();
}

$_SESSION['current_complaint'] = $complaint;

header('Location: ../Views/Admin/ComplaintDetailView.php');
exit();
