<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/AnnouncementModel.php';
require_once '../Models/AuditModel.php';

$action = $_POST['action'] ?? $_POST['action'] ?? '';
$conn = Connect();
$errors = [];
$admin_id = $_SESSION['id'];

if ($action === 'create' || $action === 'update') {
    $title = htmlspecialchars($_POST['title']);
    $body = htmlspecialchars($_POST['body']);
    $branch_id = $_POST['branch_id'];
    $id = $_POST['id'] ?? null;

    if (empty($title)) $errors['title'] = "Title is required";
    if (empty($body)) $errors['body'] = "Content is required";

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        if ($id) {
            $_SESSION['admin_announcement_form_id'] = $id;
        }
        Close($conn);
        header('Location: AdminAnnouncementFormController.php');
        exit();
    }

    if ($action === 'create') {
        if (createAnnouncement($conn, $title, $body, $branch_id, $admin_id)) {
            $new_id = mysqli_insert_id($conn);
            logAction($conn, $admin_id, "Created Announcement", "announcements", $new_id, "Title: $title");
            $_SESSION['msg'] = "Announcement posted successfully.";
        } else {
            $_SESSION['error'] = "Failed to post announcement.";
        }
    } else {
        if (updateAnnouncement($conn, $id, $title, $body, $branch_id)) {
            logAction($conn, $admin_id, "Updated Announcement", "announcements", $id, "Title: $title");
            $_SESSION['msg'] = "Announcement updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update announcement.";
        }
    }

} elseif ($action === 'delete') {
    $id = $_POST['id'];
    if (deleteAnnouncement($conn, $id)) {
        logAction($conn, $admin_id, "Deleted Announcement", "announcements", $id);
        $_SESSION['msg'] = "Announcement deleted.";
    } else {
        $_SESSION['error'] = "Failed to delete announcement.";
    }
}

Close($conn);
header('Location: AdminAnnouncementController.php');
exit();
