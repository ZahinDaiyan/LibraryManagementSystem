<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchManagerModel.php';

$conn = Connect();
$_SESSION['bm_policies'] = bmGetBranchPolicies($conn, $_SESSION['id']);
Close($conn);

header('Location: ../Views/BranchManager/PolicyView.php');
exit();

?>
