<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    echo '<tr><td colspan="4">Unauthorized</td></tr>';
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/LibrarianWorkflowModel.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$conn = Connect();
$branchInfo = getLibrarianBranchByUserId($conn, $_SESSION['id']);
$branchId = isset($branchInfo['branch_id']) ? (int)$branchInfo['branch_id'] : 0;

if ($q === '' || !$branchId) {
    echo '<tr><td colspan="4">No members found.</td></tr>';
    Close($conn);
    exit();
}

$members = searchMembersByBranch($conn, $branchId, $q);

if (empty($members)) {
    echo '<tr><td colspan="4">No members found.</td></tr>';
    Close($conn);
    exit();
}

foreach ($members as $member) {
    $id = htmlspecialchars($member['id']);
    $name = htmlspecialchars($member['name']);
    $email = htmlspecialchars($member['email']);
    $phone = htmlspecialchars($member['phone']);
    echo '<tr>';
    echo '<td>' . $name . '</td>';
    echo '<td>' . $email . '</td>';
    echo '<td>' . $phone . '</td>';
    echo '<td><a href="../../Controllers/LibrarianOperationsController.php?member_id=' . $id . '">View History</a></td>';
    echo '</tr>';
}

Close($conn);
exit();

?>
