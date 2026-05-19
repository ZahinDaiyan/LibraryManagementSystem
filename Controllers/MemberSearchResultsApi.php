<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/LibrarianWorkflowModel.php';

$q = isset($_POST['q']) ? trim($_POST['q']) : '';

$conn = Connect();
$branchInfo = getLibrarianBranchByUserId($conn, $_SESSION['id']);
$branchId = isset($branchInfo['branch_id']) ? (int)$branchInfo['branch_id'] : 0;

if ($q === '' || !$branchId) {
    echo json_encode([]);
    Close($conn);
    exit();
}

$members = searchMembersByBranch($conn, $branchId, $q);

if (empty($members)) {
    echo json_encode([]);
    Close($conn);
    exit();
}

$out = [];
foreach ($members as $member) {
    $out[] = [
        'id' => (int)$member['id'],
        'name' => $member['name'],
        'email' => $member['email'],
        'phone' => $member['phone']
    ];
}

Close($conn);
echo json_encode($out);
exit();

?>
