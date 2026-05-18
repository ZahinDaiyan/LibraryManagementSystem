<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once '../Models/DB.php';

$search = $_POST['search'] ?? '';
$role_filter = $_POST['role_filter'] ?? '';

$conn = Connect();

$where_clauses = [];
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $where_clauses[] = "(u.name LIKE '%$search%' OR u.email LIKE '%$search%' OR u.phone LIKE '%$search%')";
}
if (!empty($role_filter)) {
    $role_filter = mysqli_real_escape_string($conn, $role_filter);
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
