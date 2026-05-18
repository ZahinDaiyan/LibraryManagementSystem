<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';

$status_filter = $_POST['status_filter'] ?? '';
$conn = Connect();

$where_sql = "";
if (!empty($status_filter)) {
    $where_sql = "WHERE c.status = '$status_filter'";
}

$sql = "SELECT c.*, u.name AS member_name, u.email AS member_email 
        FROM complaints c
        JOIN users u ON c.member_id = u.id
        $where_sql
        ORDER BY c.created_at DESC";

$result = mysqli_query($conn, $sql);
$complaints = [];
while ($row = mysqli_fetch_assoc($result)) {
    $complaints[] = $row;
}

Close($conn);

$_SESSION['admin_complaints'] = $complaints;
$_SESSION['admin_complaint_status_filter'] = $status_filter;

header('Location: ../Views/Admin/ComplaintListView.php');
exit();
