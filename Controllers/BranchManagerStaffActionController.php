<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchManagerModel.php';

$action = $_POST['action'] ?? '';
$conn = Connect();

if ($action === 'assign') {
    $librarianId = isset($_POST['librarian_id']) ? (int)$_POST['librarian_id'] : 0;
    $branchId = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : 0;
    $ok = bmAssignLibrarianToBranch($conn, $_SESSION['id'], $librarianId, $branchId);
    $_SESSION[$ok ? 'msg' : 'error'] = $ok ? 'Librarian assigned successfully' : 'Unable to assign librarian';
} elseif ($action === 'remove') {
    $librarianId = isset($_POST['librarian_id']) ? (int)$_POST['librarian_id'] : 0;
    $ok = bmRemoveLibrarianAssignment($conn, $_SESSION['id'], $librarianId);
    $_SESSION[$ok ? 'msg' : 'error'] = $ok ? 'Librarian assignment removed' : 'Unable to remove librarian assignment';
}

Close($conn);

header('Location: BranchManagerStaffController.php');
exit();

?>
