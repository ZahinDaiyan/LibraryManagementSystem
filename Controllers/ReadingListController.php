<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: /LibraryManagementSystem/Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/ReadingListModel.php';

$conn = Connect();
$list = getReadingList($conn, $_SESSION['id']);
Close($conn);

$_SESSION['reading_list'] = $list;

header("Location: /LibraryManagementSystem/Views/Member/ReadingListView.php");
exit();
