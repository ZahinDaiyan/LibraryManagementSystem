<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchModel.php';

$conn = Connect();
$_SESSION['admin_branches'] = getAdminBranchesList($conn);
Close($conn);

header('Location: ../Views/Admin/BranchListView.php');
exit();
