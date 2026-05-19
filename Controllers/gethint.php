<?php
// gethint.php - returns JSON suggestions for member names
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once '../Models/DB.php';
require_once '../Models/LibrarianWorkflowModel.php';

if (!isset($_SESSION['role'])) {
    echo json_encode([]);
    exit();
}

$q = isset($_POST['q']) ? trim($_POST['q']) : '';
if ($q === '') { echo json_encode([]); exit(); }

$conn = Connect();
$branchInfo = isset($_SESSION['id']) ? getLibrarianBranchByUserId($conn, $_SESSION['id']) : array();
$branchId = isset($branchInfo['branch_id']) ? (int)$branchInfo['branch_id'] : 0;
$term = mysqli_real_escape_string($conn, $q);
$sql = "SELECT id, name, email, phone FROM users WHERE role = 'member' AND branch_id = '$branchId' AND (name LIKE '%$term%' OR email LIKE '%$term%' OR phone LIKE '%$term%') ORDER BY name LIMIT 10";
$res = mysqli_query($conn, $sql);
$suggestions = array();
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $suggestions[] = array(
            'id' => (int)$r['id'],
            'name' => $r['name'],
            'email' => $r['email'],
            'phone' => $r['phone']
        );
    }
}

Close($conn);
echo json_encode($suggestions);
exit();

?>
