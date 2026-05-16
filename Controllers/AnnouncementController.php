<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch_manager') {
    header("Location: ../Views/login.php");
    exit;
}

require_once "../config/Database.php";
require_once "../Models/AnnouncementModel.php";

$db = new Database();
$conn = $db->getConnection();

$announcementModel = new AnnouncementModel($conn);
$managerId = (int)$_SESSION['user_id'];

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete_announcement') {
        header("Content-Type: application/json");

        $announcementId = isset($_POST['announcement_id']) ? (int)$_POST['announcement_id'] : 0;

        if ($announcementId <= 0) {
            echo json_encode([
                "success" => false,
                "message" => "Invalid announcement selected."
            ]);
            exit;
        }

        echo json_encode($announcementModel->deleteAnnouncement($announcementId, $managerId));
        exit;
    }

    if ($action === 'create_announcement') {
        $branchId = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : 0;
        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');

        if ($title === '') {
            $error = "Announcement title is required.";
        } elseif ($body === '') {
            $error = "Announcement body is required.";
        } elseif (strlen($title) > 255) {
            $error = "Announcement title cannot exceed 255 characters.";
        } else {
            $result = $announcementModel->createAnnouncement($branchId, $managerId, $title, $body, $managerId);

            if ($result['success']) {
                header("Location: AnnouncementController.php?success=" . urlencode($result['message']));
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

$allowedScopes = ["", "platform", "branch", "own"];

$filters = [
    "scope" => trim($_GET['scope'] ?? ''),
    "branch_id" => isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 0,
    "keyword" => trim($_GET['keyword'] ?? '')
];

if (!in_array($filters["scope"], $allowedScopes, true)) {
    $filters["scope"] = "";
}

if ($filters["branch_id"] > 0 && !$announcementModel->isBranchManagedByManager($filters["branch_id"], $managerId)) {
    $error = "Selected branch is not under your oversight.";
    $filters["branch_id"] = 0;
}

if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

$branches = $announcementModel->getManagedBranches($managerId);
$summary = $announcementModel->getAnnouncementSummary($managerId);
$announcements = $announcementModel->getAnnouncements($managerId, $filters);

require_once "../Views/announcement.php";

?>