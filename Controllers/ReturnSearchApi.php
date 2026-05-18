<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<tr><td colspan="6">Invalid request method.</td></tr>';
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    echo '<tr><td colspan="6">Unauthorized - please login as librarian.</td></tr>';
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
    echo '<tr><td colspan="6">Please enter a borrow id or member name to search.</td></tr>';
    Close($conn);
    exit();
}

if (!$branchId) {
    echo '<tr><td colspan="6">No branch assigned to your account. Contact administrator.</td></tr>';
    Close($conn);
    exit();
}

$rows = getBorrowRecordForReturnSearch($conn, $branchId, $q);

if (empty($rows)) {
    echo '<tr><td colspan="6">No records found.</td></tr>';
    Close($conn);
    exit();
}

foreach ($rows as $record) {
    $id = htmlspecialchars($record['id']);
    $member = htmlspecialchars($record['member_name']);
    $book = htmlspecialchars($record['book_title']);
    $status = htmlspecialchars($record['status']);
    $due = htmlspecialchars($record['due_date']);

    echo '<tr>';
    echo '<td>' . $id . '</td>';
    echo '<td>' . $member . '</td>';
    echo '<td>' . $book . '</td>';
    echo '<td>' . $status . '</td>';
    echo '<td>' . $due . '</td>';
    echo '<td>';
    echo '<form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsActionController.php" method="POST">';
    echo '<input type="hidden" name="action" value="process_return">';
    echo '<input type="hidden" name="borrow_record_id" value="' . $id . '">';
    echo '<button type="submit">Mark Returned</button>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
}

Close($conn);
exit();

?>
