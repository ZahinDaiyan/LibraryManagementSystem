<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchManagerModel.php';

$conn = Connect();
$_SESSION['bm_branches'] = bmGetManagedBranches($conn, $_SESSION['id']);
$_SESSION['bm_assignable_librarians'] = bmGetAssignableLibrarians($conn, $_SESSION['id']);
$_SESSION['bm_managed_librarians'] = bmGetManagedLibrarians($conn, $_SESSION['id']);
Close($conn);

header('Location: ../Views/BranchManager/StaffView.php');
exit();

?>
