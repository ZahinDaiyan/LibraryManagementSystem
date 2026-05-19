<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    http_response_code(403);
    echo json_encode(array('error' => 'Unauthorized'));
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/LibrarianWorkflowModel.php';

$q = isset($_POST['q']) ? trim($_POST['q']) : '';

$conn = Connect();
$branchInfo = isset($_SESSION['id']) ? getLibrarianBranchByUserId($conn, $_SESSION['id']) : array();
$branchId = isset($branchInfo['branch_id']) ? (int)$branchInfo['branch_id'] : 0;

if ($q === '' || !$branchId) {
    Close($conn);
    echo json_encode(array());
    exit();
}

$members = searchMembersByBranch($conn, $branchId, $q);
$out = array();
foreach ($members as $member) {
    $out[] = array(
        'id' => (int)$member['id'],
        'name' => $member['name'],
        'email' => $member['email'],
        'phone' => $member['phone']
    );
}

Close($conn);
echo json_encode($out);
exit();

?>