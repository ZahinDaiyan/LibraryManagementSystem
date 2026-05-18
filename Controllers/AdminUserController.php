<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';
require_once '../Models/BookModel.php'; // For getBranches

$search = $_POST['search'] ?? '';
$role_filter = $_POST['role_filter'] ?? '';

$conn = Connect();

$where_clauses = [];
if (!empty($search)) {
    $where_clauses[] = "(u.name LIKE '%$search%' OR u.email LIKE '%$search%' OR u.phone LIKE '%$search%')";
}
if (!empty($role_filter)) {
    $where_clauses[] = "u.role = '$role_filter'";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(' AND ', $where_clauses);
}

$sql = "SELECT u.*, b.name AS branch_name 
        FROM users u 
        LEFT JOIN branches b ON u.branch_id = b.id 
        $where_sql 
        ORDER BY u.created_at DESC";

$result = mysqli_query($conn, $sql);
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

Close($conn);

$_SESSION['admin_users'] = $users;
$_SESSION['admin_user_search'] = $search;
$_SESSION['admin_user_role_filter'] = $role_filter;

header('Location: ../Views/Admin/UserListView.php');
exit();
