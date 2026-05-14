<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';

$conn = Connect();

$sql = "SELECT * FROM system_settings";
$result = mysqli_query($conn, $sql);
$settings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $settings[$row['setting_key']] = $row;
}

Close($conn);

$_SESSION['system_settings'] = $settings;

header('Location: ../Views/Admin/SettingsView.php');
exit();
