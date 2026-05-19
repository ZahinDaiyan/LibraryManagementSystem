<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/LoanModel.php';

$conn = Connect();
$history = getBorrowHistory($conn, $_SESSION['id']);
Close($conn);

$_SESSION['borrow_history'] = $history;

header("Location: ../Views/Member/BorrowHistoryView.php");
exit();
