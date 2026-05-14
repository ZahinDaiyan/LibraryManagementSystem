<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';

$conn = Connect();

$sql = "SELECT b.*, u.name AS manager_name, 
        (SELECT COUNT(*) FROM users WHERE branch_id = b.id AND role = 'librarian') AS librarian_count 
        FROM branches b 
        LEFT JOIN users u ON b.manager_id = u.id 
        ORDER BY b.name ASC";

$result = mysqli_query($conn, $sql);
$branches = [];
while ($row = mysqli_fetch_assoc($result)) {
    $branches[] = $row;
}

Close($conn);

$_SESSION['admin_branches'] = $branches;

header('Location: ../Views/Admin/BranchListView.php');
exit();
