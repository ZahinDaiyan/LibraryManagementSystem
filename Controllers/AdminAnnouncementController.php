<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';

$conn = Connect();

$sql = "SELECT a.*, b.name AS branch_name, u.name AS author_name 
        FROM announcements a
        LEFT JOIN branches b ON a.branch_id = b.id
        JOIN users u ON a.author_id = u.id
        ORDER BY a.published_at DESC";

$result = mysqli_query($conn, $sql);
$announcements = [];
while ($row = mysqli_fetch_assoc($result)) {
    $announcements[] = $row;
}

Close($conn);

$_SESSION['admin_announcements'] = $announcements;

header('Location: ../Views/Admin/AnnouncementListView.php');
exit();
