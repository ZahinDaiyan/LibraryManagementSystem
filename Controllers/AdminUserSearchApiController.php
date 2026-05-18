<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';

$search = $_POST['search'] ?? '';
$role_filter = $_POST['role_filter'] ?? '';

$conn = Connect();

$users_data = searchUsersWithBranch($conn, $search, $role_filter);
$users = [];
foreach ($users_data as $row) {
    $users[] = [
        'id' => $row['id'],
        'name' => htmlspecialchars($row['name']),
        'email' => htmlspecialchars($row['email']),
        'phone' => htmlspecialchars($row['phone']),
        'role' => htmlspecialchars($row['role']),
        'branch_name' => $row['branch_name'] ? htmlspecialchars($row['branch_name']) : 'Global/None',
        'is_active' => (int)$row['is_active'],
        'created_at' => date('M d, Y', strtotime($row['created_at']))
    ];
}

Close($conn);

echo json_encode($users);
exit();
?>
