<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: /LibraryManagementSystem/Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/FineModel.php';

$conn = Connect();
$unpaid = getMemberFines($conn, $_SESSION['id']);
$paid = getPaidFineHistory($conn, $_SESSION['id']);
Close($conn);

$_SESSION['unpaid_fines'] = $unpaid;
$_SESSION['paid_fines'] = $paid;

header("Location: /LibraryManagementSystem/Views/Member/FinesView.php");
exit();
