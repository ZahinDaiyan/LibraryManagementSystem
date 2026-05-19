<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/LoanModel.php';

$conn = Connect();
$loans = getActiveLoans($conn, $_SESSION['id']);
Close($conn);

$_SESSION['active_loans'] = $loans;

header("Location: ../Views/Member/MyLoansView.php");
exit();
?>