<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Unauthorized', array('system_settings' => array()), 403);
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/AdminModel.php';

$conn = Connect();
$_SESSION['system_settings'] = getSystemSettings($conn);
Close($conn);

if (adminWantsJson()) {
    adminJsonResponse(true, 'Settings loaded', array('system_settings' => $_SESSION['system_settings']));
}

header('Location: ../Views/Admin/SettingsView.php');
exit();
