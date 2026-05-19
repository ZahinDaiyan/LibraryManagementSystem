<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/ComplaintModel.php';

$conn = Connect();
$complaints = getMemberComplaints($conn, $_SESSION['id']);
Close($conn);

$_SESSION['member_complaints'] = $complaints;

header("Location: ../Views/Member/ComplaintListView.php");
exit();
