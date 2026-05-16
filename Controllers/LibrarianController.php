<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch_manager') {
    header("Location: ../Views/login.php");
    exit;
}

require_once "../config/Database.php";
require_once "../Models/LibrarianModel.php";

$db = new Database();
$conn = $db->getConnection();

$librarianModel = new LibrarianModel($conn);
$managerId = (int)$_SESSION['user_id'];

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'assign_librarian') {
        $librarianId = isset($_POST['librarian_id']) ? (int)$_POST['librarian_id'] : 0;
        $branchId = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : 0;

        if ($librarianId <= 0 || $branchId <= 0) {
            $error = "Please select both librarian and branch.";
        } else {
            $result = $librarianModel->assignLibrarian($librarianId, $branchId, $managerId);

            if ($result['success']) {
                header("Location: LibrarianController.php?success=" . urlencode($result['message']));
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }

    if ($action === 'remove_assignment') {
        $librarianId = isset($_POST['librarian_id']) ? (int)$_POST['librarian_id'] : 0;

        if ($librarianId <= 0) {
            $error = "Invalid librarian selected.";
        } else {
            $result = $librarianModel->removeLibrarianAssignment($librarianId, $managerId);

            if ($result['success']) {
                header("Location: LibrarianController.php?success=" . urlencode($result['message']));
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

$branches = $librarianModel->getManagedBranches($managerId);
$librarians = $librarianModel->getLibrarians();
$assignedLibrarians = $librarianModel->getLibrariansAssignedToManagedBranches($managerId);

require_once "../Views/librarian_assign.php";

?>