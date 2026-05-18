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
    $where_sql = "WHERE ibr.status = '$status_filter'";
}

$sql = "SELECT ibr.*, b.title AS book_title, 
               br_from.name AS from_branch_name, 
               br_to.name AS to_branch_name,
               u.name AS requester_name
        FROM inter_branch_requests ibr
        JOIN books b ON ibr.book_id = b.id
        JOIN branches br_from ON ibr.from_branch_id = br_from.id
        JOIN branches br_to ON ibr.to_branch_id = br_to.id
        JOIN users u ON ibr.requested_by = u.id
        $where_sql
        ORDER BY ibr.created_at DESC";

$result = mysqli_query($conn, $sql);
$transfers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $transfers[] = $row;
}

Close($conn);

$_SESSION['admin_transfers'] = $transfers;
$_SESSION['admin_transfer_status_filter'] = $status_filter;

header('Location: ../Views/Admin/TransferListView.php');
exit();
