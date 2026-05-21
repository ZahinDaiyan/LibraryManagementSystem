<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Unauthorized', array('branches' => array()), 403);
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchModel.php';

$conn = Connect();
$_SESSION['admin_branches'] = getAdminBranchesList($conn);
Close($conn);

if (adminWantsJson()) {
    adminJsonResponse(true, 'Branches loaded', array('branches' => $_SESSION['admin_branches']));
}

header('Location: ../Views/Admin/BranchListView.php');
exit();
