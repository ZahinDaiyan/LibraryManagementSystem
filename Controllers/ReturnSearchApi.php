<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method.']);
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized - please login as librarian.']);
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/LibrarianWorkflowModel.php';

// retrieve query and basic session data
$q = isset($_POST['q']) ? trim($_POST['q']) : '';

$conn = Connect();
$branchInfo = null;
if (isset($_SESSION['id'])) {
    $branchInfo = getLibrarianBranchByUserId($conn, $_SESSION['id']);
}
$branchId = isset($branchInfo['branch_id']) ? (int)$branchInfo['branch_id'] : 0;

if ($q === '') {
    echo json_encode(['error' => 'Please enter a borrow id or member name to search.']);
    Close($conn);
    exit();
}

if (!$branchId) {
    echo json_encode(['error' => 'No branch assigned to your account. Contact administrator.']);
    Close($conn);
    exit();
}

$rows = getBorrowRecordForReturnSearch($conn, $branchId, $q);

if (empty($rows)) {
    echo json_encode([]);
    Close($conn);
    exit();
}

$out = [];
foreach ($rows as $record) {
    $out[] = [
        'id' => (int)$record['id'],
        'member_name' => $record['member_name'],
        'book_title' => $record['book_title'],
        'status' => $record['status'],
        'due_date' => $record['due_date']
    ];
}

Close($conn);
echo json_encode($out);
exit();

?>
