<?php

session_start();

require_once '../Controllers/AdminAjaxSupport.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (adminWantsJson()) {
        adminJsonResponse(false, 'Unauthorized', array('announcements' => array()), 403);
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/AnnouncementModel.php';

$conn = Connect();
$_SESSION['admin_announcements'] = getAllAnnouncements($conn);
Close($conn);

if (adminWantsJson()) {
    adminJsonResponse(true, 'Announcements loaded', array('announcements' => $_SESSION['admin_announcements']));
}

header('Location: ../Views/Admin/AnnouncementListView.php');
exit();
