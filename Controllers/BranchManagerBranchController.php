<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchManagerModel.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$conn = Connect();
$branches = bmGetManagedBranches($conn, $_SESSION['id']);
$editBranch = null;

if ($id > 0) {
    $editBranch = bmGetManagedBranchById($conn, $_SESSION['id'], $id);
    if (!$editBranch) {
        $_SESSION['error'] = 'Branch not found under your oversight';
    }
}

Close($conn);

$_SESSION['bm_branches'] = $branches;
$_SESSION['bm_edit_branch'] = $editBranch;

header('Location: ../Views/BranchManager/BranchListView.php');
exit();

?>
